<?php
// expects: $products (array)
?>

<div class="space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">
            Products
        </h2>
        <div class="flex items-center gap-2">
            <a href="/dashboard" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                ← Back to Dashboard
            </a>
            <a href="/products/create" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                + Add Product
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-slate-100"><?= htmlspecialchars($product['name']) ?></td>
                                <td class="px-6 py-4 text-right font-mono text-slate-700 dark:text-slate-200">$<?= number_format($product['price'], 2) ?></td>
                                <td class="px-6 py-4 text-right font-mono text-slate-700 dark:text-slate-200">
                                    <?php if ($product['stock'] == 0): ?>
                                        <span class="text-red-600 font-medium">Out of stock</span>
                                    <?php else: ?>
                                        <?= $product['stock'] ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        <?php if ($product['is_active']): ?>
                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100
                                        <?php else: ?>
                                            bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200
                                        <?php endif; ?>">
                                        <?= $product['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center gap-2 justify-end">
                                        <!-- Stock Update -->
                                        <form method="POST" action="/products/<?= (int)$product['id'] ?>/stock" class="inline-flex items-center gap-1">
                                            <input type="number" name="stock" value="<?= $product['stock'] ?>" min="0" class="w-16 h-8 px-2 border border-slate-300 dark:border-slate-600 rounded text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-white dark:bg-slate-700">
                                            <button type="submit" class="p-1 text-slate-500 hover:text-indigo-600 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </form>
                                        <!-- Toggle Status -->
                                        <form method="POST" action="/products/<?= (int)$product['id'] ?>/toggle" class="inline" onsubmit="return confirm('Toggle <?= $product['is_active'] ? 'deactivate' : 'activate' ?> this product?')">
                                            <button type="submit" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-300">
                                No products found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

