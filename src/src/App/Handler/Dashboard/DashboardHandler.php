<?php

declare(strict_types=1);

namespace App\Handler\Dashboard;

use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DashboardHandler implements RequestHandlerInterface
{
    private \App\Database\Database $db;
    public function __construct(\App\Database\Database $db)
{
    $this->db = $db;
}
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $userId = Session::get('user_id');
        $userName = Session::get('user_name');
        $role = Session::get('user_role');
        $currentRoute = 'dashboard';

        $pdo = $this->db->getPdo();

        // Total Orders
        $stmt = $pdo->query('SELECT COUNT(*) FROM orders');
        $totalOrders = $stmt->fetchColumn();

        // Orders Today
        $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()");
        $ordersToday = $stmt->fetchColumn();

        // Orders This Week
        $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
        $ordersThisWeek = $stmt->fetchColumn();

        // Total Revenue (exclude cancelled)
        $stmt = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'cancelled'");
        $revenue = $stmt->fetchColumn();

        // Pending Orders
        $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
        $pendingOrders = $stmt->fetchColumn();

        // Total Returns
        $stmt = $pdo->query("SELECT COUNT(*) FROM returns");
        $totalReturns = $stmt->fetchColumn();

        // Return Rate
        $returnRate = $totalOrders > 0 ? round(($totalReturns / $totalOrders) * 100, 1) : 0;

        // Recent Orders
        $stmt = $pdo->query("
            SELECT 
                o.id,
                o.total,
                o.status,
                o.created_at,
                c.name as customer_name
            FROM orders o
            LEFT JOIN customers c ON c.id = o.customer_id
            ORDER BY o.created_at DESC 
            LIMIT 10
        ");
        $recentOrders = $stmt->fetchAll();

        $content = Template::render('dashboard/index', [
            'totalOrders' => (int)$totalOrders,
            'ordersToday' => (int)$ordersToday,
            'ordersThisWeek' => (int)$ordersThisWeek,
            'revenue' => (float)$revenue,
            'pendingOrders' => (int)$pendingOrders,
            'totalReturns' => (int)$totalReturns,
            'returnRate' => $returnRate,
            'recentOrders' => $recentOrders,
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