<?php

namespace Modules\Shipping\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Shipping\Entities\MeestCity;
use Modules\Shipping\Entities\MeestWarehouse;

class MeestSyncService
{
    const CACHE_KEY = 'meest_sync_progress';

    private MeestApiClient $api;

    private array $types = [
        'poshtomat',
        'minibranch',
        'mainbranch',
    ];

    private array $state = [
        'poshtomat'   => ['current' => 0, 'total' => 0, 'percent' => 0],
        'minibranch' => ['current' => 0, 'total' => 0, 'percent' => 0],
        'mainbranch' => ['current' => 0, 'total' => 0, 'percent' => 0],
        'cities'     => ['current' => 0, 'total' => 0, 'percent' => 0],
        'warehouses' => ['current' => 0, 'total' => 0, 'percent' => 0],
        'message'    => 'Инициализация API Meest...',
        'is_finished'=> false,
        'error'      => false,
    ];

    private string $progressFile = 'meest_sync_progress.json';

    private int $citiesTouched = 0;
    private int $warehousesTouched = 0;

    public function __construct()
    {
        $this->api = new MeestApiClient();
    }

    public function syncAll(): void
    {
        try {
            Storage::disk('local')->put($this->progressFile, json_encode($this->state));

            foreach ($this->types as $type) {
                $this->syncBranchesByType($type);
            }

            $this->finishProcess('Синхронизация справочников Meest завершена!');
        } catch (Exception $e) {
            $this->abortProcess('Ошибка: ' . $e->getMessage());
        }
    }

    private function syncBranchesByType(string $type): void
    {
        $this->updateMessage("Получение отделений Meest: {$this->getTypeName($type)}...");

        $branches = $this->api->getBranches($type);

        $total = count($branches);
        $current = 0;

        $this->updateEntityProgress($type, 0, $total);

        foreach ($branches as $data) {
            if (empty($data['br_id']) || empty($data['city_id'])) {
                $current++;
                $this->updateProgressOnStep($type, $current, $total);
                continue;
            }

            $details = $this->api->getBranch($data['num']);


            DB::transaction(function () use ($details, $type) {
                $city = $this->syncCityFromBranch($details);
                $this->syncWarehouseFromBranch($details, $city->id, $type);
            });

            $current++;
            $this->updateProgressOnStep($type, $current, $total);
        }

        $this->updateEntityProgress($type, $total, $total);
    }

    private function syncCityFromBranch(array $data): MeestCity
    {
        $city = MeestCity::withoutGlobalScope('locale')
            ->firstOrNew(['ref' => $data['city_id']]);

        $isNew = !$city->exists;

        $city->is_active = true;
        $city->save();

        $translations = $this->normalizeTranslations($data['city'] ?? []);

        foreach ($translations as $locale => $fields) {
            DB::table('meest_city_translations')->updateOrInsert(
                [
                    'meest_city_id' => $city->id,
                    'locale' => $locale,
                ],
                [
                    'name' => $fields['name'],
                ]
            );
        }

        if ($isNew) {
            $this->citiesTouched++;
            $this->updateEntityProgress('cities', $this->citiesTouched, $this->citiesTouched);
        }

        return $city;
    }

    private function syncWarehouseFromBranch(array $data, int $cityId, string $type): MeestWarehouse
    {
        $warehouse = MeestWarehouse::withoutGlobalScope('locale')->firstOrNew(['ref' => $data['num']]);


        dd(34234);
        $warehouse->ref = $data['num'] ?? null;
        $warehouse->city_id = $cityId;
        $warehouse->type = $type;
        $warehouse->is_active = true;
        $warehouse->save();

        $typeTranslations = $this->normalizeWerhouseTranslations($data ?? []);


        foreach ($typeTranslations as $locale => $fields) {

            \DB::table('meest_warehouse_translations')->updateOrInsert(
                ['meest_warehouse_id' => $warehouse->id, 'locale' => $locale],
                $fields
            );
        }

        $this->warehousesTouched++;
        $this->updateEntityProgress('warehouses', $this->warehousesTouched, $this->warehousesTouched);

        return $warehouse;
    }

