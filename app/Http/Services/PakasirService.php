<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class PakasirService
{
    private $API_URL = "https://app.pakasir.com/api/";

    /**
     * Common HTTP handler for Pakasir API
     */
    public function http($method = "GET", $endpoint = "", $data = [])
    {
        $url = $this->API_URL . $endpoint;
        $common = [
            'project' => config('pakasir.project'),
            'api_key' => config('pakasir.api_key'),
        ];

        $data = array_merge($common, $data);

        if (strtoupper($method) === 'GET') {
            $response = Http::get($url, $data);
        } else {
            $response = Http::post($url, $data);
        }

        return $response->json();
    }

    /**
     * Create a new transaction
     * 
     * @param string $method (e.g. qris, bni_va, etc)
     * @param string $orderId
     * @param int $amount
     * @return array
     */
    public function createTransaction($method, $orderId, $amount)
    {
        return $this->http('POST', "transactioncreate/{$method}", [
            'order_id' => $orderId,
            'amount' => $amount,
        ]);
    }

    /**
     * Simulate a payment (Sandbox only)
     */
    public function simulatePayment($orderId, $amount)
    {
        return $this->http('POST', 'paymentsimulation', [
            'order_id' => $orderId,
            'amount' => $amount,
        ]);
    }

    /**
     * Cancel an active transaction
     */
    public function cancelTransaction($orderId, $amount)
    {
        return $this->http('POST', 'transactioncancel', [
            'order_id' => $orderId,
            'amount' => $amount,
        ]);
    }

    /**
     * Get transaction details
     */
    public function getTransactionDetail($orderId, $amount)
    {
        return $this->http('GET', 'transactiondetail', [
            'order_id' => $orderId,
            'amount' => $amount,
        ]);
    }
}