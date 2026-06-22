<?php

return [
    'admin.shipping_methods' => [
        'index' => 'shipping::permissions.shipping_methods.index',
        'edit'  => 'shipping::permissions.shipping_methods.edit',
    ],

    'admin.nova_poshta' => [
        'index' => 'shipping::permissions.nova_poshta.index',
        'edit'  => 'shipping::permissions.nova_poshta.edit',
        'sync'  => 'shipping::permissions.nova_poshta.sync',
    ],
    'admin.meest' => [
        'index' => 'shipping::permissions.meest.index',
        'edit'  => 'shipping::permissions.meest.edit',
        'sync'  => 'shipping::permissions.meest.sync',
    ],
];
