<?php
// src/config.php
declare(strict_types=1);

return [
    'db' => [
        'host' => 'localhost',
        // cambiar siempre la mia se lalma cine no cine_bd
        'dbname' => 'cine_db',
        'user' => 'root',
        'pass' => '123456',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => 'http://localhost/cine_api/public'
    ]
];
