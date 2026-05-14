<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Database\Database;
use App\Helper\Permission;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class InventoryToolsHandler implements RequestHandlerInterface
{
    public function __construct(private Database $db)
    {
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

        $stmt = $pdo->prepare("
            SELECT id, name, price, stock, image_url, is_active
            FROM products
            ORDER BY name ASC
        ");
        $stmt->execute();

        $content = Template::render('inventory/tools', [
            'products' => $stmt->fetchAll(),
            'success'  => Session::getFlash('inventory_success'),
            'error'    => Session::getFlash('inventory_error'),
        ]);

        return new HtmlResponse(Template::render('layout', [
            'content'      => $content,
            'currentRoute' => 'import-export',
            'userName'     => Session::get('user_name'),
            'role'         => Session::get('user_role'),
        ]));
    }

    private function ensureProductColumns(PDO $pdo): void
    {
        $columns = [
            'description' => "ALTER TABLE products ADD COLUMN description TEXT NULL",
            'updated_at'  => "ALTER TABLE products ADD COLUMN updated_at DATETIME NULL",
            'image_url'   => "ALTER TABLE products ADD COLUMN image_url VARCHAR(500) NULL",
            'is_active'   => "ALTER TABLE products ADD COLUMN is_active TINYINT(1) DEFAULT 1",
        ];

        foreach ($columns as $column => $sql) {
            $stmt = $pdo->prepare("SHOW COLUMNS FROM products LIKE :column");
            $stmt->execute([':column' => $column]);
            if (! $stmt->fetch()) {
                $pdo->exec($sql);
            }
        }
    }
}
