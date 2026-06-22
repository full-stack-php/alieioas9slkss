<?php

namespace Modules\Shipping\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Shipping\Jobs\SyncNovaPoshtaJob;
use Illuminate\Support\Facades\Storage;

class NovaPoshtaController extends Controller
{
    private string $progressFile = 'np_sync_progress.json';

    public function startSync(): JsonResponse
    {
        Storage::disk('local')->delete($this->progressFile);

        dispatch(new SyncNovaPoshtaJob())->onConnection('database');

        return response()->json([
            'status' => 'success',
            'message' => 'Синхронизация запущена в фоновом режиме.'
        ]);
    }

    public function getSyncStatus(): JsonResponse
    {
        $defaultStatus = [
            'message'     => 'Ожидание начала...',
            'current'     => 0,
            'total'       => 0,
            'percent'     => 0,
            'is_finished' => false,
            'error'       => false
        ];

        if (Storage::disk('local')->exists($this->progressFile)) {
            $content = Storage::disk('local')->get($this->progressFile);
            $status = json_decode($content, true);

            return response()->json($status ?: $defaultStatus);
        }

        return response()->json($defaultStatus);
    }
}
