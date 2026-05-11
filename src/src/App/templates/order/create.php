<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-slate-100">
            Create Order
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-slate-300">
            Add a new order to the system.
        </p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6">
        <form method="POST" class="space-y-6">
            <?php if (isset($error)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 dark:bg-red-900/20 dark:border-red-800 dark:text-red-200">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-2">
                    Customer
                </label>
                <select
                    name="customer_id"
                    class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 px-3 py-2"
                    required
                >
                    <option value="">Select customer...</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?= $c['id'] ?>">
                            <?= htmlspecialchars($c['name'] . ' - ' . $c['email']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-2">
                    Product
                </label>
                <select
                    name="product_id"
                    class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 px-3 py-2"
                    required
                >
                    <option value="">Select product...</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>">
                            <?= htmlspecialchars($p['name']) ?> ($<?= number_format($p['price'], 2) ?>)
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
                    class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 px-3 py-2"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-2">
                    Status
                </label>
                <select
                    name="status"
                    class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 px-3 py-2"
                >
                    <option value="pending" selected>Pending</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                    Create Order
                </button>
                <a href="/orders" class="bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-100 rounded-lg px-4 py-2 text-sm font-medium transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
