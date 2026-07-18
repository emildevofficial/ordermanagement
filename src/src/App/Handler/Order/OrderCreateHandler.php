<?php
 
declare(strict_types=1);
 
namespace App\Handler\Order;
 
use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Session;
use App\Helper\Permission;
use Laminas\Diactoros\Response\JsonResponse;
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
        $wantsJson = strtolower($request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest'
            || strpos(strtolower($request->getHeaderLine('Accept')), 'application/json') !== false;

        // Admins manage inventory and order status; purchases belong to customer accounts only.
        if (Permission::isAllowed('admin')) {
            if ($wantsJson) {
                return new JsonResponse(['success' => false, 'error' => 'Admins cannot create customer purchases.'], 403);
            }

            return new RedirectResponse('/orders');
        }

        $stmt = $pdo->prepare("SELECT id, name, price, stock, is_active FROM products WHERE is_active = 1 ORDER BY name ASC");
        $stmt->execute();
        $products = $stmt->fetchAll();

        if ($request->getMethod() === 'POST') {

            $data = $request->getParsedBody();

            // Only users may create orders through the product catalog. Admins are blocked above.
            $productId = (int) ($data['product_id'] ?? 0);
            $quantity  = (int) ($data['quantity'] ?? 0);

            if ($productId <= 0 || $quantity <= 0) {
                if ($wantsJson) {
                    return new JsonResponse(['success' => false, 'error' => 'Please enter a valid product and quantity.'], 400);
                }

                Session::flash('purchase_error', 'Please enter a valid product and quantity.');
                return new RedirectResponse('/shop');
            }

            $stmt = $pdo->prepare("SELECT price, stock, is_active FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();

            if (!$product || !$product['is_active']) {
                if ($wantsJson) {
                    return new JsonResponse(['success' => false, 'error' => 'Product not available.'], 400);
                }

                Session::flash('purchase_error', 'Product not available.');
                return new RedirectResponse('/shop');
            }

            if ($product['stock'] < $quantity) {
                if ($wantsJson) {
                    return new JsonResponse([
                        'success' => false,
                        'error' => 'Insufficient stock. Available stock: ' . (int)$product['stock'] . '.',
                    ], 400);
                }

                Session::flash('purchase_error', 'Insufficient stock. Available stock: ' . (int)$product['stock'] . '.');
                return new RedirectResponse('/shop');
            }

            $userId = Session::get('user_id');
            $userName = Session::get('user_name');
            $userEmail = Session::get('user_email');
            $total  = $quantity * $product['price'];
            $status = 'pending';

            // Welcome discount: apply only if enabled and user has zero completed orders
            $promo = $pdo->query(
                'SELECT new_user_discount_enabled, new_user_discount_percent FROM promotion_settings LIMIT 1'
            )->fetch();

            if (
                $promo
                && (int)$promo['new_user_discount_enabled'] === 1
                && (int)$promo['new_user_discount_percent'] > 0
            ) {
                $completedCount = $pdo->prepare(
                    "SELECT COUNT(*) FROM orders WHERE user_id = :uid AND status = 'completed'"
                );
                $completedCount->execute([':uid' => $userId]);

                if ((int)$completedCount->fetchColumn() === 0) {
                    $total = round($total * (1 - $promo['new_user_discount_percent'] / 100), 2);
                }
            }

            // Ensure we have a customer_id for proper data integrity
            // Link user to their corresponding customer record by user_id (primary) and email (fallback)
            $customerId = null;
            try {
                if (!empty($userId)) {
                    // First, try to find existing customer by user_id (most reliable)
                    $stmt = $pdo->prepare("SELECT id FROM customers WHERE user_id = :user_id LIMIT 1");
                    $stmt->execute([':user_id' => $userId]);
                    $row = $stmt->fetch();
                    if ($row && !empty($row['id'])) {
                        $customerId = (int)$row['id'];
                    }
                }

                // If no customer found by user_id, try email match (but verify it's not already linked to another user)
                if ($customerId === null && !empty($userEmail)) {
                    $stmt = $pdo->prepare("SELECT id, user_id FROM customers WHERE email = :email LIMIT 1");
                    $stmt->execute([':email' => $userEmail]);
                    $row = $stmt->fetch();
                    
                    // Only reuse if customer is not linked to another user
                    if ($row && !empty($row['id'])) {
                        if (empty($row['user_id'])) {
                            // Customer exists but not linked to any user - claim it
                            $customerId = (int)$row['id'];
                            $stmt = $pdo->prepare("UPDATE customers SET user_id = :user_id WHERE id = :id");
                            $stmt->execute([':user_id' => $userId, ':id' => $customerId]);
                        } elseif ((int)$row['user_id'] === $userId) {
                            // Customer already linked to this user - use it
                            $customerId = (int)$row['id'];
                        }
                        // If linked to different user, don't reuse - will create new below
                    }
                }

                // If no customer found and we have valid user info, create one linked to this user
                if ($customerId === null && !empty($userEmail)) {
                    $stmt = $pdo->prepare("INSERT INTO customers (user_id, name, email, created_at) VALUES (:user_id, :name, :email, :created_at)");
                    $stmt->execute([
                        ':user_id' => $userId,
                        ':name' => $userName ?? 'Guest',
                        ':email' => $userEmail,
                        ':created_at' => DateTimeHelper::nowForStorage(),
                    ]);
                    $customerId = (int)$pdo->lastInsertId();
                }
            } catch (\Throwable $e) {
                // If customer lookup/creation fails, proceed with null customer_id
                // Order will still be created with user_id for proper tracking
                $customerId = null;
            }

            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_id, total, status, created_at) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $customerId, $total, $status, DateTimeHelper::nowForStorage()]);
                $orderId = $pdo->lastInsertId();

                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$orderId, $productId, $quantity, $product['price']]);

                $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stmt->execute([$quantity, $productId]);

                $pdo->commit();
                if ($wantsJson) {
                    return new JsonResponse([
                        'success' => true,
                        'message' => 'Order created successfully.',
                        'order_total' => $total,
                        'remaining_stock' => (int)$product['stock'] - $quantity,
                    ]);
                }

                return new RedirectResponse('/my-orders');
            } catch (\Exception $e) {
                $pdo->rollBack();
                if ($wantsJson) {
                    return new JsonResponse(['success' => false, 'error' => 'Failed to create order.'], 500);
                }

                Session::flash('purchase_error', 'Failed to create order. Please try again.');
                return new RedirectResponse('/shop');
            }
        }
 
        // Creation must happen from the products catalog; redirect GET to products.
        return new RedirectResponse('/shop');
    }
}
