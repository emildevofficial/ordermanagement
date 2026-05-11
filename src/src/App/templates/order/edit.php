<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-slate-100">
            Edit Order
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-slate-300">
            Update order status and notes.
        </p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <form method="POST" action="/orders/<?= (int)$order['id'] ?>/edit" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-2">
                    Status
                </label>
                <select
                    name="status"
                    class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 px-3 py-2"
                >
                    <option value="pending" <?= ($order['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="completed" <?= ($order['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= ($order['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-2">
                    Notes
                </label>
                <textarea
                    name="notes"
                    rows="5"
                    class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 px-3 py-2"
                    placeholder="Add internal notes for this order..."
                ><?= htmlspecialchars((string)($order['notes'] ?? '')) ?></textarea>
            </div>

            <!-- Product -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-2">
                    Product
                </label>
                <select
                    name="product_id"
                    class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 px-3 py-2"
                    required
                >
                    <option value="">Select Product</option>
                    <?php foreach ($products ?? [] as $product): ?>
                        <option value="<?= (int)$product['id'] ?>" <?= $currentProductId === (int)$product['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string)$product['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-2">
                    Quantity
                </label>
                <input
                    type="number"
                    name="quantity"
                    min="1"
                    value="<?= (int)($currentQuantity ?? 0) ?>"
                    class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 px-3 py-2"
                    required
                >
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <?php if ($_GET['error'] === 'no_stock'): ?>
                        Insufficient stock available.
                    <?php elseif ($_GET['error'] === 'invalid_product'): ?>
                        Please select a valid product and quantity.
                    <?php elseif ($_GET['error'] === 'update_failed'): ?>
                        Update failed. Please try again.
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="flex items-center gap-3">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                    Save Changes
                </button>
                <a href="/orders/<?= (int)$order['id'] ?>" class="bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-100 rounded-lg px-4 py-2 text-sm font-medium transition">
                    Back
                </a>
            </div>
        </form>
    </div>
</div>
