<?php

namespace Modules\Shipping\Services;

use Exception;
//use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Modules\Shipping\Entities\NpArea;
use Modules\Shipping\Entities\NpCity;
use Modules\Shipping\Entities\NpWarehouse;

class NovaPoshtaSyncService
{
    const CACHE_KEY = 'np_sync_progress';
    private $api;
    private $state = [
        'areas'       => ['current' => 0, 'total' => 0, 'percent' => 0],
        'cities'      => ['current' => 0, 'total' => 0, 'percent' => 0],
        'warehouses'  => ['current' => 0, 'total' => 0, 'percent' => 0],
        'message'     => 'Инициализация API...',
        'is_finished' => false,
        'error'       => false
    ];

    public function __construct()
    {
        $this->api = new NovaPoshtaApiClient();
    }

    public function syncAll()
    {
        try {
            Storage::disk('local')->put('np_sync_progress.json', json_encode($this->state));

            $this->deactivateOldData();

            $this->syncAreas();
            $this->syncCities();
            $this->syncWarehouses();

            $this->finishProcess('Синхронизация справочников завершена!');
        } catch (Exception $e) {
            Log::error('Nova Poshta sync failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->abortProcess('Ошибка: ' . $e->getMessage());
        }
    }

    private function deactivateOldData(): void
    {
        $this->updateMessage('Деактивация старых данных...');

        NpArea::query()->update(['is_active' => false]);
        NpCity::query()->update(['is_active' => false]);
        NpWarehouse::query()->update(['is_active' => false]);
    }

    private function syncAreas()
    {
        $this->updateMessage('Получение списка областей...');

        $areas = $this->api->request('Address', 'getAreas');

        $total = count($areas);
        $current = 0;

        foreach ($areas as $data) {
            $area = NpArea::withoutGlobalScope('locale')
                ->firstOrNew(['ref' => $data['Ref']]);

            $area->is_active = true;

            $area->save();

            $translations = [
                'uk' => $data['Description'],
                'ru' => $data['DescriptionRu'] ?? $data['Description'],
            ];

            foreach ($translations as $locale => $name) {
                \DB::table('np_area_translations')->updateOrInsert(
                    [
                        'np_area_id' => $area->id,
                        'locale' => $locale
                    ],
                    [
                        'name' => $name
                    ]
                );
            }

            $current++;
            $this->updateEntityProgress('areas', $current, $total);
        }
    }

    private function syncCities()
    {
        $this->updateMessage('Получение списка городов (это займет время)...');

        $cities = $this->api->request('Address', 'getCities');

        $total = count($cities);
        $current = 0;

        $areasCache = NpArea::pluck('id', 'ref')->toArray();

        foreach ($cities as $data) {
            $areaId = $areasCache[$data['Area']] ?? null;
            if (!$areaId) continue;

            $city = NpCity::withoutGlobalScope('locale')->firstOrNew(['ref' => $data['Ref']]);
            $city->area_id = $areaId;
            $city->is_active = true;
            $city->save();

            $translations = [
                'uk' => ['name' => $data['Description'], 'type' => $data['SettlementTypeDescription'] ?? null],
                'ru' => ['name' => $data['DescriptionRu'] ?? $data['Description'], 'type' => $data['SettlementTypeDescriptionRu'] ?? null],
            ];

            foreach ($translations as $locale => $fields) {
                \DB::table('np_city_translations')->updateOrInsert(
                    ['np_city_id' => $city->id, 'locale' => $locale],
                    $fields
                );
            }

            $current++;
            if ($current % 100 === 0) {
                $this->updateEntityProgress('cities', $current, $total);
            }
        }
        $this->updateEntityProgress('cities', $total, $total);
    }

    private function syncWarehouses()
    {
        $this->updateMessage('Получение списка отделений и почтоматов...');

        $warehouses = $this->api->request('Address', 'getWarehouses');

        $total = count($warehouses);
        $current = 0;

        $citiesCache = NpCity::pluck('id', 'ref')->toArray();

        foreach ($warehouses as $data) {
            $cityId = $citiesCache[$data['CityRef']] ?? null;

            if (!$cityId) {
                continue;
            }

            $description = mb_strtolower(
                ($data['Description'] ?? '') . ' ' . ($data['DescriptionRu'] ?? '')
            );

            $isPostomat = ($data['CategoryOfWarehouse'] ?? null) === 'Postomat'
                || str_contains($description, 'поштомат')
                || str_contains($description, 'почтомат');

            $warehouse = NpWarehouse::withoutGlobalScope('locale')
                ->firstOrNew(['ref' => $data['Ref']]);

            $warehouse->city_id = $cityId;
            $warehouse->number = $data['Number'] ?? null;
            $warehouse->is_postomat = $isPostomat;
            $warehouse->max_weight = !empty($data['TotalMaxWeightAllowed'])
                ? $data['TotalMaxWeightAllowed']
                : null;
            $warehouse->is_active = true;
            $warehouse->save();

            $translations = [
                'uk' => ['name' => $data['Description'] ?? ''],
                'ru' => ['name' => $data['DescriptionRu'] ?? ($data['Description'] ?? '')],
            ];

            foreach ($translations as $locale => $fields) {
                if (empty($fields['name'])) {
                    continue;
                }

                \DB::table('np_warehouse_translations')->updateOrInsert(
                    [
                        'np_warehouse_id' => $warehouse->id,
                        'locale' => $locale,
                    ],
                    $fields
                );
            }

            $current++;

            if ($current % 100 === 0) {
                $this->updateEntityProgress('warehouses', $current, $total);
            }
        }

        $this->updateEntityProgress('warehouses', $total, $total);
    }

    private function updateMessage(string $message)
    {
        $this->state['message'] = $message;
        Storage::disk('local')->put('np_sync_progress.json', json_encode($this->state));
    }

    private function updateEntityProgress(string $entityType, int $current, int $total)
    {
        $this->state[$entityType]['current'] = $current;
        $this->state[$entityType]['total'] = $total;
        $percent = $total > 0 ? round(($current / $total) * 100) : 0;
        $this->state[$entityType]['percent'] = $percent;

        Storage::disk('local')->put('np_sync_progress.json', json_encode($this->state));

        Log::info("Nova Poshta Sync [{$entityType}]: {$current} / {$total} ({$percent}%)");
    }

    private function finishProcess(string $message)
    {
        $this->state['message'] = $message;
        $this->state['is_finished'] = true;
        $this->state['error'] = false;
        Storage::disk('local')->put('np_sync_progress.json', json_encode($this->state));
    }

    private function abortProcess(string $message)
    {
        $this->state['message'] = $message;
        $this->state['is_finished'] = true;
        $this->state['error'] = true;
        Storage::disk('local')->put('np_sync_progress.json', json_encode($this->state));
    }
}
