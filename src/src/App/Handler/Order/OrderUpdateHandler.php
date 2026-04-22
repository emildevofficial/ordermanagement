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

        // GET → show edit page
        if ($request->getMethod() === 'GET') {

            $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $order = $stmt->fetch();

            $content = Template::render('order/edit', [
                'order' => $order
            ]);

            return new HtmlResponse(
                Template::render('layout', ['content' => $content])
            );
        }

        // POST → update
        $data = $request->getParsedBody();

        $stmt = $pdo->prepare("
            UPDATE orders 
            SET status = :status 
            WHERE id = :id
        ");

        $stmt->execute([
            'status' => $data['status'],
            'id' => $id
        ]);

        return new RedirectResponse('/orders');
    }
}