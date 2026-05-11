<?php

namespace App\Handler\Product;

use App\Database\Database;
use App\Helper\DateTimeHelper;
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
        $pdo = $this->db->getPdo();
        $this->ensureProductTimestampColumns($pdo);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $name = trim($data['name'] ?? '');
            $price = (float)($data['price'] ?? 0);
            $stock = (int)($data['stock'] ?? 0);

            if (!$name || $price <= 0) {
        return new HtmlResponse($this->template->render('layout', [
                    'content' => $this->template->render('products/create', [
                        'error' => 'Invalid data'
                    ])
                ]));
            }

            $stmt = $pdo->prepare("
                INSERT INTO products (name, price, stock, is_active, created_at, updated_at)
                VALUES (:name, :price, :stock, 1, :created_at, :updated_at)
            ");

            $now = DateTimeHelper::nowForStorage();
            $stmt->execute([
                'name' => $name,
                'price' => $price,
                'stock' => $stock,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return new RedirectResponse('/products');
        }

        return new HtmlResponse($this->template->render('layout', [
            'content' => $this->template->render('products/create')
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
