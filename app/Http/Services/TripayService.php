<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class TripayService
{
    private $apiKey;
    private $merchantCode;
    private $secretKey;
    private $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('tripay.apikey');
        $this->merchantCode = config('tripay.merchant_code');
        $this->secretKey = config('tripay.secret_key');

        // Tentukan Base URL berdasarkan mode
        $isProduction = config('tripay.production_mode');
        $this->apiUrl = $isProduction
            ? 'https://tripay.co.id/api'
            : 'https://tripay.co.id/api-sandbox';
    }

    /**
     * Mengambil daftar channel pembayaran
     */
    public function channels()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->apiUrl . '/merchant/payment-channel');

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Tripay Channels Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Mengambil instruksi pembayaran berdasarkan kode channel (misal: BRIVA)
     */
    public function instructions(string $paymentCode)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->apiUrl . '/payment/instruction', [
                'code' => $paymentCode
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Tripay Instructions Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    /**
     * Calculate fee
     */
    public function calculateFee(float|int $price, string $payment_code)
    {


        try {
            $response  = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
                ->get($this->apiUrl . '/merchant/fee-calculator', [
                    'code' => $payment_code,
                    'amount' => $price
                ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Tripay Calculate Fee Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Merchant transactions
     */
    public function transactions(int $page = 1, int $perPage = 10)
    {

        try {
            $response  = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
                ->get($this->apiUrl . '/merchant/transactions', [
                    'page' => $page,
                    'per_page' => $perPage
                ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Tripay Transactions Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Detail transactions
     */
    public function txdetail($reference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->apiUrl . '/transaction/detail', [
                'reference' => $reference
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Tripay TxDetail Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Signature generate
     */
    private function signature(string $merchantRef, int $amount)
    {
        $signature = hash_hmac('sha256', $this->merchantCode . $merchantRef . $amount, $this->secretKey);
        return $signature;
    }

    /**
     * signature for open payment
     */
    private function signatureOpen(string $merchantRef , string $methodCode)
    {
        $signature = hash_hmac('sha256', $this->merchantCode.$methodCode.$merchantRef, $this->secretKey);
        return $signature;
    }
 
    public function txcreate(array $data)
    {
        try {
            // Otomatis buat signature di sini agar Controller bersih
            $signature = $this->signature($data['merchant_ref'], $data['amount']);

            // Gabungkan signature ke data asli
            $payload = array_merge($data, [
                'signature' => $signature,
                'expired_time' => $data['expired_time'] ?? (time() + (24 * 60 * 60))
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->apiUrl . '/transaction/create', $payload);

            return $response->json();
        } catch (\Throwable $th) {
            Log::error('Tripay TxCreate Error: ' . $th->getMessage());
            return ['success' => false, 'message' => $th->getMessage()];
        }
    }

    /**
     * txopencreate
     */
    public function txopencreate(array $data) {

        try{
            $signature = $this->signatureOpen($data['merchant_ref'] , $data['method']);

            $payload = array_merge($data,[
                'signature' => $signature
            ]);
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
            ])->post($this->apiUrl.'/open-payment/create', $payload);

            return $response->json();
        }catch(\Throwable $th)
        {
            Log::error('Tripay TxOpenCreate Error: '. $th->getMessage());
            return ['success' => false,'message' => $th->getMessage()];
        }
    }
}
