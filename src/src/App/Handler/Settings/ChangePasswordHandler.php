<?php

declare(strict_types=1);

namespace App\Handler\Settings;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ChangePasswordHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $userId = Session::get('user_id');

        if (!$userId) {
            return new RedirectResponse('/login');
        }

        $pdo = $this->db->getPdo();

        if ($request->getMethod() === 'POST') {

            $data = $request->getParsedBody();

            $current = $data['current_password'] ?? '';
            $new = $data['new_password'] ?? '';
            $confirm = $data['confirm_password'] ?? '';

            // get current password from DB
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($current, $user['password'])) {
                return new HtmlResponse(
                    Template::render('settings/password', [
                        'error' => 'Current password is incorrect'
                    ])
                );
            }

            if ($new !== $confirm) {
                return new HtmlResponse(
                    Template::render('settings/password', [
                        'error' => 'Passwords do not match'
                    ])
                );
            }

            if (strlen($new) < 6) {
                return new HtmlResponse(
                    Template::render('settings/password', [
                        'error' => 'Password must be at least 6 characters'
                    ])
                );
            }

            // update password
            $hash = password_hash($new, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = ? WHERE id = ?");
            $stmt->execute([$hash, DateTimeHelper::nowForStorage(), $userId]);

            return new RedirectResponse('/settings');
        }

        return new HtmlResponse(
            Template::render('settings/password')
        );
    }
}
