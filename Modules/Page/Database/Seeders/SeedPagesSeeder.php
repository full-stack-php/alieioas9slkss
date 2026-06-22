<?php

namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Entities\AttributeValue;
use Modules\Attribute\Entities\ProductAttributeValue;
use Modules\Blog\Entities\BlogCategory;
use Modules\Blog\Entities\BlogPost;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\Media\Eloquent\HasMedia;
use Modules\Media\Entities\File;
use Illuminate\Http\File as LaravelFile;
use Modules\Option\Entities\Option;
use Modules\Option\Entities\OptionTranslation;
use Modules\Option\Entities\OptionValue;
use Modules\Product\Entities\Product;
use Symfony\Component\DomCrawler\Crawler;
class SeedPagesSeeder extends Seeder
{

    // Из твоего файла каталога (всё, что НЕ должно быть атрибутом)
    protected $basketOptions = [
        'FRAME_MATERIAL', 'BC', 'CYL', 'ADD', 'COLOR',
        'DIAMETR_LENS', 'VOLUME', 'Sph', 'PACKAGE'
    ];

// Твой словарь имен
    protected $namePropertyMap = [
        'BC' => "Радиус кривизны (BC)",
        'Sph' => "Сфера (Sph)",
        'Cyl' => "Цилиндр (Cyl)",
        'Ax' => "Градус (Ax)",
        'ADD' => "Аддидация (ADD)",
        'COLOR' => "Цвет производителя",
        'VOLUME' => "Объем",
        'COUNT_PACKAGE_VIEW' => "Количество в упаковке",
    ];
    private $categories;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        $this->command->info('Инфо: Импорт прошел успешно');    // Зеленый текст
//        $this->command->error('Ошибка: База Bitrix недоступна'); // Белый текст на красном фоне
//        $this->command->warn('Предупреждение: У страницы нет заголовка');

