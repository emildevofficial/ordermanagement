<?php
// expects: $product (array), optional $error
?>
<div class="max-w-[400px] ml-4 space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Edit Product</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Modify product details</p>
        </div>
        <a href="/products"
           class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
           ← Back to Products
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="w-[340px] max-w-full bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5">
        <form method="POST" class="w-[272px] max-w-full space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Product Name</label>
                <input name="name" required value="<?= htmlspecialchars($product['name'] ?? '') ?>" placeholder="Product name" class="w-[272px] max-w-full px-3 py-1.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Price ($)</label>
                <input name="price" type="number" step="0.01" min="0" required value="<?= number_format((float)($product['price'] ?? 0), 2) ?>" class="w-[272px] max-w-full px-3 py-1.5 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>

            <div class="flex items-center justify-end gap-2 pt-1">
                <a href="/products" class="inline-flex items-center justify-center whitespace-nowrap px-3 py-2 text-slate-600 dark:text-slate-300 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition font-medium">Cancel</a>
                <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg transition font-medium text-center shadow-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>
