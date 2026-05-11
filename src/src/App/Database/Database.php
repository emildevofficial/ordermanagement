<?php

declare(strict_types=1);

namespace App\Database;

use App\Helper\DateTimeHelper;
use PDO;

class Database
{
    private PDO $pdo;

    public function __construct(array $config)
    {
        date_default_timezone_set(DateTimeHelper::APP_TIMEZONE);

        $dsn = sprintf(
            "mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4",
            $config['host'],
            $config['port'] ?? 3306,
            $config['dbname']
        );

        $this->pdo = new PDO(
            $dsn,
            $config['user'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        $this->pdo->exec("SET time_zone = '+00:00'");
    }

    public function findUserByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE email = :email LIMIT 1"
        );

        $stmt->execute([
            'email' => $email
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    // OPTIONAL (mund ta përdorësh më vonë)
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
