<?php
// expects: $return (array), $orderItems (array)
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100">
                Review Return #<?= (int)$return['id'] ?>
            </h1>
            <p class="text-sm text-slate-500 mt-1">Review and decide action</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/returns/<?= (int)$return['id'] ?>" class="bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-100 transition">
                Back to Return
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Return Summary -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Return Summary</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Order</label>
                    <p class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100">#<?= (int)$return['order_id'] ?></p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Customer</label>
                    <p class="mt-1 text-sm text-slate-900 dark:text-slate-100"><?= htmlspecialchars($return['customer_name'] ?? 'Customer #' . ($return['customer_id'] ?? 0)) ?></p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Order Total</label>
                    <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-slate-100">$<?= number_format((float)($return['total'] ?? 0), 2) ?></p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-300 uppercase tracking-wider">Reason</label>
                    <p class="mt-2 text-sm text-slate-900 dark:text-slate-100 bg-gray-50 dark:bg-slate-700/40 p-3 rounded-lg whitespace-pre-wrap">
                        <?= nl2br(htmlspecialchars($return['reason'] ?? '')) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-4">Order Items</h3>
            <?php if (!empty($orderItems)): ?>
                <div class="space-y-3">
                    <?php foreach ($orderItems as $item): ?>
                        <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-slate-700/30 rounded-lg">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                                <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                                    <?= htmlspecialchars(substr($item['product_name'], 0, 2)) ?>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-slate-900 dark:text-slate-100 text-sm truncate">
                                    <?= htmlspecialchars($item['product_name']) ?>
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Qty: <?= (int)$item['quantity'] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-sm text-slate-500 dark:text-slate-400 text-center py-8">No items found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Action Form -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-6">Decision</h3>
        <form method="POST" action="/returns/<?= (int)$return['id'] ?>/update" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Admin Notes</label>
                <textarea 
                    name="notes" 
                    rows="4" 
                    placeholder="Add notes about your decision..." 
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-slate-700 dark:text-slate-100 transition"
                ></textarea>
            </div>
            
            <div class="flex flex-wrap gap-3 justify-end">
                <button 
                    type="submit" 
                    name="action" 
                    value="approve"
                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white rounded-lg px-6 py-2.5 text-sm font-medium transition shadow-sm hover:shadow-md"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Approve Return (Restore Stock)
                </button>
                
                <button 
                    type="submit" 
                    name="action" 
                    value="reject"
                    class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white rounded-lg px-6 py-2.5 text-sm font-medium transition shadow-sm hover:shadow-md"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m0 0A9.008 9.008 0 0112 12a9.008 9.008 0 016.364 2.364M5.636 5.636L12 12m0 0l6.364 6.364M12 12l6.364-6.364"/>
                    </svg>
                    Reject Return
                </button>
                
                <a href="/returns/<?= (int)$return['id'] ?>" class="bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg px-6 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-100 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
