<?php

namespace Modules\Product\Filters;

class ManufacturerFilterCodec
{
    public static function normalize($manufacturers): array
    {
        if (empty($manufacturers)) {
            return [];
        }

        if (is_string($manufacturers)) {
            return self::decodeCompact($manufacturers);
        }

        return collect((array) $manufacturers)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public static function encode(array $manufacturerIds): string
    {
        return collect($manufacturerIds)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->sort()
            ->map(fn ($id) => 'M' . $id)
            ->implode('');
    }

    private static function decodeCompact(string $value): array
    {
        preg_match_all('/M(\d+)/i', $value, $matches);

        return collect($matches[1] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
