<?php

declare(strict_types=1);

namespace App\Handler\Analytics;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Session;
use App\Helper\Template;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeZone;
use Laminas\Diactoros\Response\HtmlResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function round;

final class AnalyticsHandler implements RequestHandlerInterface
{
    private const LOW_STOCK_THRESHOLD = 2;

    public function __construct(private Database $db)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $pdo                   = $this->db->getPdo();
        $summary               = $this->fetchSummary($pdo);
        $dailyTrend            = $this->fetchDailyTrend($pdo);
        $monthlyTrend          = $this->fetchMonthlyTrend($pdo);
        $orderStatus           = $this->fetchOrderStatusBreakdown($pdo);
        $productAnalytics      = $this->fetchProductAnalytics($pdo);
        $customerAnalytics     = $this->fetchCustomerAnalytics($pdo);
        $returnAnalytics       = $this->fetchReturnAnalytics($pdo);
        $recentHighValueOrders = $this->fetchRecentHighValueOrders($pdo);

        $content = Template::render('analytics/index', [
            'summary'               => $summary,
            'dailyTrend'            => $dailyTrend,
            'monthlyTrend'          => $monthlyTrend,
            'orderStatus'           => $orderStatus,
            'productAnalytics'      => $productAnalytics,
            'customerAnalytics'     => $customerAnalytics,
            'returnAnalytics'       => $returnAnalytics,
            'recentHighValueOrders' => $recentHighValueOrders,
            'lowStockThreshold'     => self::LOW_STOCK_THRESHOLD,
        ]);

