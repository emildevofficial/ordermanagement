<?php
// expects: $return (array)
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">
                Return #<?= (int)$return['id'] ?>
            </h1>
            <p class="text-sm text-slate-500 mt-1">Return details</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/returns" class="bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-100 transition">
                Back to Returns
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Order ID</p>
                <p class="mt-2 text-sm text-gray-900 dark:text-slate-100">
                    #<?= (int)$return['order_id'] ?>
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Customer</p>
                <p class="mt-2 text-sm text-gray-900 dark:text-slate-100">
                    <?= htmlspecialchars($return['customer_name'] ?? 'Customer #' . ($return['customer_id'] ?? 0)) ?>
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Status</p>
                <div class="mt-2">
                    <?php $status = ($return['status'] ?? 'requested'); ?>
                    <?php
                        $badge = 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-200';
                        if ($status === 'requested') $badge = 'bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300';
                        if ($status === 'under_review') $badge = 'bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300';
                        if ($status === 'approved') $badge = 'bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300';
                        if ($status === 'rejected') $badge = 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-300';
                    ?>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?= $badge ?>">
                        <?= htmlspecialchars(ucfirst($status)) ?>
                    </span>
                </div>
            </div>

            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Created At</p>
                <p class="mt-2 text-sm text-gray-900 dark:text-slate-100">
                    <?= htmlspecialchars($return['created_at'] ?? '') ?>
                </p>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider mb-3">Reason</p>
            <div class="rounded-lg border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/40 p-4">
                <p class="text-sm text-gray-900 dark:text-slate-100 whitespace-pre-wrap">
                    <?= nl2br(htmlspecialchars($return['reason'] ?? '')) ?>
                </p>
            </div>
        </div>

        <?php if (!empty($return['notes'])): ?>
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider mb-3">Admin Notes</p>
                <div class="rounded-lg border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/40 p-4">
                    <p class="text-sm text-gray-900 dark:text-slate-100 whitespace-pre-wrap">
                        <?= nl2br(htmlspecialchars($return['notes'])) ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
