<?php

namespace Tests\Feature;

use App\Collection;
use App\Notifications\CollectionSubscriptionPaymentCompleted;
use App\Permission;
use App\Role;
use App\User;
use App\UserPermission;
use App\UserRole;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CollectionSoapSubscriptionTest extends TestCase
{
    public function testSoapSubscriptionSettingsCanBeSavedForMembersOnlyCollections()
    {
        $user = User::find(1);
        $collection = Collection::find(3);
        $originalConfig = $collection->column_config;

        $response = $this->actingAs($user)->get('/collection/3/settings');
        $response->assertStatus(200);
        $response->assertSee('SOAP based subscription');

        $this->actingAs($user)->post('/collection/3/settings', [
            '_token' => csrf_token(),
            'collection_id' => 3,
            'pdf_viewer' => 'viewerjs',
            'soap_subscription_enabled' => '1',
            'soap_subscription_amount' => '500.00',
            'soap_subscription_process_code' => '87',
            'soap_subscription_valid_days' => '180',
            'soap_subscription_sequence_length' => '5',
            'soap_subscription_challan_template' => '{{year}}{{process_code}}{{month}}{{sequence}}',
            'soap_subscription_payment_uri' => 'https://payments.example.com/pay',
            'soap_subscription_reconciliation_uri' => 'https://payments.example.com/reconcile',
            'soap_subscription_notification_email' => 'ops@example.com',
        ]);

        $collection->refresh();
        $config = json_decode($collection->column_config);

        $this->assertEquals(1, (int) $config->soap_subscription_enabled);
        $this->assertEquals('500.00', $config->soap_subscription_amount);
        $this->assertEquals('87', $config->soap_subscription_process_code);
        $this->assertEquals(180, (int) $config->soap_subscription_valid_days);
        $this->assertEquals(5, (int) $config->soap_subscription_sequence_length);
        $this->assertEquals('{{year}}{{process_code}}{{month}}{{sequence}}', $config->soap_subscription_challan_template);
        $this->assertEquals('https://payments.example.com/pay', $config->soap_subscription_payment_uri);
        $this->assertEquals('https://payments.example.com/reconcile', $config->soap_subscription_reconciliation_uri);
        $this->assertEquals('ops@example.com', $config->soap_subscription_notification_email);

        $collection->column_config = $originalConfig;
        $collection->save();
    }

    public function testSubscriptionPaymentFlowCreatesChallanAndGrantsAccess()
    {
        Notification::fake();

        $collection = Collection::find(3);
        $originalConfig = $collection->column_config;

        $config = json_decode($collection->column_config) ?? new \stdClass();
        $config->soap_subscription_enabled = 1;
        $config->soap_subscription_amount = '500.00';
        $config->soap_subscription_process_code = '87';
        $config->soap_subscription_valid_days = 180;
        $config->soap_subscription_sequence_length = 5;
        $config->soap_subscription_challan_template = '{{year}}{{process_code}}{{month}}{{sequence}}';
        $config->soap_subscription_payment_uri = 'https://payments.example.com/pay';
        $config->soap_subscription_reconciliation_uri = 'https://payments.example.com/reconcile';
        $collection->column_config = json_encode($config);
        $collection->save();

        $email = 'soap-subscription-' . uniqid() . '@example.com';

        $startResponse = $this->post('/collection/3/soap-subscription', [
            '_token' => csrf_token(),
            'name' => 'SOAP Subscriber',
            'email' => $email,
        ]);

        $startResponse->assertStatus(200);
        $startResponse->assertSee('gateway-form');
        $startResponse->assertSee('https://payments.example.com/pay');

        $subscription = \App\CollectionSubscription::where('collection_id', 3)
            ->where('user_email', $email)
            ->first();

        $this->assertNotNull($subscription);
        $this->assertNotEmpty($subscription->challan);
        $this->assertEquals('pending', $subscription->payment_status);

        $reconcileResponse = $this->post('/collection/3/soap-subscription/reconcile', [
            '_token' => csrf_token(),
            'challan' => $subscription->challan,
            'payment_status' => 'success',
            'transaction_id' => 'TXN-' . uniqid(),
        ]);

        $reconcileResponse->assertStatus(302);
        $reconcileResponse->assertRedirect('/collection/3');

        $subscription->refresh();
        $this->assertEquals('success', $subscription->payment_status);
        $this->assertNotNull($subscription->paid_at);

        $newUser = User::where('email', $email)->first();
        $this->assertNotNull($newUser);

        $viewPermission = Permission::where('name', 'VIEW')->first();
        $this->assertDatabaseHas('user_permissions', [
            'user_id' => $newUser->id,
            'collection_id' => $collection->id,
            'permission_id' => $viewPermission->id,
        ]);

        Notification::assertSentTo($newUser, CollectionSubscriptionPaymentCompleted::class);

        UserPermission::where('user_id', $newUser->id)->where('collection_id', $collection->id)->delete();
        $subscription->delete();
        $newUser->forceDelete();

        $collection->column_config = $originalConfig;
        $collection->save();
    }

    public function testDuplicateSuccessfulSubscriptionRequestsAreRedirectedWithoutCreatingNewRecords()
    {
        Notification::fake();

        $collection = Collection::find(3);
        $originalConfig = $collection->column_config;

        $config = json_decode($collection->column_config) ?? new \stdClass();
        $config->soap_subscription_enabled = 1;
        $config->soap_subscription_amount = '500.00';
        $config->soap_subscription_process_code = '87';
        $config->soap_subscription_valid_days = 180;
        $config->soap_subscription_sequence_length = 5;
        $config->soap_subscription_challan_template = '{{year}}{{process_code}}{{month}}{{sequence}}';
        $config->soap_subscription_payment_uri = 'https://payments.example.com/pay';
        $config->soap_subscription_reconciliation_uri = 'https://payments.example.com/reconcile';
        $collection->column_config = json_encode($config);
        $collection->save();

        $email = 'soap-subscription-' . uniqid() . '@example.com';

        $this->post('/collection/3/soap-subscription', [
            '_token' => csrf_token(),
            'name' => 'SOAP Subscriber',
            'email' => $email,
        ])->assertStatus(200);

        $subscription = \App\CollectionSubscription::where('collection_id', 3)
            ->where('user_email', $email)
            ->first();

        $this->post('/collection/3/soap-subscription/reconcile', [
            '_token' => csrf_token(),
            'challan' => $subscription->challan,
            'payment_status' => 'success',
            'transaction_id' => 'TXN-' . uniqid(),
        ])->assertRedirect('/collection/3');

        $firstSubscriptionCount = \App\CollectionSubscription::where('collection_id', 3)
            ->where('user_email', $email)
            ->count();

        $secondStartResponse = $this->post('/collection/3/soap-subscription', [
            '_token' => csrf_token(),
            'name' => 'SOAP Subscriber',
            'email' => $email,
        ]);

        $secondStartResponse->assertRedirect('/collection/3');

        $this->assertSame($firstSubscriptionCount, \App\CollectionSubscription::where('collection_id', 3)
            ->where('user_email', $email)
            ->count());

        $collection->column_config = $originalConfig;
        $collection->save();
    }

    public function testReconcileIsIdempotentForRepeatedSuccessCallbacks()
    {
        Notification::fake();

        $collection = Collection::find(3);
        $originalConfig = $collection->column_config;

        $config = json_decode($collection->column_config) ?? new \stdClass();
        $config->soap_subscription_enabled = 1;
        $config->soap_subscription_amount = '500.00';
        $config->soap_subscription_process_code = '87';
        $config->soap_subscription_valid_days = 180;
        $config->soap_subscription_sequence_length = 5;
        $config->soap_subscription_challan_template = '{{year}}{{process_code}}{{month}}{{sequence}}';
        $config->soap_subscription_payment_uri = 'https://payments.example.com/pay';
        $config->soap_subscription_reconciliation_uri = 'https://payments.example.com/reconcile';
        $collection->column_config = json_encode($config);
        $collection->save();

        $email = 'soap-subscription-' . uniqid() . '@example.com';

        $this->post('/collection/3/soap-subscription', [
            '_token' => csrf_token(),
            'name' => 'SOAP Subscriber',
            'email' => $email,
        ])->assertStatus(200);

        $subscription = \App\CollectionSubscription::where('collection_id', 3)
            ->where('user_email', $email)
            ->first();

        $firstCallback = [
            '_token' => csrf_token(),
            'challan' => $subscription->challan,
            'payment_status' => 'success',
            'transaction_id' => 'TXN-' . uniqid(),
        ];

        $this->post('/collection/3/soap-subscription/reconcile', $firstCallback)
            ->assertRedirect('/collection/3');

        $subscription->refresh();
        $user = User::where('email', $email)->first();
        $viewPermission = Permission::where('name', 'VIEW')->first();

        $this->post('/collection/3/soap-subscription/reconcile', $firstCallback)
            ->assertRedirect('/collection/3');

        $subscription->refresh();

        $this->assertSame(1, \App\CollectionSubscription::where('collection_id', 3)
            ->where('user_email', $email)
            ->where('payment_status', 'success')
            ->count());

        $this->assertSame(1, \App\UserPermission::where('user_id', $user->id)
            ->where('collection_id', $collection->id)
            ->where('permission_id', $viewPermission->id)
            ->count());

        $this->assertSame(1, Notification::sent($user, CollectionSubscriptionPaymentCompleted::class)->count());

        $collection->column_config = $originalConfig;
        $collection->save();
    }
}