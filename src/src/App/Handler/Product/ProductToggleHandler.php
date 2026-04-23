<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Database\Database;
use App\Helper\Session;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ProductToggleHandler implements RequestHandlerInterface
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

        $pdo = $this->db->getPdo();

        $stmt = $pdo->prepare("
            UPDATE products 
            SET is_active = NOT is_active, updated_at = NOW()
            WHERE id = :id
        ");

        $stmt->execute([':id' => $id]);

        return new RedirectResponse('/products');
    }
}

