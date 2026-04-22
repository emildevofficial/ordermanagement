<?php
// expects: $orders (array), $orderCount (int)
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">
            Orders
        </h2>

        <div class="flex items-center gap-4">
            <div class="text-sm text-slate-500 dark:text-slate-300">
                Total: <span class="font-medium text-slate-700 dark:text-slate-100"><?= (int)$orderCount ?></span>
            </div>

            <a href="/orders/create"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                + Add Order
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Customer ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                <td class="px-6 py-4 font-medium text-slate-700 dark:text-slate-100">#<?= (int)$order['id'] ?></td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-slate-900 dark:text-slate-100">
                                        <?= htmlspecialchars((string)($order['customer_name'] ?? '')) ?>
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-300">
                                        <?= htmlspecialchars((string)($order['customer_email'] ?? '')) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                    <span class="inline-block px-3 py-1 text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-100 rounded-full">
                                        <?= htmlspecialchars(ucfirst((string)($order['status'] ?? ''))) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300"><?= htmlspecialchars(number_format((float)($order['total'] ?? 0), 2)) ?></td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300"><?= htmlspecialchars(date('M d, Y', strtotime($order['created_at'] ?? '')) ) ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="/orders/<?= (int)$order['id'] ?>/edit" class="p-2 text-slate-600 dark:text-slate-300 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-700 rounded-lg transition-all duration-200" title="Edit">
                                            <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 4.5M16.862 4.487L19.5 7.125" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="/orders/<?= (int)$order['id'] ?>/delete" class="inline" onsubmit="return confirm('Are you sure you want to delete this order? This action cannot be undone.');">
                                            <button type="submit" class="p-2 text-slate-600 dark:text-slate-300 hover:text-red-600 hover:bg-red-50 dark:hover:bg-slate-700 rounded-lg transition-all duration-200" title="Delete">
                                                <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.181a48.11 48.11 0 00-3.478-.397m7.5 0a48.11 48.11 0 00-7.5 0M12 9v1m0 5v2M12 9v1m0 5v2" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-300">No orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
