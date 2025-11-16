<?php
// src/config.php
declare(strict_types=1);

return [
    'db' => [
        'host' => 'localhost',
        'dbname' => 'cine_db',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => 'http://localhost/cine_api/public'
    ]
];
