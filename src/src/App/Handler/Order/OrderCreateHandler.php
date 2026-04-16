<?php

declare(strict_types=1);

namespace App\Handler\Order;

use App\Database\Database;
use App\Helper\Session;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OrderCreateHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();
        $userId = Session::get('user_id');

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $title = trim($data['title'] ?? '');
            $description = trim($data['description'] ?? '');

            $config = require __DIR__ . '/../../../../config/autoload/database.global.php';
            $pdo = Database::getConnection($config['database']);

            $stmt = $pdo->prepare("
                INSERT INTO orders (user_id, title, description, status, created_at)
                VALUES (:user_id, :title, :description, 'pending', NOW())
            ");

            $stmt->execute([
                ':user_id' => $userId,
                ':title' => $title,
                ':description' => $description,
            ]);

            return new RedirectResponse('/orders');
        }

    // Updated Form HTML (return this in your handler for GET request)
return new HtmlResponse("
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Create Order</title>
    <script src='https://cdn.tailwindcss.com'></script>
</head>
<body class='bg-slate-50 flex items-center justify-center min-h-screen'>

    <div class='bg-white shadow-xl rounded-2xl p-8 w-full max-w-md'>
    <div class='mb-4'>
    <a href='/orders'
       class='text-sm text-slate-600 hover:text-indigo-600 transition'>
        ← Back to Orders
    </a>
</div>
        <h1 class='text-2xl font-semibold mb-6 text-slate-800'>Create Order</h1>

        <form method='POST' action='/orders/create' class='space-y-4'>
            
            <div>
                <label class='block text-sm font-medium text-slate-700 mb-1'>Title</label>
                <input type='text' name='title' required
                    class='w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none'>
            </div>

            <div>
                <label class='block text-sm font-medium text-slate-700 mb-1'>Description</label>
                <textarea name='description' rows='3'
                    class='w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none'></textarea>
            </div>

            <div>
                <label class='block text-sm font-medium text-slate-700 mb-1'>Status</label>
                <select name='status' class='w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none bg-white'>
                    <option value='pending'>Pending</option>
                    <option value='completed'>Completed</option>
                </select>
            </div>

            <button type='submit'
                class='w-full bg-indigo-600 text-white py-2 rounded-xl hover:bg-indigo-700 transition font-medium'>
                Create Order
            </button>

        </form>
    </div>

</body>
</html>
");


    }
}