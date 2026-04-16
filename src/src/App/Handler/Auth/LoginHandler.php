<?php

declare(strict_types=1);

namespace App\Handler\Auth;

use App\Database\Database;
use App\Helper\Session;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoginHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        // Already logged in → skip the form entirely
     //  if (Session::has('user_id')) {
       //   return new RedirectResponse('/dashboard');
     //   }

        // ── POST → process login ──────────────────────────────────────────
        if ($request->getMethod() === 'POST') {
            $body     = $request->getParsedBody();
            $email    = trim((string)($body['email']    ?? ''));
            $password = trim((string)($body['password'] ?? ''));

            if ($email === '' || $password === '') {
                Session::flash('login_error', 'Email and password are required.');
                return new RedirectResponse('/login');
            }

            $config = require __DIR__ . '/../../../../config/autoload/database.global.php';
            $pdo    = Database::getConnection($config['database']);

            $stmt = $pdo->prepare(
                'SELECT id, name, email, password, role
                 FROM users WHERE email = :email LIMIT 1'
            );
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password'])) {
                Session::flash('login_error', 'Invalid email or password.');
                return new RedirectResponse('/login');
            }

            session_regenerate_id(true);
            Session::set('user_id',   $user['id']);
            Session::set('user_name', $user['name']);
            Session::set('user_role', $user['role']);

           return new RedirectResponse('/dashboard');
        }

        // ── GET → render the shared auth page ────────────────────────────
        return new HtmlResponse($this->renderPage(
            loginError:    Session::getFlash('login_error'),
            registerError: Session::getFlash('register_error'),
            success:       Session::getFlash('success'),
        ));
    }

    private function renderPage(
        ?string $loginError,
        ?string $registerError,
        ?string $success
    ): string {

        $loginErrorHtml = $loginError
            ? "<div class='alert alert-error'>{$loginError}</div>"
            : '';

        $registerErrorHtml = $registerError
            ? "<div class='alert alert-error'>{$registerError}</div>"
            : '';

        $successHtml = $success
            ? "<div class='alert alert-success'>{$success}</div>"
            : '';

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .tab-active {
            color: #4f46e5 !important;
            border-bottom: 2.5px solid #4f46e5 !important;
        }
        .tab-inactive {
            color: #6b7280;
            border-bottom: 2.5px solid transparent;
        }
        .panel { display: none; }
        .panel.active { display: block; }
        input:focus {
            outline: none;
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.08);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4"
      style="background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);">

    <div class="w-full max-w-md bg-white rounded-2xl overflow-hidden"
         style="box-shadow: 0 8px 32px rgba(99,102,241,0.10), 0 1.5px 8px rgba(0,0,0,0.06);">

        <!-- ── Tabs ───────────────────────────────────────────────────── -->
        <div class="grid grid-cols-2 border-b border-gray-100">
            <button
                id="tab-login"
                onclick="switchTab('login')"
                class="py-4 text-sm font-semibold tracking-wide tab-active transition-colors duration-150">
                Sign in
            </button>
            <button
                id="tab-register"
                onclick="switchTab('register')"
                class="py-4 text-sm font-semibold tracking-wide tab-inactive transition-colors duration-150">
                Create account
            </button>
        </div>

        <div class="px-8 py-8">

            <!-- ── LOGIN PANEL ─────────────────────────────────────────── -->
            <div class="panel active" id="panel-login">

                <h1 class="text-2xl font-bold text-gray-900 text-center mb-6"
                    style="letter-spacing: -0.02em;">
                    Sign in to your account
                </h1>

                {$successHtml}
                {$loginErrorHtml}

                <form method="POST" action="/login" class="space-y-4">

                    <div>
                        <label for="login-email"
                               class="block text-sm font-medium text-gray-700 mb-1.5">
                            Email address
                        </label>
                        <input
                            type="email"
                            id="login-email"
                            name="email"
                            placeholder="you@example.com"
                            required
                            autofocus
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl
                                   text-sm text-gray-900 bg-white transition-all duration-150"
                            style="border: 1.5px solid #e5e7eb;">
                    </div>

                    <div>
                        <label for="login-password"
                               class="block text-sm font-medium text-gray-700 mb-1.5">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="login-password"
                                name="password"
                                placeholder="Your password"
                                required
                                class="w-full px-4 py-2.5 pr-14 border rounded-xl
                                       text-sm text-gray-900 bg-white transition-all duration-150"
                                style="border: 1.5px solid #e5e7eb;">
                            <button
                                type="button"
                                onclick="togglePass('login-password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2
                                       text-xs font-semibold text-indigo-500
                                       hover:text-indigo-700 transition-colors">
                                Show
                            </button>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full py-3 rounded-xl text-white text-sm font-semibold
                               tracking-wide transition-opacity duration-150 hover:opacity-90"
                        style="background: linear-gradient(90deg, #4f46e5, #7c3aed); margin-top: 8px;">
                        Sign in
                    </button>

                </form>
            </div>

            <!-- ── REGISTER PANEL ──────────────────────────────────────── -->
            <div class="panel" id="panel-register">

                <h1 class="text-2xl font-bold text-gray-900 text-center mb-6"
                    style="letter-spacing: -0.02em;">
                    Create your account
                </h1>

                {$registerErrorHtml}

                <form method="POST" action="/register" class="space-y-4">

                    <div>
                        <label for="reg-name"
                               class="block text-sm font-medium text-gray-700 mb-1.5">
                            Full name
                        </label>
                        <input
                            type="text"
                            id="reg-name"
                            name="name"
                            placeholder="John Doe"
                            required
                            class="w-full px-4 py-2.5 border rounded-xl
                                   text-sm text-gray-900 bg-white transition-all duration-150"
                            style="border: 1.5px solid #e5e7eb;">
                    </div>

                    <div>
                        <label for="reg-email"
                               class="block text-sm font-medium text-gray-700 mb-1.5">
                            Email address
                        </label>
                        <input
                            type="email"
                            id="reg-email"
                            name="email"
                            placeholder="you@example.com"
                            required
                            class="w-full px-4 py-2.5 border rounded-xl
                                   text-sm text-gray-900 bg-white transition-all duration-150"
                            style="border: 1.5px solid #e5e7eb;">
                    </div>

                    <div>
                        <label for="reg-password"
                               class="block text-sm font-medium text-gray-700 mb-1.5">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="reg-password"
                                name="password"
                                placeholder="Min. 6 characters"
                                required
                                class="w-full px-4 py-2.5 pr-14 border rounded-xl
                                       text-sm text-gray-900 bg-white transition-all duration-150"
                                style="border: 1.5px solid #e5e7eb;">
                            <button
                                type="button"
                                onclick="togglePass('reg-password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2
                                       text-xs font-semibold text-indigo-500
                                       hover:text-indigo-700 transition-colors">
                                Show
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="reg-confirm"
                               class="block text-sm font-medium text-gray-700 mb-1.5">
                            Confirm password
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="reg-confirm"
                                name="confirm"
                                placeholder="Repeat your password"
                                required
                                class="w-full px-4 py-2.5 pr-14 border rounded-xl
                                       text-sm text-gray-900 bg-white transition-all duration-150"
                                style="border: 1.5px solid #e5e7eb;">
                            <button
                                type="button"
                                onclick="togglePass('reg-confirm')"
                                class="absolute right-3 top-1/2 -translate-y-1/2
                                       text-xs font-semibold text-indigo-500
                                       hover:text-indigo-700 transition-colors">
                                Show
                            </button>
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5 pt-1">
                        <input
                            type="checkbox"
                            id="terms"
                            name="terms"
                            required
                            class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600"
                            style="accent-color: #4f46e5; flex-shrink: 0;">
                        <label for="terms" class="text-sm text-gray-500 leading-snug">
                            I agree to the terms and conditions
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="w-full py-3 rounded-xl text-white text-sm font-semibold
                               tracking-wide transition-opacity duration-150 hover:opacity-90"
                        style="background: linear-gradient(90deg, #4f46e5, #7c3aed); margin-top: 4px;">
                        Create account
                    </button>

                </form>
            </div>

        </div>
    </div>

<script>
    function switchTab(name) {
        // Hide all panels, deactivate all tabs
        document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('[id^="tab-"]').forEach(t => {
            t.classList.remove('tab-active');
            t.classList.add('tab-inactive');
        });

        // Activate the chosen panel and tab
        document.getElementById('panel-' + name).classList.add('active');
        document.getElementById('tab-'   + name).classList.remove('tab-inactive');
        document.getElementById('tab-'   + name).classList.add('tab-active');

        // Keep URL in sync without reloading
        const url = new URL(window.location);
        url.searchParams.set('tab', name);
        window.history.replaceState({}, '', url);
    }

    // On load: open the correct tab based on ?tab= param
    // RegisterHandler redirects to /login?tab=register on errors
    (function () {
        const tab = new URLSearchParams(window.location.search).get('tab');
        switchTab(tab === 'register' ? 'register' : 'login');
    })();

    function togglePass(id) {
        const input = document.getElementById(id);
        input.type  = input.type === 'password' ? 'text' : 'password';
    }
</script>

</body>
</html>
HTML;
    }
}