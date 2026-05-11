<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Permission;
use App\Helper\Session;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ProductUpdateStockHandler implements RequestHandlerInterface
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
        $data = $request->getParsedBody();
        $wantsJson = strtolower($request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest'
            || strpos(strtolower($request->getHeaderLine('Accept')), 'application/json') !== false;

        if (!Permission::isAllowed('admin')) {
            return new JsonResponse(['success' => false, 'error' => 'Forbidden'], 403);
        }

        $stock = (int)($data['stock'] ?? 0);

        if ($stock <= 0) {
            if ($wantsJson) {
                return new JsonResponse(['success' => false, 'error' => 'Please enter a valid quantity greater than 0.'], 400);
            }

            return new RedirectResponse('/products');
        }

        $pdo = $this->db->getPdo();
        $this->ensureProductMetadataColumns($pdo);

        $stmt = $pdo->prepare("
            UPDATE products 
            SET
                stock = stock + :stock,
                last_restocked_at = CASE WHEN :stock_for_date > 0 THEN :restocked_at ELSE last_restocked_at END,
                restock_count = restock_count + CASE WHEN :stock_for_count > 0 THEN 1 ELSE 0 END,
                updated_at = :updated_at
            WHERE id = :id
        ");

        $now = DateTimeHelper::nowForStorage();
        $stmt->execute([
            ':stock' => $stock,
            ':stock_for_date' => $stock,
            ':stock_for_count' => $stock,
            ':restocked_at' => $now,
            ':updated_at' => $now,
            ':id' => $id
        ]);

        if ($stmt->rowCount() === 0) {
            if ($wantsJson) {
                return new JsonResponse(['success' => false, 'error' => 'Product not found.'], 404);
            }

            return new RedirectResponse('/products');
        }

        if ($wantsJson) {
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $currentStock = (int)$stmt->fetchColumn();

            return new JsonResponse([
                'success' => true,
                'message' => 'Stock updated successfully.',
                'current_stock' => $currentStock,
            ]);
        }

        return new RedirectResponse('/products');
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

