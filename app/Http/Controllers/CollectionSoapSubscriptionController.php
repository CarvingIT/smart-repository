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
        ]);
    }

    public function start(Request $request, $collection_id)
    {
        $collection = Collection::findOrFail($collection_id);

        abort_unless($this->subscriptionService->isEnabled($collection), 404);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $name = $validated['name'] ?? optional($request->user())->name;
        $email = $validated['email'] ?? optional($request->user())->email;

        if (empty($email)) {
            return back()->withErrors(['email' => 'An email address is required to start the subscription.']);
        }

        $paymentUri = $this->subscriptionService->paymentUri($collection);
        if ($paymentUri === '') {
            Session::flash('alert-danger', 'Payment URI is not configured for this collection.');
            return back()->withInput();
        }

        $subscription = $this->subscriptionService->createPendingSubscription($collection, [
            'name' => $name,
            'email' => $email,
        ], $request->user());

        return view('collection-subscription-redirect', [
            'collection' => $collection,
            'subscription' => $subscription,
            'paymentUri' => $paymentUri,
            'payload' => $this->subscriptionService->buildGatewayPayload($subscription, $collection),
        ]);
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