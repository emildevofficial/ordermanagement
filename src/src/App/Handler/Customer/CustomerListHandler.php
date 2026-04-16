<?php

declare(strict_types=1);

namespace App\Handler\Customer;

use App\Database\Database;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CustomerListHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $config = require __DIR__ . '/../../../../config/autoload/database.global.php';
        $pdo = Database::getConnection($config['database']);

        $stmt = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC");
        $customers = $stmt->fetchAll();

        $rows = '';

        foreach ($customers as $customer) {
            $rows .= "
                <tr class='border-t'>
                    <td class='px-4 py-2'>#" . $customer['id'] . "</td>
                    <td class='px-4 py-2'>" . htmlspecialchars($customer['name']) . "</td>
                    <td class='px-4 py-2'>" . htmlspecialchars($customer['email']) . "</td>
                    <td class='px-4 py-2'>" . $customer['created_at'] . "</td>
                </tr>
            ";
        }

        if (empty($rows)) {
            $rows = "
                <tr>
                    <td colspan='4' class='text-center py-6 text-slate-500'>
                        No customers found.
                    </td>
                </tr>
            ";
        }

        return new HtmlResponse("
        <html>
        <head>
            <script src='https://cdn.tailwindcss.com'></script>
        </head>
        <body class='bg-slate-50 p-10'>

        <div class='max-w-5xl mx-auto'>

            <div class='flex justify-between items-center mb-6'>
            <a href='/customers/create'
          class='px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm'>
             + Add Customer
               </a>
                <h1 class='text-2xl font-semibold'>Customers</h1>

                <a href='/dashboard'
                   class='px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-sm'>
                    ← Back
                </a>
            </div>

            <div class='bg-white rounded-xl shadow overflow-hidden'>
                <table class='w-full text-sm'>
                    <thead class='bg-slate-100 text-left'>
                        <tr>
                            <th class='px-4 py-3'>ID</th>
                            <th class='px-4 py-3'>Name</th>
                            <th class='px-4 py-3'>Email</th>
                            <th class='px-4 py-3'>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        $rows
                    </tbody>
                </table>
            </div>

        </div>

        </body>
        </html>
        ");
    }
}