    private function normalizeWerhouseTranslations(array $values): array
    {
        $numShowcase = $values['num_showcase'] ?? null;
        $street = $values['street'] ?? [];
        $streetNumber = $values['street_number'] ?? null;
        $locationDescription = $values['location_description'] ?? null;

        $locales = [
            'uk' => 'ua',
            'ru' => 'ru',
            'en' => 'en',
        ];

        $translations = [];

        foreach ($locales as $locale => $apiLocale) {
            $streetName = $street[$apiLocale] ?? $street['ua'] ?? null;

            $parts = [];

            if ($numShowcase) {
                $parts[] = '№' . $numShowcase;
            }

            if ($streetName) {
                $parts[] = $streetName;
            }

            if ($streetNumber) {
                $parts[] = $streetNumber;
            }

            $name = trim(implode(' ', $parts));

            if ($locationDescription) {
                $name .= ' (' . $locationDescription . ')';
            }

            if ($name !== '') {
                $translations[$locale] = [
                    'name' => $name,
                ];
            }
        }

        return $translations;
    }

    private function normalizeTranslations(array $values): array
    {
        $ua = $values['ua'] ?? null;
        $ru = $values['ru'] ?? $ua;
        $en = $values['en'] ?? $ua;

        $translations = [];

        if ($ua) {
            $translations['uk'] = ['name' => $ua];
        }

        if ($ru) {
            $translations['ru'] = ['name' => $ru];
        }

        if ($en) {
            $translations['en'] = ['name' => $en];
        }

        return $translations;
    }

    private function makeWarehouseNameTranslations(array $data, string $type): array
    {
        $cityTranslations = $this->normalizeTranslations($data['city'] ?? []);
        $typeTranslations = $this->normalizeTranslations($data['type_public'] ?? []);

        $number = $data['num_showcase'] ?? null;

        if (!$number || (int) $number === 9999) {
            $number = $data['num'] ?? $data['br_id'];
        }

        $result = [];

        foreach (['uk', 'ru', 'en'] as $locale) {
            $typeName = $typeTranslations[$locale]['name'] ?? $this->getTypeName($type);
            $cityName = $cityTranslations[$locale]['name'] ?? '';

            $result[$locale] = [
                'name' => trim("{$typeName} №{$number}" . ($cityName ? ", {$cityName}" : '')),
            ];
        }

        return $result;
    }

    private function updateProgressOnStep(string $entityType, int $current, int $total): void
    {
        if ($current % 100 === 0 || $current === $total) {
            $this->updateEntityProgress($entityType, $current, $total);
        }
    }

    private function getTypeName(string $type): string
    {
        return match ($type) {
            'poshtomat' => 'Поштомат',
            'minibranch' => 'Мини-отделение',
            'mainbranch' => 'Отделение',
            default => $type,
        };
    }

    private function updateMessage(string $message): void
    {
        $this->state['message'] = $message;
        Storage::disk('local')->put($this->progressFile, json_encode($this->state));
    }

    private function updateEntityProgress(string $entityType, int $current, int $total): void
    {
        $this->state[$entityType]['current'] = $current;
        $this->state[$entityType]['total'] = $total;
        $this->state[$entityType]['percent'] = $total > 0 ? round(($current / $total) * 100) : 0;

        Storage::disk('local')->put($this->progressFile, json_encode($this->state));

        Log::info("Meest Sync [{$entityType}]: {$current} / {$total} ({$this->state[$entityType]['percent']}%)");
    }

    private function finishProcess(string $message): void
    {
        $this->state['message'] = $message;
        $this->state['is_finished'] = true;
        $this->state['error'] = false;

        Storage::disk('local')->put($this->progressFile, json_encode($this->state));
    }

    private function abortProcess(string $message): void
    {
        $this->state['message'] = $message;
        $this->state['is_finished'] = true;
        $this->state['error'] = true;

        Storage::disk('local')->put($this->progressFile, json_encode($this->state));
    }
}
