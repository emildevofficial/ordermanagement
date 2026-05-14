<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Permission;
use App\Helper\Session;
use Laminas\Diactoros\Response\RedirectResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_map;
use function filter_var;
use function is_numeric;
use function sprintf;
use function trim;

use const FILTER_VALIDATE_INT;

class ProductBulkEditHandler implements RequestHandlerInterface
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

        $data     = $request->getParsedBody();
        $selected = array_map('intval', (array) ($data['selected'] ?? []));
        $products = (array) ($data['products'] ?? []);

        if ($selected === []) {
            Session::flash('inventory_error', 'Select at least one product before applying bulk changes.');
            return new RedirectResponse('/import-export');
        }

        $pdo = $this->db->getPdo();
        $this->ensureProductColumns($pdo);

        $updated = 0;
        $skipped = 0;
        $stmt    = $pdo->prepare("
            UPDATE products
            SET price = :price,
                stock = :stock,
                is_active = :is_active,
                updated_at = :updated_at
            WHERE id = :id
        ");

        foreach ($selected as $id) {
            $row      = (array) ($products[$id] ?? []);
            $priceRaw = trim((string) ($row['price'] ?? ''));
            $stockRaw = trim((string) ($row['stock'] ?? ''));

            $hasInvalidValues = $id <= 0
                || $priceRaw === ''
                || $stockRaw === ''
                || ! is_numeric($priceRaw)
                || filter_var($stockRaw, FILTER_VALIDATE_INT) === false;

            if ($hasInvalidValues) {
                $skipped++;
                continue;
            }

            $stmt->execute([
                ':price'      => (float) $priceRaw,
                ':stock'      => (int) $stockRaw,
                ':is_active'  => isset($row['is_active']) ? 1 : 0,
                ':updated_at' => DateTimeHelper::nowForStorage(),
                ':id'         => $id,
            ]);
            $updated++;
        }

        if ($updated > 0) {
            Session::flash(
                'inventory_success',
                sprintf('Bulk edit saved: %d updated, %d skipped.', $updated, $skipped)
            );
        } else {
            Session::flash('inventory_error', sprintf('No products were updated. %d skipped.', $skipped));
        }

        return new RedirectResponse('/import-export');
    }

    private function ensureProductColumns(PDO $pdo): void
    {
        $columns = [
            'updated_at' => "ALTER TABLE products ADD COLUMN updated_at DATETIME NULL",
            'is_active'  => "ALTER TABLE products ADD COLUMN is_active TINYINT(1) DEFAULT 1",
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
