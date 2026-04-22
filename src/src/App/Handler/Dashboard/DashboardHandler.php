<?php

declare(strict_types=1);

namespace App\Handler\Dashboard;

use App\Helper\Session;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DashboardHandler implements RequestHandlerInterface
{
    private \App\Database\Database $db;
    public function __construct(\App\Database\Database $db)
{
    $this->db = $db;
}
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // AuthMiddleware already verified session.
        // Read user data from request attributes (set by AuthMiddleware).
        $userName = $request->getAttribute('user_name');
        $userRole = $request->getAttribute('user_role');
        $currentRoute = 'dashboard';
       $config = require __DIR__ . '/../../../../config/autoload/database.global.php';
$pdo = $this->db->getPdo();
$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
$result = $stmt->fetch();

$totalOrders = $result['total'] ?? 0;
    return new HtmlResponse("
<!DOCTYPE html>
<html lang='en' class='light'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Orders · Admin</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        * { font-family: 'Inter', sans-serif; }
        .dark .dark\\:block { display: block; }
        .dark .dark\\:hidden { display: none; }
        /* Profile dropdown */
        .profile-dropdown { display: none; }
        .peer:checked ~ .profile-dropdown { display: block; }
    </style>
    <script>
        tailwind.config = { darkMode: 'class' };
        window.addEventListener('load', function() {
            const toggle = document.getElementById('darkToggle');
            if (toggle) {
                toggle.addEventListener('click', () => {
                    document.documentElement.classList.toggle('dark');
                    localStorage.theme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
                });
            }
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                const dropdown = document.getElementById('profileDropdownToggle');
                const menu = document.getElementById('profileDropdownMenu');
                const checkbox = document.getElementById('profileCheckbox');
                if (dropdown && menu && checkbox) {
                    if (!dropdown.contains(e.target) && !menu.contains(e.target)) {
                        checkbox.checked = false;
                    }
                }
            });
        });
    </script>
</head>
<body class='bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 antialiased'>

<div class='flex h-screen overflow-hidden'>

    <!-- LEFT SIDEBAR -->
    <aside class='w-64 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 flex flex-col fixed h-full z-30'>
        <div class='h-16 flex items-center px-6 border-b border-slate-200 dark:border-slate-700'>
            <div class='w-8 h-8 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm'>O</div>
            <span class='ml-3 font-semibold text-lg text-slate-800 dark:text-white'>OrderHub</span>
        </div>
        <nav class='flex-1 py-6 px-3 space-y-1 overflow-y-auto'>
            <a href='/dashboard' class='block w-full'>
                <div class='flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition cursor-pointer'>
                    <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'/></svg>
                    <span>Dashboard</span>
                </div>
            </a>
            <a href='/orders' class='block w-full'>
<div class='flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl cursor-pointer'>                    <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'/></svg>
                    <span class='flex-1'>Orders</span>
                    <span class='ml-auto bg-indigo-100 dark:bg-indigo-800 text-indigo-700 dark:text-indigo-200 text-xs font-semibold px-2 py-0.5 rounded-full'>0</span>
                </div>
            </a>
               
            </a>
            <a href='/customers' class='block w-full'>
                <div class='flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition cursor-pointer'>
                    <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'/></svg>
                    <span>Customers</span>
                </div>
            </a>
            <a href='/settings' class='block w-full'>
                <div class='flex items-center gap-3 px-4 py-3 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition cursor-pointer'>
                    <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'/><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z'/></svg>
                    <span>Settings</span>
                </div>
            </a>
        </nav>
        <!-- REMOVED: user profile block from sidebar -->
    </aside>

    <!-- MAIN CONTENT -->
    <div class='flex-1 ml-64'>
        <!-- HEADER -->
        <header class='h-16 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-8 sticky top-0 z-20'>
            <h1 class='text-xl font-bold text-slate-800 dark:text-white'>Management Order System</h1>
            <div class='flex items-center gap-4'>
                <button class='relative p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition'>
                    <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'/></svg>
                    <span class='absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full'></span>
                </button>
                <button id='darkToggle' class='p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition'>
                    <svg class='w-5 h-5 dark:hidden' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z'/></svg>
                    <svg class='w-5 h-5 hidden dark:block' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z'/></svg>
                </button>
                
                <!-- PROFILE DROPDOWN -->
                <div class='relative' id='profileDropdownToggle'>
                    <input type='checkbox' id='profileCheckbox' class='peer hidden'>
                    <label for='profileCheckbox' class='flex items-center gap-3 pl-4 border-l border-slate-200 dark:border-slate-700 cursor-pointer'>
                        <div class='w-9 h-9 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm'>
                            A
                        </div>
                        <div class='text-left hidden sm:block'>
                            <p class='text-sm font-medium text-slate-800 dark:text-white'>Admin</p>
                            <p class='text-xs text-slate-500 dark:text-slate-400 capitalize'>admin</p>
                        </div>
                        <svg class='w-4 h-4 text-slate-400 transition-transform peer-checked:rotate-180' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/></svg>
                    </label>
                    
                    <!-- Dropdown menu -->
                    <div id='profileDropdownMenu' class='profile-dropdown absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 py-1 z-50'>
                        <a href='/profile' class='flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition'>
                            <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'/></svg>
                            Profile
                        </a>
                        <a href='/logout' class='flex items-center gap-3 px-4 py-2.5 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition'>
                            <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'/></svg>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main class='p-8'>
            <!-- STATS ROW (all zeros) -->
            <div class='grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6'>
                <div class='bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm'>
                    <p class='text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide'>Total Orders</p>
