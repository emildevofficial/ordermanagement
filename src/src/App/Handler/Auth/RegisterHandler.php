<?php

declare(strict_types=1);

namespace App\Handler\Auth;

use App\Database\Database;
use App\Helper\Session;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RegisterHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        // GET /register → no separate page, redirect to login with register tab
        if ($request->getMethod() === 'GET') {
            return new RedirectResponse('/login?tab=register');
        }

        // ── POST → validate and save ──────────────────────────────────────
        $body    = $request->getParsedBody();

        // Single "Full name" field — no first/last split
        $name     = trim((string)($body['name']     ?? ''));
        $email    = trim((string)($body['email']    ?? ''));
        $password = trim((string)($body['password'] ?? ''));
        $confirm  = trim((string)($body['confirm']  ?? ''));

        // All errors redirect back to /login?tab=register
        // so the register tab opens automatically with the error shown
        if ($name === '' || $email === '' || $password === '') {
            Session::flash('register_error', 'All fields are required.');
            return new RedirectResponse('/login?tab=register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('register_error', 'Please enter a valid email address.');
            return new RedirectResponse('/login?tab=register');
        }

        if (strlen($password) < 6) {
            Session::flash('register_error', 'Password must be at least 6 characters.');
            return new RedirectResponse('/login?tab=register');
        }

        if ($password !== $confirm) {
            Session::flash('register_error', 'Passwords do not match.');
            return new RedirectResponse('/login?tab=register');
        }

        $config = require __DIR__ . '/../../../../config/autoload/database.global.php';
        $pdo    = Database::getConnection($config['database']);

        // Duplicate email check
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);

        if ($stmt->fetch()) {
            Session::flash('register_error', 'This email is already registered.');
            return new RedirectResponse('/login?tab=register');
        }

        // Insert new user
        $insert = $pdo->prepare(
            'INSERT INTO users (name, email, password, role)
             VALUES (:name, :email, :password, :role)'
        );
        $insert->execute([
            ':name'     => $name,
            ':email'    => $email,
            ':password' => password_hash($password, PASSWORD_BCRYPT),
            ':role'     => 'user',
        ]);

        // Auto-login immediately after registration
        session_regenerate_id(true);
        Session::set('user_id',   (int)$pdo->lastInsertId());
        Session::set('user_name', $name);
        Session::set('user_role', 'user');

        return new RedirectResponse('/dashboard');
    }
}