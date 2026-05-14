<?php 
/**
 * @var string $userName
 * @var string $role
 */
$displayRoleLabel = ($role ?? '') === 'admin' ? 'Owner' : (($role ?? '') === 'user' ? 'Customer' : ucfirst((string)($role ?? '')));
?>

<div class="max-w-3xl">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-semibold text-slate-900 dark:text-white">
                Settings
            </h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                Manage your account preferences and security
            </p>
        </div>
    </div>

    <!-- Card -->
    <div class="bg-white dark:bg-slate-800 shadow rounded-xl overflow-hidden">

        <!-- Account Info -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">
                Account Information
            </h2>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-slate-500 uppercase">Full Name</p>
                    <p class="text-slate-900 dark:text-white font-medium">
                        <?= htmlspecialchars($userName ?? '') ?>
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-500 uppercase">Role</p>
                    <p class="text-slate-900 dark:text-white font-medium">
                        <?= htmlspecialchars($displayRoleLabel) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Security -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                    Security
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Change your password regularly to keep your account secure.
                </p>
            </div>

            <a href="/settings/password" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg">
                Change Password
            </a>
        </div>

        <?php if (!empty($isAdmin)): ?>
        <!-- Welcome Discount -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-1">
                Welcome Discount
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                Automatically offer new users a discount on their first order.
            </p>

            <?php if (!empty($promoSuccess)): ?>
                <div class="mb-4 flex items-center gap-2 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 text-emerald-700 dark:text-emerald-300 text-sm px-4 py-2.5 rounded-lg">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <?= htmlspecialchars($promoSuccess) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/settings" class="space-y-4">

                <!-- Toggle -->
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-200">Enable Welcome Discount</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Show discount notification to new users on registration</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input
                            type="checkbox"
                            name="new_user_discount_enabled"
                            value="1"
                            class="sr-only peer"
                            <?= !empty($promo['new_user_discount_enabled']) ? 'checked' : '' ?>
                        >
                        <div class="w-11 h-6 bg-slate-200 dark:bg-slate-600 rounded-full peer
                                    peer-checked:bg-indigo-500
                                    after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                    after:bg-white after:rounded-full after:h-5 after:w-5
                                    after:transition-all peer-checked:after:translate-x-5"></div>
                    </label>
                </div>

                <!-- Percentage -->
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-200">Discount Percentage</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Applied to the user's first order (0–100)</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <input
                            type="number"
                            name="new_user_discount_percent"
                            min="0"
                            max="100"
                            value="<?= (int)($promo['new_user_discount_percent'] ?? 0) ?>"
                            class="w-20 text-center border border-slate-300 dark:border-slate-600
                                   bg-white dark:bg-slate-700 text-slate-900 dark:text-white
                                   rounded-lg px-3 py-2 text-sm focus:outline-none
                                   focus:ring-2 focus:ring-indigo-500"
                        >
                        <span class="text-sm text-slate-500 dark:text-slate-400">%</span>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-indigo-500 hover:bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                        Save
                    </button>
                </div>

            </form>
        </div>
        <?php endif; ?>

        <!-- Logout -->
        <div class="p-6 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-red-600">
                    Logout
                </h2>
                <p class="text-sm text-slate-500">
                    You will be logged out from your account.
                </p>
            </div>

            <a href="/logout" class="border border-red-500 text-red-500 hover:bg-red-500 hover:text-white px-4 py-2 rounded-lg">
                Logout
            </a>
        </div>

    </div>
</div>