        $iblocks = \DB::connection('bitrix')->table('b_iblock')->select('ID', 'NAME', 'CODE')->get();

//        $this->command->info('Импорт товаров начат');
//        $this->runProducts(4);
//        $this->command->info('Импорт прошел успешно');

//        $this->command->info('Импорт прошел категорий начат');
//        $this->runCategories(4);
//        $this->command->info('Импорт прошел успешно');

//        $this->command->info('Импорт прошел атрибутов начат');
//        $this->runAttributeAdd();
//        $this->command->info('Импорт прошел успешно');

//        $this->command->info('Импорт прошел опций начат');
//        $this->runOptionsAdd();
//        $this->command->info('Импорт прошел успешно');
//
//        $this->command->info('Импорт прошел производителей начат');
//        $this->runBrandImport(6);
//        $this->command->info('Импорт прошел успешно');

//        $this->runBlogCategories(1, 37);
//        $this->runBlogCategories(9, 38);
    }


    public function importProductSkus($parentId, $laravelProductId)
    {
        $payload = [];
        $optionsCollector = [];
        $linkPropId = \DB::connection('bitrix')
            ->table('b_iblock_property')
            ->where('IBLOCK_ID', 7)
            ->where('XML_ID', 'CML2_LINK')
            ->value('ID');

        $skus = \DB::connection('bitrix')
            ->table('b_iblock_element as e')
            ->join('b_iblock_element_property as ep', 'e.ID', '=', 'ep.IBLOCK_ELEMENT_ID')
            ->where('e.IBLOCK_ID', 7)
            ->where('ep.IBLOCK_PROPERTY_ID', $linkPropId)
            ->where('ep.VALUE', $parentId)
            ->select('e.*')
            ->get();

        $basePrice = '';
        $oldPrice = '';
        $option = '';
        $optionValueIds = [];

        foreach ($skus as $sku) {
            $basePrice = \DB::connection('bitrix')
                ->table('b_catalog_price')
                ->where('PRODUCT_ID', $sku->ID)
                ->where('CATALOG_GROUP_ID', 1)
                ->first();

            $oldPrice = \DB::connection('bitrix')
                ->table('b_catalog_price')
                ->where('PRODUCT_ID', $sku->ID)
                ->where('CATALOG_GROUP_ID', 2)
                ->first();

            $quantityData = \DB::connection('bitrix')
                ->table('b_catalog_product')
                ->where('ID', $sku->ID)
                ->first();

            $skuProperties = \DB::connection('bitrix')
                ->table('b_iblock_element_property as ep')
                ->join('b_iblock_property as p', 'ep.IBLOCK_PROPERTY_ID', '=', 'p.ID')
                ->leftJoin('b_iblock_property_enum as pe', 'ep.VALUE_ENUM', '=', 'pe.ID')
                ->where('ep.IBLOCK_ELEMENT_ID', $sku->ID)
                ->where('p.ID', '!=', $linkPropId)
                ->select([
                    'pe.SORT as sort',
                    'p.NAME as attr_name',
                    'p.CODE as attr_code',
                    'p.PROPERTY_TYPE',
                    'ep.VALUE as raw_value',
                    'pe.VALUE as list_value'
                ])
                ->get()
                ->map(function($prop) {
                    $finalValue = ($prop->PROPERTY_TYPE == 'L') ? $prop->list_value : $prop->raw_value;
                    return [
                        'name' => $prop->attr_name,
                        'code' => $prop->attr_code,
                        'sort' => $prop->sort,
                        'value' => $finalValue
                    ];
                });

            $optionId = '';
            $optionValueIds = [];

            foreach ($skuProperties as $skuProperty) {

                if($skuProperty['code'] == 'STRIHCODE'){
                    continue;
                }
                if($skuProperty['code'] == 'MSG'){
                    continue;
                }
                if($skuProperty['code'] == 'PACKAGE'){
                    continue;
                }

                $finalValue = $skuProperty['value'];
                if (str_contains($finalValue, '{"')) {
                    $decoded = json_decode($finalValue, true);
                    $finalValue = $decoded['RU'] ?? $decoded['UA'] ?? $finalValue;
                }

                if (empty($finalValue)) continue;

                $option = Option::query()
                    ->join('option_translations', 'options.id', '=', 'option_translations.option_id')
                    ->where('option_translations.name', trim($skuProperty['name']))
                    ->select('options.*')
                    ->first();



                if (!$option) {
                    $this->command->warn("Опция '{$skuProperty['name']}' не найдена. Пропускаем.");
                    continue;
                }

                $optionId = $option->id;

                $optionValue = OptionValue::where('option_id', $option->id)
                    ->whereTranslation('label', $skuProperty['value'])
                    ->first();

                if (!$optionValue && !empty($skuProperty['value'])) {
                    $this->command->info("Создаем новое значение '{$skuProperty['value']}' для опции '{$option->name}'");

                    $optionValue = OptionValue::create([
                        'option_id' => $option->id,
                        'uk'        => ['label' => $skuProperty['value']],
                        'ru'        => ['label' => $skuProperty['value']],
                    ]);
                }

                if ($optionValue) {


                    if (!isset($optionsCollector[$optionId])) {
                        $optionsCollector[$optionId] = [
                            'option_id' => $optionId,
                            'type' => 'dropdown',
                            'values' => []
                        ];
                    }

                    $optionsCollector[$optionId]['values'][] = [
                        "option_value_id" => $optionValue->id,
                        "price" => $basePrice->PRICE ?? 0,
                        "price_type" => "fixed",
                        "special_price" => $oldPrice->PRICE ?? 0,
                        "special_price_type" => "fixed",
                        "position" => $skuProperty['sort'],
                        'old_id' => $sku->ID ?? 0,
                    ];
                }

            }


            if($optionId == ''){
                continue;
            }


        }
        $payload = array_values($optionsCollector);

        return $payload;
    }

    public function importProductOptions($id, $allListProperties){
        $payload = [];
        $rawOptions = \DB::connection('bitrix')
            ->table('b_iblock_element_property as ep')
            ->join('b_iblock_property as p', 'ep.IBLOCK_PROPERTY_ID', '=', 'p.ID')
            ->leftJoin('b_iblock_property_enum as pe', 'ep.VALUE_ENUM', '=', 'pe.ID')
            ->where('ep.IBLOCK_ELEMENT_ID', $id)
            ->select([
                'p.ID as prop_id',
                'p.NAME as prop_name',
                'p.FILTRABLE as filterable',
                'p.CODE as prop_code',
                'p.PROPERTY_TYPE',
                'ep.VALUE as raw_value',
                'pe.VALUE as list_value',
                'pe.SORT as sort',
            ])
            ->get()
            ->groupBy('prop_id');

        $optionId = '';


        foreach ($rawOptions as $bitrixPropId => $bitrixValues) {

            $first = $bitrixValues->first();
            $code = $first->prop_code;

            if (!in_array($code, $this->basketOptions) || str_starts_with($code, 'CML2_')) {
                continue;
            }

            $bitrixPropName = $bitrixValues->first()->prop_name;

            // 1. Ищем Опцию в Laravel по имени (используя join для обхода локали, как обсуждали)
            $option = Option::query()
                ->join('option_translations', 'options.id', '=', 'option_translations.option_id')
                ->where('option_translations.name', trim($bitrixPropName))
                ->select('options.*')
                ->first();



            if (!$option) {
                $this->command->warn("Опция '{$bitrixPropName}' не найдена в Laravel. Пропускаем или создай её заранее.");
                continue;
            }
            $option->update(['old_id' => $bitrixValues->first()->prop_id]);

            $optionId = $option->id;

            if($optionId == ''){
                continue;
            }

            $syncValueIds = [];

            foreach ($bitrixValues as $bitrixVal) {
                $valText = (string)$bitrixVal->list_value;

                if (empty($valText)) continue;

                // 2. Ищем Значение Опции (Label) в любом переводе
                $optionValue = OptionValue::where('option_id', $option->id)
                    ->whereHas('translations', function ($query) use ($valText) {
                        $query->where('label', $valText);
                    })->first();

                // 3. Если значения (например, "8.4") нет — создаем его
                if (!$optionValue) {
                    $this->command->info("Создаем значение '{$valText}' для опции '{$bitrixPropName}'");
                    $optionValue = OptionValue::create([
                        'option_id' => $option->id,
                        'uk'        => ['label' => $valText],
                        'ru'        => ['label' => $valText],
                    ]);
                }

                if ($optionValue) {
                    $syncValueIds[] = [
                        "option_value_id"=> $optionValue->id,
                        "price" => "0",
                        "price_type" => "fixed",
                        "special_price" => "0",
                        "special_price_type" => "fixed",
                        "position" => $bitrixVal->sort,
                        'old_id' => $bitrixVal->raw_value,

                    ];
                }
            }
            if(count($syncValueIds) > 0){
                $payload[] = [
                    'option_id' => $optionId,
                    "type" => "dropdown",
                    'values' => $syncValueIds,
                ];
            }

        }

        return $payload;
    }

    public function getAdditionalImages($bitrixElementId)
    {
        $imageIds = \DB::connection('bitrix')
            ->table('b_iblock_element_property as ep')
            ->join('b_iblock_property as p', 'ep.IBLOCK_PROPERTY_ID', '=', 'p.ID')
            ->where('ep.IBLOCK_ELEMENT_ID', $bitrixElementId)
            ->where('p.CODE', 'SLIDER_IMAGES')
            ->pluck('ep.VALUE');

        $images = [];

        foreach ($imageIds as $fileId) {
            if (empty($fileId)) continue;

            $bitrixPreviewRelPath = $this->getBitrixFilePath($fileId);
            $previewFileRecord = $this->migrateFile($bitrixPreviewRelPath);

            if(isset($previewFileRecord->id)){
                $images[] = $previewFileRecord->id;
            }
        }

        return $images;
    }

    public function getAttributes($id)
    {
        $properties = \DB::connection('bitrix')
            ->table('b_iblock_element_property as ep')
            ->join('b_iblock_property as p', 'ep.IBLOCK_PROPERTY_ID', '=', 'p.ID')
            ->leftJoin('b_iblock_property_enum as pe', 'ep.VALUE_ENUM', '=', 'pe.ID')
            ->where('ep.IBLOCK_ELEMENT_ID', $id)
            ->select([
                'p.ID as prop_id',
                'p.NAME as prop_name',
                'p.FILTRABLE as filterable',
                'p.CODE as prop_code',
                'p.PROPERTY_TYPE',
                'ep.VALUE as raw_value',
                'pe.VALUE as list_value',
            ])
            ->get()
            ->groupBy('prop_id');

        $syncData = [];

        foreach ($properties as $propId => $values) {
            $first = $values->first();
            $code = $first->prop_code;

            if (in_array($code, $this->basketOptions) || str_starts_with($code, 'CML2_')) {
                continue;
            }

            $attributeName = $this->namePropertyMap[$code] ?? $first->prop_name;
            $attribute = Attribute::where('old_id', $propId)->first();

            if (!$attribute) {
                //$this->command->error('Ошибка: Аттрибута нет в базе ' . $attributeName . '-' . $propId);
                continue;
            }

            // Инициализируем массив для этого атрибута, чтобы избежать ошибок индекса
            $currentAttributeValues = [];

            foreach ($values as $item) {
                if (is_null($item->raw_value) || trim($item->raw_value) === '') continue;

                // Пропускаем JSON (мультиязычность), если не умеем его парсить
                if (str_contains($item->raw_value, '{"')) continue;

                // 1. Сначала пытаемся найти по тексту (самый надежный способ для строк)
                $finalText = ($item->PROPERTY_TYPE == 'L') ? $item->list_value : $item->raw_value;

                $attributeValue = AttributeValue::where('attribute_id', $attribute->id)
                    ->whereHas('translations', function ($q) use ($finalText) {
                        $q->where('value', (string)$finalText);
                    })
                    ->first();

                if (!$attributeValue && $item->PROPERTY_TYPE == 'L') {
                    $attributeValue = AttributeValue::where('attribute_id', $attribute->id)
                        ->where('old_id', $item->raw_value)
                        ->first();
                }

                if (!$attributeValue && !empty($finalText)) {
                    $attributeValue = AttributeValue::create([
                        'attribute_id' => $attribute->id,
                        'old_id'       => ($item->PROPERTY_TYPE == 'L') ? $item->raw_value : 0,
                        'position'     => 0,
                        'uk' => ['value' => $finalText],
                        'ru' => ['value' => $finalText],
                    ]);
                }

                if ($attributeValue) {
                    $currentAttributeValues[] = $attributeValue->id;
                }
            }

            if (!empty($currentAttributeValues)) {
                $syncData[] = [
                    'attribute_id' => $attribute->id,
                    'values' => array_unique($currentAttributeValues)
                ];
//                $this->command->info('Импорт аттрибута прошел успешно: ' . $attributeName);
            }
        }

        return $syncData;
    }
    private function runProducts(int $int)
    {
        $brandPropId = \DB::connection('bitrix')
            ->table('b_iblock_property')
            ->where('IBLOCK_ID', 4)
            ->whereIn('CODE', ['BRAND', 'MANUFACTURER', 'PROIZVODITEL'])
            ->value('ID');

        $allListProperties = \DB::connection('bitrix')
            ->table('b_iblock_property')
            ->where('IBLOCK_ID', 4)
            ->where('PROPERTY_TYPE', 'L')
            ->where('ACTIVE', 'Y')
            ->pluck('ID')
            ->toArray();

        $items = \DB::connection('bitrix')
            ->table('b_iblock_element as e')
            ->where('e.IBLOCK_ID', $int)
            ->where('e.ACTIVE', 'Y')
            ->get();




        foreach ($items as $item) {
//            $this->command->info("Импорт базового товара: {$item->NAME}");


//            if(463 != $item->ID){
//                continue;
//            }

            $dataOptions = [];
            $dataOptions1 = [];
            $dataAttributes = [];


            $dataOptions = $this->importProductSkus($item->ID, null);
            $dataOptions1 = $this->importProductOptions($item->ID, $allListProperties);
            $dataAttributes = $this->getAttributes($item->ID);


            $basePrice = \DB::connection('bitrix')
                ->table('b_catalog_price')
                ->where('PRODUCT_ID', $item->ID)
                ->where('CATALOG_GROUP_ID', 1)
                ->first();

            $oldPrice = \DB::connection('bitrix')
                ->table('b_catalog_price')
                ->where('PRODUCT_ID', $item->ID)
                ->where('CATALOG_GROUP_ID', 2)
                ->first();


            // 1. Получаем все ID секций из Битрикса (дополнительные + основная)
            $bitrixSectionIds = \DB::connection('bitrix')
                ->table('b_iblock_section_element')
                ->where('IBLOCK_ELEMENT_ID', $item->ID)
                ->pluck('IBLOCK_SECTION_ID')
                ->toArray();

            if (!empty($post->IBLOCK_SECTION_ID)) {
                $bitrixSectionIds[] = $post->IBLOCK_SECTION_ID;
            }

            $bitrixSectionIds = array_unique(array_filter($bitrixSectionIds));

            $laravelCategoryIds = Category::whereIn('old_id', $bitrixSectionIds)
                ->pluck('id')
                ->toArray();

            $allData = $this->getPropData($item->ID);

            if(empty($allData)){
                $this->command->error('Ошибка: Добавления товара ' . $item->NAME . ' ID -- ' . $item->ID);
                continue;
            }


            $getUaData = $this->getBitrixElementMultilang($item->ID, 'n_multilang_element');

            $catalogData = \DB::connection('bitrix')
                ->table('b_catalog_product')
                ->where('ID', $item->ID) // ID предложения
                ->select('QUANTITY', 'WIDTH', 'HEIGHT', 'LENGTH', 'WEIGHT')
                ->first();

            $brandValue = \DB::connection('bitrix')
                ->table('b_iblock_element_property')
                ->where('IBLOCK_ELEMENT_ID', $item->ID)
                ->where('IBLOCK_PROPERTY_ID', $brandPropId)
                ->value('VALUE');

            if ($brandValue) {
                $brand =  Brand::where('old_id', $brandValue)->first();
            }

            $mainCategoryId = null;
            if(!is_null($item->IBLOCK_SECTION_ID)){
                $mainCategoryId = Category::where('old_id', $item->IBLOCK_SECTION_ID)->first();
            }



            $metaData = [
                'uk' => [
                    'meta_title' => $allData['ELEMENT_META_TITLE_UA'] ?? '',
                    'meta_description' =>  $allData['ELEMENT_META_DESCRIPTION_UA'] ?? '',
                ],
                'ru' => [
                    'meta_title' => $allData['ELEMENT_META_TITLE'] ?? '',
                    'meta_description' => $allData['ELEMENT_META_DESCRIPTION'] ?? '',
                ]
            ];

            $mirroredCategoryIds = ['389','390','391','392','393','394','395','396','397','398','399','400','401','402','403','404','405'];

            $isMirrored = !empty(array_intersect($laravelCategoryIds, $mirroredCategoryIds));


            $this->command->info('Добавляем товар ' . $item->NAME . ' в базу. ID -- ' . $item->ID);

            $payload = [
                'uk' => [
                    'name' => $getUaData->NAME ?? $item->NAME,
                    'h1_name' => ($allData['ELEMENT_PAGE_TITLE_UA']) ?? ($getUaData->NAME ?? $item->NAME),
                    'description' => $getUaData->DETAIL_TEXT ?? '',
                ],
                'ru' => [
                    'name' => $item->NAME,
                    'h1_name' => $allData['ELEMENT_PAGE_TITLE']?? $item->NAME,
                    'description' => $item->DETAIL_TEXT ?? '',
                ],
                'is_active' => ($item->ACTIVE === 'Y'),
                'special_price_type' => 'fixed',
                'qty' => (int)($catalogData->QUANTITY ?? 0),
                'in_stock' => 1,
                'is_mirrored' => $isMirrored,
                'manage_stock' => true,
                'brand_id' => $brand->id ?? null,
                'main_category_id' => $mainCategoryId->id ?? null,
                'old_id' => $item->ID,
                '1c_id' => $item->ID,
                'price' => ($oldPrice->PRICE ?? $basePrice->PRICE ?? 0),
                'special_price' => (isset($oldPrice->PRICE) && isset($basePrice->PRICE)) ? $basePrice->PRICE : null,
                'slug' => $item->CODE ?: \Str::slug($item->NAME),
            ];



            $additionalImages = $this->getAdditionalImages($item->ID);
            $bitrixPreviewRelPath = $this->getBitrixFilePath($item->DETAIL_PICTURE);
            $previewFileRecord = $this->migrateFile($bitrixPreviewRelPath);
//
//
            $product = '';
            $product = Product::create($payload);
//
//
            $syncData = [];
            if ($previewFileRecord) {
                $syncData['base_image'] = [$previewFileRecord->id];
                if(!empty($additionalImages)){
                    $syncData['additional_images'] = $additionalImages ;
                }
            }


            if (!empty($syncData)) {
                $this->syncFiles($syncData, Product::class, $product->id);
            }

            if (!empty($laravelCategoryIds)) {
                $product->categories()->sync($laravelCategoryIds);
            } else {
                $this->command->warn("Для товара {$item->NAME} не найдено соответствий категорий в новой базе.");
            }

            if (!empty($dataAttributes)) {
                $productAttributeValues = [];
                foreach ($dataAttributes as $attribute) {
                    $productAttribute = $product->attributes()->create([
                        'attribute_id' => $attribute['attribute_id'],
                    ]);

                    foreach ($attribute['values'] as $valueId) {
                        $productAttributeValues[] = [
                            'product_attribute_id' => $productAttribute->id,
                            'attribute_value_id' => $valueId,
                        ];
                    }
                }
                try {
                    ProductAttributeValue::insert($productAttributeValues);
                } catch (\Exception $exception) {
                    dd($productAttributeValues);
                }

            }

            if(!empty($dataOptions1)){
                $counter = 0;
                foreach ($dataOptions1 as $attributes) {
                    $optionId = $attributes['option_id'] ?? $attributes['id'];

                    $productOption = $product->options()->updateOrCreate(
                        [
                            'id' => $attributes['id'] ?? null,
                        ],
                        [
                            'option_id' => $optionId,
                            'is_required' => $attributes['is_required'] ?? false,
                            'position' => ++$counter,
                        ]
                    );

                    $productOption->saveValues($attributes['values'] ?? []);
                }
            }

            if(!empty($dataOptions)){
                $counter = 0;
                foreach ($dataOptions as $attributes) {
                    $optionId = $attributes['option_id'] ?? $attributes['id'];

                    $productOption = $product->options()->updateOrCreate(
                        [
                            'id' => $attributes['id'] ?? null,
                        ],
                        [
                            'option_id' => $optionId,
                            'is_required' => $attributes['is_required'] ?? false,
                            'position' => ++$counter,
                        ]
                    );

                    $productOption->saveValues($attributes['values'] ?? []);
                }
            }

            $this->syncMeta($metaData, Product::class, $product->id);
        }

    }

    private function runOptionsAdd(){
        $properties = \DB::connection('bitrix')
            ->table('b_iblock_property')
            ->where('IBLOCK_ID', 7)
            ->where('ACTIVE', 'Y')
            // Нам нужны только те, что имеют код и тип Список (L), Строка (S) или Число (N)
            ->whereIn('PROPERTY_TYPE', ['L', 'S', 'N'])
            ->get();

        foreach ($properties as $property) {
            $this->command->info("Синхронизация определения атрибута: {$property->NAME}");


            $uaProp = $this->getBitrixPropsMultilang( $property->ID);

            $payload = [
                'uk' => ['name' => $uaProp->NAME ?? $property->NAME],
                'ru' => ['name' => $property->NAME],
                'type' => 'dropdown',
                'old_id' => $property->ID,
            ];

            $option = Option::create($payload);

            if ($property->PROPERTY_TYPE === 'L') {
                $data = $this->syncOptionEnumValues($property->ID, $option->id);
            }
        }
    }

    protected function syncOptionEnumValues($bitrixPropId, $laravelAttrId)
    {
        $enums = \DB::connection('bitrix')
            ->table('b_iblock_property_enum')
            ->where('PROPERTY_ID', $bitrixPropId)
            ->get();



        foreach ($enums as $k => $enum) {
            $enumUa = \DB::connection('bitrix')
                ->table('n_multilang_enum')
                ->where('ENUM_XML_ID', $enum->XML_ID)
                ->first();

            OptionValue::create(
                [
                    'option_id' => $laravelAttrId,
                    'old_id' => $enum->ID,
                    'uk' => ['label' => $enumUa->VALUE ?? $enum->VALUE],
                    'ru' => ['label' => $enum->VALUE],
                ]
            );
        }
    }
    private function runAttributeAdd()
    {
        $properties = \DB::connection('bitrix')
            ->table('b_iblock_property')
            ->where('IBLOCK_ID', 4)
            ->where('ACTIVE', 'Y')
            // Нам нужны только те, что имеют код и тип Список (L), Строка (S) или Число (N)
            ->whereIn('PROPERTY_TYPE', ['L', 'S', 'N'])
            ->get();

        foreach ($properties as $prop) {

            $this->command->info("Синхронизация определения атрибута: {$prop->NAME}");

            // 1. Получаем перевод имени свойства (UA)
            $uaProp = $this->getBitrixPropsMultilang( $prop->ID);

            $payload = [
                'uk' => ['name' => $uaProp->NAME ?? $prop->NAME],
                'ru' => ['name' => $prop->NAME],
                'is_filterable' => ($prop->FILTRABLE === 'Y'),
                'old_id' => $prop->ID,
                'slug' => Str::slug($prop->CODE ?: $prop->NAME),
                'attribute_set_id' => 1,
            ];

            $attribute = Attribute::create($payload);

            if ($prop->PROPERTY_TYPE === 'L') {
                $data = $this->syncAttributeEnumValues($prop->ID, $attribute->id);
            }

            // 3. Если это тип "Список" (L), импортируем все возможные значения (Enum)

        }

    }

    protected function syncAttributeEnumValues($bitrixPropId, $laravelAttrId)
    {
        $enums = \DB::connection('bitrix')
            ->table('b_iblock_property_enum')
            ->where('PROPERTY_ID', $bitrixPropId)
            ->get();

        foreach ($enums as $k => $enum) {
            $enumUa = \DB::connection('bitrix')
                ->table('n_multilang_enum')
                ->where('ENUM_XML_ID', $enum->XML_ID)
                ->first();


            AttributeValue::create(
                [
                    'attribute_id' => $laravelAttrId,
                    'position' => $k,
                    'old_id' => $enum->ID,
                    'uk' => ['value' => $enumUa->VALUE ?? $enum->VALUE],
                    'ru' => ['value' => $enum->VALUE],
                ]
            );
        }
    }

    public function runCategories($blockId, $parent_id = null)
    {
        $categories = \DB::connection('bitrix')
            ->table('b_iblock_section')
            ->where('IBLOCK_ID', $blockId)
            ->get();

        $parents = [];

        foreach ($categories as $k => $category) {
            $getUaName = $this->getBitrixSectionMultilang($category->ID, 'n_multilang_section');


            $getMetaTitle = $this->getBitrixProp($category->ID, 'SECTION_META_TITLE', 'S');
            $getMetaDesc = $this->getBitrixProp($category->ID, 'SECTION_META_DESCRIPTION', 'S');

            $getH1Title = $this->getBitrixProp($category->ID, 'SECTION_PAGE_TITLE', 'S');

            $getMetaTitleUa = $this->getBitrixProp($category->ID, 'SECTION_META_TITLE_UA', 'S');
            $getMetaDescUa = $this->getBitrixProp($category->ID, 'SECTION_META_DESCRIPTION_UA', 'S');

            $getH1TitleUa = $this->getBitrixProp($category->ID, 'SECTION_PAGE_TITLE_UA', 'S');



            $metaData = [
                'uk' => [
                    'meta_title' => $getMetaTitleUa->TEMPLATE ?? '',
                    'meta_description' =>  $getMetaDescUa->TEMPLATE ?? '',
                ],
                'ru' => [
                    'meta_title' => $getMetaTitle->TEMPLATE ?? '',
                    'meta_description' => $getMetaDesc->TEMPLATE ?? '',
                ]
            ];

            $parent_id = null;
            if(!is_null($category->IBLOCK_SECTION_ID)){
                $parent_id = $parents[$category->IBLOCK_SECTION_ID];
            }

            $payload = [
                'uk' => [
                    'name' => $getUaName->NAME ?? $category->NAME,
                    'h1_name' => ($getH1TitleUa && $getH1TitleUa->TEMPLATE)
                        ? $this->replaceThisName($category->NAME, $getH1TitleUa->TEMPLATE)
                        : ($getUaName->NAME ?? $category->NAME),
                    'description' => $getUaName->DESCRIPTION ?? '',
                ],
                'ru' => [
                    'name' => $category->NAME,
                    'h1_name' => ($getH1Title?->TEMPLATE)
                        ? $this->replaceThisName($category->NAME, $getH1Title->TEMPLATE)
                        : ($this->replaceThisName($category->NAME, $generalPageTitleTemplate?->TEMPLATE ?? '') ?: $category->NAME),
                    'description' => $category->DESCRIPTION ?? '',
                ],
                'is_active' => ($category->ACTIVE === 'Y'),
                'parent_id' => $parent_id,
                'is_searchable' => 0,
                'old_id' => $category->ID,
                'slug' => $category->CODE ?: \Str::slug($category->NAME),
            ];

            $bitrixPreviewRelPath = $this->getBitrixFilePath($category->PICTURE);
            $previewFileRecord = $this->migrateFile($bitrixPreviewRelPath);



            $categoryImp = Category::create(
                $payload
            );

            $parents[$category->ID] = $categoryImp->id;

            $this->command->info('Импорт прошел успешно категории: ' . $category->NAME);

            if (!$categoryImp) {
                throw new \Exception(
                    $this->command->error('Ошибка: Категория не добавлена')
                );
            }

            $syncData = [];
            if ($previewFileRecord) {
                $syncData['logo'] = [$previewFileRecord->id];
            }

            if (!empty($syncData)) {
                $this->syncFiles($syncData, Category::class, $categoryImp->id);
            }


            $this->syncMeta($metaData, Category::class, $categoryImp->id);

            $getFAQs = \DB::connection('bitrix')
                ->table('b_iblock_section_element')
                ->where('IBLOCK_SECTION_ID', $category->ID)
                ->get();

            $faqData = [];

            foreach ($getFAQs as $getFAQ) {
                $faqDataRu = \DB::connection('bitrix')
                    ->table('b_iblock_element')
                    ->where('ID', $getFAQ->IBLOCK_ELEMENT_ID)
                    ->first();

                $faqDataUA = \DB::connection('bitrix')
                    ->table('n_multilang_element')
                    ->where('ELEMENT_ID', $getFAQ->IBLOCK_ELEMENT_ID)
                    ->first();

                $faqData[$faqDataRu->SORT] = [
                    'uk' => [
                        'question' => $faqDataUA->DETAIL_TEXT ?? '',
                        'answer' => $faqDataUA->PREVIEW_TEXT ?? '',
                    ],
                    'ru' => [
                        'question' => $faqDataRu->DETAIL_TEXT ?? '',
                        'answer' => $faqDataRu->PREVIEW_TEXT ?? '',
                    ]
                ];
            }

            if(count($faqData) > 0){
                $this->syncFaq($faqData, Category::class, $categoryImp->id);
            }

        }
    }

    public function runBrandImport($blockId){

        $brands = \DB::connection('bitrix')
            ->table('b_iblock_element as e')
            ->where('e.IBLOCK_ID', 6) // Твой ID из URL
            ->where('e.ACTIVE', 'Y')
            ->select('e.ID', 'e.NAME', 'e.CODE', 'e.PREVIEW_PICTURE', 'e.DETAIL_TEXT')
            ->get();

        foreach ($brands as $brand){
            $this->addBrand($brand);
        }
    }
    private function addBrand($brand){

        $ruName = $brand->NAME;
        $ruDescription = $brand->DETAIL_TEXT;
        $ruMetaTitleTemplate = $this->getBitrixProp($brand->ID, 'ELEMENT_META_TITLE', 'E');
        $ruPageTitleTemplate = $this->getBitrixProp($brand->ID, 'ELEMENT_PAGE_TITLE', 'E');
        $ruDescTitleTemplate = $this->getBitrixProp($brand->ID, 'ELEMENT_META_DESCRIPTION', 'E');

        $uaData = $this->getBitrixElementMultilang($brand->ID, 'n_multilang_element');
        $uaName = $uaData->NAME;
        $uaDescription = $uaData->DETAIL_TEXT;
        $uaMetaTitleTemplate = $this->getBitrixProp($brand->ID, 'ELEMENT_META_TITLE_UA', 'E');
        $uaPageTitleTemplate = $this->getBitrixProp($brand->ID, 'ELEMENT_PAGE_TITLE_UA', 'E');
        $uaDescTitleTemplate = $this->getBitrixProp($brand->ID, 'ELEMENT_META_DESCRIPTION_UA', 'E');

        $metaData = [
            'uk' => [
                'meta_title' => $uaMetaTitleTemplate->TEMPLATE ?? '',
                'meta_description' =>  $uaDescTitleTemplate->TEMPLATE ?? '',
            ],
            'ru' => [
                'meta_title' => $ruMetaTitleTemplate->TEMPLATE ?? '',
                'meta_description' => $ruDescTitleTemplate->TEMPLATE ?? '',
            ]
        ];


        $payload = [
            'uk' => [
                'name' => $uaName ?? $brand->NAME,
                'h1_name' => ($uaPageTitleTemplate && $uaPageTitleTemplate->TEMPLATE)
                    ? $this->replaceThisName($brand->NAME, $uaPageTitleTemplate->TEMPLATE)
                    : ($uaName ?? $brand->NAME),
                'description' => $uaDescription,
            ],
            'ru' => [
                'name' => $brand->NAME,
                'h1_name' => ($ruPageTitleTemplate?->TEMPLATE)
                    ? $this->replaceThisName($brand->NAME, $ruPageTitleTemplate->TEMPLATE)
                    : ($this->replaceThisName($brand->NAME, $generalPageTitleTemplate?->TEMPLATE ?? '') ?: $brand->NAME),
                'description' => $ruDescription,
            ],
            'is_active' => true,
            'old_id' => $brand->ID,
            'slug' => $brand->CODE ?: \Str::slug($brand->NAME),
        ];

        $bitrixPreviewRelPath = $this->getBitrixFilePath($brand->PREVIEW_PICTURE);
        $previewFileRecord = $this->migrateFile($bitrixPreviewRelPath);

        $brand = Brand::create(
            $payload
        );

        $syncData = [];
        if ($previewFileRecord) {
            $syncData['logo'] = [$previewFileRecord->id];
        }


        if (!empty($syncData)) {
            $this->syncFiles($syncData, Brand::class, $brand->id);
        }
        $this->syncMeta($metaData, Brand::class, $brand->id);
    }
    public function runBlogCategories($mainCategoryId = 1, $parent_id = 3)
    {
        $generalMetaTitleTemplate = $this->getBitrixProp($mainCategoryId, 'SECTION_META_TITLE', 'B');
        $generalPageTitleTemplate = $this->getBitrixProp($mainCategoryId, 'SECTION_PAGE_TITLE', 'B');
        $generalDescTitleTemplate = $this->getBitrixProp($mainCategoryId, 'SECTION_META_DESCRIPTION', 'B');


        $sections = \DB::connection('bitrix')
            ->table('b_iblock_section')
            ->where('IBLOCK_ID', $mainCategoryId)
            ->get();

        foreach ($sections as $section) {

            $getUaName = $this->getBitrixSectionMultilang($section->ID, 'n_multilang_section');

            $getMetaTitle = $this->getBitrixProp($section->ID, 'SECTION_META_TITLE', 'S');
            $getMetaDesc = $this->getBitrixProp($section->ID, 'SECTION_META_DESCRIPTION', 'S');

            $getH1Title = $this->getBitrixProp($section->ID, 'SECTION_PAGE_TITLE', 'S');

            $getMetaTitleUa = $this->getBitrixProp($section->ID, 'SECTION_META_TITLE_UA', 'S');
            $getMetaDescUa = $this->getBitrixProp($section->ID, 'SECTION_META_DESCRIPTION_UA', 'S');

            $getH1TitleUa = $this->getBitrixProp($section->ID, 'SECTION_PAGE_TITLE_UA', 'S');




            $metaData = [
                'uk' => [
                    'meta_title' => $getMetaTitleUa->TEMPLATE ?? '',
                    'meta_description' =>  $getMetaDescUa->TEMPLATE ?? '',
                ],
                'ru' => [
                    'meta_title' => $getMetaTitle->TEMPLATE ?? $this->replaceThisName($section->NAME, $generalMetaTitleTemplate->TEMPLATE) ?? '',
                    'meta_description' => $getMetaDesc->TEMPLATE ?? $this->replaceThisName($section->NAME, $generalDescTitleTemplate->TEMPLATE) ?? '',
                ]
            ];



            $payload = [
                'uk' => [
                    'name' => $getUaName->NAME ?? $section->NAME,
                    'h1_name' => ($getH1TitleUa && $getH1TitleUa->TEMPLATE)
                        ? $this->replaceThisName($section->NAME, $getH1TitleUa->TEMPLATE)
                        : ($getUaName->NAME ?? $section->NAME),
                    'description' => '',
                ],
                'ru' => [
                    'name' => $section->NAME,
                    'h1_name' => ($getH1Title?->TEMPLATE)
                        ? $this->replaceThisName($section->NAME, $getH1Title->TEMPLATE)
                        : ($this->replaceThisName($section->NAME, $generalPageTitleTemplate?->TEMPLATE ?? '') ?: $section->NAME),
                    'description' => '',
                ],
                'is_active' => ($section->ACTIVE === 'Y'),
                'parent_id' => $parent_id,
                'slug' => $section->CODE ?: \Str::slug($section->NAME),
            ];



            $blogCategory = BlogCategory::create(
                $payload
            );

            $this->command->info('Импорт прошел успешно категории: ' . $section->NAME);

            if (!$blogCategory) {
                throw new \Exception(
                    $this->command->error('Ошибка: Категория не добавлена')
                );
            }


            $this->syncMeta($metaData, BlogPost::class, $blogCategory->id);

            $this->getBlogPoleznoPosts($section->ID, $blogCategory->id);
        }
    }

    public function getBlogPoleznoPosts($iblockSectionId = 9, $category_id){

        $posts = \DB::connection('bitrix')
            ->table('b_iblock_element as e')
            ->leftJoin('b_iblock_section_element as se', 'e.ID', '=', 'se.IBLOCK_ELEMENT_ID')
            ->leftJoin('b_iblock_section as s', function($join) {
                $join->on('e.IBLOCK_SECTION_ID', '=', 's.ID')
                    ->orOn('se.IBLOCK_SECTION_ID', '=', 's.ID');
            })
            ->where(function($query) use ($iblockSectionId) {
                $query->where('e.IBLOCK_SECTION_ID', $iblockSectionId)
                    ->orWhere('se.IBLOCK_SECTION_ID', $iblockSectionId);
            })
            ->where('e.ACTIVE', 'Y') // Берем только активные статьи
            ->select('e.*', 's.NAME as category_name')
            ->distinct()
            ->get();

        foreach ($posts as $post) {


            $textDataRu = $this->cleanBlogRuHtml($post->DETAIL_TEXT);
            $uaData = $this->getBitrixElementMultilang($post->ID, 'n_multilang_element');

            $getH1Title = $this->getBitrixProp($post->ID, 'ELEMENT_PAGE_TITLE', 'E');
            $getMetaTitle = $this->getBitrixProp($post->ID, 'ELEMENT_META_TITLE', 'E');

            $metaTitle = ($getMetaTitle?->TEMPLATE)
                ? $this->replaceThisName($post->NAME, $getMetaTitle->TEMPLATE)
                : $post->NAME;

            $getMetaDesc = $this->getBitrixProp($post->ID, 'ELEMENT_META_DESCRIPTION', 'E');

            $uaSEOData = $this->getBitrixMultilang($post->ID, 'n_multilang_seo');
            $textDataUa = $this->cleanBlogUaHtml($uaData->DETAIL_TEXT);
            $bitrixPreviewRelPath = $this->getBitrixFilePath($post->PREVIEW_PICTURE);
            $bitrixDetailRelPath = $this->getBitrixFilePath($post->DETAIL_PICTURE);
            $previewFileRecord = $this->migrateFile($bitrixPreviewRelPath);
            $detailFileRecord = $this->migrateFile($bitrixDetailRelPath);


            $faqData = $this->mergeFaqs($textDataRu['faq'], $textDataUa['faq']);
            $metaData = [
                'uk' => [
                    'meta_title' => $uaSEOData->TITLE ?? $post->NAME,
                    'meta_description' =>  $uaSEOData->DESC ?? '',
                ],
                'ru' => [
                    'meta_title' => $metaTitle ?? $post->NAME,
                    'meta_description' => ($getMetaDesc?->TEMPLATE)
                        ? $this->replaceThisName($post->NAME, $getMetaDesc->TEMPLATE)
                        : '',
                ]
            ];
            $payload = [
                'uk' => [
                    'name' => $uaData->NAME ?? $post->NAME,
                    'h1_name' => $uaSEOData->H1 ?? $post->NAME,
                    'description' => $textDataUa['content'],
                ],
                'ru' => [
                    'name' => $post->NAME,
                    'h1_name' => ($getH1Title?->TEMPLATE)
                        ? $this->replaceThisName($post->NAME, $getH1Title->TEMPLATE)
                        : $post->NAME,
                    'description' => $textDataRu['content'],
                ],
                'is_active' => ($post->ACTIVE === 'Y'),
                'blog_category_id' => $category_id,
                'slug' => $post->CODE ?: \Str::slug($post->NAME),
                'created_at' => $post->DATE_CREATE,
                'updated_at' => $post->DATE_CREATE,
            ];
            $blogPost = BlogPost::create(
                $payload
            );

            if (!$blogPost) {
                throw new \Exception(
                    $this->command->error('Ошибка: Страница не добавлена')
                );
            }

            $syncData = [];
            if ($previewFileRecord) {
                $syncData['preview'] = [$previewFileRecord->id];
            }

            if ($detailFileRecord) {
                $syncData['full_image'] = [$detailFileRecord->id];
            }

            if (!empty($syncData)) {
                $this->syncFiles($syncData, BlogPost::class, $blogPost->id);
            }

            $this->syncMeta($metaData, BlogPost::class, $blogPost->id);
            $this->syncFaq($faqData, BlogPost::class, $blogPost->id);
            $this->command->info('Импорт прошел успешно записи: ' . $post->NAME);
        }
    }


    private function getPropData($id){
        $page_data = [];
        $data = DB::connection('bitrix')
            ->table('b_iblock_element_iprop')
            ->where('ELEMENT_ID', $id)
            ->get();



        foreach ($data as $k =>  $item) {
            $get_prop_name =  DB::connection('bitrix')
                ->table('b_iblock_iproperty')
                ->where('ID', $item->IPROP_ID)
                ->select('CODE')
                ->first();
            if(str_contains($get_prop_name->CODE, 'SECTION')){
                continue;
            }
            $page_data[$get_prop_name->CODE] = $item->VALUE;
        }

        return $page_data;
    }
    private function getBitrixProp($id, $name, $entityType){
        return DB::connection('bitrix')
            ->table('b_iblock_iproperty')
            ->where('ENTITY_ID', $id)
            ->where('CODE', $name)
            ->where('ENTITY_TYPE', $entityType)
            ->first();
    }
    private function getBitrixSectionMultilang($id, $table){

        return DB::connection('bitrix')
            ->table($table)
            ->where('SECTION_ID', $id)
            ->where('LANG', 'ua')
            ->first();
    }
    private function getBitrixElementMultilang($id, $table){

        return DB::connection('bitrix')
            ->table($table)
            ->where('ELEMENT_ID', $id)
            ->where('LANG', 'ua')
            ->first();
    }
    private function getBitrixPropsMultilang($id){

        return DB::connection('bitrix')
            ->table('n_multilang_props')
            ->where('PROPERTY_ID', $id)
            ->where('LANG', 'ua')
            ->first();
    }
    private function getBitrixMultilang($id, $table){

        return DB::connection('bitrix')
            ->table($table)
            ->where('OBJECT_ID', $id)
            ->where('LANG', 'ua')
            ->first();
    }
    private function cleanBlogRuHtml($html)
    {
        $rawHtml = $html;
        $html = preg_replace_callback('/&#([0-9]+);/', function($matches) {
            $code = (int)$matches[1];
            if ($code >= 1040 && $code <= 1103) {
                return mb_convert_encoding(pack('n', $code), 'UTF-8', 'UTF-16BE');
            }
            return mb_convert_encoding(pack('n', $code), 'UTF-8', 'UTF-16BE');
        }, $rawHtml);
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
        $html = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $html);
        $html = preg_replace('/\s+/', ' ', $html); // схлопываем лишние пробелы


        $crawler = new Crawler($html);



        $faq = [];
        $crawler->filter('[itemtype="https://schema.org/FAQPage"] [itemprop="mainEntity"]')->each(function (Crawler $node) use (&$faq) {
            $question = $node->filter('[itemprop="name"]')->text();
            $answer = $node->filter('[itemprop="text"]')->html(); // Сохраняем с внутренней разметкой (списки и т.д.)

            $faq[]= [
                'question' => trim($question),
                'answer'   => trim($answer)
            ];
        });

        $crawler->filter('h2')->each(function (Crawler $node) {
            if (str_contains($node->text(), 'Частые вопросы') || str_contains($node->text(), 'FAQ')) {
                $node->getNode(0)->parentNode->removeChild($node->getNode(0));
            }
        });

        $crawler->filter('[itemtype*="FAQPage"]')->each(function (Crawler $node) {
            $node->getNode(0)->parentNode->removeChild($node->getNode(0));
        });

        $crawler->filter('script')->each(function (Crawler $node) {
            $node->getNode(0)->parentNode->removeChild($node->getNode(0));
        });

        foreach ($crawler->filter('[style]') as $node) {
            $node->removeAttribute('style');
        }

        foreach ($crawler->filter('[width], [height]') as $node) {
            $node->removeAttribute('width');
            $node->removeAttribute('height');
        }

        $finalContent = '';
        foreach ($crawler->filter('body')->children() as $node) {
            $finalContent .= $node->ownerDocument->saveHTML($node);
        }

        $finalContent = html_entity_decode($finalContent, ENT_QUOTES, 'UTF-8');

        return [
            'content' => $finalContent,
            'faq' => $faq
        ];
    }
    private function cleanBlogUaHtml($html)
    {
        $rawHtml = $html;
        $html = preg_replace_callback('/&#([0-9]+);/', function($matches) {
            $code = (int)$matches[1];
            if ($code >= 1040 && $code <= 1103) {
                return mb_convert_encoding(pack('n', $code), 'UTF-8', 'UTF-16BE');
            }
            return mb_convert_encoding(pack('n', $code), 'UTF-8', 'UTF-16BE');
        }, $rawHtml);
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
        $html = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $html);
        $html = preg_replace('/\s+/', ' ', $html); // схлопываем лишние пробелы


        $crawler = new Crawler($html);



        $faq = [];
        $crawler->filter('[itemtype="https://schema.org/FAQPage"] [itemprop="mainEntity"]')->each(function (Crawler $node) use (&$faq) {
            $question = $node->filter('[itemprop="name"]')->text();
            $answer = $node->filter('[itemprop="text"]')->html(); // Сохраняем с внутренней разметкой (списки и т.д.)

            $faq[] = [
                'question' => trim($question),
                'answer'   => trim($answer)
            ];
        });

        $crawler->filter('h2')->each(function (Crawler $node) {
            if (str_contains($node->text(), 'Часті запитання') || str_contains($node->text(), 'FAQ')) {
                $node->getNode(0)->parentNode->removeChild($node->getNode(0));
            }
        });

        $crawler->filter('[itemtype*="FAQPage"]')->each(function (Crawler $node) {
            $node->getNode(0)->parentNode->removeChild($node->getNode(0));
        });

        $crawler->filter('script')->each(function (Crawler $node) {
            $node->getNode(0)->parentNode->removeChild($node->getNode(0));
        });

        foreach ($crawler->filter('[style]') as $node) {
            $node->removeAttribute('style');
        }

        foreach ($crawler->filter('[width], [height]') as $node) {
            $node->removeAttribute('width');
            $node->removeAttribute('height');
        }

        $body = $crawler->filter('body');
        $finalContent = '';

        if ($body->count() > 0) {
            foreach ($body->filter('body')->children() as $node) {
                $finalContent .= $node->ownerDocument->saveHTML($node);
            }
        }

        $finalContent = html_entity_decode($finalContent, ENT_QUOTES, 'UTF-8');

        return [
            'content' => $finalContent,
            'faq' => $faq
        ];
    }
    protected function getBitrixFilePath($fileId)
    {
        if (!$fileId) return null;

        $file = \DB::connection('bitrix')
            ->table('b_file')
            ->where('ID', $fileId)
            ->first();

        if ($file) {
            // Путь в Битриксе строится так: /upload/[SUBDIR]/[FILE_NAME]
            return "/upload/{$file->SUBDIR}/{$file->FILE_NAME}";
        }

        return null;
    }
    protected function mergeFaqs($ruFaqs, $ukFaqs)
    {
        $merged = [];
        foreach ($ruFaqs as $index => $ruFaq) {
            $merged[] = [
                'uk' => [
                    'question' => $ukFaqs[$index]['question'] ?? $ruFaq['question'],
                    'answer' => $ukFaqs[$index]['answer'] ?? $ruFaq['answer'],
                ],
                'ru' => [
                    'question' => $ruFaq['question'],
                    'answer' => $ruFaq['answer'],
                ]
            ];
        }
        return $merged;
    }
    public function syncFaq(array $faqData = [], $entityType, $entityId): void
    {
        if (empty($faqData)) {
            return;
        }

        $entity = app($entityType)::find($entityId);

        if (!$entity) {
            $this->command->error("Entity not found for FAQ Sync: {$entityType} ID {$entityId}");
            return;
        }

        if (method_exists($entity, 'syncFaqs')) {
            $entity->syncFaqs($faqData);
        } else {
            $this->command->error("Model {$entityType} does not use FAQ's trait.");
        }
    }
    public function syncMeta(array $metaData = [], $entityType, $entityId): void
    {
        if (empty($metaData)) {
            return;
        }

        $entity = app($entityType)::find($entityId);

        if (!$entity) {
            $this->command->error("Entity not found for Meta Sync: {$entityType} ID {$entityId}");
            return;
        }

        if (method_exists($entity, 'saveMetaData')) {
            $entity->saveMetaData($metaData);
        } else {
            $this->command->error("Model {$entityType} does not use HasMetaData trait.");
        }
    }

    public function syncFiles(array $files = [], $entityType, $entityId): void
    {
        if (empty($files)) {
            return;
        }

        foreach ($files as $zone => $fileIds) {
            $syncList = [];

            foreach (array_wrap($fileIds) as $fileId) {
                if (!empty($fileId)) {
                    $syncList[$fileId]["zone"] = $zone;
                    $syncList[$fileId]["entity_type"] = $entityType;
                }
            }

            $this->filterFiles($zone, $entityType, $entityId)->detach();
            $this->filterFiles($zone, $entityType, $entityId)->attach(
                $syncList
            );
        }
    }

    public function filterFiles(string|array $zones, string $entityType, int $entity_id) {
        $entity = app($entityType)->withoutGlobalScopes()->find($entity_id);

        if (!$entity) {
            throw new \Exception(
                "Entity not found for type {$entityType} with ID {$entity_id}.
             Check if the record exists in the database table."
            );
        }

        return $entity->files()->wherePivotIn("zone", array_wrap($zones));
    }

    protected function migrateFile($bitrixRelativePath)
    {
        if (!$bitrixRelativePath) return null;

        // Полный путь к файлу в системе Bitrix (подправь путь к корню битрикса)
        $fullPath = 'F:\OSPanel\home\leansold' . $bitrixRelativePath;

        if (!file_exists($fullPath)) {
            return null;
        }

        // Имитируем загрузку файла
        $laravelFile = new LaravelFile($fullPath);

        // Сохраняем файл в Laravel Storage (папка 'media')
        $path = Storage::putFile('media', $laravelFile);

        // Создаем запись в твоей таблице файлов
        return File::create([
            'user_id' => 1, // ID администратора для импорта
            'disk' => config('filesystems.default'),
            'filename' => substr(basename($fullPath), 0, 255),
            'path' => $path,
            'extension' => pathinfo($fullPath, PATHINFO_EXTENSION) ?? '',
            'mime' => mime_content_type($fullPath),
            'size' => filesize($fullPath),
        ]);
    }
    private function replaceThisName($name, $template){

        if($template == null){
            return false;
        }
        return str_replace('{=this.Name}', $name, $template);
    }


}
