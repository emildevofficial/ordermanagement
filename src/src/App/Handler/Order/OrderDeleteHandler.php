<?php

declare(strict_types=1);

namespace App\Handler\Order;

use App\Database\Database;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OrderDeleteHandler implements RequestHandlerInterface
{

private Database $db;

public function __construct(Database $db)
{
    $this->db = $db;
}
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');

       $pdo = $this->db->getPdo();
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return new RedirectResponse('/orders');
    }
}