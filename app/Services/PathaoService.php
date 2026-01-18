<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Courier;

class PathaoService
{
    protected $baseUrl;
    protected $token;
    protected $credentials;

    public function __construct()
    {
        $this->credentials = Courier::where('id', 1)->first();

        $this->baseUrl = rtrim($this->credentials->is_live == 1
            ? $this->credentials->base_url
            : $this->credentials->test_base_url, '/');

        $this->authenticate();
    }

    protected function authenticate()
    {
        if($this->credentials->is_live == 1){
            $response = Http::withOptions([
                'verify' => config('app.env') === 'production',
            ])->post($this->baseUrl . '/aladdin/api/v1/issue-token', [
                'client_id' => $this->credentials->client_id,
                'client_secret' => $this->credentials->client_secret,
                'grant_type' => 'password',
                'username' => $this->credentials->client_email,
                'password' => $this->credentials->client_password,
            ]);
        }else{
            $response = Http::withOptions([
                'verify' => config('app.env') === 'production',
            ])->post($this->baseUrl . '/aladdin/api/v1/issue-token', [
                'client_id' => $this->credentials->test_client_id,
                'client_secret' => $this->credentials->test_client_secret,
                'grant_type' => 'password',
                'username' => $this->credentials->client_email,
                'password' => $this->credentials->client_password,
            ]);
        }

        if ($response->successful()) {
            $this->token = $response->json()['access_token'];
        } else {
            throw new \Exception('Pathao authentication failed: ' . $response->body());
        }
    }

    public function getToken()
    {
        return $this->token;
    }


    public function createOrder(array $payload)
    {
        $response = Http::withOptions([
            'verify' => config('app.env') === 'production',
        ])->withToken($this->token)
            ->post($this->baseUrl . '/aladdin/api/v1/orders', $payload);

        return $response->json();
    }

    public function getCities()
    {
        $response = Http::withOptions([
            'verify' => config('app.env') === 'production',
        ])->withToken($this->token)
            ->get($this->baseUrl . '/aladdin/api/v1/city-list');

        return $response->json();
    }

    public function getZones($cityId)
    {
        $response = Http::withOptions([
            'verify' => config('app.env') === 'production',
        ])->withToken($this->token)
            ->get($this->baseUrl . "/aladdin/api/v1/cities/{$cityId}/zone-list");

        return $response->json();
    }

    public function getAreas($zoneId)
    {
        $response = Http::withOptions([
            'verify' => config('app.env') === 'production',
        ])->withToken($this->token)
            ->get($this->baseUrl . "/aladdin/api/v1/zones/{$zoneId}/area-list");

        return $response->json();
    }

    public function getStores()
    {
        $response = Http::withOptions([
            'verify' => config('app.env') === 'production',
        ])->withToken($this->token)
            ->get($this->baseUrl . '/aladdin/api/v1/stores');

        return $response->json();
    }
    public function getDeliveryChargeWithPayload(array $payload)
    {
        $response = Http::withOptions([
            'verify' => config('app.env') === 'production',
        ])->withToken($this->token)
            ->post($this->baseUrl . '/aladdin/api/v1/merchant/price-plan', $payload);

        return $response->json();
    }

    public function getOrderInfo($consignmentId)
    {
        $response = Http::withOptions([
            'verify' => config('app.env') === 'production',
        ])->withToken($this->token)
            ->get($this->baseUrl . "/aladdin/api/v1/orders/{$consignmentId}/info");

        return $response->json();
    }


}
