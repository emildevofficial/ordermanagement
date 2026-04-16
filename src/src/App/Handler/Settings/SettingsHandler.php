<?php

declare(strict_types=1);

namespace App\Handler\Settings;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SettingsHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userName = $request->getAttribute('user_name', 'User');
        $userRole = $request->getAttribute('user_role', 'user');

     return new HtmlResponse("
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Settings · Account</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap' rel='stylesheet'>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class='bg-slate-50 antialiased'>

    <div class='max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8'>
        
        <!-- Page header -->
       <div class='flex items-center justify-between mb-6'>
    
    <div>
        <h1 class='text-3xl font-semibold text-slate-900 tracking-tight'>Settings</h1>
        <p class='text-slate-500 text-sm mt-1'>Manage your account preferences and security.</p>
    </div>

    <a href='/dashboard'
       class='inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition'>
        ← Back to Dashboard
    </a>

</div>

        <!-- Main card -->
        <div class='bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden'>
            
            <!-- Account Information Section -->
            <div class='p-6'>
                <h2 class='text-lg font-semibold text-slate-800 mb-4'>Account Information</h2>
                <div class='bg-slate-50/80 rounded-xl p-5 border border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-5'>
                    <div>
                        <p class='text-xs font-medium text-slate-400 uppercase tracking-wider mb-1'>Full name</p>
                        <p class='text-slate-900 font-medium'>{$userName}</p>
                    </div>
                    <div>
                        <p class='text-xs font-medium text-slate-400 uppercase tracking-wider mb-1'>Role</p>
                        <p class='text-slate-900 font-medium capitalize'>{$userRole}</p>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class='border-t border-slate-100'></div>

            <!-- Security Section -->
            <div class='p-6'>
                <h2 class='text-lg font-semibold text-slate-800 mb-4'>Security</h2>
                <div class='flex flex-wrap items-center justify-between gap-4'>
                    <div>
                        <p class='font-medium text-slate-800'>Password</p>
                        <p class='text-sm text-slate-500'>Change your password regularly to keep your account secure.</p>
                    </div>
                    <a href='/profile/password' class='inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition'>
                        Change Password
                    </a>
                </div>
            </div>

            <!-- Divider -->
            <div class='border-t border-slate-100'></div>

            <!-- Danger Zone -->
            <div class='p-6'>
                <h2 class='text-lg font-semibold text-slate-800 mb-4'>Danger Zone</h2>
                <div class='flex flex-wrap items-center justify-between gap-4'>
                    <div>
                        <p class='font-medium text-slate-800'>Sign out of your account</p>
                        <p class='text-sm text-slate-500'>You will be redirected to the login page.</p>
                    </div>
                    <a href='/logout' class='inline-flex items-center px-4 py-2 text-sm font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-200 transition'>
                        Logout
                    </a>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
");
    }
}