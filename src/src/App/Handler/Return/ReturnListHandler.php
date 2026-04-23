<?php

declare(strict_types=1);

namespace App\Handler\Return;

use App\Database\Database;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ReturnListHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();
        $userRole = Session::get('user_role') ?? 'user';
        $currentRoute = 'returns';

        $pdo = $this->db->getPdo();

        $stmt = $pdo->query("
            SELECT 
                r.*,
                o.id as order_id,
                CASE r.status 
                    WHEN 'requested' THEN 'Requested'
                    WHEN 'under_review' THEN 'Under Review'
                    WHEN 'approved' THEN 'Approved'
                    WHEN 'rejected' THEN 'Rejected'
                END as status_label
            FROM returns r
            JOIN orders o ON r.order_id = o.id
            ORDER BY r.created_at DESC
        ");

        $returns = $stmt->fetchAll();
        $returnCount = count($returns);

        $content = Template::render('return/list', [
            'returns' => $returns,
            'returnCount' => $returnCount,
        ]);

        return new HtmlResponse(
            Template::render('layout', [
                'content' => $content,
                'currentRoute' => $currentRoute,
            ])
        );
    }
}
