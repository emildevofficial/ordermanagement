<?php

declare(strict_types=1);

namespace App\Handler\Product;

use App\Database\Database;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ProductListHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $pdo = $this->db->getPdo();

        $stmt = $pdo->prepare("
            SELECT id, name, price, stock, is_active
            FROM products
            ORDER BY id DESC
        ");
        $stmt->execute();

        $products = $stmt->fetchAll();

        $content = Template::render('products/list', [
            'products' => $products,
        ]);

        return new HtmlResponse(
            Template::render('layout', [
                'content' => $content,
                'currentRoute' => 'products',
                'userName' => Session::get('user_name') ?? 'User',
                'role' => Session::get('user_role') ?? 'user',
            ])
        );
    }
}
