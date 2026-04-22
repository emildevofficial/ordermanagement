<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">
                Edit Order #<?= (int)$order['id'] ?>
            </h1>
            <p class="text-sm text-slate-500 mt-1">Update order status</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="/orders/<?= (int)$order['id'] ?>/edit" class="space-y-4">

            <div>
                <label class="block text-sm text-slate-600 mb-1">
                    Status
                </label>

                <select
                    name="status"
                    class="w-full rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 transition p-2 text-slate-900"
                >
                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                    <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                    <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2">
                    Save
                </button>

                <a href="/orders" class="bg-slate-200 hover:bg-slate-300 rounded-lg px-4 py-2 text-slate-700">
                    Cancel
                </a>
            </div>

        </form>
    </div>

</div>
