<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Session;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ProductToggleHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $id = (int)$request->getAttribute('id');

        $pdo = $this->db->getPdo();
        $this->ensureProductTimestampColumns($pdo);

        $stmt = $pdo->prepare("
            UPDATE products 
            SET is_active = NOT is_active, updated_at = :updated_at
            WHERE id = :id
        ");

        $stmt->execute([
            ':updated_at' => DateTimeHelper::nowForStorage(),
            ':id' => $id,
        ]);

        return new RedirectResponse('/products');
    }

    private function ensureProductTimestampColumns(\PDO $pdo): void
    {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM products LIKE 'updated_at'");
        $stmt->execute();
        if (!$stmt->fetch()) {
            $pdo->exec("ALTER TABLE products ADD COLUMN updated_at DATETIME NULL");
        }
    }
}

