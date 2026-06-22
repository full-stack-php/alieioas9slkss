<?php

return [
    'attributes' => [
        'attribute_set_id' => 'Группа атрибутов',
        'name' => 'Название',
        'categories' => 'Категории',
        'slug' => 'URL',
        'is_filterable' => 'Фильтруемый',
    ],
    'attribute_sets' => [
        'name' => 'Название',
    ],
    'product_attributes' => [
        'attributes.*.attribute_id' => 'Атрибут',
        'attributes.*.values' => 'Значения',
    ],
];
