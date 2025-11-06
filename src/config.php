<?php
// src/config.php
declare(strict_types=1);

return [
    'db' => [
        'host' => 'localhost',
        'dbname' => 'cine',
        'user' => 'root',
        'pass' => '123456',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => 'http://cine/api' // o la IP/puerto
    ]
];
