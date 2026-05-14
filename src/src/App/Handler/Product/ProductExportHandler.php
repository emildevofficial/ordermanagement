<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Database\Database;
use App\Helper\Permission;
use App\Helper\Session;
use Laminas\Diactoros\Response\TextResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function fclose;
use function fopen;
use function fputcsv;
use function rewind;
use function stream_get_contents;

class ProductExportHandler implements RequestHandlerInterface
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
            SELECT id, name, description, price, stock, image_url, is_active, created_at, updated_at
            FROM products
            ORDER BY id ASC
        ");
        $stmt->execute();

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, [
            'id',
            'name',
            'description',
            'price',
            'stock',
            'image_url',
            'is_active',
            'created_at',
            'updated_at',
        ]);

        while ($row = $stmt->fetch()) {
            fputcsv($handle, [
                $row['id'],
                $row['name'],
                $row['description'],
                $row['price'],
                $row['stock'],
                $row['image_url'],
                $row['is_active'],
                $row['created_at'],
                $row['updated_at'],
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return new TextResponse((string) $csv, 200, [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="products.csv"',
        ]);
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
