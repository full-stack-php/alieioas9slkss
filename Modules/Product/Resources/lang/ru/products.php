<?php

return [
    'product' => 'Товар',
    'products' => 'Товары',
    'save' => 'Сохранить',
    'save_and_exit' => 'Save & Exit',
    'save_and_edit' => 'Save & Edit',

    'section' => [
        'expand_all' => 'Expand All',
        'collapse_all' => 'Collapse All',
        'order_saved' => 'Order saved',
    ],

    'table' => [
        'thumbnail' => 'Миниатюра',
        'name' => 'Наименование',
        'price' => 'Цена',
        'stock' => 'Запас',
    ],

    'tabs' => [
        'group' => [
            'basic_information' => 'Основная информация',
            'advanced_information' => 'Advanced Information',
        ],
        'general' => 'Общие',
        'price' => 'Цена',
        'inventory' => 'Запас',
        'images' => 'Изображения',
        'seo' => 'SEO',
        'related_products' => 'Сопутствующие товары',
        'colors' => 'Варианты',
        'cross_sells' => 'Cross-Sells',
        'additional' => 'Дополнительно',
        'packaging' => 'Упаковки',
        'gift_packaging' => 'Подарочные упаковки',
        'gifts' => 'Подарки',
        'bundles' => 'Комплекты',
        'videos' => 'Видео',
        'documents' => 'Документы',
    ],

    'attributes' => [
        'attribute' => 'Атрибут',
        'values' => 'Значения',
        'add_attribute' => 'Добавить атрибут',
    ],

    'options' => [
        'new_option' => 'New Option',
        'add_option' => 'Add Option',
        'add_row' => 'Add Row',
        'insert' => 'Insert',
        'option_inserted' => 'Option inserted',
    ],

    'form' => [
        'the_product_won\'t_be_shipped' => 'Необходима доставка',
        'enable_the_product' => 'Включить товар',
        'enable_the_product_option_mirrored' => 'Включить зеркальное отображение опций',
        'base_image' => 'Изображение товара',
        'additional_images' => 'Дополнительные изображения',
        'special_price_types' => [
            'fixed' => 'Фиксированный',
            'percent' => 'Процент',
        ],
        'manage_stock_states' => [
            0 => 'Не отслеживать запасы',
            1 => 'Отслеживать запасы',
        ],
        'stock_availability_states' => [
            1 => 'В наличии',
            0 => 'Нет в наличии',
            3 => 'Пред заказ',
            4 => 'Снято с производства',
        ],
        'gift' => [
            'add_gift' => 'Добавить подарок',
            'select_product' => 'Товар',
            'price' => 'Цена',
            'min_qty' => 'Мин. кол-во',
            'search_placeholder' => 'Введите название товара',
            'no_results' => 'Ничего не найдено',
            'select' => 'Выбрать',
        ],
        'bundle' => [
            'main_product_settings' => 'Настройки текущего товара',
            'main_product_qty' => 'Кол-во товара',
            'bundle_product_qty' => 'Кол-во в наборе',
            'price' => 'Цена',
            'special_price' => 'Специальная цена',
            'special_price_type' => 'Тип цены',
            'bundle_product' => 'Комплект товаров',
            'bundle_product_settings' => 'Настройки комплекта товаров',
            'search_placeholder' => 'Введите название товара',
            'no_results' => 'Нет результатов',
            'select' => 'Выбрать',
            'add_bundle' => 'Добавить комплект',
        ],
        'packaging' => [
            'name' => 'Название',
            'qty' => 'Кол в упк.',
            'price' => 'Цена за ед.',
            'special_price' => 'Специальная цена',
            'special_price_type' => 'Тип специальной цены',
            'add_new_packaging' => 'Добавить упаковку',
            'add_new_packaging_gift' => 'Добавить подарочную упаковку',
            'select' => 'Выбрать',
        ],

        'options' => [
            'name' => 'Name',
            'type' => 'Type',
            'is_required' => 'Required',
            'label' => 'Label',
            'price' => 'Цена',
            'price_type' => 'Price Type',

            'option_types' => [
                'please_select' => 'Please Select',
                'text' => 'Text',
                'field' => 'Field',
                'textarea' => 'Textarea',
                'select' => 'Select',
                'dropdown' => 'Dropdown',
                'checkbox' => 'Checkbox',
                'checkbox_custom' => 'Custom Checkbox',
                'radio' => 'Radio Button',
                'radio_custom' => 'Custom Radio Button',
                'multiple_select' => 'Multiple Select',
                'date' => 'Date',
                'date_time' => 'Date & Time',
                'time' => 'Time',
            ],

            'price_types' => [
                'fixed' => 'Фиксированный',
                'percent' => 'Процент',
            ],
            'base_image' => 'Изображение товара',
            'additional_images' => 'Дополнительные изображения',
        ],
        'video' => [
            'main' => 'Главное',
            'title' => 'Название видео',
            'url' => 'YouTube ссылка',
            'url_placeholder' => 'https://www.youtube.com/watch?v=...',
            'sort_order' => 'Порядок',
            'add_video' => 'Добавить видео',
            'delete' => 'Удалить',
        ],
        'documents' => [
            'title' => 'Документы и файлы',
            'description' => 'Можно прикрепить PDF, JPG или PNG: сертификаты, инструкции, документы качества и другие вложения.',
            'download' => 'Скачать',
        ],
    ],
    'one_c_mappings' => [
        'title' => '1С ID опций и упаковок',
        'single' => '1С ID запись',

        'table' => [
            'product' => 'Товар',
            'target' => 'Упаковка / опции',
            'external_id' => 'Заданный ID',
            'one_c_id' => 'Итоговый 1С ID',
        ],

        'form' => [
            'product' => 'Товар',
            'product_search_placeholder' => 'Начните вводить название, SKU или 1С ID товара',
            'packaging' => 'Упаковка',
            'options' => 'Опции товара',
            'external_id' => 'Заданный ID',
            'one_c_id_preview' => 'Итоговый 1С ID',
            'select_product_first' => 'Сначала выберите товар',
            'no_options' => 'У этого товара нет опций',
        ],
    ],
];
