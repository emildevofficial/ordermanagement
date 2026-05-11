<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Permission;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ProductEditHandler implements RequestHandlerInterface
{
    private Database $db;
    private Template $template;

    public function __construct(Database $db, Template $template)
    {
        $this->db = $db;
        $this->template = $template;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // enforce admin-only
        $deny = Permission::requireRole('admin');
        if ($deny !== null) {
            return $deny;
        }

        $pdo = $this->db->getPdo();
        $this->ensureProductTimestampColumns($pdo);

        $id = (int) ($request->getAttribute('id') ?? 0);
        if ($id <= 0) {
            return new RedirectResponse('/products');
        }

        // load existing product
        $stmt = $pdo->prepare("
            SELECT 
                p.id, 
                p.name, 
                p.price, 
                p.is_active,
                p.created_at,
                p.updated_at
            FROM products p
            WHERE p.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();

        if (!$product) {
            return new RedirectResponse('/products?error=notfound');
        }

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $name = trim((string) ($data['name'] ?? ''));
            $price = (float) ($data['price'] ?? 0);

            if ($name === '' || $price <= 0) {
                $content = $this->template->render('products/edit', [
                    'product' => $product,
                    'error' => 'Invalid data',
                ]);

                return new HtmlResponse($this->template->render('layout', [
                    'content' => $content,
                    'currentRoute' => 'products',
                ]));
            }

            $update = $pdo->prepare("
                UPDATE products
                SET
                    name = :name,
                    price = :price,
                    updated_at = :updated_at
                WHERE id = :id
            ");
            $update->execute([
                ':name' => $name,
                ':price' => $price,
                ':updated_at' => DateTimeHelper::nowForStorage(),
                ':id' => $id,
            ]);

            return new RedirectResponse('/products');
        }

        $content = $this->template->render('products/edit', [
            'product' => $product,
        ]);

        return new HtmlResponse($this->template->render('layout', [
            'content' => $content,
            'currentRoute' => 'products',
        ]));
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
