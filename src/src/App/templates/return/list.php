<?php
// expects: $returns (array), $returnCount (int)
use App\Helper\Permission;
use App\Helper\DateTimeHelper;

$isAdmin = Permission::isAllowed('admin');

$formatDateTime = static function ($value): array {
    return DateTimeHelper::formatDateTimeParts($value);
};

$statusBadge = static function (string $status): string {
    $normalized = strtolower($status);
    $badge = 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-200';

    if ($normalized === 'pending') {
        $badge = 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300';
    }
    if ($normalized === 'approved') {
        $badge = 'bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300';
    }
    if ($normalized === 'rejected') {
        $badge = 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-300';
    }

    return $badge;
};

$statusLabel = static function (array $return): string {
    $status = (string)($return['status'] ?? 'pending');
    return (string)($return['status_label'] ?? ucfirst(str_replace('_', ' ', $status)));
};
?>

<div class="w-full <?= $isAdmin ? 'max-w-5xl' : 'max-w-6xl' ?> space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <?php if ($isAdmin): ?>
                <nav class="mb-2 text-sm text-slate-500 dark:text-slate-400" aria-label="Breadcrumb">
                    <a href="/dashboard" class="font-medium text-slate-600 transition hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400">Dashboard</a>
                    <span class="mx-2 text-slate-400">/</span>
                    <span>Returns</span>
                </nav>
            <?php endif; ?>
            <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">
                Returns
            </h2>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-sm text-slate-500 dark:text-slate-300">
                Total: <span class="font-medium text-slate-700 dark:text-slate-100"><?= (int)$returnCount ?></span>
            </div>
        </div>
    </div>

    <div class="w-full bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="<?= $isAdmin ? 'w-full' : 'overflow-x-auto' ?>">
            <table class="w-full <?= $isAdmin ? 'table-fixed text-xs' : 'text-sm' ?>">
                <thead class="bg-slate-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <?php if ($isAdmin): ?>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Order Timestamp</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Return Timestamp</th>
                            <th class="px-3 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Return Status</th>
                            <th class="px-3 py-2.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Actions</th>
                        <?php else: ?>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Return Timestamp</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Total Amount Spent</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    <?php if (!empty($returns)): ?>
                        <?php foreach ($returns as $return): ?>
                            <?php
                                $rawStatus = (string)($return['status'] ?? 'pending');
                                $label = $statusLabel($return);
                                [$returnDate, $returnTime] = $formatDateTime($return['created_at'] ?? null);
                                [$orderDate, $orderTime] = $formatDateTime($return['order_created_at'] ?? null);
                            ?>
                            <tr class="return-row hover:bg-slate-50 dark:hover:bg-slate-700/40 transition" data-return-status="<?= htmlspecialchars($rawStatus) ?>">
                                <?php if ($isAdmin): ?>
                                    <td class="px-3 py-3 align-middle">
                                        <div class="text-sm font-medium text-slate-900 dark:text-slate-100"><?= $orderDate ?></div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400"><?= $orderTime ?></div>
                                    </td>
                                    <td class="px-3 py-3 align-middle">
                                        <div class="text-sm font-medium text-slate-900 dark:text-slate-100"><?= $returnDate ?></div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400"><?= $returnTime ?></div>
                                    </td>
                                    <td class="px-3 py-3 align-middle">
                                        <span class="return-status-badge inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold <?= $statusBadge($rawStatus) ?>">
                                            <?= htmlspecialchars($label) ?>
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-right align-middle">
                                        <div class="flex min-w-0 flex-col items-end gap-1">
                                            <div class="flex w-full flex-row flex-nowrap items-center justify-end gap-1.5">
                                                <a href="/returns/<?= (int)$return['id'] ?>"
                                                   class="inline-flex shrink-0 items-center justify-center rounded-md border border-slate-200 dark:border-slate-600 px-2 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                                    Details
                                                </a>

                                                <div class="return-status-actions inline-flex shrink-0 items-center gap-1.5" data-update-url="/returns/<?= (int)$return['id'] ?>/update">
                                                    <form method="POST" action="/returns/<?= (int)$return['id'] ?>/update" class="inline-flex">
                                                        <input type="hidden" name="redirect_to" value="/returns">
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit"
                                                            data-status-option="approved"
                                                            class="return-status-option inline-flex items-center justify-center rounded-md px-2.5 py-1.5 text-xs font-semibold transition <?= $rawStatus === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-300 dark:hover:bg-emerald-500/20 opacity-70' ?>">
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="/returns/<?= (int)$return['id'] ?>/update" class="inline-flex">
                                                        <input type="hidden" name="redirect_to" value="/returns">
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit"
                                                            data-status-option="rejected"
                                                            class="return-status-option inline-flex items-center justify-center rounded-md px-2.5 py-1.5 text-xs font-semibold transition <?= $rawStatus === 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300' : 'bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-300 dark:hover:bg-red-500/20 opacity-70' ?>">
                                                            Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                <?php else: ?>
                                    <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100">
                                        <?= htmlspecialchars((string)($return['product_name'] ?? '-')) ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                                        <div class="text-sm font-medium text-slate-900 dark:text-slate-100"><?= $returnDate ?></div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400"><?= $returnTime ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300 max-w-xs break-words">
                                        <?= htmlspecialchars((string)($return['reason'] ?? '-')) ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?= $statusBadge($rawStatus) ?>">
                                            <?= htmlspecialchars($label) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-700 dark:text-slate-200">
                                        $<?= htmlspecialchars(number_format((float)($return['total_amount_spent'] ?? 0), 2)) ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="/returns/<?= (int)$return['id'] ?>" class="text-blue-500 hover:text-blue-400">
                                            View Details
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $isAdmin ? '4' : '6' ?>" class="px-6 py-12 text-center text-slate-500 dark:text-slate-300">
                                No returns found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var approvedBaseClasses = ['bg-emerald-50', 'text-emerald-700', 'hover:bg-emerald-100', 'dark:bg-emerald-500/10', 'dark:text-emerald-300', 'dark:hover:bg-emerald-500/20'];
    var rejectedBaseClasses = ['bg-red-50', 'text-red-700', 'hover:bg-red-100', 'dark:bg-red-500/10', 'dark:text-red-300', 'dark:hover:bg-red-500/20'];
    var approvedActiveClasses = ['bg-emerald-100', 'text-emerald-700', 'dark:bg-emerald-500/20', 'dark:text-emerald-300'];
    var rejectedActiveClasses = ['bg-red-100', 'text-red-700', 'dark:bg-red-500/20', 'dark:text-red-300'];
    var pendingBadgeClasses = ['bg-amber-100', 'text-amber-700', 'dark:bg-amber-900/40', 'dark:text-amber-300'];
    var approvedBadgeClasses = ['bg-green-100', 'text-green-600', 'dark:bg-green-900/40', 'dark:text-green-300'];
    var rejectedBadgeClasses = ['bg-red-100', 'text-red-600', 'dark:bg-red-900/40', 'dark:text-red-300'];
    var allBadgeClasses = pendingBadgeClasses.concat(approvedBadgeClasses, rejectedBadgeClasses, ['bg-slate-100', 'text-slate-600', 'dark:bg-slate-700', 'dark:text-slate-200']);

    function setButtonState(toggle, status) {
        toggle.querySelectorAll('.return-status-option').forEach(function (button) {
            var option = button.dataset.statusOption;
            button.classList.remove.apply(button.classList, approvedActiveClasses.concat(rejectedActiveClasses, approvedBaseClasses, rejectedBaseClasses, ['opacity-70']));

            if (option === 'approved' && status === 'approved') {
                button.classList.add.apply(button.classList, approvedActiveClasses);
                return;
            }

            if (option === 'rejected' && status === 'rejected') {
                button.classList.add.apply(button.classList, rejectedActiveClasses);
                return;
            }

            button.classList.add.apply(button.classList, option === 'approved' ? approvedBaseClasses : rejectedBaseClasses);
            button.classList.add('opacity-70');
        });
    }

    function setBadgeState(row, status) {
        var badge = row.querySelector('.return-status-badge');
        if (!badge) return;

        badge.classList.remove.apply(badge.classList, allBadgeClasses);

        if (status === 'approved') {
            badge.classList.add.apply(badge.classList, approvedBadgeClasses);
            badge.textContent = 'Approved';
            return;
        }

        if (status === 'rejected') {
            badge.classList.add.apply(badge.classList, rejectedBadgeClasses);
            badge.textContent = 'Rejected';
            return;
        }

        badge.classList.add.apply(badge.classList, pendingBadgeClasses);
        badge.textContent = 'Pending';
    }

    function setRowStatus(row, status) {
        row.dataset.returnStatus = status;
        setBadgeState(row, status);
        var toggle = row.querySelector('.return-status-actions');
        if (toggle) {
            setButtonState(toggle, status);
        }
    }

    document.querySelectorAll('.return-status-actions form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            var row = form.closest('.return-row');
            var toggle = form.closest('.return-status-actions');
            var previousStatus = row ? row.dataset.returnStatus : 'pending';
            var statusInput = form.querySelector('input[name="status"]');
            var nextStatus = statusInput ? statusInput.value : '';

            if (!row || !toggle) return;
            if (['approved', 'rejected'].indexOf(nextStatus) === -1) return;

            setRowStatus(row, nextStatus);

            var body = new URLSearchParams(new FormData(form));

            fetch(toggle.dataset.updateUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: body.toString()
            })
                .then(function (response) {
                    return response.json().then(function (json) {
                        if (!response.ok || !json.success) {
                            throw new Error(json.error || 'Status update failed');
                        }
                        return json;
                    });
                })
                .then(function (json) {
                    setRowStatus(row, json.status);
                })
                .catch(function (err) {
                    setRowStatus(row, previousStatus);
                });
        });
    });
});
</script>
