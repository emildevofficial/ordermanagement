<?php
// expects: $customers (array)
use App\Helper\Permission;
use App\Helper\DateTimeHelper;
?>

<div class="w-full lg:w-[40vw] lg:min-w-[40rem] lg:max-w-[48rem] px-6 py-8 space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">
            Customers
        </h2>

        <div class="flex flex-wrap items-center justify-end gap-3">
            <a href="/dashboard"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                ← Back to Dashboard
            </a>
            <?php if (!Permission::isAllowed('admin')): ?>
            <a href="/customers/create"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                + Add Customer
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="w-full bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden border border-slate-200 dark:border-slate-700">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-600">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Registered At</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-100">
                                    <a href="/customers/<?= (int)$customer['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                        <?= htmlspecialchars((string)$customer['name']) ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                    <a href="/customers/<?= (int)$customer['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                        <?= htmlspecialchars((string)$customer['email']) ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="/customers/<?= (int)$customer['id'] ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                        <div class="text-sm font-medium text-slate-700 dark:text-slate-100">
                                            <?= DateTimeHelper::format($customer['created_at'] ?? null, 'M d, Y') ?>
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                            <?= !empty($customer['created_at']) ? DateTimeHelper::format($customer['created_at'], 'h:i A') : '' ?>
                                        </div>
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="/customers/<?= (int)$customer['id'] ?>"
                                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-slate-700 rounded-lg hover:bg-indigo-100 dark:hover:bg-slate-600 transition">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-300">
                                No customers found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
