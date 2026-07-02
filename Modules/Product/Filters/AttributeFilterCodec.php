<?php

namespace Modules\Product\Filters;

use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeValue;

class AttributeFilterCodec
{
    public static function normalize($attributeFilters): array
    {
        if (empty($attributeFilters)) {
            return [];
        }

        if (is_string($attributeFilters)) {
            return self::decodeCompact($attributeFilters);
        }

        return self::normalizeArray($attributeFilters);
    }

    public static function encode(array $groups): string
    {
        $result = '';

        ksort($groups);

        foreach ($groups as $attributeId => $valueIds) {
            $valueIds = collect((array) $valueIds)
                ->filter(fn ($id) => is_numeric($id))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->sort()
                ->values();

            if ($valueIds->isEmpty()) {
                continue;
            }

            $result .= 'F' . (int) $attributeId;

            foreach ($valueIds as $valueId) {
                $result .= 'V' . (int) $valueId;
            }
        }

        return $result;
    }

    public static function selected($attributeFilters, int $attributeId, int $valueId): bool
    {
        $groups = self::normalize($attributeFilters);

        return in_array($valueId, $groups[$attributeId] ?? [], true);
    }

    public static function withoutAttribute(array $filters, int $attributeId, ?string $attributeSlug = null): array
    {
        if (!isset($filters['attribute'])) {
            return $filters;
        }

        if (is_string($filters['attribute'])) {
            $groups = self::normalize($filters['attribute']);

            unset($groups[$attributeId]);

            if (empty($groups)) {
                unset($filters['attribute']);
            } else {
                $filters['attribute'] = self::encode($groups);
            }

            return $filters;
        }

        if (is_array($filters['attribute'])) {
            unset($filters['attribute'][$attributeId]);

            if ($attributeSlug) {
                unset($filters['attribute'][$attributeSlug]);
            }

            if (empty($filters['attribute'])) {
                unset($filters['attribute']);
            }
        }

        return $filters;
    }

    private static function decodeCompact(string $value): array
    {
        $groups = [];

        preg_match_all('/F(\d+)((?:V\d+)+)/i', $value, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $attributeId = (int) $match[1];

            preg_match_all('/V(\d+)/i', $match[2], $valueMatches);

            $valueIds = collect($valueMatches[1] ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (!empty($valueIds)) {
                $groups[$attributeId] = $valueIds;
            }
        }

        return $groups;
    }

    private static function normalizeArray(array $attributeFilters): array
    {
        $groups = [];

        foreach ($attributeFilters as $attributeKey => $values) {
            $attributeId = self::attributeId($attributeKey);

            if (!$attributeId) {
                continue;
            }

            $valueIds = self::valueIds($attributeId, (array) $values);

            if (!empty($valueIds)) {
                $groups[$attributeId] = $valueIds;
            }
        }

        return $groups;
    }

    private static function attributeId($attributeKey): ?int
    {
        if (is_numeric($attributeKey)) {
            return (int) $attributeKey;
        }

        return Attribute::where('slug', $attributeKey)->value('id');
    }

    private static function valueIds(int $attributeId, array $values): array
    {
        $numericIds = collect($values)
            ->filter(fn ($value) => is_numeric($value))
            ->map(fn ($value) => (int) $value)
            ->values();

        if ($numericIds->isNotEmpty()) {
            return $numericIds->all();
        }

        return AttributeValue::where('attribute_id', $attributeId)
            ->whereTranslationIn('value', $values)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }
}
