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
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');

        $config = require __DIR__ . '/../../../../config/autoload/database.global.php';
        $pdo = Database::getConnection($config['database']);

        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return new RedirectResponse('/orders');
    }
}