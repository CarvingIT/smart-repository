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
        preg_match('/<Field9>(.*?)<\/Field9>/i', $decodedXml, $field9Match);
        preg_match('/<ChallanNo>(.*?)<\/ChallanNo>/i', $decodedXml, $challanMatch);
        preg_match('/<TranID>(.*?)<\/TranID>/i', $decodedXml, $tranIdMatch);
        preg_match('/<MhPayID>(.*?)<\/MhPayID>/i', $decodedXml, $mhPayIdMatch);
        preg_match('/<Transaction_Amount>(.*?)<\/Transaction_Amount>/i', $decodedXml, $amtMatch);
        preg_match('/<Amt>(.*?)<\/Amt>/i', $decodedXml, $altAmtMatch);
        preg_match('/<BankType>(.*?)<\/BankType>/i', $decodedXml, $bankTypeMatch);
        preg_match('/<BankCode>(.*?)<\/BankCode>/i', $decodedXml, $bankCodeMatch);
        preg_match('/<TranDate>(.*?)<\/TranDate>/i', $decodedXml, $tranDateMatch);

        $status = $statusMatch[1] ?? ($field9Match[1] ?? 'failed');
        $challanNo = $challanMatch[1] ?? null;
        $tranId = $tranIdMatch[1] ?? ($mhPayIdMatch[1] ?? null);
        $amount = $amtMatch[1] ?? ($altAmtMatch[1] ?? null);
        $bankType = $bankTypeMatch[1] ?? null;
        $bankCode = $bankCodeMatch[1] ?? null;
        $tranDate = $tranDateMatch[1] ?? null;

        if ($challanNo) {
            // Find subscription across ANY collection using ChallanNo
            $subscription = CollectionSubscription::where('challan', $challanNo)->first();

            if ($subscription) {
                $isSuccess = in_array(strtolower(trim($status)), ['success', 'captured', 'paid'], true);
                $payload = [
                    'payment_status'     => $isSuccess ? 'success' : 'failed',
                    'transaction_id'     => $tranId,
                    'transaction_amount' => $amount,
                    'status'             => $status,
                    'bank_type'          => $bankType,
                    'bank_code'          => $bankCode,
                    'tran_date'          => $tranDate,
                    'raw_callback'       => $decodedXml,
                ];

                $this->subscriptionService->reconcile($subscription, $payload);

                if ($isSuccess) {
                    Log::info("Global SPPU Payment Successful for Challan: $challanNo (Collection ID: {$subscription->collection_id})");
                } else {
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
        $rawChallan = $request->input('ChallanNo') ?? $request->input('challan');
        $challanNo = $rawChallan;

        // If ChallanNo is encrypted (base64 cipher text from SPPU gateway), decrypt it
        if ($rawChallan && (!ctype_digit($rawChallan) || strlen($rawChallan) > 15)) {
            $decrypted = $this->subscriptionService->decryptChallanNo($rawChallan);
            if (!empty($decrypted)) {
                $challanNo = $decrypted;
            }
        }

        if ($challanNo) {
            $subscription = CollectionSubscription::where('challan', $challanNo)->first();
            if ($subscription) {
                if ($subscription->payment_status === 'success') {
                    return redirect('/collection/' . $subscription->collection_id)
                        ->with('alert-success', 'Payment successful! Access granted to the collection.');
                }
                return redirect('/collection/' . $subscription->collection_id . '/soap-subscription')
                    ->with('alert-danger', 'Payment was not completed or failed.');
            }
        }

        return redirect('/dashboard')->with('alert-success', 'Your payment is being processed. If successful, your collection access will be granted shortly.');
    }
}
