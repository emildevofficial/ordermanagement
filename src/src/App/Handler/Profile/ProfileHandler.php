<?php

declare(strict_types=1);

namespace App\Handler\Profile;

use App\Database\Database;
use App\Helper\DateTimeHelper;
use App\Helper\Session;
use App\Helper\Template;
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
        $profileRoleLabel = $userRole === 'admin' ? 'Owner' : ($userRole === 'user' ? 'Customer' : ucfirst($userRole));
        $profileHeaderName = $userRole === 'admin' ? 'Owner' : $userName;
        $profileFullName = $userRole === 'admin' ? 'Owner' : $userName;
        $displayProfileRole = htmlspecialchars($profileRoleLabel, ENT_QUOTES, 'UTF-8');
        $displayProfileHeaderName = htmlspecialchars($profileHeaderName, ENT_QUOTES, 'UTF-8');
        $displayProfileFullName = htmlspecialchars($profileFullName, ENT_QUOTES, 'UTF-8');
        $displayMemberSince = htmlspecialchars($memberSince, ENT_QUOTES, 'UTF-8');
        $avatarInitial = htmlspecialchars(strtoupper(substr($userName !== '' ? $userName : 'U', 0, 1)), ENT_QUOTES, 'UTF-8');

        $backHref = $userRole === 'admin' ? '/dashboard' : '/shop';
        $backLabel = $userRole === 'admin' ? 'Back to Dashboard' : 'Back to Shop';
        $backHref = htmlspecialchars($backHref, ENT_QUOTES, 'UTF-8');
        $backLabel = htmlspecialchars($backLabel, ENT_QUOTES, 'UTF-8');
        $profileSuccess = Session::getFlash('profile_success');
        $profileSuccessHtml = '';

        if ($profileSuccess !== null) {
            $safeProfileSuccess = htmlspecialchars((string) $profileSuccess, ENT_QUOTES, 'UTF-8');
            $profileSuccessHtml = "
            <div class='mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300'>
                {$safeProfileSuccess}
            </div>";
        }

        $content = "
    <div class='py-2'>
        <div class='max-w-4xl'>
            <div class='mb-8'>
                <h1 class='text-3xl font-semibold text-slate-900 dark:text-slate-100 tracking-tight'>Profile</h1>
                <p class='text-slate-500 dark:text-slate-400 mt-1 text-sm'>Manage your account settings and preferences.</p>
            </div>
            {$profileSuccessHtml}

            <div class='bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 dark:text-slate-100 overflow-hidden'>
                <div class='px-6 py-6 border-b border-slate-100 dark:border-slate-700 flex flex-wrap items-center gap-5'>
                    <div class='h-16 w-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-lg font-semibold shadow'>
                        {$avatarInitial}
                    </div>
                    <div class='flex-1'>
                        <h2 class='text-2xl font-semibold text-slate-900 dark:text-slate-100'>{$displayProfileHeaderName}</h2>
                    </div>
                    <div class='flex items-center gap-2'>
                        <a href='{$backHref}' class='inline-flex items-center px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-100 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-600 transition shadow-sm'>
                            <svg class='w-4 h-4 mr-2' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 19l-7-7m0 0l7-7m-7 7h18'/></svg>
                            {$backLabel}
                        </a>
                    </div>
                </div>

                <div class='p-6 space-y-6'>
                    <div>
                        <h3 class='text-base font-semibold text-slate-800 dark:text-slate-100 mb-3'>Account Information</h3>
                        <div class='bg-slate-50 dark:bg-slate-800/60 rounded-xl p-5 border border-slate-100 dark:border-slate-700 grid grid-cols-1 sm:grid-cols-2 gap-5'>
                            <div>
                                <p class='text-xs font-medium text-slate-400 dark:text-slate-400 uppercase tracking-wider mb-1'>Full name</p>
                                <p class='text-slate-900 dark:text-slate-100 font-medium'>{$displayProfileFullName}</p>
                            </div>
                            <div>
                                <p class='text-xs font-medium text-slate-400 dark:text-slate-400 uppercase tracking-wider mb-1'>Email address</p>
                                <p class='text-slate-900 dark:text-slate-100 font-medium'>{$displayEmail}</p>
                            </div>
                            <div>
                                <p class='text-xs font-medium text-slate-400 dark:text-slate-400 uppercase tracking-wider mb-1'>USER ROLE</p>
                                <p class='text-slate-900 dark:text-slate-100 font-medium'>{$displayProfileRole}</p>
                            </div>
                            <div>
                                <p class='text-xs font-medium text-slate-400 dark:text-slate-400 uppercase tracking-wider mb-1'>Member since</p>
                                <p class='text-slate-900 dark:text-slate-100 font-medium'>{$displayMemberSince}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class='text-base font-semibold text-slate-800 dark:text-slate-100 mb-3'>Security</h3>
                        <div class='bg-slate-50 dark:bg-slate-800/60 rounded-xl p-5 border border-slate-100 dark:border-slate-700 flex flex-wrap items-center justify-between gap-4'>
                            <div>
                                <p class='font-medium text-slate-800 dark:text-slate-100'>Password</p>
                                <p class='text-sm text-slate-500 dark:text-slate-400'>Change your password regularly to keep your account secure.</p>
                            </div>
                            <div class='flex gap-2'>
                                <a href='/profile/edit' class='px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition'>
                                    Edit Profile
                                </a>
                                <a href='/profile/password' class='px-4 py-2 text-sm font-medium text-indigo-700 dark:text-indigo-200 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-xl border border-indigo-200 dark:border-indigo-800 transition'>
                                    Change password
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class='pt-4 border-t border-slate-100 dark:border-slate-700'>
                        <div class='flex flex-wrap items-center justify-between gap-4'>
                            <div>
                                <p class='font-medium text-slate-800 dark:text-slate-100'>Sign out of your account</p>
                                <p class='text-sm text-slate-500 dark:text-slate-400'>You will be redirected to the login page.</p>
                            </div>
                            <a href='/logout' class='inline-flex items-center px-4 py-2 text-sm font-medium text-rose-600 dark:text-rose-300 bg-rose-50 dark:bg-rose-900/30 hover:bg-rose-100 dark:hover:bg-rose-900/50 rounded-xl border border-rose-200 dark:border-rose-800 transition'>
                                <svg class='w-4 h-4 mr-2' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'/></svg>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
";

        return new HtmlResponse(
            Template::render('layout', [
                'content' => $content,
                'currentRoute' => 'profile',
                'userName' => $userName,
                'role' => $userRole,
            ])
        );
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
