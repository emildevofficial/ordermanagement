<?php
// expects: $customer (array), $orders (array), $orderItems (array), $customerOrderSummary (array)
use App\Helper\DateTimeHelper;

$summary = $customerOrderSummary ?? [];

$formatDateTime = static function ($value): array {
    return DateTimeHelper::formatDateTimeParts($value);
};

$statusBadge = static function (string $status): string {
    $badge = 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-200';
    if ($status === 'processing') {
        $badge = 'bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300';
    }
    if ($status === 'shipped') {
        $badge = 'bg-purple-100 text-purple-600 dark:bg-purple-900/40 dark:text-purple-300';
    }
    if ($status === 'delivered' || $status === 'completed') {
        $badge = 'bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300';
    }
    if ($status === 'cancelled') {
        $badge = 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-300';
    }

    return $badge;
};

[$latestOrderDate, $latestOrderTime] = $formatDateTime($summary['latest_order_timestamp'] ?? null);
?>

<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">
                Customer Order Details
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-300 mt-1">
                Customer profile, order totals, and purchase history.
            </p>
        </div>

        <a href="/customers"
           class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition">
            &larr; Back to Customers
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-3 sm:p-4">
        <div class="mb-3">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100 uppercase tracking-wider">
                Customer Information
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Order activity overview
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-x-8 gap-y-3">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Customer Name</p>
                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?= htmlspecialchars((string)($customer['name'] ?? 'Unknown')) ?></p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Total Orders Count</p>
                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?= (int)($summary['total_orders'] ?? count($orders)) ?></p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Total Products Ordered</p>
                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?= (int)($summary['total_products_ordered'] ?? 0) ?></p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Total Amount Spent</p>
                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">$<?= number_format((float)($summary['total_amount_spent'] ?? 0), 2) ?></p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Latest Order Timestamp</p>
                <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?= $latestOrderDate ?></p>
                <?php if ($latestOrderTime !== ''): ?>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5"><?= $latestOrderTime ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="px-5 sm:px-6 py-3 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Order Information / Order History</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Products, quantities, totals, and order status.</p>
        </div>

        <?php if (!empty($orders)): ?>
            <div class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($orders as $order): ?>
                    <?php
                        $items = $orderItems[$order['id']] ?? [];
                        $status = (string)($order['status'] ?? 'pending');
                        [$orderDate, $orderTime] = $formatDateTime($order['created_at'] ?? null);
                    ?>
                    <div class="px-5 sm:px-6 py-3">
                        <div class="grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-x-8 gap-y-3">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Order Timestamp</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?= $orderDate ?></p>
                                    <?php if ($orderTime !== ''): ?>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5"><?= $orderTime ?></p>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Order Status</p>
                                    <p class="mt-1">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?= $statusBadge($status) ?>">
                                            <?= htmlspecialchars(ucfirst($status)) ?>
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Total Amount Spent</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">$<?= number_format((float)($order['total'] ?? 0), 2) ?></p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Ordered Quantity</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">
                                        <?php
                                            $orderQuantity = 0;
                                            foreach ($items as $item) {
                                                $orderQuantity += (int)($item['quantity'] ?? 0);
                                            }
                                        ?>
                                        <?= $orderQuantity ?>
                                    </p>
                                </div>
                            </div>

                            <div class="lg:min-w-[15rem]">
                                <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Ordered Product(s)</p>
                                <?php if (!empty($items)): ?>
                                    <div class="mt-1 space-y-1">
                                        <?php foreach ($items as $item): ?>
                                            <p class="text-sm font-medium text-slate-900 dark:text-slate-100">
                                                <?= htmlspecialchars((string)($item['product_name'] ?? 'Unknown Product')) ?>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">No items found for this order.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="px-6 py-8 text-center">
                <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="text-slate-500 dark:text-slate-400 font-medium">No orders found for this customer.</p>
                <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Order details will appear here once the customer places orders.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
