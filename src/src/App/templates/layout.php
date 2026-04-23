<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        };

        (function () {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>

<body class="min-h-screen bg-gray-100 dark:bg-slate-900 dark:text-slate-100 transition-colors duration-200">
<header class="bg-white dark:bg-slate-800 shadow transition-colors duration-200">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
            Order Management System
        </h1>

        <button
            id="theme-toggle"
            type="button"
            class="p-2 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition"
            aria-label="Toggle dark mode"
        ></button>
    </div>
</header>

<main class="container mx-auto px-4 py-8 text-slate-900 dark:text-slate-100 transition-colors duration-200">
    <?= $content ?? '' ?>
</main>

<?php include 'partials/footer.php'; ?>

<script>
(function () {
    const toggle = document.getElementById('theme-toggle');
    if (!toggle) return;

    function isDark() {
        return document.documentElement.classList.contains('dark');
    }

    function applyTheme(dark) {
        if (dark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
        toggle.textContent = dark ? '☀️' : '🌙';
    }

    toggle.addEventListener('click', function () {
        applyTheme(!isDark());
    });

    applyTheme(isDark());
})();
</script>
</body>
</html>
