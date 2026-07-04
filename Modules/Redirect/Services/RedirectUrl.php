<?php

namespace Modules\Redirect\Services;

class RedirectUrl
{
    public static function normalizeOldUrl(?string $url): string
    {
        $url = trim((string) $url);

        $path = parse_url($url, PHP_URL_PATH);

        if (!is_null($path)) {
            $url = $path;
        }

        return trim(ltrim($url, '/'), '/');
    }

    public static function normalizeNewUrl(?string $url): string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $url)) {
            $host = parse_url($url, PHP_URL_HOST);
            $path = parse_url($url, PHP_URL_PATH) ?: '/';
            $query = parse_url($url, PHP_URL_QUERY);

            $currentHost = request()->getHost();
            $appHost = parse_url(config('app.url'), PHP_URL_HOST);

            if ($host === $currentHost || $host === $appHost) {
                return '/' . trim($path, '/') . ($query ? '?' . $query : '');
            }

            return $url;
        }

        return '/' . trim(ltrim($url, '/'), '/');
    }

    public static function isValid(?string $url, bool $allowFullUrl = true): bool
    {
        $url = trim((string) $url);

        if ($url === '') {
            return false;
        }

        if (preg_match('/\s/', $url)) {
            return false;
        }

        if (preg_match('#^https?://#i', $url)) {
            return $allowFullUrl && filter_var($url, FILTER_VALIDATE_URL);
        }

        return true;
    }

    public static function detectPageType(?string $url): string
    {
        $url = self::normalizeOldUrl($url);

        return match (true) {
            str_contains($url, '/product/') || str_starts_with($url, 'product/') => 'product',
            str_contains($url, '/category/') || str_starts_with($url, 'category/') => 'category',
            str_contains($url, '/brands/') || str_starts_with($url, 'brands/') => 'brand',
            str_contains($url, '/blog/') || str_starts_with($url, 'blog/') => 'blog',
            default => 'other',
        };
    }
}
