<?php

namespace Modules\Shipping\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class MeestApiClient
{
    private string $apiUrl = 'https://publicapi.meest.com';

    private const ALLOWED_BRANCH_TYPES = [
        'poshtomat',
        'minibranch',
        'mainbranch',
    ];

    /**
     * Получить все отделения по типу с учетом пагинации.
     */
    public function getBranches(string $type, int $limit = 1000): array
    {
        if (!in_array($type, self::ALLOWED_BRANCH_TYPES, true)) {
            throw new Exception("Недопустимый тип отделений Meest: {$type}");
        }

        $page = 1;
        $branches = [];
        $seen = [];

        while (true) {
            $response = $this->request('/branches', [
                'type' => $type,
                'page' => $page,
                'limit' => $limit,
            ], ['type' => $type, 'page' => $page]);

            $items = $this->extractItems($response);

            if (empty($items)) {
                break;
            }

            $newItemsOnPage = 0;

            foreach ($items as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $key = $item['br_id'] ?? $item['num'] ?? null;

                if ($key && isset($seen[$key])) {
                    continue;
                }

                if ($key) {
                    $seen[$key] = true;
                }

                $branches[] = $item;
                $newItemsOnPage++;
            }

            $total = $this->extractTotal($response);

            if ($total !== null && count($branches) >= $total) {
                break;
            }

            if (count($items) < $limit || $newItemsOnPage === 0) {
                break;
            }

            $page++;
        }

        return $branches;
    }

    /**
     * Получить полную информацию по конкретному отделению.
     */
    public function getBranch(string $num): array
    {
        $response = $this->request('/branches/' . rawurlencode($num), [], ['num' => $num]);

        $item = $this->extractSingleItem($response);

        if (empty($item)) {
            Log::warning('Meest branch detail is empty.', ['num' => $num, 'response' => $response]);
        }

        return $item;
    }

    private function request(string $path, array $query = [], array $logContext = []): array
    {
        $url = rtrim($this->apiUrl, '/') . $path;

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($curlError) {
            Log::error('Meest cURL Error: ' . $curlError, $logContext + ['url' => $url]);
            throw new Exception('Ошибка соединения с сервером Meest.');
        }

        if ($httpCode !== 200) {
            Log::error("Meest HTTP Error: {$httpCode}. Response: {$response}", $logContext + ['url' => $url]);
            throw new Exception("Сервер Meest ответил ошибкой (HTTP {$httpCode}).");
        }

        $decoded = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Meest JSON Decode Error. Raw response: ' . $response, $logContext + ['url' => $url]);
            throw new Exception('Получен некорректный ответ от API Meest.');
        }

        if (!is_array($decoded)) {
            Log::error('Meest unexpected response format.', $logContext + [
                    'url' => $url,
                    'response' => $decoded,
                ]);

            throw new Exception('API Meest вернул ответ в неожиданном формате.');
        }

        return $decoded;
    }

    /**
     * Для списка отделений API может вернуть:
     * - массив сразу;
     * - ['result' => [...]];
     * - ['data' => [...]];
     * - ['result' => ['items' => [...]]].
     */
    private function extractItems(array $response): array
    {
        $data = $response['result'] ?? $response['data'] ?? $response;

        if (isset($data['items']) && is_array($data['items'])) {
            return $data['items'];
        }

        if (isset($data['branches']) && is_array($data['branches'])) {
            return $data['branches'];
        }

        if (isset($data[0]) && is_array($data[0])) {
            return $data;
        }

        return [];
    }

    /**
     * Для одного отделения API может вернуть:
     * - объект сразу;
     * - ['result' => объект];
     * - ['data' => объект];
     * - ['result' => [объект]].
     */
    private function extractSingleItem(array $response): array
    {
        $data = $response['result'] ?? $response['data'] ?? $response;

        if (isset($data[0]) && is_array($data[0])) {
            return $data[0];
        }

        if (isset($data['items'][0]) && is_array($data['items'][0])) {
            return $data['items'][0];
        }

        if (isset($data['branches'][0]) && is_array($data['branches'][0])) {
            return $data['branches'][0];
        }

        return is_array($data) ? $data : [];
    }

    private function extractTotal(array $response): ?int
    {
        $data = $response['result'] ?? $response['data'] ?? $response;

        foreach (['total', 'count', 'total_count', 'totalCount'] as $key) {
            if (isset($data[$key]) && is_numeric($data[$key])) {
                return (int) $data[$key];
            }
        }

        return null;
    }
}
