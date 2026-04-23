<?php
// expects: $returns (array), $returnCount (int)
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">
            Returns
        </h2>

        <div class="flex items-center gap-4">
            <div class="text-sm text-slate-500 dark:text-slate-300">
                Total: <span class="font-medium text-slate-700 dark:text-slate-100"><?= (int)$returnCount ?></span>
            </div>
            <a href="/dashboard"
               class="inline-flex items-center gap-2 bg-slate-600 hover:bg-slate-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
               ← Back to Dashboard
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    <?php if (!empty($returns)): ?>
                        <?php foreach ($returns as $return): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                <td class="px-6 py-4 font-medium text-slate-700 dark:text-slate-100">#<?= (int)$return['id'] ?></td>
                                <td class="px-6 py-4 text-slate-700 dark:text-slate-100">#<?= (int)$return['order_id'] ?></td>
                                <td class="px-6 py-4">
                                    <?php $status = $return['status_label'] ?? $return['status']; ?>
                                    <?php
                                        $badge = 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-200';
                                        if (stripos($status, 'Requested') !== false) $badge = 'bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300';
                                        if (stripos($status, 'Under Review') !== false) $badge = 'bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300';
                                        if (stripos($status, 'Approved') !== false) $badge = 'bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300';
                                        if (stripos($status, 'Rejected') !== false) $badge = 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-300';
                                    ?>
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?= $badge ?>">
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400"><?= htmlspecialchars($return['created_at'] ?? '') ?></td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center gap-2">
                                        <a href="/returns/<?= (int)$return['id'] ?>" class="text-blue-400 hover:text-blue-300 mr-2">
                                            👁 View
                                        </a>
                                        <?php if (($return['status'] ?? '') === 'requested'): ?>
                                            <a href="/returns/<?= (int)$return['id'] ?>/review" class="text-green-400 hover:text-green-300">
                                                ✔ Review
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-300">No returns found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
