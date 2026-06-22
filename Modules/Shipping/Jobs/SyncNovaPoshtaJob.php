<?php

namespace Modules\Shipping\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Shipping\Services\NovaPoshtaSyncService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncNovaPoshtaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;

    public function handle(NovaPoshtaSyncService $service)
    {
        Log::info('Запуск фоновой задачи: Синхронизация Новой Почты...');

        try {
            $service->syncAll();

            Log::info('Фоновая задача успешно завершена: Синхронизация Новой Почты.');
        } catch (\Exception $e) {
            Log::error('Критическая ошибка в Job Новой Почты: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            Cache::put(NovaPoshtaSyncService::CACHE_KEY, [
                'message'     => 'Ошибка: ' . $e->getMessage(),
                'current'     => 0,
                'total'       => 0,
                'percent'     => 0,
                'is_finished' => true,
                'error'       => true
            ], now()->addMinutes(10));
        }
    }
}
