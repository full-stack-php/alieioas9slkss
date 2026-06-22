<?php

namespace Modules\Payment\Libraries;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MonobankClient
{
    private string $baseUrl = 'https://api.monobank.ua/api/merchant';

    public function createInvoice(array $payload): array
    {
        return $this->post('/invoice/create', $payload);
    }

    public function invoiceStatus(string $invoiceId): array
    {
        return $this->get('/invoice/status', [
            'invoiceId' => $invoiceId,
        ]);
    }

    public function publicKey(): string
    {
        return Cache::remember('monobank_public_key', now()->addHours(12), function () {
            $response = $this->get('/pubkey');

            return $response['key'] ?? '';
        });
    }

    private function get(string $uri, array $query = []): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->baseUrl . $uri, $query);

        if ($response->failed()) {
            throw new \Exception($response->json('errText') ?: $response->body());
        }

        return $response->json() ?: [];
    }

    private function post(string $uri, array $payload): array
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->baseUrl . $uri, $payload);

        if ($response->failed()) {
            \Log::error('Monobank API error', [
                'url' => $this->baseUrl . $uri,
                'status' => $response->status(),
                'body' => $response->body(),
                'json' => $response->json(),
                'payload' => $payload,
                'token_exists' => ! empty(setting('monobank_token') ?: setting('mono_bank_secret')),
            ]);

            throw new \Exception(
                $response->json('errText')
                    ?: $response->json('message')
                    ?: $response->body()
                        ?: 'Monobank payment error'
            );
        }

        return $response->json() ?: [];
    }

    private function headers(): array
    {
        return [
            'X-Token' => setting('monobank_token'),
            'X-Cms' => 'Korf AK',
            'X-Cms-Version' => app()->version(),
        ];
    }
}
