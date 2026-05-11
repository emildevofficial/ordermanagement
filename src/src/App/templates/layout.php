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
        .dark .dark\:block { display: block; }
        .dark .dark\:hidden { display: none; }
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

<div class='flex min-h-screen'>

    <!-- LEFT SIDEBAR -->
    <aside class='w-64 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 flex flex-col fixed h-full z-30'>
        <div class='h-16 flex items-center px-6 border-b border-slate-200 dark:border-slate-700'>
            <div class='w-8 h-8 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm'>O</div>
            <span class='ml-3 font-semibold text-lg text-slate-800 dark:text-white'>OrderHub</span>
        </div>
        <nav class='flex-1 py-6 px-3 space-y-1 overflow-y-auto'>
            <?php use App\Helper\Permission; $isAdmin = Permission::isAllowed('admin'); ?>

            <?php if ($isAdmin): ?>
                <a href='/dashboard' class='block w-full'>
                    <div class='flex items-center gap-3 px-4 py-3 <?= ($currentRoute ?? '') === "dashboard" ? "bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400" : "text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700" ?> rounded-xl transition cursor-pointer'>
                        <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'/></svg>
                        <span>Dashboard</span>
                    </div>
                </a>

                <a href='/orders' class='block w-full'>
                    <div class='flex items-center gap-3 px-4 py-3 <?= ($currentRoute ?? '') === "orders" ? "bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400" : "text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700" ?> rounded-xl transition cursor-pointer'>
                        <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'/></svg>
                        <span class='flex-1'>Orders</span>
                    </div>
                </a>

                <a href='/products' class='block w-full'>
                    <div class='flex items-center gap-3 px-4 py-3 <?= ($currentRoute ?? '') === "products" ? "bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400" : "text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700" ?> rounded-xl transition cursor-pointer'>
                        <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M20.618 5.984A11.955 11.955 0 0112 3 11.955 11.955 0 013.382 5.984M15.204 5.983h.007M14.799 8.343a4 4 0 00-4.598 0M12 16a4 4 0 108 0 4 4 0 00-8 0zm0 0v1m0 0h.008v.008H12v-.008z'/></svg>
                        <span>Products</span>
                    </div>
                </a>

                <a href='/returns' class='block w-full'>
                    <div class='flex items-center gap-3 px-4 py-3 <?= ($currentRoute ?? '') === "returns" ? "bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400" : "text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700" ?> rounded-xl transition cursor-pointer'>
                        <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3.5 12h6m-6 6h12m-12-6h12m0-6h-6m6 0h6'/></svg>
                        <span>Returns</span>
                    </div>
                </a>

                <a href='/customers' class='block w-full'>
                    <div class='flex items-center gap-3 px-4 py-3 <?= ($currentRoute ?? '') === "customers" ? "bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400" : "text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700" ?> rounded-xl transition cursor-pointer'>
                        <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'/></svg>
                        <span>Customers</span>
                    </div>
                </a>

                <a href='/settings' class='block w-full'>
                    <div class='flex items-center gap-3 px-4 py-3 <?= ($currentRoute ?? '') === "settings" ? "bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400" : "text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700" ?> rounded-xl transition cursor-pointer'>
                        <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'/><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z'/></svg>
                        <span>Settings</span>
                    </div>
                </a>
            <?php else: ?>
                <a href='/shop' class='block w-full'>
                    <div class='flex items-center gap-3 px-4 py-3 <?= ($currentRoute ?? '') === "products" ? "bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400" : "text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700" ?> rounded-xl transition cursor-pointer'>
                        <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M20.618 5.984A11.955 11.955 0 0112 3 11.955 11.955 0 013.382 5.984M15.204 5.983h.007M14.799 8.343a4 4 0 00-4.598 0M12 16a4 4 0 108 0 4 4 0 00-8 0zm0 0v1m0 0h.008v.008H12v-.008z'/></svg>
                        <span>Shop</span>
                    </div>
                </a>

                <a href='/orders' class='block w-full'>
                    <div class='flex items-center gap-3 px-4 py-3 <?= ($currentRoute ?? '') === "orders" ? "bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400" : "text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700" ?> rounded-xl transition cursor-pointer'>
                        <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'/></svg>
                        <span class='flex-1'>My Orders</span>
                    </div>
                </a>

                <a href='/returns' class='block w-full'>
                    <div class='flex items-center gap-3 px-4 py-3 <?= ($currentRoute ?? '') === "returns" ? "bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400" : "text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700" ?> rounded-xl transition cursor-pointer'>
                        <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M3.5 12h6m-6 6h12m-12-6h12m0-6h-6m6 0h6'/></svg>
                        <span>My Returns</span>
                    </div>
                </a>

                <a href='/profile' class='block w-full'>
                    <div class='flex items-center gap-3 px-4 py-3 <?= ($currentRoute ?? '') === "settings" || ($currentRoute ?? '') === "profile" ? "bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400" : "text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700" ?> rounded-xl transition cursor-pointer'>
                        <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'/></svg>
                        <span>Profile / Settings</span>
                    </div>
                </a>
            <?php endif; ?>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <div class='flex-1 ml-64 min-h-screen'>

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
                            <?= strtoupper(substr($userName ?? 'A', 0, 1)) ?>
                        </div>
                        <div class='text-left hidden sm:block'>
                            <p class='text-sm font-medium text-slate-800 dark:text-white'><?= htmlspecialchars($userName ?? 'Admin') ?></p>
                            <p class='text-xs text-slate-500 dark:text-slate-400 capitalize'><?= htmlspecialchars($role ?? 'admin') ?></p>
                        </div>
                        <svg class='w-4 h-4 text-slate-400 transition-transform peer-checked:rotate-180' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/></svg>
                    </label>
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
        <main id='main-content' class='p-8'>
            <?= $content ?>
        </main>

    </div>
</div>

<?php
    use App\Helper\Session;
    $welcomeDiscount = Session::getFlash('welcome_discount');
?>
<?php if ($welcomeDiscount): ?>
<div
    id='welcomeDiscountToast'
    class='fixed bottom-6 right-6 z-50 flex items-start gap-3 bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-700 shadow-xl rounded-xl px-5 py-4 max-w-sm'
    role='alert'
>
    <div class='shrink-0 w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center'>
        <svg class='w-4 h-4 text-emerald-600 dark:text-emerald-400' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'/>
        </svg>
    </div>
    <div class='flex-1 min-w-0'>
        <p class='text-sm font-semibold text-slate-800 dark:text-white'>Welcome offer unlocked</p>
        <p class='text-sm text-slate-600 dark:text-slate-300 mt-0.5'><?= htmlspecialchars($welcomeDiscount) ?></p>
    </div>
    <button
        onclick="document.getElementById('welcomeDiscountToast').remove()"
        class='shrink-0 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition'
        aria-label='Dismiss'
    >
        <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12'/>
        </svg>
    </button>
</div>
<script>
    setTimeout(function () {
        var t = document.getElementById('welcomeDiscountToast');
        if (t) t.remove();
    }, 8000);
</script>
<?php endif; ?>



</body>
</html>
