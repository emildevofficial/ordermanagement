<?php

declare(strict_types=1);

namespace App\Handler\Order;

use App\Database\Database;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OrderCreateHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        if (!Session::get('user_id')) {
            return new RedirectResponse('/login');
        }

        $pdo = $this->db->getPdo();

        // 🔥 POST → krijo order
        if ($request->getMethod() === 'POST') {

            $data = $request->getParsedBody();

            $customerId = (int) ($data['customer_id'] ?? 0);
            $status     = $data['status'] ?? 'pending';
            $total      = (float) ($data['total'] ?? 0);

            if ($customerId <= 0 || $total <= 0) {
                return new HtmlResponse('Invalid data');
            }

            $userId = Session::get('user_id');

$stmt = $pdo->prepare("
    INSERT INTO orders (user_id, customer_id, status, total, created_at)
    VALUES (:user_id, :customer_id, :status, :total, NOW())
");

$stmt->execute([
    'user_id'     => $userId,
    'customer_id' => $customerId,
    'status'      => $status,
    'total'       => $total
]);

           

            return new RedirectResponse('/orders');
        }

        // 🔥 GET → merr customers për dropdown
        $customers = $pdo
            ->query("SELECT id, name, email FROM customers ORDER BY name ASC")
            ->fetchAll();

        $content = Template::render('order/create', [
            'customers' => $customers
        ]);

        return new HtmlResponse(
            Template::render('layout', ['content' => $content])
        );
    }
}