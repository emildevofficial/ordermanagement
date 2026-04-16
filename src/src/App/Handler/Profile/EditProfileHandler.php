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

class EditProfileHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $userId   = Session::get('user_id');
        $userName = Session::get('user_name');

        $config = require __DIR__ . '/../../../../config/autoload/database.global.php';
        $pdo    = Database::getConnection($config['database']);

        // 🔹 POST → update profile
        if ($request->getMethod() === 'POST') {

            $body = $request->getParsedBody();
            $name = trim($body['name'] ?? '');

            if ($name === '') {
                return new RedirectResponse('/profile/edit');
            }

            $stmt = $pdo->prepare("UPDATE users SET name = :name WHERE id = :id");
            $stmt->execute([
                ':name' => $name,
                ':id'   => $userId,
            ]);

            // update session
            Session::set('user_name', $name);

            return new RedirectResponse('/profile');
        }

        // 🔹 GET → show form
        return new HtmlResponse("
        <!DOCTYPE html>
        <html>
        <head>
            <title>Edit Profile</title>
            <script src='https://cdn.tailwindcss.com'></script>
        </head>

        <body class='bg-gray-100 flex items-center justify-center min-h-screen'>

            <form method='POST' class='bg-white p-6 rounded-xl shadow w-full max-w-md'>
                <h2 class='text-lg font-bold mb-4'>Edit Profile</h2>

                <label class='block mb-2 text-sm'>Name</label>
                <input type='text' name='name' value='{$userName}'
                    class='w-full border rounded px-3 py-2 mb-4' />

                <button type='submit'
                    class='w-full bg-indigo-600 text-white py-2 rounded'>
                    Save
                </button>

                <a href='/profile'
                   class='block text-center mt-3 text-gray-500'>
                   Cancel
                </a>
            </form>

        </body>
        </html>
        ");
    }
}