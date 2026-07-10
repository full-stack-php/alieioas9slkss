<?php

return [
    'fields' => [
        'phone' => [
            'enabled' => true,
            'required' => true,
        ],

        'comment' => [
            'enabled' => true,
            'required' => false,
        ],
    ],

    'payment_methods_priority' => [
        'cod',
        'bank_transfer',
        'check_payment',
    ],

    'shipping_methods_priority' => [
        'local_pickup',
        'flat_rate',
        'nova_poshta_branch',
        'nova_poshta_address',
        'nova_poshta_postomat',
    ],
];
