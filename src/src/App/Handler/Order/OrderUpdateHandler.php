<?php

declare(strict_types=1);

namespace App\Handler\Order;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Session;
use App\Helper\Template;
use App\Helper\Permission;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use PDO;
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

        if ($request->getMethod() === 'GET') {
            if (Permission::isAllowed('admin')) {
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

            $stmt = $pdo->prepare("SELECT id, name FROM products WHERE is_active = 1 ORDER BY name ASC");
            $stmt->execute();
            $products = $stmt->fetchAll();
            
            $stmt = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = :id");
            $stmt->execute([':id' => $id]);
            $orderItems = $stmt->fetch(PDO::FETCH_ASSOC);
            
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

        $data = $request->getParsedBody();
        $status = (string) ($data['status'] ?? 'pending');
        $notes = isset($data['notes']) ? trim((string)$data['notes']) : null;
        $notes = ($notes === '') ? null : $notes;
        $productId = (int) ($data['product_id'] ?? 0);
        $quantity = (int) ($data['quantity'] ?? 0);

        $allowedStatuses = ['pending', 'completed', 'cancelled'];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'pending';
        }

        if ($productId <= 0 || $quantity <= 0) {
            return new RedirectResponse('/orders/' . $id . '/edit?error=invalid_product');
        }

        $pdo->beginTransaction();

        try {
            if (Permission::isAllowed('admin')) {
                $stmt = $pdo->prepare("SELECT id FROM orders WHERE id = :id FOR UPDATE");
                $stmt->execute([':id' => $id]);
            } else {
                $stmt = $pdo->prepare("SELECT id FROM orders WHERE id = :id AND user_id = :user_id FOR UPDATE");
                $stmt->execute([
                    ':id' => $id,
                    ':user_id' => $userId,
                ]);
            }

            if (!$stmt->fetch()) {
                $pdo->rollBack();
                return new RedirectResponse('/orders');
            }

            $stmt = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = :id FOR UPDATE");
            $stmt->execute([':id' => $id]);
            $oldItems = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

            if (!empty($oldItems)) {
                $oldProductId = (int) key($oldItems);
                $oldQuantity = (int) $oldItems[$oldProductId];
                
                $stmt = $pdo->prepare("UPDATE products SET stock = stock + :qty WHERE id = :pid");
                $stmt->execute([':qty' => $oldQuantity, ':pid' => $oldProductId]);
            }

            $stmt = $pdo->prepare("SELECT price, stock FROM products WHERE id = :id FOR UPDATE");
            $stmt->execute([':id' => $productId]);
            $product = $stmt->fetch();

            if (!$product || $quantity > (int)$product['stock']) {
                $pdo->rollBack();
                return new RedirectResponse('/orders/' . $id . '/edit?error=no_stock');
            }

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

            $stmt = $pdo->prepare("UPDATE products SET stock = stock - :qty WHERE id = :pid");
            $stmt->execute([':qty' => $quantity, ':pid' => $productId]);

            $updatedAt = DateTimeHelper::nowForStorage();
            $total = round($quantity * (float)$product['price'], 2);

            if (Permission::isAllowed('admin')) {
                $stmt = $pdo->prepare("
                    UPDATE orders 
                    SET status = :status, notes = :notes, total = :total, updated_at = :updated_at 
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':status' => $status,
                    ':notes' => $notes,
                    ':total' => $total,
                    ':updated_at' => $updatedAt,
                    ':id' => $id,
                ]);
            } else {
                $stmt = $pdo->prepare("
                    UPDATE orders 
                    SET status = :status, notes = :notes, total = :total, updated_at = :updated_at 
                    WHERE id = :id AND user_id = :user_id
                ");
                $stmt->execute([
                    ':status' => $status,
                    ':notes' => $notes,
                    ':total' => $total,
                    ':updated_at' => $updatedAt,
                    ':id' => $id,
                    ':user_id' => $userId,
                ]);
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            return new RedirectResponse('/orders/' . $id . '/edit?error=update_failed');
        }

        return new RedirectResponse('/orders/' . $id);
    }
}

