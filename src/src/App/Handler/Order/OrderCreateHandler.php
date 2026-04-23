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
            $productId = (int) ($data['product_id'] ?? 0);
            $quantity = (int) ($data['quantity'] ?? 0);
            $status = $data['status'] ?? 'pending';

            if ($customerId <= 0 || $productId <= 0 || $quantity <= 0) {
                $content = Template::render('order/create', [
                    'customers' => $customers,
                    'products' => $products,
                    'error' => 'Please select customer, product and quantity.',
                ]);
                return new HtmlResponse(
                    Template::render('layout', ['content' => $content])
                );
            }

            // Check product stock
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id AND is_active = 1");
            $stmt->execute([':id' => $productId]);
            $productStock = $stmt->fetchColumn();

            if ($productStock === false || $productStock < $quantity) {
                $content = Template::render('order/create', [
                    'customers' => $customers,
                    'products' => $products,
                    'error' => 'Insufficient stock for selected product.',
                ]);
                return new HtmlResponse(
                    Template::render('layout', ['content' => $content])
                );
            }

            // Get product price
            $stmt = $pdo->prepare("SELECT price FROM products WHERE id = :id");
            $stmt->execute([':id' => $productId]);
            $productPrice = $stmt->fetchColumn();

            if ($productPrice === false) {
                $content = Template::render('order/create', [
                    'customers' => $customers,
                    'products' => $products,
                    'error' => 'Invalid product selected.',
                ]);
                return new HtmlResponse(
                    Template::render('layout', ['content' => $content])
                );
            }

            $userId = Session::get('user_id');
            $total = $quantity * (float)$productPrice;

            $pdo->beginTransaction();

            try {
                // Insert order
                $stmt = $pdo->prepare("
                    INSERT INTO orders (user_id, customer_id, status, total, created_at)
                    VALUES (:user_id, :customer_id, :status, :total, NOW())
                ");
                $stmt->execute([
                    ':user_id' => $userId,
                    ':customer_id' => $customerId,
                    ':status' => $status,
                    ':total' => $total
                ]);
                $orderId = $pdo->lastInsertId();

                // Insert order item
                $stmt = $pdo->prepare("
                    INSERT INTO order_items (order_id, product_id, quantity, price)
                    VALUES (:order_id, :product_id, :quantity, :price)
                ");
                $stmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $productId,
                    ':quantity' => $quantity,
                    ':price' => $productPrice
                ]);

                // Update product stock
                $stmt = $pdo->prepare("
                    UPDATE products 
                    SET stock = stock - :quantity, updated_at = NOW()
                    WHERE id = :product_id
                ");
                $stmt->execute([
                    ':quantity' => $quantity,
                    ':product_id' => $productId
                ]);

                $pdo->commit();

            } catch (\Exception $e) {
                $pdo->rollBack();
                $content = Template::render('order/create', [
                    'customers' => $customers,
                    'products' => $products,
                    'error' => 'Order creation failed. Please try again.',
                ]);
                return new HtmlResponse(
                    Template::render('layout', ['content' => $content])
                );
            }

            return new RedirectResponse('/orders');
        }

        // 🔥 GET → merr customers + products për dropdown
        $customers = $pdo
            ->query("SELECT id, name, email FROM customers ORDER BY name ASC")
            ->fetchAll();

        $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE is_active = 1 ORDER BY name ASC");
        $stmt->execute();
        $products = $stmt->fetchAll();

        $content = Template::render('order/create', [
            'customers' => $customers,
            'products' => $products
        ]);

        return new HtmlResponse(
            Template::render('layout', ['content' => $content])
        );
    }
}