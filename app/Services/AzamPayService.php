<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AzamPayService
{
    protected $baseUrl;
    protected $authUrl;
    protected $clientId;
    protected $clientSecret;
    protected $appName;
    protected $apiKey;
    protected $isSandbox;

    public function __construct()
    {
        $this->isSandbox = config('services.azampay.sandbox', true);
        $this->baseUrl = $this->isSandbox 
            ? 'https://sandbox.azampay.co.tz' 
            : 'https://checkout.azampay.co.tz';
            
        $this->authUrl = $this->isSandbox 
            ? 'https://authenticator-sandbox.azampay.co.tz' 
            : 'https://authenticator.azampay.co.tz';

        $this->clientId = config('services.azampay.client_id');
        $this->clientSecret = config('services.azampay.client_secret');
        $this->appName = config('services.azampay.app_name');
        $this->apiKey = config('services.azampay.api_key');
    }

    /**
     * Generate or retrieve Bearer Token
     */
    public function getToken()
    {
        return Cache::remember('azampay_token', 3000, function () {
            $response = Http::post("{$this->authUrl}/App/Login", [
                'appName' => $this->appName,
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret,
            ]);

            if ($response->successful()) {
                return $response->json()['data']['accessToken'];
            }

            Log::error('AzamPay Token Error: ' . $response->body());
            throw new \Exception('Failed to authenticate with AzamPay');
        });
    }

    /**
     * Mobile Money Checkout (MNO)
     */
    public function mobileCheckout($accountNumber, $amount, $transactionId, $provider)
    {
        $token = $this->getToken();
        
        $payload = [
            'accountNumber' => $accountNumber,
            'amount' => (string) $amount,
            'currency' => 'TZS',
            'externalId' => $transactionId,
            'provider' => $provider, // Airtel, Tigo, Halotel, AzamPesa
        ];

        $response = Http::withToken($token)
            ->withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])
            ->post("{$this->baseUrl}/azampay/mno/checkout", $payload);

        return $response->json();
    }

    /**
     * Bank Checkout (Redirect)
     */
    public function bankCheckout($amount, $transactionId, $redirectUrl)
    {
        $token = $this->getToken();
        
        $payload = [
            'amount' => (string) $amount,
            'currency' => 'TZS',
            'externalId' => $transactionId,
            'sequenceNumber' => $transactionId,
            'successRedirectUrl' => $redirectUrl,
            'failRedirectUrl' => $redirectUrl,
            'cancelRedirectUrl' => $redirectUrl,
            'merchantName' => $this->appName,
        ];

        $response = Http::withToken($token)
            ->withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])
            ->post("{$this->baseUrl}/azampay/bank/checkout", $payload);

        return $response->json();
    }
}
