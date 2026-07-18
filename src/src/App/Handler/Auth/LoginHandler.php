<?php

declare(strict_types=1);

namespace App\Handler\Auth;

use App\Database\Database;
use App\Helper\Session;
use App\Helper\Template;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoginHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $method = $request->getMethod();

        // 🔹 GET → show page
        if ($method === 'GET') {
            return new HtmlResponse(
                Template::render('auth/login', [
                    'loginError' => Session::getFlash('login_error'),
                    'registerError' => Session::getFlash('register_error')
                ])
            );
        }

        // 🔹 POST → process login
        if ($method === 'POST') {

            $body = $request->getParsedBody();

            $email = trim($body['email'] ?? '');
            $password = trim($body['password'] ?? '');

            // 🔍 gjej user
            $user = $this->db->findUserByEmail($email);

            // ❌ invalid
            if (!$user || !password_verify($password, $user['password'])) {
                return new HtmlResponse(
                    Template::render('auth/login', [
                        'loginError' => 'Invalid email or password',
                        'registerError' => null
                    ])
                );
            }

            // ✅ sukses
            Session::set('user_id', $user['id']);
            Session::set('user_name', $user['name']);
            Session::set('user_role', $user['role']);
            Session::set('user_email', $user['email']);

            return new RedirectResponse($user['role'] === 'admin' ? '/dashboard' : '/shop');
        }

        // fallback
        return new RedirectResponse('/login');
    }
}
