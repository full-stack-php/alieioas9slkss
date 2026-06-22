<?php

namespace Modules\Support\Exports;

use Illuminate\Support\Collection;
use Modules\Support\Entities\Export;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ExportEngine
{
    /**
     * Основной метод запуска экспорта
     */
    public function run(Export $profile)
    {
        $originalLocale = app()->getLocale();

        try {
            $rawColumns = collect($profile->columns)
                ->filter(fn($col) => isset($col['enabled']) && $col['enabled'] == 1)
                ->values()
                ->toArray();

            if (empty($rawColumns)) {
                throw new \Exception('В профиле нет активных колонок для экспорта.');
            }

            $locale = $profile->locale ?? 'all';

            if ($locale !== 'all') {
                app()->setLocale($locale);
            }

            $columns = $this->expandColumnsForLocales($rawColumns, $locale, $profile->entity);

            $driverClass = $this->getDriverClass($profile->format);
            $driver = new $driverClass();

            $customName = $profile->file_name ?? null;

            if (!empty($customName)) {
                $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '', $customName);
                $fileName = $safeName . '.' . $profile->format;
            } else {
                $fileName = 'export_' . $profile->id . '_' . time() . '.' . $profile->format;
            }

            $fullPath = \Storage::disk('public_storage')->path('app/' . $fileName);
            $this->ensureDirectoryExists($fullPath);

            $settings = $profile->settings ?? [];

            $settings['locale'] = $locale;

            $driver->open($fullPath, $settings);

            $headers = array_column($columns, 'column');
            $driver->addRow($headers, $settings);

            $query = $this->buildQuery($profile);
            $this->eagerLoadRelations($query, $columns);

            foreach ($query->lazyById(500) as $item) {
                $rows = $this->processItem($item, $columns);
                foreach ($rows as $row) {
                    $driver->addRow($row, $settings, $item);
                }
            }

            $driver->close();

        } catch (\Exception $e) {
            \Log::error("Ошибка экспорта профиля #{$profile->id}: " . $e->getMessage());
            throw $e;
        } finally {
            app()->setLocale($originalLocale);
        }
    }

    /**
     * Размножение колонок для мультиязычности
     */
    protected function expandColumnsForLocales(array $columns, string $locale, string $entityClass): array
    {
        $expanded = [];
        $translatedAttributes = $this->getTranslatedAttributes(new $entityClass);
        $systemLocales = function_exists('supported_locales') ? array_keys(supported_locales()) : ['ru', 'uk', 'en'];

        foreach ($columns as $col) {
            $type = $col['type'] ?? 'field';
            $field = $col['field'] ?? '';

            // 1. Проверяем, является ли это базовым переводимым полем (name, description)
            $isTranslatedBaseField = ($type === 'field' && in_array($field, $translatedAttributes));

            // 2. Проверяем, является ли это связью "Один-к-одному", которую мы хотим разбить на колонки
            $isTranslatedRelation = ($type === 'relation' && in_array($field, ['meta']));

            // Если выбраны ВСЕ языки И это поле/связь подлежит размножению
            if (($isTranslatedBaseField || $isTranslatedRelation) && $locale === 'all') {
                foreach ($systemLocales as $loc) {
                    $newCol = $col;
                    $newCol['export_locale'] = $loc; // Сохраняем язык конкретно для этой колонки
                    $newCol['column'] = $col['column'] . ' ' . strtoupper($loc); // Добавляем RU/UK в заголовок
                    $expanded[] = $newCol;
                }
            } else {
                // Для обычных связей (bundles, packagings, colors) сохраняем 'all',
                // чтобы метод formatRelationData склеил их внутри одной ячейки
                $col['export_locale'] = $locale;
                $expanded[] = $col;
            }
        }

        return $expanded;
    }

    /**
     * Получение списка переводимых полей модели
     */
    protected function getTranslatedAttributes($model): array
    {
        if (isset($model->translatedAttributes) && is_array($model->translatedAttributes)) {
            return $model->translatedAttributes;
        }

        if (property_exists($model, 'translatedAttributes')) {
            $reflection = new \ReflectionClass($model);
            $property = $reflection->getProperty('translatedAttributes');
            $property->setAccessible(true);
            return $property->getValue($model) ?? [];
        }

        return [];
    }

    /**
     * Получение обычного поля с учетом мультиязычности и очистки тегов
     */
    protected function getFieldValue($item, string $field, array $colData): string
    {
        $translatedAttributes = $this->getTranslatedAttributes($item);

        $locale = (isset($colData['export_locale']) && $colData['export_locale'] !== 'all')
            ? $colData['export_locale']
            : app()->getLocale();

        $stripTags = !empty($colData['strip_tags']);

        if (in_array($field, $translatedAttributes)) {
            // Используем наш безопасный хелпер
            $value = $this->getTranslationSafe($item, $field, $locale);
            if (!$value) $value = data_get($item, $field); // Фолбэк
        } else {
            $value = data_get($item, $field);
        }

        if (is_iterable($value) && !is_string($value)) {
            $value = collect($value)->filter()->implode(', ');
        }

        $value = (string) $value;

        return $stripTags ? $this->cleanHtmlTags($value) : $value;
    }

    /**
     * Динамическая подгрузка связей для решения проблемы N+1
     */
    protected function eagerLoadRelations(Builder $query, array $columns): void
    {
        $withArray[] = 'files';

        // 1. Грузим ВСЕ переводы товара, принудительно отключая фильтр по текущему языку
        $withArray['translations'] = function ($q) {
            if (method_exists($q, 'withoutGlobalScope')) {
                $q->withoutGlobalScope('locale');
            }
        };

        // 2. Обрабатываем связи из колонок
        foreach ($columns as $col) {
            if (($col['type'] ?? 'field') === 'relation') {
                $relationName = $col['field'];

                if ($relationName === 'media') {
                    continue;
                }

                if (!isset($withArray[$relationName]) && !in_array($relationName, $withArray)) {
                    $withArray[] = $relationName;
                }

                if (in_array($relationName, ['allPackagings'])) {
                    $withArray[$relationName . '.translations'] = function ($qTrans) {
                        if (method_exists($qTrans, 'withoutGlobalScope')) {
                            $qTrans->withoutGlobalScope('locale');
                        }
                    };
                }



                if ($relationName === 'meta') {
                    $withArray['meta.translations'] = function($q) {
                        if (method_exists($q, 'withoutGlobalScope')) {
                            $q->withoutGlobalScope('locale');
                        }
                    };
                }

                // === НОВОЕ: Подгружаем связанные товары ПОДАРКОВ и их переводы ===
                if ($relationName === 'productGifts') {
                    $key = array_search('productGifts', $withArray);
                    if ($key !== false) {
                        unset($withArray[$key]);
                    }

                    $withArray['productGifts'] = function ($q) {};

                    $withArray['productGifts.giftProduct'] = function ($q) {
                        if (method_exists($q, 'withoutGlobalScopes')) {
                            $q->withoutGlobalScopes();
                        }
                    };

                    $withArray['productGifts.giftProduct.translations'] = function ($qTrans) {
                        if (method_exists($qTrans, 'withoutGlobalScope')) {
                            $qTrans->withoutGlobalScope('locale');
                        }
                    };
                }


                if (in_array($relationName, ['colorProducts', 'crossSellProducts', 'relatedProducts'])) {
                    $withArray[$relationName . '.translations'] = function ($qTrans) {
                        if (method_exists($qTrans, 'withoutGlobalScope')) {
                            $qTrans->withoutGlobalScope('locale');
                        }
                    };
                }

                if ($relationName === 'attributes') {
                    $withArray['attributes.attribute.translations'] = function($q) {
                        if (method_exists($q, 'withoutGlobalScope')) $q->withoutGlobalScope('locale');
                    };
                    $withArray['attributes.values.attributeValue.translations'] = function($q) {
                        if (method_exists($q, 'withoutGlobalScope')) $q->withoutGlobalScope('locale');
                    };
                }

                if ($relationName === 'options') {
                    $withArray['options.option.translations'] = function($q) {
                        if (method_exists($q, 'withoutGlobalScope')) $q->withoutGlobalScope('locale');
                    };
                    $withArray['options.values.optionValue.translations'] = function($q) {
                        if (method_exists($q, 'withoutGlobalScope')) $q->withoutGlobalScope('locale');
                    };
                }


                if ($relationName === 'bundles') {
                    $key = array_search('bundles', $withArray);
                    if ($key !== false) {
                        unset($withArray[$key]);
                    }
                    $withArray['bundles'] = function ($q) {
                        // $q->withTrashed();
                    };
                    $withArray['bundles.bundleProduct'] = function ($q) {
                        if (method_exists($q, 'withoutGlobalScopes')) {
                            $q->withoutGlobalScopes(); // Снимет и active, и locale, и всё остальное!
                        }
                    };
                    $withArray['bundles.bundleProduct.translations'] = function ($qTrans) {
                        if (method_exists($qTrans, 'withoutGlobalScope')) {
                            $qTrans->withoutGlobalScope('locale');
                        }
                    };
                }
            }
        }

        if (!empty($withArray)) {
            $query->with($withArray);
        }
    }

    protected function processItem($item, array $columns): array
    {
        $baseRow = [];
        $explodedGroups = [];

        $idColumnIndex = null;
        $nameColumnIndices = [];

        foreach ($columns as $idx => $col) {
            if (($col['type'] ?? 'field') === 'field') {
                if ($col['field'] === 'id') {
                    $idColumnIndex = $idx;
                } elseif (in_array($col['field'], ['name', 'h1_name'])) {
                    $nameColumnIndices[] = $idx;
                }
            }
        }

        foreach ($columns as $index => $col) {
            $type = $col['type'] ?? 'field';

            if ($type === 'field') {
                $baseRow[$index] = $this->getFieldValue($item, $col['field'], $col);
            } elseif ($type === 'relation') {
                $relationName = $col['field'];
                $relationFields = $col['relation_fields'] ?? [];

                if (!empty($col['explode'])) {
                    // Сохраняем весь $col для дальнейшей передачи
                    $explodedGroups[$relationName][$index] = [
                        'fields' => $relationFields,
                        'col_data' => $col
                    ];
                    $baseRow[$index] = null;
                } else {
                    if (in_array($relationName, ['packagings', 'allPackagings'])) {
                        $relationFields = array_unique(array_merge($relationFields, ['is_gift', 'gift_id']));
                    }
                    // Передаем настройки $col третьим параметром
                    $baseRow[$index] = $this->formatRelationData($item->$relationName, $relationFields, $col);
                }
            }
        }

        if (empty($explodedGroups)) {
            ksort($baseRow);
            return [array_values($baseRow)];
        }

        $rows = [$baseRow];

        foreach ($explodedGroups as $relationName => $colsData) {
            $newRows = [];
            $relationData = $item->$relationName;

            if (empty($relationData)) {
                $relatedItems = collect();
            } else {
                $relatedItems = $relationData instanceof \Illuminate\Support\Collection || is_array($relationData)
                    ? collect($relationData)
                    : collect([$relationData]);
            }

            $isPackagingRelation = in_array($relationName, ['packagings', 'allPackagings']);


            if ($isPackagingRelation) {
                $itemsToIterate = $relatedItems->filter(fn($p) => empty($p->is_gift));
            } else {
                $itemsToIterate = $relatedItems;
            }

            if ($itemsToIterate->isEmpty()) {
                foreach ($rows as $row) {
                    foreach ($colsData as $colIndex => $data) {
                        $row[$colIndex] = '';
                    }
                    $newRows[] = $row;
                }
            } else {
                foreach ($rows as $row) {
                    foreach ($itemsToIterate as $relatedItem) {
                        $clonedRow = $row;

                        if ($idColumnIndex !== null && isset($relatedItem->id)) {
                            $clonedRow[$idColumnIndex] = $item->id . '_' . $relatedItem->id;
                        }

                        if ($isPackagingRelation) {
                            foreach ($nameColumnIndices as $nameIdx) {
                                $colData = $columns[$nameIdx];
                                $loc = $colData['export_locale'] ?? app()->getLocale();

                                $relName = $this->getTranslationSafe($relatedItem, 'name', $loc);
                                if (empty($relName)) {
                                    $relName = data_get($relatedItem, 'name');
                                }

                                if (!empty($relName)) {
                                    $packagingName = str_replace('%s', $relatedItem->qty ?? '', $relName);
                                    $clonedRow[$nameIdx] = trim($row[$nameIdx] . ' ' . $packagingName);
                                }
                            }
                        }

                        foreach ($colsData as $colIndex => $data) {
                            $fields = $data['fields'];
                            $colData = $data['col_data'];

                            if ($isPackagingRelation) {
                                $gift = $relatedItems->first(function ($p) use ($relatedItem) {
                                    $isGift = !empty($p->is_gift);
                                    $basePointsToGift = !empty($relatedItem->gift_id) && $p->id == $relatedItem->gift_id;
                                    $giftPointsToBase = !empty($p->gift_id) && $p->gift_id == $relatedItem->id;
                                    return $isGift && ($basePointsToGift || $giftPointsToBase);
                                });

                                // Передаем $colData
                                $clonedRow[$colIndex] = empty($fields) || !$gift
                                    ? ''
                                    : $this->formatRelationData([$gift], $fields, $colData);
                            } else {
                                $clonedRow[$colIndex] = empty($fields) ? '' : $this->formatRelationData([$relatedItem], $fields, $colData);
                            }
                        }

                        $newRows[] = $clonedRow;
                    }
                }
            }
            $rows = $newRows;
        }

        $finalRows = [];
        foreach ($rows as $row) {
            ksort($row);
            $finalRows[] = array_values($row);
        }

        return $finalRows;
    }

    /**
     * Форматирование данных связи
     */
    protected function formatRelationData($relationData, array $fields, array $colData): string
    {
        if (empty($relationData)) return '';

        $separator = $colData['separator'] ?? "\n";
        if ($separator === '\n') $separator = "\n";

        $exportLocale = $colData['export_locale'] ?? 'all';
        $systemLocales = function_exists('supported_locales') ? array_keys(supported_locales()) : ['ru', 'uk', 'en'];

        $items = $relationData instanceof \Illuminate\Support\Collection || is_array($relationData)
            ? $relationData
            : [$relationData];

        $result = [];

        foreach ($items as $item) {
            if (!$item) continue;

            $fieldValues = [];

            foreach ($fields as $field) {
                // === МАГИЯ ВЛОЖЕННОСТИ: Обработка полей через точку (например, bundleProduct.name) ===
                $targetItem = $item;
                $targetField = $field;

                if (str_contains($field, '.')) {
                    $parts = explode('.', $field);
                    $targetField = array_pop($parts);
                    $relationPath = implode('.', $parts);
                    // data_get маппит пути '.*.' автоматически, возвращая коллекцию!
                    $targetItem = data_get($item, $relationPath);
                }

                if (!$targetItem) continue;

                // БЕЗОПАСНАЯ ПРОВЕРКА: Если цель - коллекция, берем первый элемент для проверки его полей
                $modelForAttributes = $targetItem instanceof \Illuminate\Support\Collection || is_array($targetItem)
                    ? collect($targetItem)->first()
                    : $targetItem;

                $translatedAttributes = $modelForAttributes ? $this->getTranslatedAttributes($modelForAttributes) : [];
                if (empty($translatedAttributes)) {
                    // Подстраховка стандартными полями
                    $translatedAttributes = ['name', 'h1_name', 'description', 'value', 'label'];
                }

                // --- ПЕРЕВОДИМОЕ ПОЛЕ ---
                if (in_array($targetField, $translatedAttributes)) {
                    if ($exportLocale === 'all') {
                        foreach ($systemLocales as $loc) {
                            $tVal = $this->getTranslationSafe($targetItem, $targetField, $loc);

                            if ($tVal) {
                                if ($targetField === 'name' && str_contains($tVal, '%s')) {
                                    $tVal = str_replace('%s', $item->qty ?? '', $tVal);
                                }
                                $fieldValues[] = strtoupper($loc) . ': ' . $this->cleanHtmlTags($tVal);
                            }
                        }
                        continue;
                    } else {
                        $tVal = $this->getTranslationSafe($targetItem, $targetField, $exportLocale);
                        if ($targetField === 'name' && str_contains($tVal, '%s')) {
                            $tVal = str_replace('%s', $item->qty ?? '', $tVal);
                        }
                        $val = $tVal;
                    }
                }
                // --- ОБЫЧНОЕ ПОЛЕ ---
                else {
                    $val = data_get($targetItem, $targetField);
                    if ($targetField === 'name' && is_string($val) && str_contains($val, '%s')) {
                        $val = str_replace('%s', $item->qty ?? '', $val);
                    }
                }

                // Форматирование значений
                if (is_bool($val)) {
                    $val = $val ? '1' : '0';
                } elseif (is_array($val) || is_object($val)) {
                    $val = method_exists($val, '__toString') ? (string) $val : json_encode($val, JSON_UNESCAPED_UNICODE);
                }

                $cleaned = $this->cleanHtmlTags((string) $val);
                if ($cleaned !== '') {
                    $fieldValues[] = $cleaned;
                }
            }

            $joinedFields = implode(' | ', $fieldValues);

            if ($joinedFields !== '') {
                $result[] = $joinedFields;
            }
        }

        return implode($separator, $result);
    }

    /**
     * Очистка от HTML-тегов
     */
    protected function cleanHtmlTags(string $string): string
    {
        if ($string === strip_tags($string)) {
            return trim($string);
        }

        $string = str_replace(['<', '>'], [' <', '> '], $string);
        $string = strip_tags($string);

        return trim(preg_replace('/\s+/', ' ', $string));
    }

    /**
     * Построение запроса с применением фильтров из профиля
     */
    protected function buildQuery(Export $profile): Builder
    {
        $entityClass = $profile->entity;
        $query = $entityClass::query();
        $model = new $entityClass;

        $translatedAttributes = [];
        if (property_exists($model, 'translatedAttributes')) {
            $reflection = new \ReflectionClass($model);
            $property = $reflection->getProperty('translatedAttributes');
            $property->setAccessible(true);
            $translatedAttributes = $property->getValue($model) ?? [];
        }

        $filters = $profile->filters ?? [];

        foreach ($filters as $filter) {
            $field = $filter['field'] ?? null;
            $operator = strtoupper($filter['operator'] ?? '=');
            $value = $filter['value'] ?? null;

            if (!$field) continue;
            if (is_null($value) && $value === '' && !in_array($operator, ['NULL', 'NOT NULL'])) continue;

            if (in_array($field, $translatedAttributes)) {
                $query->whereHas('translations', function ($q) use ($field, $operator, $value) {
                    $this->applyWhereClause($q, $field, $operator, $value);
                });
            } elseif (str_contains($field, '.')) {
                [$relationName, $relationColumn] = explode('.', $field, 2);

                $query->whereHas($relationName, function ($q) use ($relationColumn, $operator, $value) {
                    $this->applyWhereClause($q, $relationColumn, $operator, $value);
                });
            } else {
                $this->applyWhereClause($query, $field, $operator, $value);
            }
        }

        return $query;
    }

    /**
     * Железобетонное получение перевода в обход глобальных Scope
     */
    /**
     * Железобетонное получение перевода в обход глобальных Scope (с поддержкой коллекций)
     */
    protected function getTranslationSafe($item, string $field, string $locale): string
    {
        // МАГИЯ ВЛОЖЕННОСТИ: Если пришла коллекция (например values.*.attributeValue), переводим каждый элемент!
        if ($item instanceof \Illuminate\Support\Collection || is_array($item)) {
            return collect($item)->map(fn($i) => $this->getTranslationSafe($i, $field, $locale))->filter()->implode(', ');
        }

        if (!is_object($item)) return '';

        $val = '';

        if (method_exists($item, 'relationLoaded') && $item->relationLoaded('translations')) {
            $t = $item->translations->firstWhere('locale', $locale);
            if ($t) $val = (string) $t->{$field};
        }

        if (!$val && method_exists($item, 'translate')) {
            $t = $item->translate($locale, false);
            if ($t) $val = (string) $t->{$field};
        }

        if (!$val && method_exists($item, 'translations')) {
            $t = $item->translations()->withoutGlobalScope('locale')->where('locale', $locale)->first();
            if ($t) $val = (string) $t->{$field};
        }

        return $val;
    }

    protected function applyWhereClause($query, $field, $operator, $value): void
    {
        switch ($operator) {
            case 'NULL':
                $query->whereNull($field);
                break;
            case 'NOT NULL':
                $query->whereNotNull($field);
                break;
            case 'IN':
                $valuesArray = array_map('trim', explode(',', (string) $value));
                $query->whereIn($field, $valuesArray);
                break;
            case 'NOT IN':
                $valuesArray = array_map('trim', explode(',', (string) $value));
                $query->whereNotIn($field, $valuesArray);
                break;
            case 'BETWEEN':
                $valuesArray = array_map('trim', explode(',', (string) $value));
                if (count($valuesArray) === 2) {
                    $query->whereBetween($field, $valuesArray);
                }
                break;
            case 'LIKE':
                $query->where($field, 'LIKE', "%{$value}%");
                break;
            default:
                $query->where($field, $operator, $value);
                break;
        }
    }

    protected function ensureDirectoryExists($path) {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    protected function getDriverClass($format) {
        $drivers = [
            'csv' => \Modules\Support\Exports\Drivers\CsvFormatDriver::class,
            'xml' => \Modules\Support\Exports\Drivers\XmlFormatDriver::class,
        ];
        return $drivers[$format];
    }


}
