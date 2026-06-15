<?php

namespace App\Http\Controllers;

use App\Collection;
use App\CollectionSubscription;
use App\Services\CollectionSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CollectionSoapSubscriptionController extends Controller
{
    public function __construct(protected CollectionSubscriptionService $subscriptionService)
    {
    }

    public function showForm($collection_id)
    {
        $collection = Collection::findOrFail($collection_id);

        abort_unless($this->subscriptionService->isEnabled($collection), 404);

        return view('collection-subscription', [
            'collection' => $collection,
            'subscriptionConfig' => $this->subscriptionService->subscriptionConfig($collection),
            'activePage' => 'collections',
        ]);
    }

    public function start(Request $request, $collection_id)
    {
        $collection = Collection::findOrFail($collection_id);

        abort_unless($this->subscriptionService->isEnabled($collection), 404);

        $validated = $request->validate([
            'name'   => ['nullable', 'string', 'max:255'],
            'email'  => ['nullable', 'email', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:20'],
        ]);

        $name   = $validated['name']   ?? optional($request->user())->name;
        $email  = $validated['email']  ?? optional($request->user())->email;
        $mobile = $validated['mobile'] ?? null;

        $existingSuccess = $this->subscriptionService->successfulSubscriptionFor($collection, $request->user(), $email);
        if ($existingSuccess) {
            Session::flash('alert-success', 'You are already subscribed to this collection.');
            return redirect('/collection/' . $collection->id);
        }

        if (empty($email)) {
            return back()->withErrors(['email' => 'An email address is required to start the subscription.']);
        }

        $paymentUri = $this->subscriptionService->paymentUri($collection);
        if ($paymentUri === '') {
            Session::flash('alert-danger', 'Payment URI is not configured for this collection.');
            return back()->withInput();
        }

        $subscription = $this->subscriptionService->createPendingSubscription($collection, [
            'name'   => $name,
            'email'  => $email,
            'mobile' => $mobile,
        ], $request->user());

        // Pre-register with the SPPU gateway (GetPaymentDetails SOAP call).
        $registered = $this->subscriptionService->registerWithSppuGateway($subscription, $collection);
        if (!$registered) {
            Session::flash('alert-danger', 'Could not register the payment request with the gateway. Please try again or contact support.');
            return back()->withInput();
        }

        // SPPU requires redirection to the Payment URI with Application_ID = Encrypted ChallanNo
        $encryptedChallan = $this->subscriptionService->encryptChallanNo($subscription->challan);
        $redirectUrl = rtrim($paymentUri, '/') . '?Application_ID=' . urlencode($encryptedChallan);

        return redirect()->away($redirectUrl);
    }

    public function reconcile(Request $request, $collection_id)
    {
        $collection = Collection::findOrFail($collection_id);

        $subscription = CollectionSubscription::query()
            ->where('collection_id', $collection->id)
            ->where(function ($query) use ($request) {
                if ($request->filled('challan') && $request->filled('subscription_id')) {
                    $query->where('challan', $request->input('challan'))
                        ->orWhere('id', $request->input('subscription_id'));
                    return;
                }

                if ($request->filled('challan')) {
                    $query->where('challan', $request->input('challan'));
                    return;
                }

                if ($request->filled('subscription_id')) {
                    $query->where('id', $request->input('subscription_id'));
                }
            })
            ->firstOrFail();

        $reconciled = $this->subscriptionService->reconcile($subscription, $request->all());

        if ($reconciled->payment_status === 'success') {
            Session::flash('alert-success', 'Payment recorded and access granted successfully.');
            return redirect('/collection/' . $collection->id);
        }

        Session::flash('alert-danger', 'Payment reconciliation failed.');
        return redirect('/collection/' . $collection->id . '/soap-subscription');
    }
}