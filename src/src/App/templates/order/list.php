<?php
// expects: $orders (array), $orderCount (int)
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-slate-100">Orders</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-300">
                Manage and track all order records.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="/dashboard"
               class="bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium transition">
                ← Back to Dashboard
            </a>
            <a href="/orders/create"
               class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                + Add Order
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <div class="flex items-center justify-end mb-4">
            <span class="text-sm text-gray-500 dark:text-slate-300">
                Total: <?= (int)$orderCount ?>
            </span>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-slate-700">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">ID</th>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Status</th>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-slate-100">#<?= (int)$order['id'] ?></td>

                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-slate-100">
                                        <?= htmlspecialchars((string)($order['customer_name'] ?? ('Customer #' . (int)($order['customer_id'] ?? 0)))) ?>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-slate-300">
                                        <?= htmlspecialchars((string)($order['customer_email'] ?? '')) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-slate-100">
                                    <?= htmlspecialchars((string)($order['product_name'] ?? '-')) ?>
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-gray-700 dark:text-slate-200">
                                    <?= (int)($order['quantity'] ?? 0) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php $status = (string)($order['status'] ?? 'pending'); ?>

                                    <?php
                                        $badge = 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-200';
                                        if ($status === 'processing') $badge = 'bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300';
                                        if ($status === 'shipped') $badge = 'bg-purple-100 text-purple-600 dark:bg-purple-900/40 dark:text-purple-300';
                                        if ($status === 'delivered') $badge = 'bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300';
                                        if ($status === 'cancelled') $badge = 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-300';
                                    ?>
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?= $badge ?>">
                                        <?= htmlspecialchars(ucfirst($status)) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-slate-200">
                                    $<?= htmlspecialchars(number_format((float)($order['total'] ?? 0), 2)) ?>
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-slate-200">
                                    <?= htmlspecialchars(date('M d, Y', strtotime((string)($order['created_at'] ?? 'now')))) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2 items-center">
                                        <a href="/orders/<?= (int)$order['id'] ?>"
                                           class="text-slate-500 hover:text-indigo-600 cursor-pointer transition"
                                           title="View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.01 9.963 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.01-9.964-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>
                                        <a href="/orders/<?= (int)$order['id'] ?>/edit"
                                           class="text-slate-500 hover:text-indigo-600 cursor-pointer transition"
                                           title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                            </svg>
                                        </a>
                                        <?php if (($order['status'] ?? '') !== 'cancelled'): ?>
                                            <form method="POST" action="/orders/<?= (int)$order['id'] ?>/cancel" class="inline" onsubmit="return confirm('Cancel this order?');">
                                                <button type="submit"
                                                        class="text-red-500 hover:text-red-600 cursor-pointer transition"
                                                        title="Cancel">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 hover:text-red-600 transition" fill="currentColor" viewBox="0 0 24 24">
                                                      <path d="M6 7h12l-1 14H7L6 7zm3-3h6l1 2H8l1-2z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>

                            <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-slate-300">
                                No orders found.
                            </td>

                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
