<?php

declare(strict_types=1);

namespace App\Handler\Customer;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CustomerCreateHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $name = trim((string)($data['name'] ?? ''));
            $email = trim((string)($data['email'] ?? ''));

            if ($name !== '' && $email !== '') {
                $pdo = $this->db->getPdo();
                
                // Check if a user exists with this email to link the customer
                $userStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
                $userStmt->execute([':email' => $email]);
                $userRow = $userStmt->fetch();
                $linkedUserId = $userRow ? (int)$userRow['id'] : null;

                if ($linkedUserId !== null) {
                    $existingStmt = $pdo->prepare("SELECT id FROM customers WHERE user_id = :user_id LIMIT 1");
                    $existingStmt->execute([':user_id' => $linkedUserId]);
                    if ($existingStmt->fetch()) {
                        return new RedirectResponse('/customers');
                    }
                }

                $existingEmailStmt = $pdo->prepare("SELECT id FROM customers WHERE email = :email LIMIT 1");
                $existingEmailStmt->execute([':email' => $email]);
                if ($existingEmailStmt->fetch()) {
                    return new RedirectResponse('/customers');
                }
                
                $stmt = $pdo->prepare("
                    INSERT INTO customers (user_id, name, email, created_at, is_active)
                    VALUES (:user_id, :name, :email, :created_at, 1)
                ");
                $stmt->execute([
                    ':user_id' => $linkedUserId,
                    ':name' => $name,
                    ':email' => $email,
                    ':created_at' => DateTimeHelper::nowForStorage(),
                ]);
            }

            return new RedirectResponse('/customers');
        }

        $content = Template::render('customers/create', []);

        return new HtmlResponse(
            Template::render('layout', [
                'content' => $content,
                'currentRoute' => 'customers',
                'userName' => Session::get('user_name') ?? 'User',
                'role' => Session::get('user_role') ?? 'user',
            ])
        );
    }
}