<p class='text-2xl font-bold text-slate-800 dark:text-white mt-1'>
    " . $totalOrders . "
</p>                    <p class='text-xs text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1'>↑ 0% this week</p>
                </div>
                <div class='bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm'>
                    <p class='text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide'>Ordered Items</p>
                    <p class='text-2xl font-bold text-slate-800 dark:text-white mt-1'>0</p>
                    <p class='text-xs text-slate-500 dark:text-slate-400 mt-1'>Across all orders</p>
                </div>
                <div class='bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm'>
                    <p class='text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide'>Returns</p>
                    <p class='text-2xl font-bold text-slate-800 dark:text-white mt-1'>0</p>
                    <p class='text-xs text-amber-600 dark:text-amber-400 mt-1'>0% return rate</p>
                </div>
                <div class='bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm'>
                    <p class='text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide'>Fulfilled</p>
                    <p class='text-2xl font-bold text-slate-800 dark:text-white mt-1'>0</p>
                    <p class='text-xs text-emerald-600 dark:text-emerald-400 mt-1'>0% fulfillment</p>
                </div>
                <div class='bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm'>
                    <p class='text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide'>Delivered</p>
                    <p class='text-2xl font-bold text-slate-800 dark:text-white mt-1'>0</p>
                    <p class='text-xs text-emerald-600 dark:text-emerald-400 mt-1'>0% on-time</p>
                </div>
            </div>

            <!-- FILTER BAR (unchanged) -->
            <div class='bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 mb-6 shadow-sm'>
                <div class='flex flex-col lg:flex-row lg:items-center gap-4'>
                    <div class='flex flex-wrap items-center gap-3 flex-1'>
                        <select class='px-4 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none'>
                            <option>Payment Status</option><option>Paid</option><option>Pending</option><option>Refunded</option>
                        </select>
                        <select class='px-4 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none'>
                            <option>Fulfillment Status</option><option>Fulfilled</option><option>Unfulfilled</option>
                        </select>
                        <select class='px-4 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none'>
                            <option>Delivery Status</option><option>Shipped</option><option>In Transit</option><option>Delivered</option>
                        </select>
                        <select class='px-4 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none'>
                            <option>Delivery Method</option><option>Standard</option><option>Express</option><option>Pickup</option>
                        </select>
                    </div>
                    <div class='relative'>
                        <svg class='absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'/></svg>
                        <input type='text' placeholder='Search orders...' class='pl-10 pr-4 py-2 w-full lg:w-64 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 outline-none'>
                    </div>
                </div>
            </div>

            <!-- ORDERS TABLE (empty state) -->
            <div class='bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm'>
                <div class='overflow-x-auto'>
                    <table class='w-full text-sm'>
                        <thead class='bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-600'>
                            <tr>
                                <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Order ID</th>
                                <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Date</th>
                                <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Customer</th>
                                <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Total</th>
                                <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Payment</th>
                                <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Fulfillment</th>
                                <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Items</th>
                                <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Delivery</th>
                            </tr>
                        </thead>
                        <tbody class='divide-y divide-slate-200 dark:divide-slate-700'>
                            <tr>
                                <td colspan='8' class='px-6 py-12 text-center text-slate-500 dark:text-slate-400'>
                                    No orders found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PAGINATION (minimal) -->
            <div class='flex items-center justify-between mt-6'>
                <p class='text-sm text-slate-500 dark:text-slate-400'>
                    Showing <span class='font-medium text-slate-800 dark:text-white'>0-0</span> of <span class='font-medium text-slate-800 dark:text-white'>0</span> orders
                </p>
                <div class='flex items-center gap-2'>
                    <button class='px-4 py-2 text-sm font-medium text-slate-400 dark:text-slate-500 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg cursor-not-allowed opacity-50' disabled>Previous</button>
                    <button class='px-4 py-2 text-sm font-medium text-slate-400 dark:text-slate-500 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg cursor-not-allowed opacity-50' disabled>Next</button>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
");
    }
}