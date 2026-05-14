<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OrderManagement - Authentication</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        input:focus { outline: none; border-color: #4f46e5 !important; box-shadow: 0 0 0 3px rgba(79,70,229,0.12); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">

<?php $tab = $_GET['tab'] ?? 'login'; ?>

<!-- MAIN CARD: contains both the brand strip and the form as one element -->
<div class="bg-white rounded-2xl shadow-xl flex overflow-hidden items-stretch">

    <!-- BRAND STRIP (left) - thin, attached to the same card -->
    <div class="bg-indigo-600 w-12 flex items-center justify-center">
        <span class="text-white text-sm font-semibold -rotate-90 leading-none">OrderManagement</span>
    </div>

    <!-- FORM (right) -->
    <div class="p-8 w-full max-w-md">

        <?php if ($tab === 'register'): ?>

            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Create account</h2>
                <p class="text-sm text-gray-500 mt-1">Join OrderManagement today</p>
            </div>

            <?php if (isset($registerError)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded mb-4 text-sm">
                    <?= htmlspecialchars($registerError) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/register" class="space-y-3">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full name</label>
                    <input id="name" name="name" type="text" required placeholder="John Doe" class="w-full px-3 py-3 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label for="reg-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="reg-email" name="email" type="email" required placeholder="you@example.com" class="w-full px-3 py-3 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label for="reg-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="reg-password" name="password" type="password" required placeholder="Min. 6 characters" class="w-full px-3 py-3 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label for="confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirm password</label>
                    <input id="confirm" name="confirm" type="password" required placeholder="Repeat your password" class="w-full px-3 py-3 border border-gray-300 rounded-lg text-sm">
                </div>

                <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-base transition">Create account</button>
            </form>

            <div class="mt-4 text-sm text-gray-600">
                Already have an account? <a href="/login" class="text-indigo-600 font-medium hover:text-indigo-700">Sign in</a>
            </div>

        <?php else: ?>

            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Sign in</h2>
                <p class="text-sm text-gray-500 mt-1">Welcome back</p>
            </div>

            <?php if (isset($loginError)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded mb-4 text-sm">
                    <?= htmlspecialchars($loginError) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/login" class="space-y-3">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" required placeholder="you@example.com" class="w-full px-3 py-3 border border-gray-300 rounded-lg text-sm">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" name="password" type="password" required placeholder="Your password" class="w-full px-3 py-3 border border-gray-300 rounded-lg text-sm">
                </div>

                <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-base transition">Sign in</button>
            </form>

            <div class="mt-4 text-sm text-gray-600">
                Don't have an account? <a href="/login?tab=register" class="text-indigo-600 font-medium hover:text-indigo-700">Create account</a>
            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>
