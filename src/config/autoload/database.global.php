<?php

$databaseUrl = getenv('DATABASE_URL') ?: getenv('MYSQL_URL') ?: '';
$urlConfig = [];

if ($databaseUrl !== '') {
    $parts = parse_url($databaseUrl);

    if (is_array($parts)) {
        $urlConfig = [
            'host' => $parts['host'] ?? '',
            'port' => isset($parts['port']) ? (int) $parts['port'] : 3306,
            'dbname' => isset($parts['path']) ? ltrim($parts['path'], '/') : '',
            'user' => isset($parts['user']) ? rawurldecode($parts['user']) : '',
            'password' => isset($parts['pass']) ? rawurldecode($parts['pass']) : '',
        ];
    }
}

return [
    'database' => [
        'host' => getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: ($urlConfig['host'] ?? ''),
        'port' => (int) (getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: ($urlConfig['port'] ?? 3306)),
        'dbname' => getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: ($urlConfig['dbname'] ?? ''),
        'user' => getenv('DB_USER') ?: getenv('MYSQLUSER') ?: ($urlConfig['user'] ?? ''),
        'password' => getenv('DB_PASS') ?: getenv('MYSQLPASSWORD') ?: ($urlConfig['password'] ?? ''),
    ],
];
