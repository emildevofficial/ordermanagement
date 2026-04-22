<div class="space-y-6">

    <div>
        <h1 class="text-xl font-bold text-slate-900">
            Create Order
        </h1>
        <p class="text-sm text-slate-500 mt-1">Create a new order</p>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" class="space-y-4">

            <!-- CUSTOMER -->
            <div>
                <label class="block text-sm text-slate-600 mb-1">
                    Customer
                </label>

                <select
                    name="customer_id"
                    class="w-full rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 transition p-2 text-slate-900"
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

            <!-- STATUS -->
            <div>
                <label class="block text-sm text-slate-600 mb-1">
                    Status
                </label>

                <select
                    name="status"
                    class="w-full rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 transition p-2 text-slate-900"
                >
                    <option value="pending" selected>Pending</option>
                    <option value="processing">Processing</option>
                </select>
            </div>

            <!-- TOTAL -->
            <div>
                <label class="block text-sm text-slate-600 mb-1">
                    Total Amount
                </label>

                <input
                    type="number"
                    name="total"
                    min="0"
                    step="0.01"
                    placeholder="0.00"
                    class="w-full rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 transition p-2 text-slate-900"
                    required
                >
            </div>

            <!-- BUTTONS -->
            <div class="flex gap-2 pt-2">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2">
                    Create Order
                </button>

                <a href="/orders" class="bg-slate-200 hover:bg-slate-300 rounded-lg px-4 py-2 text-slate-700">
                    Cancel
                </a>
            </div>

        </form>
    </div>

</div>
