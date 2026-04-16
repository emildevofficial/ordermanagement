<?php

declare(strict_types=1);

namespace App\Handler\Profile;

use App\Database\Database;
use App\Helper\Session;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ChangePasswordHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $userId = Session::get('user_id');

        $config = require __DIR__ . '/../../../../config/autoload/database.global.php';
        $pdo    = Database::getConnection($config['database']);

        if ($request->getMethod() === 'POST') {

            $body = $request->getParsedBody();

            $current = $body['current_password'] ?? '';
            $new     = $body['new_password'] ?? '';
            $confirm = $body['confirm_password'] ?? '';

            // fetch user
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
            $stmt->execute([':id' => $userId]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($current, $user['password'])) {
                return new HtmlResponse("Wrong current password");
            }

            if ($new !== $confirm) {
                return new HtmlResponse("Passwords do not match");
            }

            if (strlen($new) < 6) {
                return new HtmlResponse("Password too short");
            }

            $hashed = password_hash($new, PASSWORD_BCRYPT);

            $update = $pdo->prepare("UPDATE users SET password = :p WHERE id = :id");
            $update->execute([
                ':p' => $hashed,
                ':id' => $userId
            ]);

            return new RedirectResponse('/profile');
        }

      return new HtmlResponse("
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Change Password</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap' rel='stylesheet'>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class='bg-slate-50 flex items-center justify-center min-h-screen p-4'>

    <div class='w-full max-w-md'>
        <!-- Back link -->
        <a href='/profile' class='inline-block mb-4 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition'>
            ← Back to Profile
        </a>

        <!-- Card -->
        <div class='bg-white rounded-2xl shadow-md border border-slate-100 p-8'>
            <h2 class='text-2xl font-bold text-slate-900 mb-6'>Change Password</h2>

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