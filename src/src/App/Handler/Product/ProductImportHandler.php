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
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function array_map;
use function fclose;
use function fgetcsv;
use function filter_var;
use function fopen;
use function fwrite;
use function in_array;
use function is_numeric;
use function rewind;
use function sprintf;
use function strtolower;
use function trim;

use const FILTER_VALIDATE_INT;
use const UPLOAD_ERR_OK;

class ProductImportHandler implements RequestHandlerInterface
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

        $upload = $request->getUploadedFiles()['products_csv'] ?? null;
        if (! $upload instanceof UploadedFileInterface || $upload->getError() !== UPLOAD_ERR_OK) {
            Session::flash('inventory_error', 'Please choose a valid CSV file to import.');
            return new RedirectResponse('/import-export');
        }

        $pdo = $this->db->getPdo();
        $this->ensureProductColumns($pdo);

        $csv    = $upload->getStream()->getContents();
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $csv);
        rewind($handle);

        $headers = fgetcsv($handle);
        if ($headers === false) {
            fclose($handle);
            Session::flash('inventory_error', 'The CSV file is empty.');
            return new RedirectResponse('/import-export');
        }

        $headers = array_map(static fn ($header) => strtolower(trim((string) $header)), $headers);
        $created = 0;
        $updated = 0;
        $skipped = 0;

        $pdo->beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $data = $this->combineRow($headers, $row);

                $name     = trim((string) ($data['name'] ?? ''));
                $priceRaw = trim((string) ($data['price'] ?? ''));
                $stockRaw = trim((string) ($data['stock'] ?? ''));

                $hasInvalidRequiredValues = $name === ''
                    || $priceRaw === ''
                    || $stockRaw === ''
                    || ! is_numeric($priceRaw)
                    || filter_var($stockRaw, FILTER_VALIDATE_INT) === false;

                if ($hasInvalidRequiredValues) {
                    $skipped++;
                    continue;
                }

                $id          = filter_var(trim((string) ($data['id'] ?? '')), FILTER_VALIDATE_INT);
                $description = trim((string) ($data['description'] ?? ''));
                $imageUrl    = trim((string) ($data['image_url'] ?? ''));
                $isActive    = $this->normalizeActive($data['is_active'] ?? null);
                $now         = DateTimeHelper::nowForStorage();

                $existingId = null;
                if ($id !== false && $id > 0) {
                    $find = $pdo->prepare("SELECT id FROM products WHERE id = :id LIMIT 1");
                    $find->execute([':id' => $id]);
                    $existingId = $find->fetchColumn() ?: null;
                }

                if ($existingId === null) {
                    $find = $pdo->prepare("SELECT id FROM products WHERE name = :name LIMIT 1");
                    $find->execute([':name' => $name]);
                    $existingId = $find->fetchColumn() ?: null;
                }

                if ($existingId !== null) {
                    $update = $pdo->prepare("
                        UPDATE products
                        SET name = :name,
                            description = :description,
                            price = :price,
                            stock = :stock,
                            image_url = :image_url,
                            is_active = :is_active,
                            updated_at = :updated_at
                        WHERE id = :id
                    ");
                    $update->execute([
                        ':name'        => $name,
                        ':description' => $description !== '' ? $description : null,
                        ':price'       => (float) $priceRaw,
                        ':stock'       => (int) $stockRaw,
                        ':image_url'   => $imageUrl !== '' ? $imageUrl : null,
                        ':is_active'   => $isActive,
                        ':updated_at'  => $now,
                        ':id'          => (int) $existingId,
                    ]);
                    $updated++;
                    continue;
                }

                $insert = $pdo->prepare("
                    INSERT INTO products (name, description, price, stock, image_url, is_active, created_at, updated_at)
                    VALUES (:name, :description, :price, :stock, :image_url, :is_active, :created_at, :updated_at)
                ");
                $insert->execute([
                    ':name'        => $name,
                    ':description' => $description !== '' ? $description : null,
                    ':price'       => (float) $priceRaw,
                    ':stock'       => (int) $stockRaw,
                    ':image_url'   => $imageUrl !== '' ? $imageUrl : null,
                    ':is_active'   => $isActive,
                    ':created_at'  => $now,
                    ':updated_at'  => $now,
                ]);
                $created++;
            }

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            fclose($handle);
            Session::flash('inventory_error', 'Import failed. No product changes were saved.');
            return new RedirectResponse('/import-export');
        }

        fclose($handle);

        Session::flash(
            'inventory_success',
            sprintf('Import complete: %d created, %d updated, %d skipped.', $created, $updated, $skipped)
        );

        return new RedirectResponse('/import-export');
    }

    private function combineRow(array $headers, array $row): array
    {
        $data = [];
        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }
            $data[$header] = $row[$index] ?? '';
        }

        return $data;
    }

    private function normalizeActive(mixed $value): int
    {
        $value = strtolower(trim((string) $value));
        if ($value === '') {
            return 1;
        }

        return in_array($value, ['1', 'true', 'yes', 'active', 'on'], true) ? 1 : 0;
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
