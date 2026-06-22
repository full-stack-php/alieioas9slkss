<?php

namespace Modules\Shipping\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Shipping\Jobs\SyncMeestJob;

class MeestController extends Controller
{
    private string $progressFile = 'meest_sync_progress.json';

    public function startSync(): JsonResponse
    {
        Storage::disk('local')->delete($this->progressFile);

        dispatch(new SyncMeestJob())->onConnection('database');

        return response()->json([
            'status' => 'success',
            'message' => 'Синхронизация Meest запущена в фоновом режиме.',
        ]);
    }

    public function getSyncStatus(): JsonResponse
    {
        $defaultStatus = [
            'poshtomat'   => ['current' => 0, 'total' => 0, 'percent' => 0],
            'minibranch' => ['current' => 0, 'total' => 0, 'percent' => 0],
            'mainbranch' => ['current' => 0, 'total' => 0, 'percent' => 0],
            'cities'     => ['current' => 0, 'total' => 0, 'percent' => 0],
            'warehouses' => ['current' => 0, 'total' => 0, 'percent' => 0],
            'message'    => 'Ожидание начала...',
            'is_finished'=> false,
            'error'      => false,
        ];

        if (Storage::disk('local')->exists($this->progressFile)) {
            $content = Storage::disk('local')->get($this->progressFile);
            $status = json_decode($content, true);

            return response()->json($status ?: $defaultStatus);
        }

        return response()->json($defaultStatus);
    }
}
