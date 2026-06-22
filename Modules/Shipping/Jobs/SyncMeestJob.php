<?php

namespace Modules\Shipping\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Shipping\Services\MeestSyncService;

class SyncMeestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 900;

    public function handle(MeestSyncService $service): void
    {
        Log::info('Запуск фоновой задачи: Синхронизация Meest...');

        try {
            $service->syncAll();

            Log::info('Фоновая задача успешно завершена: Синхронизация Meest.');
        } catch (\Exception $e) {
            Log::error('Критическая ошибка в Job Meest: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            Cache::put(MeestSyncService::CACHE_KEY, [
                'message' => 'Ошибка: ' . $e->getMessage(),
                'current' => 0,
                'total' => 0,
                'percent' => 0,
                'is_finished' => true,
                'error' => true,
            ], now()->addMinutes(10));
        }
    }
}
