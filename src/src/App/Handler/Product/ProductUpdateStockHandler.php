<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Database\Database;
use App\Helper\Session;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ProductUpdateStockHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $id = (int)$request->getAttribute('id');
        $data = $request->getParsedBody();

        $stock = (int)($data['stock'] ?? 0);

        if ($stock < 0) {
            return new RedirectResponse('/products');
        }

        $pdo = $this->db->getPdo();

        $stmt = $pdo->prepare("
            UPDATE products 
            SET stock = :stock, updated_at = NOW() 
            WHERE id = :id
        ");

        $stmt->execute([
            ':stock' => $stock,
            ':id' => $id
        ]);

        return new RedirectResponse('/products');
    }
}

