<?php

declare(strict_types=1);

namespace App\Handler\Dashboard;

use App\Helper\DateTimeHelper;
use App\Helper\Permission;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use PDO;
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

        $userId      = (int) Session::get('user_id');
        $userName    = Session::get('user_name');
        $role        = Session::get('user_role');
        $currentRoute = 'dashboard';

        $pdo     = $this->db->getPdo();
        $isAdmin = Permission::isAllowed('admin');
        $lowStockThreshold = 2;
        [$todayStart, $todayEnd] = DateTimeHelper::localPeriodStorageRange('day');
        [$weekStart, $weekEnd] = DateTimeHelper::localPeriodStorageRange('week');
        [$monthStart, $monthEnd] = DateTimeHelper::localPeriodStorageRange('month');

        // ── 1. All order counters + revenue in ONE query ──────────────────
        if ($isAdmin) {
            $stmt = $pdo->prepare("
                SELECT
                    COUNT(*)                                                      AS total_orders,
                    SUM(created_at >= :today_start AND created_at < :today_end)   AS orders_today,
                    SUM(created_at >= :week_start AND created_at < :week_end)     AS orders_this_week,
                    SUM(created_at >= :month_start AND created_at < :month_end)   AS orders_this_month,
                    SUM(status = 'completed')                                     AS completed_orders,
                    SUM(status = 'pending')                                       AS pending_orders,
                    SUM(status = 'cancelled')                                     AS cancelled_orders,
                    COALESCE(SUM(CASE WHEN status='completed' THEN total END), 0) AS revenue
                FROM orders
            ");
            $stmt->execute([
                ':today_start' => $todayStart,
                ':today_end' => $todayEnd,
                ':week_start' => $weekStart,
                ':week_end' => $weekEnd,
                ':month_start' => $monthStart,
                ':month_end' => $monthEnd,
            ]);
            $row = $stmt->fetch();
        } else {
            $stmt = $pdo->prepare("
                SELECT
                    COUNT(*)                                                      AS total_orders,
                    SUM(created_at >= :today_start AND created_at < :today_end)   AS orders_today,
                    SUM(created_at >= :week_start AND created_at < :week_end)     AS orders_this_week,
                    SUM(created_at >= :month_start AND created_at < :month_end)   AS orders_this_month,
                    SUM(status = 'completed')                                     AS completed_orders,
                    SUM(status = 'pending')                                       AS pending_orders,
                    SUM(status = 'cancelled')                                     AS cancelled_orders,
                    COALESCE(SUM(CASE WHEN status='completed' THEN total END), 0) AS revenue
                FROM orders
                WHERE user_id = :uid
            ");
            $stmt->execute([
                ':today_start' => $todayStart,
                ':today_end' => $todayEnd,
                ':week_start' => $weekStart,
                ':week_end' => $weekEnd,
                ':month_start' => $monthStart,
                ':month_end' => $monthEnd,
                ':uid' => $userId,
            ]);
            $row = $stmt->fetch();
        }

        $totalOrders    = (int)$row['total_orders'];
        $ordersToday    = (int)$row['orders_today'];
        $ordersThisWeek = (int)$row['orders_this_week'];
        $ordersThisMonth = (int)$row['orders_this_month'];
        $completedOrders = (int)$row['completed_orders'];
        $pendingOrders  = (int)$row['pending_orders'];
        $cancelledOrders = (int)$row['cancelled_orders'];
        $revenue        = (float)$row['revenue'];

        // ── 2. Total returns ──────────────────────────────────────────────
        if ($isAdmin) {
            $totalReturns = (int)$pdo->query("SELECT COUNT(*) FROM returns")->fetchColumn();
        } else {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM returns r
                JOIN orders o ON r.order_id = o.id
                WHERE o.user_id = :uid
            ");
            $stmt->execute([':uid' => $userId]);
            $totalReturns = (int)$stmt->fetchColumn();
        }

        $returnRate = $totalOrders > 0 ? round(($totalReturns / $totalOrders) * 100, 1) : 0;

        $buyProducts = $pdo->query("
            SELECT id, name, price, stock
            FROM products
            WHERE is_active = 1
            ORDER BY name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // ── 3. Registered customers (admin only) ─────────────────────────
        $totalCustomers = $isAdmin
            ? (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn()
            : 0;

        // ── 4. Top purchasing / spending customers (admin only) ───────────
        $topPurchasingCustomers = [];
        $topSpendingCustomers   = [];

        if ($isAdmin) {
            $topPurchasingCustomers = $pdo->query("
                SELECT COALESCE(c.name, u.name, 'Unknown Customer') AS customer_name,
                       COUNT(o.id) AS order_count
                FROM orders o
                LEFT JOIN customers c ON c.id = o.customer_id
                LEFT JOIN users     u ON u.id = o.user_id
                GROUP BY customer_name
                ORDER BY order_count DESC, customer_name ASC
                LIMIT 3
            ")->fetchAll();

            $topSpendingCustomers = $pdo->query("
                SELECT COALESCE(c.name, u.name, 'Unknown Customer') AS customer_name,
                       COALESCE(SUM(oi.quantity * oi.price), 0) AS total_spent
                FROM orders o
                JOIN order_items oi ON oi.order_id = o.id
                LEFT JOIN customers c ON c.id = o.customer_id
                LEFT JOIN users     u ON u.id = o.user_id
                WHERE o.status IN ('completed', 'delivered')
                GROUP BY customer_name
                ORDER BY total_spent DESC, customer_name ASC
                LIMIT 3
            ")->fetchAll();
        }

        // ── 5. Low-stock products (admin only) ────────────────────────────
        $lowStockProducts = [];
        $productAnalytics = [
            'total_products' => 0,
            'in_stock_products' => 0,
            'out_of_stock_products' => 0,
            'low_stock_products' => 0,
        ];
        if ($isAdmin) {
            $stmt = $pdo->prepare("
                SELECT id, name, stock FROM products
                WHERE stock BETWEEN 0 AND :threshold
                ORDER BY stock ASC, name ASC
            ");
            $stmt->execute([':threshold' => $lowStockThreshold]);
            $lowStockProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("
                SELECT
                    COUNT(*) AS total_products,
                    SUM(stock > 0) AS in_stock_products,
                    SUM(stock = 0) AS out_of_stock_products,
                    SUM(stock > 0 AND stock <= :threshold) AS low_stock_products
                FROM products
            ");
            $stmt->execute([':threshold' => $lowStockThreshold]);
            $productAnalyticsRow = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
            $productAnalytics = [
                'total_products' => (int)($productAnalyticsRow['total_products'] ?? 0),
                'in_stock_products' => (int)($productAnalyticsRow['in_stock_products'] ?? 0),
                'out_of_stock_products' => (int)($productAnalyticsRow['out_of_stock_products'] ?? 0),
                'low_stock_products' => (int)($productAnalyticsRow['low_stock_products'] ?? 0),
            ];
        }

        // ── 6. Recent orders ──────────────────────────────────────────────
        if ($isAdmin) {
            $recentOrders = $pdo->query("
                SELECT o.id, o.total, o.status, o.created_at, c.name AS customer_name
                FROM orders o
                LEFT JOIN customers c ON c.id = o.customer_id
                ORDER BY o.created_at DESC
                LIMIT 10
            ")->fetchAll();
        } else {
            $stmt = $pdo->prepare("
                SELECT o.id, o.total, o.status, o.created_at, c.name AS customer_name
                FROM orders o
                LEFT JOIN customers c ON c.id = o.customer_id
                WHERE o.user_id = :uid
                ORDER BY o.created_at DESC
                LIMIT 10
            ");
            $stmt->execute([':uid' => $userId]);
            $recentOrders = $stmt->fetchAll();
        }

        $data = [
            'totalOrders'            => $totalOrders,
            'ordersToday'            => $ordersToday,
            'ordersThisWeek'         => $ordersThisWeek,
            'ordersThisMonth'        => $ordersThisMonth,
            'revenue'                => $revenue,
            'totalCustomers'         => $totalCustomers,
            'completedOrders'        => $completedOrders,
            'pendingOrders'          => $pendingOrders,
            'cancelledOrders'        => $cancelledOrders,
            'totalReturns'           => $totalReturns,
            'returnRate'             => $returnRate,
            'buyProducts'            => $buyProducts,
            'topPurchasingCustomers' => $topPurchasingCustomers,
            'topSpendingCustomers'   => $topSpendingCustomers,
            'lowStockProducts'       => $lowStockProducts,
            'lowStockThreshold'      => $lowStockThreshold,
            'productAnalytics'       => $productAnalytics,
            'recentOrders'           => $recentOrders,
        ];

        $content = Template::render('dashboard/index', $data);

        return new HtmlResponse(
            Template::render('layout', [
                'content'      => $content,
                'currentRoute' => $currentRoute,
                'userName'     => $userName,
                'role'         => $role,
            ])
        );
    }
}
