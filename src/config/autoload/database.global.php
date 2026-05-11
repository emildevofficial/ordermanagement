<?php

return [
    'database' => [
        'host' => getenv('DB_HOST') ?: '',
        'port' => (int) (getenv('DB_PORT') ?: 3306),
        'dbname' => getenv('DB_NAME') ?: '',
        'user' => getenv('DB_USER') ?: '',
        'password' => getenv('DB_PASS') ?: '',
    ],
];
