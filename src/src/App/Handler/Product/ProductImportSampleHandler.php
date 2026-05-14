<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Helper\Permission;
use App\Helper\Session;
use Laminas\Diactoros\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function file_get_contents;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

class ProductImportSampleHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $deny = Permission::requireRole('admin');
        if ($deny !== null) {
            return $deny;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Product Import Sample');

        $rows = [
            ['id', 'name', 'description', 'price', 'stock', 'image_url', 'is_active'],
            ['', 'Wireless Mouse', 'Ergonomic wireless mouse', '19.99', '10', 'https://example.com/images/wireless-mouse.jpg', '1'],
            ['5', 'New Tech Product', 'Updated product description', '199.99', '8', 'https://example.com/images/product.jpg', '1'],
        ];

        $sheet->fromArray($rows);
        $sheet->freezePane('A2');

        $sheet->getStyle('A1:G1')->applyFromArray([
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

        $sheet->getStyle('A1:G3')->applyFromArray([
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

        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getRowDimension(1)->setRowHeight(24);

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'product_import_sample_');
        $writer->save($tempFile);

        $xlsx = (string) file_get_contents($tempFile);
        unlink($tempFile);
        $spreadsheet->disconnectWorksheets();

        $response = new Response('php://memory', 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="product_import_sample.xlsx"',
        ]);

        $response->getBody()->write($xlsx);
        $response->getBody()->rewind();

        return $response;
    }
}
