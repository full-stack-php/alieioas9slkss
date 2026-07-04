<?php

namespace Modules\Redirect\Entities;

use Illuminate\Http\Request;
use Modules\Admin\Ui\AdminTable;
use Illuminate\Http\JsonResponse;
use Modules\Support\Eloquent\Model;
use Modules\Redirect\Services\RedirectUrl;

class Redirect extends Model
{
    protected $fillable = [
        'old_url',
        'new_url',
        'status_code',
        'is_active',
        'comment',
        'page_type',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'status_code' => 'integer',
    ];

    protected static function booted()
    {
        static::saving(function ($redirect) {
            $redirect->old_url = RedirectUrl::normalizeOldUrl($redirect->old_url);
            $redirect->new_url = RedirectUrl::normalizeNewUrl($redirect->new_url);
            $redirect->status_code = in_array((int) $redirect->status_code, [301, 302], true)
                ? (int) $redirect->status_code
                : 301;
            $redirect->page_type = RedirectUrl::detectPageType($redirect->old_url);
        });

        static::addActiveGlobalScope();
    }

    public static function findForPath(string $path): ?self
    {
        $normalized = RedirectUrl::normalizeOldUrl($path);

        $variants = array_values(array_unique(array_filter([
            $normalized,
            '/' . $normalized,
            $normalized . '/',
            '/' . $normalized . '/',
        ])));

        return static::whereIn('old_url', $variants)->first();
    }

    public static function activeRedirectForOldUrl(string $url, ?int $exceptId = null): ?self
    {
        $query = static::withoutGlobalScope('active')
            ->where('is_active', true)
            ->where('old_url', RedirectUrl::normalizeOldUrl($url));

        if ($exceptId) {
            $query->where('id', '<>', $exceptId);
        }

        return $query->first();
    }

    public static function hasChain(string $newUrl, ?int $exceptId = null): bool
    {
        return !is_null(static::activeRedirectForOldUrl($newUrl, $exceptId));
    }

    public static function hasCycle(string $oldUrl, string $newUrl, ?int $exceptId = null): bool
    {
        $oldUrl = RedirectUrl::normalizeOldUrl($oldUrl);
        $nextUrl = RedirectUrl::normalizeOldUrl($newUrl);

        if ($oldUrl === $nextUrl) {
            return true;
        }

        $visited = [];

        for ($i = 0; $i < 20; $i++) {
            if (in_array($nextUrl, $visited, true)) {
                return true;
            }

            $visited[] = $nextUrl;

            $redirect = static::activeRedirectForOldUrl($nextUrl, $exceptId);

            if (!$redirect) {
                return false;
            }

            $nextUrl = RedirectUrl::normalizeOldUrl($redirect->new_url);

            if ($nextUrl === $oldUrl) {
                return true;
            }
        }

        return true;
    }

    public function targetUrl(): string
    {
        return RedirectUrl::normalizeNewUrl($this->new_url);
    }

    public function table(Request $request)
    {
        $query = $this->newQuery()->withoutGlobalScope('active');

        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        if ($request->filled('status_code')) {
            $query->where('status_code', (int) $request->get('status_code'));
        }

        if ($request->filled('page_type')) {
            $query->where('page_type', $request->get('page_type'));
        }

        return new AdminTable($query);
    }
}
