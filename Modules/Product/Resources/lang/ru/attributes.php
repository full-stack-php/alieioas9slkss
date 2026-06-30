<?php

return [
    'name' => 'Наименование',
    'slug' => 'URL',
    'description' => 'Описание',
    'h1_name' => 'HTML-тег H1',
    'brand_id' => 'Бренд',
    'manufacturer_id' => 'Производитель',
    'main_category_id' => 'Главная категория',
    '1c_id' => 'Id Товара в 1С',

    'categories' => 'Категории',
    'is_mirrored' => 'Зеркальные опции',
    'is_active' => 'Статус',
    'price' => 'Цена',
    'special_price' => 'Специальная цена',
    'special_price_type' => 'Тип специальной цены',
    'special_price_start' => 'Дата начала',
    'special_price_end' => 'Дата окончания',
    'sku' => 'Артикул',
    'manage_stock' => 'Управление запасами',
    'qty' => 'Количество',
    'in_stock' => 'Наличие на складе',
    'new_from' => 'Новинка от',
    'new_to' => 'Новинка до',
    'colors' => 'Цвета',
    'cross_sells' => 'Перекрестные продажи',
    'related_products' => 'Сопутствующие товары',
    'notice' => 'Уведомление на товаре',
    'attributes' => [
        '*.attribute_id' => 'Атрибут',
        '*.values' => 'Значения',
    ],
    'options' => [
        '*.name' => 'Название',
        '*.type' => 'Тип',
        '*.values.*.label' => 'Значение',
        '*.values.*.option_value_id' => 'Значение опции',
        '*.values.*.price' => 'Цена',
        '*.values.*.price_type' => 'Тип цены',
        '*.values.*.special_price' => 'Скидка',
        '*.values.*.special_price_type' => 'Тип скидки',
        'option_id' => 'Опция',
        'values' => [
            'option_value_id' => 'Значение опции',
        ],
    ],
    'packagings' => [
        'name_with_locale' => 'Название упаковки (:locale)',
        'qty' => 'Количество в упаковке',
        'price' => 'Цена за единицу',
        'special_price' => 'Специальная цена',
        'special_price_type' => 'Тип специальной цены',
    ],

    'gift_packagings' => [
        'name_with_locale' => 'Название подарочной упаковки (:locale)',
    ],

    'videos' => [
        'title' => 'Название видео',
        'url' => 'YouTube ссылка',
        'sort_order' => 'Порядок видео',
        'main_video' => 'Главное видео',
    ],
];
