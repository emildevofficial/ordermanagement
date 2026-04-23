<!-- STATS ROW -->
<div class='grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6'>
    <div class='bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm'>
        <p class='text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide'>Total Orders</p>
        <p class='text-2xl font-bold text-slate-800 dark:text-white mt-1'><?= $totalOrders ?></p>
        <p class='text-xs text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1'>↑ 0% this week</p>
    </div>
    <div class='bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm'>
        <p class='text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide'>Ordered Today</p>
        <p class='text-2xl font-bold text-slate-800 dark:text-white mt-1'><?= $ordersToday ?></p>
        <p class='text-xs text-slate-500 dark:text-slate-400 mt-1'>Orders today</p>
    </div>
    <div class='bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm'>
        <p class='text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide'>Returns</p>
        <p class='text-2xl font-bold text-slate-800 dark:text-white mt-1'><?= $totalReturns ?></p>
        <p class='text-xs text-amber-600 dark:text-amber-400 mt-1'><?= $returnRate ?>% return rate</p>
    </div>
    <div class='bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm'>
        <p class='text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide'>Pending</p>
        <p class='text-2xl font-bold text-slate-800 dark:text-white mt-1'><?= $pendingOrders ?></p>
        <p class='text-xs text-amber-600 dark:text-amber-400 mt-1'>Awaiting processing</p>
    </div>
    <div class='bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm'>
        <p class='text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide'>Revenue</p>
        <p class='text-2xl font-bold text-slate-800 dark:text-white mt-1'>$<?= number_format($revenue, 2) ?></p>
        <p class='text-xs text-emerald-600 dark:text-emerald-400 mt-1'>Excl. cancelled</p>
    </div>
</div>

<!-- FILTER BAR -->
<div class='bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 mb-6 shadow-sm'>
    <div class='flex flex-col lg:flex-row lg:items-center gap-4'>
        <div class='flex flex-wrap items-center gap-3 flex-1'>
            <select class='px-4 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none'>
                <option>Payment Status</option><option>Paid</option><option>Pending</option><option>Refunded</option>
            </select>
            <select class='px-4 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none'>
                <option>Fulfillment Status</option><option>Fulfilled</option><option>Unfulfilled</option>
            </select>
            <select class='px-4 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none'>
                <option>Delivery Status</option><option>Shipped</option><option>In Transit</option><option>Delivered</option>
            </select>
            <select class='px-4 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none'>
                <option>Delivery Method</option><option>Standard</option><option>Express</option><option>Pickup</option>
            </select>
        </div>
        <div class='relative'>
            <svg class='absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'/></svg>
            <input type='text' placeholder='Search orders...' class='pl-10 pr-4 py-2 w-full lg:w-64 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 outline-none'>
        </div>
    </div>
</div>

<!-- ORDERS TABLE -->
<div class='bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm'>
    <div class='overflow-x-auto'>
        <table class='w-full text-sm'>
            <thead class='bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-600'>
                <tr>
                    <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Order ID</th>
                    <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Date</th>
                    <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Customer</th>
                    <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Total</th>
                    <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Payment</th>
                    <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Fulfillment</th>
                    <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Items</th>
                    <th class='px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider'>Delivery</th>
                </tr>
            </thead>
            <tbody class='divide-y divide-slate-200 dark:divide-slate-700'>
                <?php if (empty($recentOrders)): ?>
                    <tr>
                        <td colspan='8' class='px-6 py-12 text-center text-slate-500 dark:text-slate-400'>
                            No orders found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr class='hover:bg-slate-50 dark:hover:bg-slate-700/40 transition'>
                            <td class='px-6 py-4 font-mono text-xs text-slate-500 dark:text-slate-400'>#<?= htmlspecialchars((string) $order['id']) ?></td>
                            <td class='px-6 py-4 text-slate-600 dark:text-slate-300'><?= htmlspecialchars(date('M j, Y', strtotime($order['created_at']))) ?></td>
                            <td class='px-6 py-4 text-slate-800 dark:text-white font-medium'><?= htmlspecialchars($order['customer_name'] ?? '—') ?></td>
                            <td class='px-6 py-4 text-slate-800 dark:text-white'>$<?= number_format((float) $order['total'], 2) ?></td>
                            <td class='px-6 py-4'>
                                <span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300'>—</span>
                            </td>
                            <td class='px-6 py-4'>
                                <?php
                                $statusClasses = [
                                    'pending'    => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
                                    'processing' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                                    'shipped'    => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400',
                                    'delivered'  => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                                    'cancelled'  => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                                ];
                                $cls = $statusClasses[$order['status']] ?? 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300';
                                ?>
                                <span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $cls ?>'>
                                    <?= ucfirst(htmlspecialchars($order['status'])) ?>
                                </span>
                            </td>
                            <td class='px-6 py-4 text-slate-500 dark:text-slate-400'>—</td>
                            <td class='px-6 py-4 text-slate-500 dark:text-slate-400'>—</td>
                        </tr>
                    <?php endforeach ?>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</div>

<!-- PAGINATION -->
<div class='flex items-center justify-between mt-6'>
    <p class='text-sm text-slate-500 dark:text-slate-400'>
        Showing <span class='font-medium text-slate-800 dark:text-white'><?= $recentOrders ? '1-' . count($recentOrders) : '0-0' ?></span>
        of <span class='font-medium text-slate-800 dark:text-white'><?= $totalOrders ?></span> orders
    </p>
    <div class='flex items-center gap-2'>
        <button class='px-4 py-2 text-sm font-medium text-slate-400 dark:text-slate-500 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg cursor-not-allowed opacity-50' disabled>Previous</button>
        <button class='px-4 py-2 text-sm font-medium text-slate-400 dark:text-slate-500 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg cursor-not-allowed opacity-50' disabled>Next</button>
    </div>
</div>