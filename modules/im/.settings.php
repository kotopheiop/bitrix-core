<?php

return [
    'controllers' => [
        'value' => [
            'defaultNamespace' => '\\Bitrix\\Im\\Controller',
            'restIntegration' => [
                'enabled' => true
            ]
        ],
        'readonly' => true,
    ],
    'services' => [
        'value' => [
            'Im.Services.Message' => [
                'className' => '\\Bitrix\\Im\\Services\\Message',
            ],
            'Im.Services.MessageParam' => [
                'className' => '\\Bitrix\\Im\\Services\\MessageParam',
            ],
        ],
        'readonly' => true,
    ],
];