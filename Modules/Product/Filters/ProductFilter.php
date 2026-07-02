<?php

namespace Modules\Product\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductFilter
{
    private Request $request;
    private QueryStringFilter $queryStringFilter;

    public function __construct(Request $request, QueryStringFilter $queryStringFilter)
    {
        $this->request = $request;
        $this->queryStringFilter = $queryStringFilter;
    }

    public function apply($query)
    {
        $query = $query->forCard();

        foreach ($this->filters() as $name => $value) {
            if ($value === null || $value === '' || $value === []) {
                continue;
            }

            $method = $this->methodForFilter($name);

            if ($method) {
                $this->queryStringFilter->{$method}($query, $value);
            }
        }

        return $query;
    }

    private function filters(): array
    {
        return array_filter($this->request->query(), function ($filter) {
            return $this->filterExists($filter);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function filterExists($filter): bool
    {
        return $this->methodForFilter($filter) !== null;
    }

    private function methodForFilter($filter): ?string
    {
        foreach ([$filter, Str::camel($filter)] as $method) {
            if (
                method_exists($this->queryStringFilter, $method) &&
                is_callable([$this->queryStringFilter, $method])
            ) {
                return $method;
            }
        }

        return null;
    }
}
