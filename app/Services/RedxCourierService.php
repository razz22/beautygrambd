<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Courier;
use Illuminate\Support\Facades\Log;

class RedxCourierService
{
    protected $credentials;
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        // Fetch credentials from the couriers table
        $this->credentials = Courier::where('title', 'Redx')->first();
        if (!$this->credentials) {
            $this->credentials = Courier::where('id', 3)->first();
        }
        if ($this->credentials) {
             $this->baseUrl = rtrim($this->credentials->is_live == 1
                ? $this->credentials->base_url
                : $this->credentials->test_base_url, '/');

            // Token mapped to client_id (Live) and test_client_id (Test) based on UI configuration
            $this->token = $this->credentials->is_live == 1 
                ? $this->credentials->client_id 
                : $this->credentials->test_client_id;

        } else {
            Log::error('Redx Courier configuration not found in database.');
            $this->baseUrl = 'https://openapi.redx.com.bd'; // Default fallback
            $this->token = '';
        }
    }

    /**
     * ✅ Place a parcel order with RedX
     */
    public function createParcel(array $data)
    {
        // Note: keeping existing endpoint logic. If strict API versioning is needed, 
        // this might need to be updated to match getAreas pattern (e.g. /parcel)
        $endpoint = $this->baseUrl . '/parcel';

        $response = Http::withOptions([
            'verify' => config('app.env') === 'production',
        ])->asJson()->withHeaders([
            'API-ACCESS-TOKEN' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($endpoint, $data);

        Log::info('RedX Create Parcel Request', [
            'url' => $endpoint,
            'payload' => $data,
            'headers' => ['API-ACCESS-TOKEN' => 'Bearer ' . $this->token],
            'response' => $response->json()
        ]);

        return $response->json();
    }

    /**
     * 📍 Get Areas
     * Documentation: GET /areas
     * 
     * Supported filters:
     * - post_code (integer)
     * - district_name (string)
     */
    public function getAreas(array $filters = [])
    {
        $endpoint = $this->baseUrl . '/areas';
        $response = Http::withOptions([
            'verify' => config('app.env') === 'production',
        ])->withHeaders([
            'API-ACCESS-TOKEN' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ])->get($endpoint, $filters);

        Log::info('RedX Get Areas Request', [
            'url' => $endpoint,
            'filters' => $filters,
            'headers' => ['API-ACCESS-TOKEN' => 'Bearer ' . $this->token],
            'response' => $response->json()
        ]);

        return $response->json();
    }

    /**
     * Get Areas by Post Code
     * Endpoint: /areas?post_code=<postal_code>
     */
    public function getAreasByPostCode($postCode)
    {
        return $this->getAreas(['post_code' => $postCode]);
    }

    /**
     * Get Areas by District Name
     * Endpoint: /areas?district_name=<district_name>
     */
    public function getAreasByDistrictName($districtName)
    {
        return $this->getAreas(['district_name' => $districtName]);
    }
}