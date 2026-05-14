<?php

namespace App\Handler\Product;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Permission;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ProductCreateHandler implements RequestHandlerInterface
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
        Session::start();

        $deny = Permission::requireRole('admin');
        if ($deny !== null) {
            return $deny;
        }

        $pdo = $this->db->getPdo();
        $this->ensureProductColumns($pdo);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $name = trim($data['name'] ?? '');
            $price = (float)($data['price'] ?? 0);
            $stock = (int)($data['stock'] ?? 0);
            $imageUrl = trim((string)($data['image_url'] ?? ''));

            if (!$name || $price <= 0) {
        return new HtmlResponse($this->template->render('layout', [
                    'content' => $this->template->render('products/create', [
                        'error' => 'Invalid data'
                    ])
                ]));
            }

            $stmt = $pdo->prepare("
                INSERT INTO products (name, price, stock, image_url, is_active, created_at, updated_at)
                VALUES (:name, :price, :stock, :image_url, 1, :created_at, :updated_at)
            ");

            $now = DateTimeHelper::nowForStorage();
            $stmt->execute([
                'name' => $name,
                'price' => $price,
                'stock' => $stock,
                'image_url' => $imageUrl !== '' ? $imageUrl : null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return new RedirectResponse('/products');
        }

        return new HtmlResponse($this->template->render('layout', [
            'content' => $this->template->render('products/create')
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
