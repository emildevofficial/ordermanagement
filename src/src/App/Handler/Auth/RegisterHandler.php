<?php

declare(strict_types=1);

namespace App\Handler\Auth;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Session;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RegisterHandler implements RequestHandlerInterface
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }
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

        // Duplicate email check
        $existing = $this->db->findUserByEmail($email);
        if ($existing) {
            Session::flash('register_error', 'This email is already registered.');
            return new RedirectResponse('/login?tab=register');
        }
        // Insert new user
        $pdo = $this->db->getPdo();
        $pdo->beginTransaction();

        try {
            $now = DateTimeHelper::nowForStorage();

            $insert = $pdo->prepare(
                'INSERT INTO users (name, email, password, role, created_at)
                 VALUES (:name, :email, :password, :role, :created_at)'
            );
            $insert->execute([
                ':name'     => $name,
                ':email'    => $email,
                ':password' => password_hash($password, PASSWORD_BCRYPT),
                ':role'     => 'user',
                ':created_at' => $now,
            ]);

            $userId = (int)$pdo->lastInsertId();

            $customerInsert = $pdo->prepare(
                'INSERT INTO customers (user_id, name, email, created_at, is_active)
                 VALUES (:user_id, :name, :email, :created_at, 1)'
            );
            $customerInsert->execute([
                ':user_id' => $userId,
                ':name' => $name,
                ':email' => $email,
                ':created_at' => $now,
            ]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            Session::flash('register_error', 'Registration failed. Please try again.');
            return new RedirectResponse('/login?tab=register');
        }

        // Auto-login immediately after registration
        session_regenerate_id(true);
        Session::set('user_id', $userId);
        Session::set('user_name', $name);
        Session::set('user_role', 'user');
        Session::set('user_email', $email);

        // Welcome discount notification
        $promo = $pdo->query(
            'SELECT new_user_discount_enabled, new_user_discount_percent FROM promotion_settings LIMIT 1'
        )->fetch();
        if ($promo && (int)$promo['new_user_discount_enabled'] === 1) {
            Session::flash(
                'welcome_discount',
                'Welcome! You\'ve earned ' . (int)$promo['new_user_discount_percent'] . '% off your first order.'
            );
        }

        return new RedirectResponse('/shop');
    }
}
