<?php

return [
    'admin.orders' => [
        'index' => 'order::permissions.index',
        'show' => 'order::permissions.show',
        'edit' => 'order::permissions.edit',
    ],
    'admin.order_statuses' => [
        'index' => 'order::permissions.order_statuses.index',
        'create' => 'order::permissions.order_statuses.create',
        'edit' => 'order::permissions.order_statuses.edit',
        'destroy' => 'order::permissions.order_statuses.destroy',
    ],
];
