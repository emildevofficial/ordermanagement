<?php // expects: $order ?>
<?php
use App\Helper\DateTimeHelper;

[$createdDate, $createdTime] = DateTimeHelper::formatDateTimeParts($order['created_at'] ?? null);
[$updatedDate, $updatedTime] = DateTimeHelper::formatDateTimeParts($order['updated_at'] ?? null);
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                Order Details
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Status: <span class="px-2 py-1 rounded-full text-xs font-medium
                    <?php if ($order['status'] === 'pending'): ?>
                        bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200
                    <?php elseif ($order['status'] === 'processing'): ?>
                        bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200
                    <?php elseif ($order['status'] === 'shipped'): ?>
                        bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200
                    <?php elseif ($order['status'] === 'delivered'): ?>
                        bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200
                    <?php elseif ($order['status'] === 'cancelled'): ?>
                        bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200
                    <?php endif; ?>">
                    <?= ucfirst($order['status']) ?>
                </span>
            </p>
        </div>
        <div class="flex gap-3">
            <a href="/orders/<?= (int)$order['id'] ?>/edit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                Edit Order
            </a>
            <a href="/orders" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                Back to Orders
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- ORDER DETAILS -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200 dark:border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Order Details</h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Date</dt>
                    <dd class="text-lg text-slate-900 dark:text-slate-100"><?= $createdDate ?> <?= $createdTime ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Total</dt>
                    <dd class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">$<?= number_format((float)$order['total'], 2) ?></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Updated</dt>
                    <dd class="text-sm text-slate-600 dark:text-slate-400"><?= !empty($order['updated_at']) ? $updatedDate . ' ' . $updatedTime : 'Never' ?></dd>
                </div>
            </dl>
        </div>

        <!-- CUSTOMER INFO -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200 dark:border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Customer</h3>
            <div class="space-y-2">
                <div>
                    <span class="text-sm text-slate-500 dark:text-slate-400">Name</span>
                    <div class="font-semibold text-slate-900 dark:text-slate-100"><?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></div>
                </div>
                <div>
                    <span class="text-sm text-slate-500 dark:text-slate-400">Email</span>
                    <div class="font-medium text-slate-700 dark:text-slate-200"><?= htmlspecialchars($order['customer_email'] ?? 'N/A') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- NOTES SECTION -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Notes</h3>
        <?php if (trim($order['notes'] ?? '') === ''): ?>
            <p class="text-slate-500 dark:text-slate-400 italic text-sm">No notes added yet.</p>
        <?php else: ?>
            <div class="bg-slate-50 dark:bg-slate-700 p-4 rounded-lg border border-slate-200 dark:border-slate-600">
                <p class="whitespace-pre-wrap text-slate-900 dark:text-slate-100"><?= htmlspecialchars($order['notes']) ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- ACTIONS -->
    <div class="flex gap-3">
        <a href="/orders/<?= (int)$order['id'] ?>/edit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition">
            Update Status & Notes
        </a>
        <?php if (!empty($order['can_cancel'])): ?>
            <form method="POST" action="/orders/<?= (int)$order['id'] ?>/cancel" class="inline" onsubmit="return confirm('Cancel this order? Status will be set to Cancelled.');">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition">
                    Cancel Order
                </button>
            </form>
        <?php endif; ?>
        <?php if (!empty($order['can_return'])): ?>
            <a href="/returns/create?order_id=<?= (int)$order['id'] ?>" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition">
                Request Return
            </a>
        <?php endif; ?>
    </div>
</div>

