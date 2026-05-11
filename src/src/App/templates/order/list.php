<?php
// expects: $orders (array), $orderCount (int)
use App\Helper\Permission;
use App\Helper\DateTimeHelper;

$isAdmin = Permission::isAllowed('admin');
$returnOrderId = null;

if (!$isAdmin) {
    foreach ($orders as $orderOption) {
        if (empty($orderOption['has_return'])) {
            $returnOrderId = (int)$orderOption['id'];
            break;
        }
    }
}
?>

<div class="w-full lg:w-[72rem] lg:max-w-full px-0 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-slate-100">Orders</h1>
        </div>

        <div class="flex items-center gap-2">
            <?php if ($isAdmin): ?>
            <a href="/dashboard"
               class="bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 px-4 py-2 rounded-lg text-sm font-medium transition">
                ← Back to Dashboard
            </a>
            <?php endif; ?>
            <?php if (!$isAdmin): ?>
            <button type="button"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-500"
                title="Return Order"
                data-return-modal-open
                data-order-id="<?= (int)($returnOrderId ?? 0) ?>">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                </svg>
                <span>Return Order</span>
            </button>
            <a href="/shop"
               class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                + Shop Products
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="w-full ml-6 bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <div class="flex items-center justify-end mb-4">
            <span class="text-sm text-gray-500 dark:text-slate-300">
                Total: <?= (int)$orderCount ?>
            </span>
        </div>

        <div class="m-0 rounded-lg border border-gray-200 dark:border-slate-700">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-700">
                    <tr>
                        <?php if ($isAdmin): ?>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Order Timestamp</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Customer</th>
                        <?php else: ?>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Ordered At</th>
                        <?php endif; ?>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Quantity</th>
                        <?php if ($isAdmin): ?>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">TOTAL ($)</th>
                        <?php else: ?>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">TOTAL ($)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Status</th>
                        <?php endif; ?>
                        <?php if ($isAdmin): ?>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition" data-order-row data-order-id="<?= (int)$order['id'] ?>" data-order-status="<?= htmlspecialchars((string)($order['status'] ?? 'pending')) ?>">
                                <?php if ($isAdmin): ?>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-slate-100">
                                        <?= DateTimeHelper::format($order['created_at'] ?? null, 'M d, Y') ?>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400">
                                        <?= !empty($order['created_at']) ? DateTimeHelper::format($order['created_at'], 'h:i A') : '' ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-slate-100">
                                        <?= htmlspecialchars((string)($order['customer_name'] ?? 'Unknown Customer')) ?>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-slate-300">
                                        <?= htmlspecialchars((string)($order['customer_email'] ?? '')) ?>
                                    </div>
                                </td>
                                <?php else: ?>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-slate-100">
                                        <?= DateTimeHelper::format($order['created_at'] ?? null, 'M d, Y') ?>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400">
                                        <?= !empty($order['created_at']) ? DateTimeHelper::format($order['created_at'], 'h:i A') : '' ?>
                                    </div>
                                </td>
                                <?php endif; ?>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-slate-100">
                                    <?= htmlspecialchars((string)($order['product_name'] ?? '-')) ?>
                                </td>
                                <td class="px-6 py-4 text-left font-mono text-gray-700 dark:text-slate-200">
                                    <?= (int)($order['quantity'] ?? 0) ?>
                                </td>
                                <?php if (!$isAdmin): ?>
                                <td class="px-6 py-4 text-gray-700 dark:text-slate-200">
                                    <?= htmlspecialchars(number_format((float)($order['total'] ?? 0), 2)) ?>
                                </td>
                                <?php endif; ?>
                                <td class="px-6 py-4">
                                    <?php $status = (string)($order['status'] ?? 'pending'); ?>

                                    <?php
                                        $badge = 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-200';
                                        if ($status === 'completed') $badge = 'bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300';
                                        if ($status === 'processing') $badge = 'bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300';
                                        if ($status === 'shipped') $badge = 'bg-purple-100 text-purple-600 dark:bg-purple-900/40 dark:text-purple-300';
                                        if ($status === 'delivered') $badge = 'bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300';
                                        if ($status === 'cancelled') $badge = 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-300';
                                    ?>
                                    <span data-order-status-badge class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?= $badge ?>">
                                        <?= htmlspecialchars(ucfirst($status)) ?>
                                    </span>
                                </td>
                                <?php if ($isAdmin): ?>
                                <td class="px-6 py-4 text-gray-700 dark:text-slate-200">
                                    <?= htmlspecialchars(number_format((float)($order['total'] ?? 0), 2)) ?>
                                </td>
                                <?php endif; ?>
                                <?php if ($isAdmin): ?>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-wrap justify-center items-center gap-2">
                                        <?php if ($status === 'pending'): ?>
                                            <div class="order-status-actions inline-flex items-center gap-1.5" data-update-url="/orders/<?= (int)$order['id'] ?>/status-action">
                                                <button type="button"
                                                    data-order-status-action="complete"
                                                    class="inline-flex items-center justify-center rounded-md bg-emerald-50 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-300 dark:hover:bg-emerald-500/20">
                                                    Complete
                                                </button>
                                                <button type="button"
                                                    data-order-status-action="cancel"
                                                    class="inline-flex items-center justify-center rounded-md bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100 dark:bg-red-500/10 dark:text-red-300 dark:hover:bg-red-500/20">
                                                    Cancel
                                                </button>
                                            </div>
                                            <p class="order-status-error hidden w-full text-center text-xs font-medium text-red-600 dark:text-red-400"></p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>

                            <td colspan="<?= $isAdmin ? 7 : 5 ?>" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-slate-300">
                                No orders found.
                            </td>

                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!$isAdmin): ?>
