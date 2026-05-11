<?php

declare(strict_types=1);

namespace App\Handler\Return;

use App\Database\Database;
use App\Helper\Session;
use App\Helper\Permission;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ReturnListHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();
        $currentRoute = 'returns';

        $pdo = $this->db->getPdo();
        $userId = (int) Session::get('user_id');
        $isAdmin = Permission::isAllowed('admin');

        if ($isAdmin) {
            $stmt = $pdo->query("
            SELECT 
                r.*,
                o.id as order_id,
                o.created_at as order_created_at,
                CASE r.status 
                    WHEN 'pending' THEN 'Pending'
                    WHEN 'approved' THEN 'Approved'
                    WHEN 'rejected' THEN 'Rejected'
                END as status_label
            FROM returns r
            JOIN orders o ON r.order_id = o.id
            ORDER BY r.created_at DESC
        ");
            $returns = $stmt->fetchAll();
        } else {
            $stmt = $pdo->prepare("
            SELECT 
                r.*,
                o.id as order_id,
                o.total as total_amount_spent,
                (
                    SELECT GROUP_CONCAT(p.name ORDER BY p.name SEPARATOR ', ')
                    FROM order_items oi
                    JOIN products p ON p.id = oi.product_id
                    WHERE oi.order_id = o.id
                ) as product_name,
                CASE r.status 
                    WHEN 'pending' THEN 'Pending'
                    WHEN 'approved' THEN 'Approved'
                    WHEN 'rejected' THEN 'Rejected'
                END as status_label
            FROM returns r
            JOIN orders o ON r.order_id = o.id
            WHERE o.user_id = :user_id
            ORDER BY r.created_at DESC
        ");
            $stmt->execute([':user_id' => $userId]);
            $returns = $stmt->fetchAll();
        }

        $returnCount = count($returns);
        $returnCount = count($returns);

        $userName = Session::get('user_name');
        $role = Session::get('user_role');
        $currentRoute = 'returns';

        $content = Template::render('return/list', [
            'returns' => $returns,
            'returnCount' => $returnCount,
        ]);

        return new HtmlResponse(
            Template::render('layout', [
                'content' => $content,
                'currentRoute' => $currentRoute,
                'userName' => $userName,
                'role' => $role,
            ])
        );

    }
}
