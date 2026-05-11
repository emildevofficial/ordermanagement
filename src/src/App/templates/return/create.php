<?php
// expects: $order (array), optional: $error, $reason
?>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Request Return</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Request a return for this order</p>
        </div>
        <a href="/orders" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
            ← Back to Orders
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-6 py-4 rounded-xl">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <form method="POST" action="/returns/create" class="space-y-5">
            <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Return Reason <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="reason" 
                    required 
                    placeholder="Please describe why you want to return this order..."
                    class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" 
                    rows="4"
                ><?= htmlspecialchars($reason ?? '') ?></textarea>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Required field</p>
            </div>

            <div class="flex gap-3 pt-3">
                <a href="/orders" class="px-6 py-2.5 text-slate-600 dark:text-slate-300 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition font-medium">
                    Cancel
                </a>
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg transition font-medium text-center shadow-sm">
                    Submit Return Request
                </button>
            </div>
        </form>
    </div>
</div>