<div id="returnRequestModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 px-4 py-6" aria-hidden="true">
    <div class="w-full max-w-lg rounded-xl border border-slate-700 bg-slate-900 text-slate-100 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-700 px-6 py-4">
            <h2 class="text-lg font-semibold">Confirm return request</h2>
            <button type="button" class="text-slate-400 transition hover:text-white" data-return-modal-close aria-label="Close return request modal">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>

        <form method="POST" action="/returns/create" class="space-y-5 px-6 py-5" id="returnRequestForm">
            <input type="hidden" name="order_id" id="returnRequestOrderId" value="">

            <label class="flex items-center gap-3 text-sm text-slate-200">
                <input type="checkbox" id="returnRequestConfirm" class="h-4 w-4 rounded border-slate-600 bg-slate-800 text-indigo-500 focus:ring-indigo-500">
                <span>I confirm my return request</span>
            </label>

            <div>
                <textarea
                    name="reason"
                    id="returnRequestReason"
                    required
                    rows="5"
                    placeholder="Please enter the respective reason"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-100 placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                ></textarea>
                <p class="mt-2 hidden text-sm text-red-400" id="returnRequestError">Reason is required.</p>
            </div>

            <div class="flex justify-center pt-1">
                <button type="submit" id="returnRequestProceed" disabled class="min-w-40 rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:cursor-not-allowed disabled:bg-slate-700 disabled:text-slate-400">
                    Proceed
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('returnRequestModal');
    var form = document.getElementById('returnRequestForm');
    var orderInput = document.getElementById('returnRequestOrderId');
    var confirmInput = document.getElementById('returnRequestConfirm');
    var reasonInput = document.getElementById('returnRequestReason');
    var proceedButton = document.getElementById('returnRequestProceed');
    var errorText = document.getElementById('returnRequestError');

    if (!modal || !form || !orderInput || !confirmInput || !reasonInput || !proceedButton || !errorText) {
        return;
    }

    function setOpen(isOpen) {
        modal.classList.toggle('hidden', !isOpen);
        modal.classList.toggle('flex', isOpen);
        modal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        document.body.classList.toggle('overflow-hidden', isOpen);
    }

    function resetForm() {
        form.reset();
        orderInput.value = '';
        errorText.classList.add('hidden');
        proceedButton.disabled = true;
    }

    document.querySelectorAll('[data-return-modal-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            resetForm();
            orderInput.value = button.dataset.orderId || '';
            setOpen(true);
            reasonInput.focus();
        });
    });

    document.querySelectorAll('[data-return-modal-close]').forEach(function (button) {
        button.addEventListener('click', function () {
            setOpen(false);
            resetForm();
        });
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            setOpen(false);
            resetForm();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            setOpen(false);
            resetForm();
        }
    });

    confirmInput.addEventListener('change', function () {
        proceedButton.disabled = !confirmInput.checked;
    });

    reasonInput.addEventListener('input', function () {
        if (reasonInput.value.trim() !== '') {
            errorText.classList.add('hidden');
        }
    });

    form.addEventListener('submit', function (event) {
        if (!confirmInput.checked || reasonInput.value.trim() === '') {
            event.preventDefault();
            if (reasonInput.value.trim() === '') {
                errorText.classList.remove('hidden');
                reasonInput.focus();
            }
        }
    });
});
</script>
<?php endif; ?>

