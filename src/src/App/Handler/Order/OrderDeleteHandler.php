<?php

declare(strict_types=1);

namespace App\Handler\Order;

use App\Database\Database;
use App\Helper\Session;
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
        Session::start();

        $id = (int) $request->getAttribute('id');
        $role = Session::get('user_role') ?? 'user';
        $userId = (int) (Session::get('user_id') ?? 0);

        $pdo = $this->db->getPdo();

        if ($role === 'admin') {
            $stmt = $pdo->prepare("
                UPDATE orders
                SET status = :status, updated_at = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                ':status' => 'cancelled',
                ':id' => $id,
            ]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE orders
                SET status = :status, updated_at = NOW()
                WHERE id = :id AND user_id = :user_id
            ");
            $stmt->execute([
                ':status' => 'cancelled',
                ':id' => $id,
                ':user_id' => $userId,
            ]);
        }

        return new RedirectResponse('/orders');
    }
}
