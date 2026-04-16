<?php

declare(strict_types=1);

namespace App\Handler\Order;

use App\Database\Database;
use App\Helper\Session;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OrderUpdateHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $id = $request->getAttribute('id');

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $status = $data['status'] ?? 'pending';

            $config = require __DIR__ . '/../../../../config/autoload/database.global.php';
            $pdo = Database::getConnection($config['database']);

            $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
            $stmt->execute([
                ':status' => $status,
                ':id' => $id,
            ]);
        }

        return new RedirectResponse('/orders');
    }
}