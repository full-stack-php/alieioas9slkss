<?php

namespace Modules\SeoFilter\Services;

class SeoRobots
{
    private array $filterKeys = [
        'price',
        'fromPrice',
        'toPrice',
        'manufacturers',
        'manufacturer',
        'attribute',
        'attributes',
        'has_discount',
        'specials',
    ];

    public function forQuery(array $query): string
    {
        return $this->hasFilterQuery($query)
            ? 'noindex, nofollow'
            : 'index, follow';
    }

    public function hasFilterQuery(array $query): bool
    {
        foreach ($this->filterKeys as $key) {
            if (!array_key_exists($key, $query)) {
                continue;
            }

            if ($query[$key] === null || $query[$key] === '' || $query[$key] === []) {
                continue;
            }

            return true;
        }

        return false;
    }
}
