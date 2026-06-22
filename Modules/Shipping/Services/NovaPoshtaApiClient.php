<?php

namespace Modules\Shipping\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class NovaPoshtaApiClient
{

    private string $apiUrl = 'https://api.novaposhta.ua/v2.0/json/';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = setting('novaPoshta_api_key', '');
    }

    public function request(string $modelName, string $calledMethod, array $methodProperties = []): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('API ключ Новой Почты не настроен.');
        }

        $payload = [
            'apiKey'           => $this->apiKey,
            'modelName'        => $modelName,
            'calledMethod'     => $calledMethod,
            'methodProperties' => empty($methodProperties) ? new \stdClass() : $methodProperties,
        ];

        $ch = curl_init($this->apiUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        // Выполнение запроса
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($curlError) {
            Log::error('Nova Poshta cURL Error: ' . $curlError);
            throw new Exception("Ошибка соединения с сервером Новой Почты.");
        }

        if ($httpCode !== 200) {
            Log::error("Nova Poshta HTTP Error: {$httpCode}. Response: {$response}");
            throw new Exception("Сервер Новой Почты ответил ошибкой (HTTP {$httpCode}).");
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Nova Poshta JSON Decode Error. Raw response: ' . $response);
            throw new Exception("Получен некорректный ответ от API Новой Почты.");
        }

        if (empty($decoded['success'])) {
            $errors = implode('; ', $decoded['errors'] ?? ['Неизвестная ошибка API']);
            Log::error("Nova Poshta API Logic Error: {$errors}", ['payload' => $payload]);
            throw new Exception("Ошибка API Новой Почты: " . $errors);
        }

        return $decoded['data'] ?? [];
    }
}
