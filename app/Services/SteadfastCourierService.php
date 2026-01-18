<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Courier;

class SteadfastCourierService
{
    protected $credentials;
    protected $baseUrl;
    protected $apiKey;
    protected $secretKey;

    public function __construct()
    {
        // Fetch credentials from the couriers table
        $this->credentials = Courier::where('title', 'SteadFast')->first();

        // Decide which URL to use (live or test)
        $this->baseUrl = $this->credentials->is_live == 1
            ? $this->credentials->base_url
            : $this->credentials->test_base_url;

        // Set API credentials from DB fields
        $this->apiKey    = $this->credentials->client_id;
        $this->secretKey = $this->credentials->client_secret;
    }

    /**
     * 📦 Place an order on Steadfast Courier
     * Path: /create_order
     * Method: POST
     */
    public function createOrder(array $data)
    {
        $endpoint = rtrim($this->baseUrl, '/') . '/create_order';
        $response = Http::withOptions([
            'verify' => config('app.env') === 'production',
        ])->asJson()->withHeaders([
            'Api-Key'    => $this->apiKey,
            'Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json'
        ])->post($endpoint, $data);

        return $response->json();
    }

    /**
     * 🚚 Check Delivery Status by Consignment ID
     * Path: /status_by_cid/{id}
     * Method: GET
     */
    public function getDeliveryStatus($consignmentId)
    {
        $endpoint = rtrim($this->baseUrl, '/') . '/status_by_cid/' . $consignmentId;

        $response = Http::withOptions([
            'verify' => config('app.env') === 'production',
        ])->withHeaders([
            'Api-Key'    => $this->apiKey,
            'Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json'
        ])->get($endpoint);

        return $response->json();
    }

    /**
     * 💰 Check Current Balance
     * Path: /get_balance
     * Method: GET
     */
    public function getCurrentBalance()
    {
        $endpoint = rtrim($this->baseUrl, '/') . '/get_balance';

        $response = Http::withOptions([
            'verify' => config('app.env') === 'production',
        ])->withHeaders([
            'Api-Key'    => $this->apiKey,
            'Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json'
        ])->get($endpoint);

        return $response->json();
    }
}