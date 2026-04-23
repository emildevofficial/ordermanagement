<?php
// expects: $customer (array), $orders (array)
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">
                Customer Details
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-300 mt-1">
                View customer profile and related orders.
            </p>
        </div>

        <a href="/customers"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition">
            ← Back to Customers
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200 dark:border-slate-700 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Name</p>
                <p class="mt-1 text-slate-900 dark:text-slate-100 font-medium">
                    <?= htmlspecialchars((string)$customer['name']) ?>
                </p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Email</p>
                <p class="mt-1 text-slate-900 dark:text-slate-100 font-medium">
                    <?= htmlspecialchars((string)$customer['email']) ?>
                </p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Customer ID</p>
                <p class="mt-1 text-slate-900 dark:text-slate-100 font-medium">
                    #<?= (int)$customer['id'] ?>
                </p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Created At</p>
                <p class="mt-1 text-slate-900 dark:text-slate-100 font-medium">
                    <?= htmlspecialchars((string)$customer['created_at']) ?>
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Orders</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-600">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Created At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-100 font-medium">
                                    #<?= (int)$order['id'] ?>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                    <?= htmlspecialchars((string)$order['status']) ?>
                                </td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-200 font-medium">
                                    €<?= number_format((float)$order['total'], 2) ?>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                                    <?= htmlspecialchars((string)$order['created_at']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-500 dark:text-slate-300">
                                No orders found for this customer.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
