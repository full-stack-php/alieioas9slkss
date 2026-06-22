<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class CheckQueryParamsForRobots
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $queryParams = $request->query();

        if (count($queryParams) > 0) {
            if (count($queryParams) === 1 && $request->has('page')) {
                View::share('robotsMeta', 'noindex, follow');
            }
            else {
                View::share('robotsMeta', 'noindex, nofollow');
            }
        }

        return $next($request);
    }

}