        return new HtmlResponse(
            Template::render('layout', [
                'content'      => $content,
                'currentRoute' => 'analytics',
                'userName'     => Session::get('user_name'),
                'role'         => Session::get('user_role'),
            ])
        );
    }

    /**
     * @return array<string, int|float>
     */
    private function fetchSummary(PDO $pdo): array
    {
        [$monthStart, $monthEnd] = DateTimeHelper::localPeriodStorageRange('month');

        $stmt = $pdo->prepare("
            SELECT
                COUNT(*) AS total_orders,
                COALESCE(SUM(CASE WHEN status IN ('completed', 'delivered') THEN total ELSE 0 END), 0) AS revenue,
                COALESCE(AVG(CASE WHEN status IN ('completed', 'delivered') THEN total END), 0) AS average_order_value,
                SUM(status IN ('completed', 'delivered')) AS completed_orders,
                SUM(status = 'cancelled') AS cancelled_orders
            FROM orders
        ");
        $stmt->execute();
        $orders = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $totalOrders  = (int) ($orders['total_orders'] ?? 0);
        $totalReturns = (int) $pdo->query("SELECT COUNT(*) FROM returns")->fetchColumn();

        $stmt = $pdo->prepare("
            SELECT
                COUNT(*) AS total_products,
                SUM(is_active = 1) AS active_products,
                SUM(stock = 0) AS out_of_stock_products,
                SUM(stock > 0 AND stock <= :threshold) AS low_stock_products
            FROM products
        ");
        $stmt->execute([':threshold' => self::LOW_STOCK_THRESHOLD]);
        $products = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM customers
            WHERE created_at >= :start AND created_at < :end
        ");
        $stmt->execute([':start' => $monthStart, ':end' => $monthEnd]);

        $completedOrders    = (int) ($orders['completed_orders'] ?? 0);
        $cancelledOrders    = (int) ($orders['cancelled_orders'] ?? 0);
        $lowStockProducts   = (int) ($products['low_stock_products'] ?? 0);
        $outOfStockProducts = (int) ($products['out_of_stock_products'] ?? 0);
        $completionRate     = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0.0;
        $cancellationRate   = $totalOrders > 0 ? round(($cancelledOrders / $totalOrders) * 100, 1) : 0.0;
        $inventoryRiskCount = $lowStockProducts + $outOfStockProducts;

        return [
            'revenue'                  => (float) ($orders['revenue'] ?? 0),
            'total_orders'             => $totalOrders,
            'average_order_value'      => (float) ($orders['average_order_value'] ?? 0),
            'total_returns'            => $totalReturns,
            'return_rate'              => $totalOrders > 0 ? round(($totalReturns / $totalOrders) * 100, 1) : 0.0,
            'completed_orders'         => $completedOrders,
            'cancelled_orders'         => $cancelledOrders,
            'completion_rate'          => $completionRate,
            'cancellation_rate'        => $cancellationRate,
            'active_products'          => (int) ($products['active_products'] ?? 0),
            'low_stock_products'       => $lowStockProducts,
            'out_of_stock_products'    => $outOfStockProducts,
            'inventory_risk_count'     => $inventoryRiskCount,
            'new_customers_this_month' => (int) $stmt->fetchColumn(),
        ];
    }

    /**
     * @return list<array{label: string, orders: int, revenue: float}>
     */
    private function fetchDailyTrend(PDO $pdo): array
    {
        $timezone        = new DateTimeZone(DateTimeHelper::APP_TIMEZONE);
        $storageTimezone = new DateTimeZone('UTC');
        $today           = new DateTimeImmutable('today', $timezone);
        $startLocal      = $today->modify('-6 days');
        $endLocal        = $today->modify('+1 day');
        $start           = $startLocal->setTimezone($storageTimezone)->format('Y-m-d H:i:s');
        $end             = $endLocal->setTimezone($storageTimezone)->format('Y-m-d H:i:s');

        $stmt = $pdo->prepare("
            SELECT
                DATE(created_at) AS day_key,
                COUNT(*) AS orders,
                COALESCE(SUM(CASE WHEN status IN ('completed', 'delivered') THEN total ELSE 0 END), 0) AS revenue
            FROM orders
            WHERE created_at >= :start AND created_at < :end
            GROUP BY DATE(created_at)
            ORDER BY day_key ASC
        ");
        $stmt->execute([':start' => $start, ':end' => $end]);

        $rowsByDay = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $rowsByDay[(string) $row['day_key']] = $row;
        }

        $trend  = [];
        $period = new DatePeriod($startLocal, new DateInterval('P1D'), $endLocal);
        foreach ($period as $day) {
            $key     = $day->setTimezone($storageTimezone)->format('Y-m-d');
            $row     = $rowsByDay[$key] ?? null;
            $trend[] = [
                'label'   => $day->format('D'),
                'orders'  => (int) ($row['orders'] ?? 0),
                'revenue' => (float) ($row['revenue'] ?? 0),
            ];
        }

        return $trend;
    }

    /**
     * @return list<array{label: string, revenue: float}>
     */
    private function fetchMonthlyTrend(PDO $pdo): array
    {
        $timezone        = new DateTimeZone(DateTimeHelper::APP_TIMEZONE);
        $storageTimezone = new DateTimeZone('UTC');
        $firstMonth      = (new DateTimeImmutable('first day of this month', $timezone))
            ->setTime(0, 0)
            ->modify('-5 months');
        $endLocal        = (new DateTimeImmutable('first day of next month', $timezone))->setTime(0, 0);
        $start           = $firstMonth->setTimezone($storageTimezone)->format('Y-m-d H:i:s');
        $end             = $endLocal->setTimezone($storageTimezone)->format('Y-m-d H:i:s');

        $stmt = $pdo->prepare("
            SELECT
                DATE_FORMAT(created_at, '%Y-%m') AS month_key,
                COALESCE(SUM(CASE WHEN status IN ('completed', 'delivered') THEN total ELSE 0 END), 0) AS revenue
            FROM orders
            WHERE created_at >= :start AND created_at < :end
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month_key ASC
        ");
        $stmt->execute([':start' => $start, ':end' => $end]);

        $rowsByMonth = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $rowsByMonth[(string) $row['month_key']] = $row;
        }

        $trend  = [];
        $period = new DatePeriod($firstMonth, new DateInterval('P1M'), $endLocal);
        foreach ($period as $month) {
            $key     = $month->format('Y-m');
            $row     = $rowsByMonth[$key] ?? null;
            $trend[] = [
                'label'   => $month->format('M'),
                'revenue' => (float) ($row['revenue'] ?? 0),
            ];
        }

        return $trend;
    }

    /**
     * @return array{rows: list<array{status: string, count: int, percentage: float}>, total: int}
     */
    private function fetchOrderStatusBreakdown(PDO $pdo): array
    {
        $rows = $pdo->query("
            SELECT status, COUNT(*) AS order_count
            FROM orders
            GROUP BY status
            ORDER BY order_count DESC, status ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $total = 0;
        foreach ($rows as $row) {
            $total += (int) $row['order_count'];
        }

        $statusRows = [];
        foreach ($rows as $row) {
            $count        = (int) $row['order_count'];
            $statusRows[] = [
                'status'     => (string) $row['status'],
                'count'      => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0.0,
            ];
        }

        return ['rows' => $statusRows, 'total' => $total];
    }

    /**
     * @return array<string, list<array<string, int|float|string|null>>>
     */
    private function fetchProductAnalytics(PDO $pdo): array
    {
        $topSelling = $pdo->query("
            SELECT p.id, p.name, COALESCE(SUM(oi.quantity), 0) AS quantity_sold
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            JOIN orders o ON o.id = oi.order_id
            WHERE o.status IN ('completed', 'delivered')
            GROUP BY p.id, p.name
            ORDER BY quantity_sold DESC, p.name ASC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        $topRevenue = $pdo->query("
            SELECT p.id, p.name, COALESCE(SUM(oi.quantity * oi.price), 0) AS revenue
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            JOIN orders o ON o.id = oi.order_id
            WHERE o.status IN ('completed', 'delivered')
            GROUP BY p.id, p.name
            ORDER BY revenue DESC, p.name ASC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        $slowMoving = $pdo->query("
            SELECT p.id,
                   p.name,
                   p.stock,
                   COALESCE(SUM(CASE WHEN o.id IS NOT NULL THEN oi.quantity ELSE 0 END), 0) AS quantity_sold
            FROM products p
            LEFT JOIN order_items oi ON oi.product_id = p.id
            LEFT JOIN orders o ON o.id = oi.order_id AND o.status IN ('completed', 'delivered')
            WHERE p.is_active = 1
            GROUP BY p.id, p.name, p.stock
            ORDER BY quantity_sold ASC, p.stock DESC, p.name ASC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("
            SELECT id, name, stock, is_active
            FROM products
            WHERE stock <= :threshold OR stock = 0
            ORDER BY stock ASC, name ASC
            LIMIT 8
        ");
        $stmt->execute([':threshold' => self::LOW_STOCK_THRESHOLD]);

        return [
            'topSelling'    => $topSelling,
            'topRevenue'    => $topRevenue,
            'slowMoving'    => $slowMoving,
            'inventoryRisk' => $stmt->fetchAll(PDO::FETCH_ASSOC),
        ];
    }

    /**
     * @return array<string, int|list<array<string, int|float|string|null>>>
     */
    private function fetchCustomerAnalytics(PDO $pdo): array
    {
        [$monthStart, $monthEnd] = DateTimeHelper::localPeriodStorageRange('month');

        $topSpenders = $pdo->query("
            SELECT COALESCE(c.name, u.name, 'Unknown Customer') AS customer_name,
                   COALESCE(SUM(oi.quantity * oi.price), 0) AS total_spent
            FROM orders o
            JOIN order_items oi ON oi.order_id = o.id
            LEFT JOIN customers c ON c.id = o.customer_id
            LEFT JOIN users u ON u.id = o.user_id
            WHERE o.status IN ('completed', 'delivered')
            GROUP BY customer_name
            ORDER BY total_spent DESC, customer_name ASC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        $topOrderCounts = $pdo->query("
            SELECT COALESCE(c.name, u.name, 'Unknown Customer') AS customer_name,
                   COUNT(o.id) AS order_count
            FROM orders o
            LEFT JOIN customers c ON c.id = o.customer_id
            LEFT JOIN users u ON u.id = o.user_id
            GROUP BY customer_name
            ORDER BY order_count DESC, customer_name ASC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM customers
            WHERE created_at >= :start AND created_at < :end
        ");
        $stmt->execute([':start' => $monthStart, ':end' => $monthEnd]);
        $newCustomersThisMonth = (int) $stmt->fetchColumn();

        $customersWithReturns = $pdo->query("
            SELECT COALESCE(c.name, u.name, 'Unknown Customer') AS customer_name,
                   COUNT(r.id) AS return_count
            FROM returns r
            LEFT JOIN customers c ON c.id = r.customer_id
            LEFT JOIN users u ON u.id = r.user_id
            GROUP BY customer_name
            ORDER BY return_count DESC, customer_name ASC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        return [
            'topSpenders'           => $topSpenders,
            'topOrderCounts'        => $topOrderCounts,
            'newCustomersThisMonth' => $newCustomersThisMonth,
            'customersWithReturns'  => $customersWithReturns,
        ];
    }

    /**
     * @return array{
     *     statusRows: list<array{status: string, count: int, percentage: float}>,
     *     mostReturnedProducts: list<array<string, int|string|null>>,
     *     pendingReturns: list<array<string, int|string|null>>,
     *     total: int
     * }
     */
    private function fetchReturnAnalytics(PDO $pdo): array
    {
        $rows = $pdo->query("
            SELECT status, COUNT(*) AS return_count
            FROM returns
            GROUP BY status
            ORDER BY return_count DESC, status ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $total = 0;
        foreach ($rows as $row) {
            $total += (int) $row['return_count'];
        }

        $statusRows = [];
        foreach ($rows as $row) {
            $count        = (int) $row['return_count'];
            $statusRows[] = [
                'status'     => (string) $row['status'],
                'count'      => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0.0,
            ];
        }

        $mostReturnedProducts = $pdo->query("
            SELECT p.id, p.name, COUNT(DISTINCT r.id) AS return_count
            FROM returns r
            JOIN order_items oi ON oi.order_id = r.order_id
            JOIN products p ON p.id = oi.product_id
            GROUP BY p.id, p.name
            ORDER BY return_count DESC, p.name ASC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        $pendingReturns = $pdo->query("
            SELECT r.id, r.created_at, COALESCE(c.name, u.name, 'Unknown Customer') AS customer_name
            FROM returns r
            LEFT JOIN customers c ON c.id = r.customer_id
            LEFT JOIN users u ON u.id = r.user_id
            WHERE r.status = 'pending'
            ORDER BY r.created_at ASC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        return [
            'statusRows'           => $statusRows,
            'mostReturnedProducts' => $mostReturnedProducts,
            'pendingReturns'       => $pendingReturns,
            'total'                => $total,
        ];
    }

    /**
     * @return list<array<string, int|float|string|null>>
     */
    private function fetchRecentHighValueOrders(PDO $pdo): array
    {
        return $pdo->query("
            SELECT o.id, o.total, o.status, o.created_at, COALESCE(c.name, u.name, 'Unknown Customer') AS customer_name
            FROM orders o
            LEFT JOIN customers c ON c.id = o.customer_id
            LEFT JOIN users u ON u.id = o.user_id
            ORDER BY o.total DESC, o.created_at DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
}
