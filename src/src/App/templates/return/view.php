<?php
// expects: $return (array), $order (array), $orderItems (array)
use App\Helper\DateTimeHelper;
use App\Helper\Permission;

$formatDateTime = static function ($value): array {
    return DateTimeHelper::formatDateTimeParts($value);
};

$returnStatus = (string)($return['status'] ?? 'pending');
$isAdmin = Permission::isAllowed('admin');

$badgeForStatus = static function (string $status): string {
    $badge = 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-200';
    if ($status === 'pending') {
        $badge = 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300';
    }
    if ($status === 'approved') {
        $badge = 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300';
    }
    if ($status === 'rejected') {
        $badge = 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300';
    }

    return $badge;
};

[$returnDate, $returnTime] = $formatDateTime($return['created_at'] ?? null);
[$orderDate, $orderTime] = $formatDateTime($order['created_at'] ?? null);
$statusLabel = ucfirst(str_replace('_', ' ', $returnStatus));
$adminNotes = trim((string)($return['admin_notes'] ?? ''));
$adminNotesText = $adminNotes !== '' ? $adminNotes : ($returnStatus === 'pending' ? 'Awaiting admin decision' : $statusLabel);
$orderedItems = [];
$orderQuantity = 0;

foreach ($orderItems as $item) {
    $orderedItems[] = (string)($item['product_name'] ?? 'Unknown Product');
    $orderQuantity += (int)($item['quantity'] ?? 0);
}

$orderedItemsText = !empty($orderedItems) ? implode(', ', $orderedItems) : 'No order items found.';
?>

<div class="max-w-2xl p-8 space-y-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">
                Return Details
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                <?= $isAdmin ? 'Detailed return management and order review.' : 'Your return request and order summary.' ?>
            </p>
        </div>
        <a href="/returns"
           class="inline-flex items-center justify-center bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-100 transition">
            Back to Returns
        </a>
    </div>

    <div class="space-y-2">
        <section class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 px-3 py-2.5">
            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Return Information</h2>
            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-2 items-start">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Return Timestamp</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?= $returnDate ?></p>
                    <?php if ($returnTime !== ''): ?>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5"><?= $returnTime ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</p>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?= $badgeForStatus($returnStatus) ?>">
                        <?= htmlspecialchars($statusLabel) ?>
                    </span>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Return Reason</p>
                    <p class="mt-1 text-sm text-slate-900 dark:text-slate-100 whitespace-pre-wrap"><?= nl2br(htmlspecialchars((string)($return['reason'] ?? 'No reason provided.'))) ?></p>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Admin Notes</p>
                    <p class="mt-1 text-sm text-slate-900 dark:text-slate-100 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($adminNotesText)) ?></p>
                </div>
            </div>
        </section>

        <section class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 px-3 py-2.5">
            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Order Information</h2>
            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-2 items-start">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Order Timestamp</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?= $orderDate ?></p>
                    <?php if ($orderTime !== ''): ?>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5"><?= $orderTime ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Total Amount Spent</p>
                    <p class="mt-1 text-lg font-bold text-slate-900 dark:text-slate-100">$<?= number_format((float)($order['total'] ?? 0), 2) ?></p>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Ordered Item</p>
                    <p class="mt-1 text-sm text-slate-900 dark:text-slate-100"><?= htmlspecialchars($orderedItemsText) ?></p>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Order Quantity</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100"><?= (int)$orderQuantity ?></p>
                </div>
            </div>
        </section>
    </div>
</div>
