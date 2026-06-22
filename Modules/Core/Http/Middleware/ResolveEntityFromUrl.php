<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Core\Http\UrlResolver;

class ResolveEntityFromUrl
{
    protected $resolver;

    public function __construct(UrlResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function handle(Request $request, Closure $next)
    {

        $path = $request->path();
        $locale = LaravelLocalization::getCurrentLocale();
        $cleanPath = preg_replace("#^{$locale}/?#", '', $path);

        $resolved = $this->resolver->resolve($cleanPath);

        if ($resolved) {
            $request->route()->setParameter('category', $resolved['slug'] ?? $resolved['id']);
            $request->route()->setParameter('slug', $resolved['slug'] ?? $resolved['id']);
            $request->attributes->add(['resolved_entity' => $resolved]);
        }

        return $next($request);
    }
}
