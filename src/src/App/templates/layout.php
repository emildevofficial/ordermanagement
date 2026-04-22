<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management System</title>

    <!-- Tailwind config -->
    <script>
        tailwind.config = {
            darkMode: 'class'
        }

        // Apply saved theme BEFORE render
        (function () {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gray-100 dark:bg-slate-900 transition-colors duration-200">

<header class="bg-white dark:bg-slate-800 shadow transition-colors duration-200">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
            Order Management System
        </h1>

        <!-- ✅ BUTTON FIX -->
        <button id="theme-toggle"
            class="p-2 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition">
        </button>
    </div>
</header>

<main class="container mx-auto px-4 py-8 text-slate-900 dark:text-slate-100 transition-colors duration-200">
    <?= $content ?? '' ?>
</main>

<?php include 'partials/footer.php'; ?>

<!-- ✅ SINGLE CLEAN SCRIPT -->
<script>
(function () {
    const toggle = document.getElementById('theme-toggle');
    if (!toggle) return;

    function setTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('theme', theme);
        updateIcon();
    }

    function updateIcon() {
        const isDark = document.documentElement.classList.contains('dark');
        toggle.innerHTML = isDark ? '☀️' : '🌙';
    }

    toggle.addEventListener('click', function () {
        const isDark = document.documentElement.classList.contains('dark');
        setTheme(isDark ? 'light' : 'dark');
    });

    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);
})();
</script>

</body>
</html>