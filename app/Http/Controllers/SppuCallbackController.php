<?php

namespace App\Http\Controllers;

use App\CollectionSubscription;
use App\Services\CollectionSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SppuCallbackController extends Controller
{
    protected $subscriptionService;

    public function __construct(CollectionSubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * This is the GLOBAL "Webservice URL" where SPPU sends the SOAP XML success/failure message.
     * It handles the GetPGReceivedDetails SOAP action.
     */
    public function reconcile(Request $request)
    {
        $xmlContent = $request->getContent(); // Get raw POST body (SOAP XML)
        Log::info('GLOBAL SPPU Callback Received', ['raw_body' => $xmlContent]);

        $decodedXml = html_entity_decode($xmlContent);

        preg_match('/<Status>(.*?)<\/Status>/i', $decodedXml, $statusMatch);
        preg_match('/<ChallanNo>(.*?)<\/ChallanNo>/i', $decodedXml, $challanMatch);

        $status = $statusMatch[1] ?? 'failed';
        $challanNo = $challanMatch[1] ?? null;

        if ($challanNo) {
            // Find subscription across ANY collection using ChallanNo
            $subscription = CollectionSubscription::where('challan', $challanNo)->first();

            if ($subscription) {
                // If payment was successful
                if (strtolower($status) === 'success' || strtolower($status) === 'captured') {
                    $this->subscriptionService->reconcile($subscription, ['payment_status' => 'success']);
                    Log::info("Global SPPU Payment Successful for Challan: $challanNo (Collection ID: {$subscription->collection_id})");
                } else {
                    $this->subscriptionService->reconcile($subscription, ['payment_status' => 'failed']);
                    Log::warning("Global SPPU Payment Failed for Challan: $challanNo");
                }
            } else {
                Log::error("Global SPPU Callback Error: Challan not found in DB - $challanNo");
            }
        } else {
            Log::error("Global SPPU Callback Error: ChallanNo not found in XML payload");
        }

        // Return a standard SOAP Response back to SPPU
        return response(
            '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
              <soap:Body>
                <GetPGReceivedDetailsResponse xmlns="http://tempuri.org/">
                  <GetPGReceivedDetailsResult>1</GetPGReceivedDetailsResult>
                </GetPGReceivedDetailsResponse>
              </soap:Body>
            </soap:Envelope>', 
            200
        )->header('Content-Type', 'text/xml');
    }

    /**
     * This is the GLOBAL "Return URL" where the user's browser is redirected after payment.
     */
    public function returnPage(Request $request)
    {
        // Add a success message to the session so it shows on the dashboard
        return redirect('/dashboard')->with('alert-success', 'Your payment is being processed. If successful, your collection access will be granted shortly.');
    }
}
