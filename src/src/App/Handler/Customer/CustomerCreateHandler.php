<?php

declare(strict_types=1);

namespace App\Handler\Customer;

use App\Database\Database;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CustomerCreateHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {

            $data = $request->getParsedBody();

            $name = trim($data['name'] ?? '');
            $email = trim($data['email'] ?? '');

            $config = require __DIR__ . '/../../../../config/autoload/database.global.php';
            $pdo = Database::getConnection($config['database']);

            $stmt = $pdo->prepare("
                INSERT INTO customers (name, email)
                VALUES (:name, :email)
            ");

            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
            ]);

            return new RedirectResponse('/customers');
        }

        return new HtmlResponse("
        <html>
        <head>
            <script src='https://cdn.tailwindcss.com'></script>
        </head>
        <body class='bg-slate-50 p-10'>

        <div class='max-w-md mx-auto bg-white p-6 rounded-xl shadow'>

            <h1 class='text-xl font-semibold mb-4'>Create Customer</h1>

            <form method='POST' action='/customers/create' class='space-y-4'>

                <input type='text' name='name' placeholder='Name'
                    class='w-full px-4 py-2 border rounded-lg' required>

                <input type='email' name='email' placeholder='Email'
                    class='w-full px-4 py-2 border rounded-lg' required>

                <button class='w-full bg-indigo-600 text-white py-2 rounded-lg'>
                    Create Customer
                </button>

            </form>

        </div>

        </body>
        </html>
        ");
    }
}