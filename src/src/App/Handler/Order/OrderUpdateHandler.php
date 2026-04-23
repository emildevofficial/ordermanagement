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

class OrderUpdateHandler implements RequestHandlerInterface
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
        $pdo = $this->db->getPdo();

        $role = Session::get('user_role') ?? 'user';
        $userId = (int) (Session::get('user_id') ?? 0);
        $userName = Session::get('user_name') ?? 'User';
        $currentRoute = 'orders';

        // GET → show edit page
        if ($request->getMethod() === 'GET') {
            if ($role === 'admin') {
                $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
                $stmt->execute([':id' => $id]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id AND user_id = :user_id LIMIT 1");
                $stmt->execute([
                    ':id' => $id,
                    ':user_id' => $userId,
                ]);
            }

            $order = $stmt->fetch();

            if (!$order) {
                return new RedirectResponse('/orders');
            }

            // Load products and order_items for edit form
            $stmt = $pdo->prepare("SELECT id, name FROM products WHERE is_active = 1 ORDER BY name ASC");
            $stmt->execute();
            $products = $stmt->fetchAll();
            
            $stmt = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = :id");
            $stmt->execute([':id' => $id]);
            $orderItems = $stmt->fetch();
            
            $currentProductId = $orderItems ? $orderItems['product_id'] : 0;
            $currentQuantity = $orderItems ? $orderItems['quantity'] : 0;

            $content = Template::render('order/edit', [
                'order' => $order,
                'products' => $products,
                'currentProductId' => $currentProductId,
                'currentQuantity' => $currentQuantity,
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

        // POST → update with stock-safe order_items
        $data = $request->getParsedBody();
        $status = (string) ($data['status'] ?? 'pending');
        $notes = isset($data['notes']) ? trim((string)$data['notes']) : null;
        $notes = ($notes === '') ? null : $notes;
        $productId = (int) ($data['product_id'] ?? 0);
        $quantity = (int) ($data['quantity'] ?? 0);

        $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'pending';
        }

        if ($productId <= 0 || $quantity <= 0) {
            return new RedirectResponse('/orders/' . $id . '/edit?error=invalid_product');
        }

        $pdo->beginTransaction();

        try {
            // 1. Get old order_items
            $stmt = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = :id");
            $stmt->execute([':id' => $id]);
            $oldItems = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            if (!empty($oldItems)) {
                $oldProductId = (int) key($oldItems);
                $oldQuantity = (int) $oldItems[$oldProductId];
                
                // Restore old stock
                $stmt = $pdo->prepare("UPDATE products SET stock = stock + :qty WHERE id = :pid");
                $stmt->execute([':qty' => $oldQuantity, ':pid' => $oldProductId]);
            }

            // 2. Validate new stock
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id");
            $stmt->execute([':id' => $productId]);
            $stock = $stmt->fetchColumn();

            if ($quantity > (int)$stock) {
                $pdo->rollBack();
                return new RedirectResponse('/orders/' . $id . '/edit?error=no_stock');
            }

            // 3. Update order_items
            $stmt = $pdo->prepare("
                UPDATE order_items 
                SET product_id = :pid, quantity = :qty 
                WHERE order_id = :oid
            ");
            $stmt->execute([
                ':pid' => $productId,
                ':qty' => $quantity,
                ':oid' => $id
            ]);

            // 4. Deduct new stock
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - :qty WHERE id = :pid");
            $stmt->execute([':qty' => $quantity, ':pid' => $productId]);

            // 5. Update order status/notes
            if ($role === 'admin') {
                $stmt = $pdo->prepare("
                    UPDATE orders 
                    SET status = :status, notes = :notes, total = :total, updated_at = NOW() 
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':status' => $status,
                    ':notes' => $notes,
                    ':total' => ($quantity * 10), // simple price assumption
                    ':id' => $id,
                ]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE orders 
                    SET status = :status, notes = :notes, total = :total, updated_at = NOW() 
                    WHERE id = :id AND user_id = :user_id
                ");
                $stmt->execute([
                    ':status' => $status,
                    ':notes' => $notes,
                    ':total' => ($quantity * 10),
                    ':id' => $id,
                    ':user_id' => $userId,
                ]);
            }

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            return new RedirectResponse('/orders/' . $id . '/edit?error=update_failed');
        }

        return new RedirectResponse('/orders/' . $id);
    }

}