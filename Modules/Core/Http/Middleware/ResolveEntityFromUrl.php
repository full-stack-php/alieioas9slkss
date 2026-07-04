<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Http\UrlResolver;
use Modules\Redirect\Entities\Redirect as RedirectEntity;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ResolveEntityFromUrl
{
    protected $resolver;

    public function __construct(UrlResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function handle(Request $request, Closure $next)
    {
        $path = trim($request->path(), '/');
        $locale = LaravelLocalization::getCurrentLocale();
        $cleanPath = trim(preg_replace("#^{$locale}/?#", '', $path), '/');

        if ($redirect = $this->findRedirect($request, $path, $cleanPath)) {
            return redirect()->to($redirect->targetUrl(), $redirect->status_code);
        }

        $resolved = $this->resolver->resolve($cleanPath);

        if ($resolved) {
            $request->route()->setParameter('category', $resolved['slug'] ?? $resolved['id']);
            $request->route()->setParameter('slug', $resolved['slug'] ?? $resolved['id']);
            $request->attributes->add(['resolved_entity' => $resolved]);
        }

        return $next($request);
    }

    private function findRedirect(Request $request, string $path, string $cleanPath): ?RedirectEntity
    {
        if ($this->shouldSkipRedirect($cleanPath)) {
            return null;
        }

        $redirect = RedirectEntity::findForPath($cleanPath)
            ?: RedirectEntity::findForPath($path);

        if (!$redirect) {
            return null;
        }

        if ($this->isRedirectLoop($request, $redirect->targetUrl(), $cleanPath)) {
            return null;
        }

        return $redirect;
    }

    private function shouldSkipRedirect(string $cleanPath): bool
    {
        return str_starts_with($cleanPath, 'admin')
            || str_starts_with($cleanPath, 'api');
    }

    private function isRedirectLoop(Request $request, string $targetUrl, string $cleanPath): bool
    {
        $targetPath = parse_url($targetUrl, PHP_URL_PATH) ?: $targetUrl;

        $targetPath = trim($targetPath, '/');
        $currentPath = trim($request->path(), '/');

        return $targetPath === $currentPath || $targetPath === $cleanPath;
    }
}
