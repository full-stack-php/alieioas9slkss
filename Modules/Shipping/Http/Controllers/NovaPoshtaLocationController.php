<?php

namespace Modules\Shipping\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Shipping\Entities\NpArea;
use Modules\Shipping\Entities\NpCity;
use Modules\Shipping\Entities\NpWarehouse;

class NovaPoshtaLocationController extends Controller
{
    public function areas(Request $request)
    {
        $query = trim((string) $request->input('q'));

        return NpArea::query()
            ->where('is_active', true)
            ->with('translation')
            ->when($query, function ($builder) use ($query) {
                $builder->whereHas('translations', function ($translationQuery) use ($query) {
                    $translationQuery->where('name', 'like', "%{$query}%");
                });
            })
            ->limit(20)
            ->get()
            ->map(function (NpArea $area) {
                return [
                    'id' => $area->id,
                    'text' => optional($area->translation)->name,
                ];
            })
            ->filter(fn ($area) => ! empty($area['text']))
            ->values();
    }

    public function cities(Request $request)
    {
        $request->validate([
            'area' => 'required|string',
            'q' => 'nullable|string',
        ]);

        $areaName = $this->normalizeSearchValue($request->input('area'));
        $query = $this->normalizeSearchValue($request->input('q'));

        $nameWithoutBracketsSql = $this->nameWithoutBracketsSql();

        $area = NpArea::query()
            ->where('is_active', true)
            ->whereHas('translations', function ($translationQuery) use ($areaName, $nameWithoutBracketsSql) {
                $translationQuery->whereRaw(
                    "{$nameWithoutBracketsSql} = ?",
                    [$areaName]
                );
            })
            ->first();

        if (! $area) {
            return collect();
        }

        return NpCity::query()
            ->where('is_active', true)
            ->where('area_id', $area->id)
            ->with('translation')
            ->when($query, function ($builder) use ($query, $nameWithoutBracketsSql) {
                $builder->whereHas('translations', function ($translationQuery) use ($query, $nameWithoutBracketsSql) {
                    $translationQuery->whereRaw(
                        "{$nameWithoutBracketsSql} LIKE ?",
                        ["%{$query}%"]
                    );
                });
            })
            ->limit(20)
            ->get()
            ->map(function (NpCity $city) {
                $rawName = optional($city->translation)->name;
                $cleanName = $this->normalizeSearchValue($rawName);
                $type = optional($city->translation)->type;

                return [
                    'id' => $city->id,
                    'text' => trim(($type ? "{$type} " : '') . $cleanName),
                    'name' => $cleanName,
                    'raw_name' => $rawName,
                ];
            })
            ->filter(fn ($city) => ! empty($city['name']))
            ->values();
    }

    public function warehouses(Request $request)
    {
        $request->validate([
            'city' => 'required|string',
            'type' => 'required|in:branch,postomat',
            'q' => 'nullable|string',
        ]);

        $cityName = trim($request->input('city'));
        $query = trim((string) $request->input('q'));
        $isPostomat = $request->input('type') === 'postomat';

        $city = NpCity::query()
            ->where('is_active', true)
            ->whereHas('translations', function ($translationQuery) use ($cityName) {
                $translationQuery
                    ->where('name', $cityName)
                    ->orWhereRaw("CONCAT(COALESCE(type, ''), ' ', name) = ?", [$cityName]);
            })
            ->first();

        if (! $city) {
            return collect();
        }

        return NpWarehouse::query()
            ->where('is_active', true)
            ->where('city_id', $city->id)
            ->where('is_postomat', $isPostomat)
            ->with('translation')
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($innerQuery) use ($query) {
                    $innerQuery
                        ->where('number', $query)
                        ->orWhereHas('translations', function ($translationQuery) use ($query) {
                            $translationQuery->where('name', 'like', "%{$query}%");
                        });
                });
            })
            ->orderBy('number')
            ->limit(20)
            ->get()
            ->map(function (NpWarehouse $warehouse) {
                return [
                    'id' => $warehouse->id,
                    'number' => $warehouse->number,
                    'text' => optional($warehouse->translation)->name,
                ];
            })
            ->filter(fn ($warehouse) => ! empty($warehouse['text']))
            ->values();
    }

    private function normalizeSearchValue(?string $value): string
    {
        $value = trim((string) $value);
        $value = preg_replace('/\s*\([^)]*\)/u', '', $value);

        return trim($value);
    }

    private function nameWithoutBracketsSql(string $column = 'name'): string
    {
        return "
        TRIM(
            CASE
                WHEN LOCATE('(', {$column}) > 0
                THEN SUBSTRING({$column}, 1, LOCATE('(', {$column}) - 1)
                ELSE {$column}
            END
        )
    ";
    }
}
