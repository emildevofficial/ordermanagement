<?php

declare(strict_types=1);

namespace App\Handler\Return;

use App\Database\Database;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ReturnReviewHandler implements RequestHandlerInterface
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
        $currentRoute = 'returns';

        if ($id <= 0) {
            return new RedirectResponse('/returns');
        }

        $pdo = $this->db->getPdo();

        // Fetch return info
        $stmt = $pdo->prepare("
            SELECT 
                r.*,
                o.customer_id,
                c.name AS customer_name,
                o.total
            FROM returns r
            JOIN orders o ON r.order_id = o.id
            LEFT JOIN customers c ON o.customer_id = c.id
            WHERE r.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $return = $stmt->fetch();

        if (!$return || $return['status'] !== 'requested') {
            return new RedirectResponse('/returns');
        }

        // Fetch order_items for this return's order
        $stmt = $pdo->prepare("
            SELECT 
                oi.product_id,
                p.name as product_name,
                oi.quantity
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ");
        $stmt->execute([':order_id' => $return['order_id']]);
        $orderItems = $stmt->fetchAll();

        $content = Template::render('return/review', [
            'return' => $return,
            'orderItems' => $orderItems
        ]);

        return new HtmlResponse(
            Template::render('layout', [
                'content' => $content,
                'currentRoute' => $currentRoute,
            ])
        );
    }
}
