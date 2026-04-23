<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Create Product</h1>
            <p class="text-sm text-slate-500 mt-1">Add new product to inventory</p>
        </div>
        <a href="/products"
           class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
           ← Back to Products
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 text-slate-900 dark:text-slate-100">
        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Product Name</label>
                <input name="name" required placeholder="Product name" class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Price ($)</label>
                    <input name="price" type="number" step="0.01" min="0" required placeholder="0.00" class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Stock Quantity</label>
                    <input name="stock" type="number" min="0" required placeholder="0" class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="/products" class="px-6 py-2.5 text-slate-600 border border-slate-300 rounded-xl hover:bg-slate-50 transition font-medium">Cancel</a>
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl transition font-medium text-center">Create Product</button>
            </div>
        </form>
    </div>
</div>
