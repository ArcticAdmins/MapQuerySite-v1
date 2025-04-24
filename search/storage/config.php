<?php
return [
    'database' => [
        'driver' => 'sqlite',
        'url' => __DIR__ . '/database.sqlite',
        'prefix' => '',
    ],
    'storage' => [
        'json_db' => __DIR__ . '/icebear.json',
    ],
    'registration' => [
        'open' => true,
        'require_invitation_code' => false,
    ],
];