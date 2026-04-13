<?php

namespace Tests\Feature;

use App\Collection;
use App\Notifications\CollectionSubscriptionCreated;
use App\Permission;
use App\Role;
use App\User;
use App\UserPermission;
use App\UserRole;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiCollectionSubscriptionTest extends TestCase
{
    public function testSubscribeEndpointRequiresAuthentication()
    {
        $response = $this->postJson('/api/collections/subscribe-user', [
            'email' => 'guest@example.com',
            'collection_id' => [1],
            'till_date' => now()->addMonth()->format('Y-m-d'),
        ]);

        $response->assertStatus(401);
    }

    public function testSubscribeEndpointBlocksNonAdminUser()
    {
        $user = User::find(2);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/collections/subscribe-user', [
            'email' => 'nonadmin@example.com',
            'collection_id' => [1],
            'till_date' => now()->addMonth()->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
    }

    public function testAdminCanCreateAndSubscribeUserToCollection()
    {
        Notification::fake();

        $admin = User::find(1);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        UserRole::firstOrCreate([
            'user_id' => $admin->id,
            'role_id' => $adminRole->id,
        ]);

        $viewPermission = Permission::firstOrCreate(
            ['name' => 'VIEW'],
            ['description' => 'Can view any content']
        );

        $collection = Collection::create([
            'name' => 'api-subscribe-' . uniqid(),
            'description' => 'Collection for API subscribe test',
            'type' => 'Members Only',
            'user_id' => $admin->id,
        ]);

        $email = 'api_subscribe_' . uniqid() . '@example.com';
        $tillDate = now()->addMonth()->format('Y-m-d');

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/collections/subscribe-user', [
            'email' => $email,
            'name' => 'API Subscribed User',
            'collection_id' => [$collection->id],
            'till_date' => $tillDate,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User subscription saved successfully.',
                'user_created' => true,
                'email' => $email,
                'permission' => 'VIEW',
                'till_date' => $tillDate,
            ]);

        $newUser = User::where('email', $email)->first();
        $this->assertNotNull($newUser);
        $this->assertNotNull($newUser->email_verified_at);

        $this->assertDatabaseHas('user_permissions', [
            'user_id' => $newUser->id,
            'collection_id' => $collection->id,
            'permission_id' => $viewPermission->id,
            'till_date' => $tillDate,
        ]);

        Notification::assertSentTo($newUser, CollectionSubscriptionCreated::class);

        UserPermission::where('user_id', $newUser->id)->where('collection_id', $collection->id)->delete();
        $collection->delete();
        $newUser->forceDelete();
    }

    public function testAdminCanSubscribeUserUsingFormPayload()
    {
        Notification::fake();

        $admin = User::find(1);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        UserRole::firstOrCreate([
            'user_id' => $admin->id,
            'role_id' => $adminRole->id,
        ]);

        $viewPermission = Permission::firstOrCreate(
            ['name' => 'VIEW'],
            ['description' => 'Can view any content']
        );

        $collection = Collection::create([
            'name' => 'api-subscribe-form-' . uniqid(),
            'description' => 'Collection for API subscribe form payload test',
            'type' => 'Members Only',
            'user_id' => $admin->id,
        ]);

        $email = 'api_subscribe_form_' . uniqid() . '@example.com';
        $tillDate = now()->addMonth()->format('Y-m-d');

        Sanctum::actingAs($admin);

        $response = $this->post('/api/collections/subscribe-user', [
            'email' => $email,
            'name' => 'API Form Subscribed User',
            'collection_id' => (string) $collection->id,
            'till_date' => $tillDate,
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User subscription saved successfully.',
                'user_created' => true,
                'email' => $email,
                'permission' => 'VIEW',
                'till_date' => $tillDate,
            ]);

        $newUser = User::where('email', $email)->first();
        $this->assertNotNull($newUser);
        $this->assertNotNull($newUser->email_verified_at);

        $this->assertDatabaseHas('user_permissions', [
            'user_id' => $newUser->id,
            'collection_id' => $collection->id,
            'permission_id' => $viewPermission->id,
            'till_date' => $tillDate,
        ]);

        Notification::assertSentTo($newUser, CollectionSubscriptionCreated::class);

        UserPermission::where('user_id', $newUser->id)->where('collection_id', $collection->id)->delete();
        $collection->delete();
        $newUser->forceDelete();
    }
}
