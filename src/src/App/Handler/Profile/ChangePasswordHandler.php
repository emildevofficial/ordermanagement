<?php

declare(strict_types=1);

namespace App\Handler\Profile;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Session;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ChangePasswordHandler implements RequestHandlerInterface
{
    public function __construct(private Database $db)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $userId = (int) (Session::get('user_id') ?? 0);

        if ($userId <= 0) {
            return new RedirectResponse('/login');
        }

        $backHref = str_starts_with($request->getUri()->getPath(), '/settings')
            ? '/settings'
            : '/profile';

        if ($request->getMethod() !== 'POST') {
            return $this->renderForm(null, $backHref);
        }

        $body = $request->getParsedBody();
        $current = (string) ($body['current_password'] ?? '');
        $new = (string) ($body['new_password'] ?? '');
        $confirm = (string) ($body['confirm_password'] ?? '');

        $pdo = $this->db->getPdo();
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($current, (string) $user['password'])) {
            return $this->renderForm('Current password is incorrect.', $backHref);
        }

        if ($new !== $confirm) {
            return $this->renderForm('Passwords do not match.', $backHref);
        }

        if (strlen($new) < 6) {
            return $this->renderForm('Password must be at least 6 characters.', $backHref);
        }

        $stmt = $pdo->prepare(
            'UPDATE users
                SET password = :password,
                    updated_at = :updated_at
              WHERE id = :id'
        );
        $stmt->execute([
            ':password' => password_hash($new, PASSWORD_DEFAULT),
            ':updated_at' => DateTimeHelper::nowForStorage(),
            ':id' => $userId,
        ]);

        Session::flash('profile_success', 'Password updated successfully.');

        return new RedirectResponse($backHref);
    }

    private function renderForm(?string $error, string $backHref): HtmlResponse
    {
        $safeBackHref = htmlspecialchars($backHref, ENT_QUOTES, 'UTF-8');
        $errorHtml = '';

        if ($error !== null) {
            $safeError = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
            $errorHtml = "
                <div class='mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700'>
                    {$safeError}
                </div>";
        }

        return new HtmlResponse("
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Change Password</title>
    <script src='https://cdn.tailwindcss.com/3.4.16'></script>
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap' rel='stylesheet'>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class='bg-slate-50 flex items-center justify-center min-h-screen p-4'>

    <div class='w-full max-w-md'>
        <a href='{$safeBackHref}' class='inline-block mb-4 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition'>
            Back
        </a>

        <div class='bg-white rounded-2xl shadow-md border border-slate-100 p-8'>
            <h2 class='text-2xl font-bold text-slate-900 mb-6'>Change Password</h2>
            {$errorHtml}

            <form method='POST' class='space-y-4'>
                <div>
                    <input
                        type='password'
                        name='current_password'
                        placeholder='Current password'
                        required
                        class='w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition'
                    >
                </div>
                <div>
                    <input
                        type='password'
                        name='new_password'
                        placeholder='New password'
                        required
                        class='w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition'
                    >
                </div>
                <div>
                    <input
                        type='password'
                        name='confirm_password'
                        placeholder='Confirm new password'
                        required
                        class='w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition'
                    >
                </div>
                <button
                    type='submit'
                    class='w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-xl transition shadow-sm'
                >
                    Update Password
                </button>
            </form>
        </div>
    </div>

</body>
</html>
");
    }
}
