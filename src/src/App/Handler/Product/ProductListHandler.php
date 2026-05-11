<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Database\Database;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ProductListHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $pdo = $this->db->getPdo();
        $this->ensureProductMetadataColumns($pdo);

        $stmt = $pdo->prepare("
            SELECT id, name, price, stock, is_active, created_at, updated_at, last_restocked_at, restock_count
            FROM products
            ORDER BY id DESC
        ");
        $stmt->execute();

        $products = $stmt->fetchAll();

        $userName = Session::get('user_name');
        $role = Session::get('user_role');
        $currentRoute = 'products';

        $content = Template::render('products/list', [
            'products' => $products,
        ]);

        return new HtmlResponse(
            Template::render('layout', [
                'content' => $content,
                'currentRoute' => $currentRoute,
                'userName' => $userName,
                'role' => $role,
            ])
        );
    }

    private function ensureProductMetadataColumns(\PDO $pdo): void
    {
        $columns = [
            'last_restocked_at' => "ALTER TABLE products ADD COLUMN last_restocked_at DATETIME NULL",
            'restock_count' => "ALTER TABLE products ADD COLUMN restock_count INT NOT NULL DEFAULT 0",
            'updated_at' => "ALTER TABLE products ADD COLUMN updated_at DATETIME NULL",
        ];

        foreach ($columns as $column => $sql) {
            $stmt = $pdo->prepare("SHOW COLUMNS FROM products LIKE :column");
            $stmt->execute([':column' => $column]);
            if (!$stmt->fetch()) {
                $pdo->exec($sql);
            }
        }
    }
}