<?php if ($isAdmin): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const badgeClasses = [
        'bg-slate-100', 'text-slate-600', 'dark:bg-slate-700', 'dark:text-slate-200',
        'bg-green-100', 'text-green-600', 'dark:bg-green-900/40', 'dark:text-green-300',
        'bg-red-100', 'text-red-600', 'dark:bg-red-900/40', 'dark:text-red-300',
        'bg-blue-100', 'text-blue-600', 'dark:bg-blue-900/40', 'dark:text-blue-300',
        'bg-purple-100', 'text-purple-600', 'dark:bg-purple-900/40', 'dark:text-purple-300'
    ];
    function statusLabel(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    }

    function setBadge(row, status) {
        const badge = row.querySelector('[data-order-status-badge]');
        if (!badge) return;

        badge.classList.remove.apply(badge.classList, badgeClasses);
        if (status === 'completed') {
            badge.classList.add('bg-green-100', 'text-green-600', 'dark:bg-green-900/40', 'dark:text-green-300');
        } else if (status === 'cancelled') {
            badge.classList.add('bg-red-100', 'text-red-600', 'dark:bg-red-900/40', 'dark:text-red-300');
        } else {
            badge.classList.add('bg-slate-100', 'text-slate-600', 'dark:bg-slate-700', 'dark:text-slate-200');
        }
        badge.textContent = statusLabel(status);
    }

    function messageFromError(errorResult) {
        if (typeof errorResult === 'string') return errorResult;
        if (errorResult && typeof errorResult.message === 'string') return errorResult.message;
        return 'Could not update order status.';
    }

    function setRowStatus(row, status) {
        row.dataset.orderStatus = status;
        setBadge(row, status);
        const actions = row.querySelector('.order-status-actions');
        if (actions && status !== 'pending') {
            actions.classList.add('hidden');
        } else if (actions) {
            actions.classList.remove('hidden');
        }
    }

    document.querySelectorAll('[data-order-status-action]').forEach(function (button) {
        button.addEventListener('click', function () {
            const actions = button.closest('.order-status-actions');
            const row = button.closest('[data-order-row]');
            if (!actions || !row) return;

            const error = row.querySelector('.order-status-error');
            const previousStatus = row.dataset.orderStatus || 'pending';
            const action = button.dataset.orderStatusAction || '';
            const nextStatus = action === 'complete' ? 'completed' : 'cancelled';

            if (error) {
                error.textContent = '';
                error.classList.add('hidden');
            }

            actions.querySelectorAll('[data-order-status-action]').forEach(function (option) {
                option.disabled = true;
                option.classList.add('opacity-60', 'cursor-not-allowed');
            });
            setRowStatus(row, nextStatus);

            const body = new URLSearchParams();
            body.set('action', action);

            fetch(actions.dataset.updateUrl, {
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
                            throw new Error(messageFromError(json.error));
                        }
                        return json;
                    });
                })
                .then(function (json) {
                    setRowStatus(row, json.status || nextStatus);
                })
                .catch(function (errorResult) {
                    setRowStatus(row, previousStatus);
                    if (error) {
                        error.textContent = messageFromError(errorResult);
                        error.classList.remove('hidden');
                    }
                })
                .finally(function () {
                    actions.querySelectorAll('[data-order-status-action]').forEach(function (option) {
                        option.disabled = row.dataset.orderStatus !== 'pending';
                        option.classList.toggle('opacity-60', option.disabled);
                        option.classList.toggle('cursor-not-allowed', option.disabled);
                    });
                });
        });
    });
});
</script>
<?php endif; ?>
