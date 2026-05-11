<?php

declare(strict_types=1);

namespace App\Handler\Profile;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Session;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ProfileHandler implements RequestHandlerInterface
{
    public function __construct(private Database $db)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        Session::start();

        $userId = (int)($request->getAttribute('user_id') ?? Session::get('user_id') ?? 0);
        $profile = $this->loadProfile($userId);

        $userName = (string)($profile['name'] ?? $request->getAttribute('user_name') ?? Session::get('user_name') ?? '');
        $userEmail = (string)($profile['email'] ?? Session::get('user_email') ?? '');
        $userRole = (string)($profile['role'] ?? $request->getAttribute('user_role') ?? Session::get('user_role') ?? 'user');
        $memberSince = DateTimeHelper::format($profile['member_since'] ?? null, 'M d, Y');

        $displayName = htmlspecialchars($userName, ENT_QUOTES, 'UTF-8');
        $displayEmail = htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8');
        $displayRole = htmlspecialchars(ucfirst($userRole), ENT_QUOTES, 'UTF-8');
        $displayMemberSince = htmlspecialchars($memberSince, ENT_QUOTES, 'UTF-8');
        $avatarInitial = htmlspecialchars(strtoupper(substr($userName !== '' ? $userName : 'U', 0, 1)), ENT_QUOTES, 'UTF-8');

        $backHref = $userRole === 'admin' ? '/dashboard' : '/shop';
        $backLabel = $userRole === 'admin' ? 'Back to Dashboard' : 'Back to Shop';
        $backHref = htmlspecialchars($backHref, ENT_QUOTES, 'UTF-8');
        $backLabel = htmlspecialchars($backLabel, ENT_QUOTES, 'UTF-8');

        return new HtmlResponse("
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Profile - Account</title>
    <script src='https://cdn.tailwindcss.com'></script>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap' rel='stylesheet'>
    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class='bg-slate-50 antialiased'>

    <div class='min-h-screen py-10 px-4 sm:px-6 lg:px-8'>
        <div class='max-w-4xl mx-auto'>
            <div class='mb-8'>
                <h1 class='text-3xl font-semibold text-slate-900 tracking-tight'>Profile</h1>
                <p class='text-slate-500 mt-1 text-sm'>Manage your account settings and preferences.</p>
            </div>

            <div class='bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden'>
                <div class='px-6 py-6 border-b border-slate-100 flex flex-wrap items-center gap-5'>
                    <div class='h-16 w-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-lg font-semibold shadow'>
                        {$avatarInitial}
                    </div>
                    <div class='flex-1'>
                        <h2 class='text-2xl font-semibold text-slate-900'>{$displayName}</h2>
                        <div class='flex items-center gap-3 mt-1'>
                            <span class='inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border border-indigo-100'>
                                {$displayRole}
                            </span>
                            <span class='text-sm text-slate-400'>&middot;</span>
                            <span class='text-sm text-slate-500'>{$displayEmail}</span>
                        </div>
                    </div>
                    <div class='flex items-center gap-2'>
                        <a href='{$backHref}' class='inline-flex items-center px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition shadow-sm'>
                            <svg class='w-4 h-4 mr-2' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 19l-7-7m0 0l7-7m-7 7h18'/></svg>
                            {$backLabel}
                        </a>
                    </div>
                </div>

                <div class='p-6 space-y-6'>
                    <div>
                        <h3 class='text-base font-semibold text-slate-800 mb-3'>Account Information</h3>
                        <div class='bg-slate-50/80 rounded-xl p-5 border border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-5'>
                            <div>
                                <p class='text-xs font-medium text-slate-400 uppercase tracking-wider mb-1'>Full name</p>
                                <p class='text-slate-900 font-medium'>{$displayName}</p>
                            </div>
                            <div>
                                <p class='text-xs font-medium text-slate-400 uppercase tracking-wider mb-1'>Email address</p>
                                <p class='text-slate-900 font-medium'>{$displayEmail}</p>
                            </div>
                            <div>
                                <p class='text-xs font-medium text-slate-400 uppercase tracking-wider mb-1'>Role</p>
                                <p class='text-slate-900 font-medium'>{$displayRole}</p>
                            </div>
                            <div>
                                <p class='text-xs font-medium text-slate-400 uppercase tracking-wider mb-1'>Member since</p>
                                <p class='text-slate-900 font-medium'>{$displayMemberSince}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class='text-base font-semibold text-slate-800 mb-3'>Security</h3>
                        <div class='bg-slate-50/80 rounded-xl p-5 border border-slate-100 flex flex-wrap items-center justify-between gap-4'>
                            <div>
                                <p class='font-medium text-slate-800'>Password</p>
                                <p class='text-sm text-slate-500'>Change your password regularly to keep your account secure.</p>
                            </div>
                            <div class='flex gap-2'>
                                <a href='/profile/edit' class='px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition'>
                                    Edit Profile
                                </a>
                                <a href='/profile/password' class='px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-xl border border-indigo-200 transition'>
                                    Change password
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class='pt-4 border-t border-slate-100'>
                        <div class='flex flex-wrap items-center justify-between gap-4'>
                            <div>
                                <p class='font-medium text-slate-800'>Sign out of your account</p>
                                <p class='text-sm text-slate-500'>You will be redirected to the login page.</p>
                            </div>
                            <a href='/logout' class='inline-flex items-center px-4 py-2 text-sm font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-200 transition'>
                                <svg class='w-4 h-4 mr-2' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'/></svg>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
");
    }

    private function loadProfile(int $userId): ?array
    {
        if ($userId <= 0) {
            return null;
        }

        $stmt = $this->db->getPdo()->prepare("
            SELECT
                u.name,
                u.email,
                u.role,
                COALESCE(c.created_at, u.created_at) AS member_since
            FROM users u
            LEFT JOIN customers c ON c.user_id = u.id
            WHERE u.id = :user_id
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $userId]);
        $profile = $stmt->fetch();

        return $profile ?: null;
    }
}
