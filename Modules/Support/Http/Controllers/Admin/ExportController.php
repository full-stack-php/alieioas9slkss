<?php

namespace Modules\Support\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Modules\Admin\Traits\HasCrudActions;
use Modules\Support\Entities\Export;
use Modules\Support\Exports\ExportEngine;
use Modules\Support\Http\Requests\SaveExportRequest;

class ExportController
{

    use HasCrudActions;

    /**
     * Model for the resource.
     *
     * @var string
     */
    protected $model = Export::class;

    /**
     * Label of the resource.
     *
     * @var string
     */
    protected $label = 'support::exports.export';

    /**
     * View path of the resource.
     *
     * @var string
     */
    protected $viewPath = 'support::admin.exports';


    protected $routePrefix = 'admin.exports';

    /**
     * Form requests for the resource.
     *
     * @var array|string
     */
    protected $validation = SaveExportRequest::class;


    public function run($id)
    {
        try {
            $exportProfile = Export::findOrFail($id);

            if (!$exportProfile->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Невозможно запустить: профиль выключен.'
                ], 400);
            }
             $engine = new ExportEngine();
             $engine->run($exportProfile);
            return response()->json([
                'success' => true,
                'message' => 'Процесс экспорта успешно запущен в фоновом режиме!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEntityFields(Request $request)
    {
        $entityClass = $request->get('entity');

        if (!$entityClass || !class_exists($entityClass)) {
            return response()->json(['fields' => [], 'relations' => []]);
        }

        try {
            $model = new $entityClass;
            $tableName = $model->getTable();

            $fields = [];
            if (Schema::hasTable($tableName)) {
                foreach (Schema::getColumnListing($tableName) as $column) {
                    if (in_array($column, ['password', 'remember_token', 'deleted_at'])) continue;
                    $fields[$column] = $column . ' (Поле БД)';
                }
            }

            if (property_exists($model, 'translatedAttributes')) {
                $reflection = new \ReflectionClass($model);
                $property = $reflection->getProperty('translatedAttributes');
                $property->setAccessible(true);
                foreach ($property->getValue($model) ?? [] as $attribute) {
                    $fields[$attribute] = $attribute . ' (Перевод)';
                }
            }

            ksort($fields);
            $relations = [];

            if (property_exists($model, 'availableRelationForExport')) {
                foreach ($model->availableRelationForExport as $relation) {
                    $methodName = 'exportFieldsFor' . ucfirst($relation);

                    $relationFields = method_exists($model, $methodName)
                        ? $model->{$methodName}()
                        : ['id' => 'ID', 'name' => 'Название'];

                    $relations[$relation] = [
                        'label'         => "Связь: " . ucfirst($relation),
                        'fields'        => $relationFields,
                        'can_explode'   => in_array($relation, ['allPackagings'])
                    ];
                }
            }
            return response()->json([
                'fields'    => $fields,
                'relations' => $relations
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка парсинга сущности для экспорта: ' . $e->getMessage());
            return response()->json(['fields' => [], 'relations' => []], 500);
        }
    }
}
