<?php
namespace Modules\Product\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Product\Entities\Product;
use Modules\Product\Transformers\ProductExportResource;
use Illuminate\Support\Facades\File;

class ProductExportController extends Controller
{
    public function exportToJson()
    {
        // 1. Собираем данные с жадной загрузкой (eager loading) для оптимизации
        $products = Product::with([
            'translations',
            'brand',
            'categories',
            'bundles',
            'files'
        ])->get();

        // 2. Трансформируем данные через ресурс
        $data = ProductExportResource::collection($products);

        // 3. Путь к файлу в папке public
        $fileName = 'products_export.json';
        $path = public_path($fileName);

        try {
            // 4. Записываем файл с форматированием (JSON_PRETTY_PRINT для читаемости)
            File::put($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            return response()->json([
                'success' => true,
                'message' => 'Файл успешно сгенерирован',
                'url' => asset($fileName)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка записи: ' . $e->getMessage()
            ], 500);
        }
    }
}
