<?php

declare(strict_types=1);

namespace App\Handler\Return;

use App\Database\Database;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ReturnViewHandler implements RequestHandlerInterface
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
        $currentRoute = 'returns';

        if ($id <= 0) {
            return new RedirectResponse('/returns');
        }

        $pdo = $this->db->getPdo();

        $stmt = $pdo->prepare("
            SELECT 
                r.*,
                o.customer_id,
                c.name AS customer_name
            FROM returns r
            JOIN orders o ON r.order_id = o.id
            LEFT JOIN customers c ON o.customer_id = c.id
            WHERE r.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $return = $stmt->fetch();

        if (!$return) {
            return new RedirectResponse('/returns');
        }

        $content = Template::render('return/view', [
            'return' => $return
        ]);

        return new HtmlResponse(
            Template::render('layout', [
                'content' => $content,
                'currentRoute' => $currentRoute,
            ])
        );
    }
}
