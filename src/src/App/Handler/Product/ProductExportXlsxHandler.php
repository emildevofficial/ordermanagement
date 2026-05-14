<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Database\Database;
use App\Helper\Permission;
use App\Helper\Session;
use Laminas\Diactoros\Response;
use PDO;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function count;
use function file_get_contents;
use function range;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

class ProductExportXlsxHandler implements RequestHandlerInterface
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

        $rows = [[
            'id',
            'name',
            'description',
            'price',
            'stock',
            'image_url',
            'is_active',
            'created_at',
            'updated_at',
        ]];

        while ($row = $stmt->fetch()) {
            $rows[] = [
                $row['id'],
                $row['name'],
                $row['description'],
                $row['price'],
                $row['stock'],
                $row['image_url'],
                $row['is_active'],
                $row['created_at'],
                $row['updated_at'],
            ];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Products');
        $sheet->fromArray($rows);
        $sheet->freezePane('A2');

        $lastRow = count($rows);
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A1:I' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CBD5E1'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getRowDimension(1)->setRowHeight(24);

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'products_export_');
        $writer->save($tempFile);

        $xlsx = (string) file_get_contents($tempFile);
        unlink($tempFile);
        $spreadsheet->disconnectWorksheets();

        $response = new Response('php://memory', 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="products.xlsx"',
        ]);

        $response->getBody()->write($xlsx);
        $response->getBody()->rewind();

        return $response;
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
