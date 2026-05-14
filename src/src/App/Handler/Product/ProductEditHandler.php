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
        $this->ensureProductColumns($pdo);

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
                p.image_url,
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
            $imageUrl = trim((string)($data['image_url'] ?? ''));

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
                    image_url = :image_url,
                    updated_at = :updated_at
                WHERE id = :id
            ");
            $update->execute([
                ':name' => $name,
                ':price' => $price,
                ':image_url' => $imageUrl !== '' ? $imageUrl : null,
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

    private function ensureProductColumns(\PDO $pdo): void
    {
        $columns = [
            'updated_at' => "ALTER TABLE products ADD COLUMN updated_at DATETIME NULL",
            'image_url' => "ALTER TABLE products ADD COLUMN image_url VARCHAR(500) NULL",
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
