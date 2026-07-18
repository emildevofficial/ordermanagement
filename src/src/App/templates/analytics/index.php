<?php

use App\Helper\DateTimeHelper;

$money = static fn (float|int|string|null $value): string => '$' . number_format((float)$value, 2);
$number = static fn (float|int|string|null $value): string => number_format((float)$value);
$percent = static fn (float|int|string|null $value): string => number_format((float)$value, 1) . '%';
$escape = static fn (mixed $value): string => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
$statusLabel = static fn (string $status): string => ucwords(str_replace('_', ' ', $status));

$dailyMaxRevenue = 0.0;
$dailyMaxOrders = 0;
foreach (($dailyTrend ?? []) as $day) {
    $dailyMaxRevenue = max($dailyMaxRevenue, (float)($day['revenue'] ?? 0));
    $dailyMaxOrders = max($dailyMaxOrders, (int)($day['orders'] ?? 0));
}

$monthlyMaxRevenue = 0.0;
foreach (($monthlyTrend ?? []) as $month) {
    $monthlyMaxRevenue = max($monthlyMaxRevenue, (float)($month['revenue'] ?? 0));
}

$maxTopProductQty = 0;
foreach (($productAnalytics['topSelling'] ?? []) as $product) {
    $maxTopProductQty = max($maxTopProductQty, (int)($product['quantity_sold'] ?? 0));
}

$maxTopProductRevenue = 0.0;
foreach (($productAnalytics['topRevenue'] ?? []) as $product) {
    $maxTopProductRevenue = max($maxTopProductRevenue, (float)($product['revenue'] ?? 0));
}

$maxCustomerSpend = 0.0;
foreach (($customerAnalytics['topSpenders'] ?? []) as $customer) {
    $maxCustomerSpend = max($maxCustomerSpend, (float)($customer['total_spent'] ?? 0));
}

$maxCustomerOrders = 0;
foreach (($customerAnalytics['topOrderCounts'] ?? []) as $customer) {
    $maxCustomerOrders = max($maxCustomerOrders, (int)($customer['order_count'] ?? 0));
}

$rankRows = static function (array $rows, string $nameKey, string $valueKey, float|int $maxValue, callable $formatValue, string $barClass = 'bg-indigo-500') use ($escape): void {
    if (empty($rows)) {
        echo "<div class='analytics-empty'>No data available yet.</div>";
        return;
    }

    foreach ($rows as $index => $row) {
        $rawValue = (float)($row[$valueKey] ?? 0);
        $width = $maxValue > 0 ? max(4, round(($rawValue / (float)$maxValue) * 100, 1)) : 4;
        $name = $escape($row[$nameKey] ?? 'Unknown');
        $value = $escape($formatValue($row[$valueKey] ?? 0));
        $rank = $index + 1;
        echo "
            <div class='analytics-rank-row'>
                <span class='analytics-rank-badge'>{$rank}</span>
                <div class='min-w-0'>
                    <div class='flex items-center justify-between gap-3'>
                        <span class='truncate text-sm font-semibold text-slate-700 dark:text-slate-200'>{$name}</span>
                        <span class='shrink-0 text-xs font-bold text-slate-500 dark:text-slate-400'>{$value}</span>
                    </div>
                    <div class='mt-2 h-1.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700'>
                        <div class='h-full rounded-full {$barClass}' style='width: {$width}%'></div>
                    </div>
                </div>
            </div>
        ";
    }
};
?>

<style>
    .analytics-card {
        border: 1px solid rgb(226 232 240);
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
    }
    .dark .analytics-card {
        border-color: rgb(51 65 85);
        background: rgb(30 41 59);
    }
    .analytics-empty {
        border: 1px dashed rgb(203 213 225);
        border-radius: 0.625rem;
        padding: 1.5rem;
        text-align: center;
        font-size: 0.875rem;
        color: rgb(100 116 139);
    }
    .dark .analytics-empty {
        border-color: rgb(71 85 105);
        color: rgb(148 163 184);
    }
    .analytics-rank-row {
        display: grid;
        grid-template-columns: 1.75rem minmax(0, 1fr);
        gap: 0.75rem;
        align-items: center;
        padding: 0.7rem 0;
    }
    .analytics-rank-row + .analytics-rank-row {
        border-top: 1px solid rgb(241 245 249);
    }
    .dark .analytics-rank-row + .analytics-rank-row {
        border-top-color: rgb(51 65 85 / 0.75);
    }
    .analytics-rank-badge {
        display: inline-flex;
        width: 1.5rem;
        height: 1.5rem;
        align-items: center;
        justify-content: center;
        border-radius: 0.45rem;
        background: rgb(238 242 255);
        color: rgb(79 70 229);
        font-size: 0.75rem;
        font-weight: 800;
    }
    .dark .analytics-rank-badge {
        background: rgb(79 70 229 / 0.18);
        color: rgb(129 140 248);
    }
    .analytics-status-dot {
        width: 0.55rem;
        height: 0.55rem;
        border-radius: 999px;
        display: inline-block;
    }
</style>

<div class="space-y-6">
    <div>
        <nav class="mb-2 text-sm text-slate-500 dark:text-slate-400" aria-label="Breadcrumb">
            <a href="/dashboard" class="font-medium text-slate-600 transition hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400">Dashboard</a>
            <span class="mx-2">/</span>
            <span>Analytics</span>
        </nav>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Detailed Analytics</h2>
                <!-- <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Business health across orders, inventory, customers, and returns.</p> -->
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-6">
        <?php
        $kpis = [
            ['label' => 'Revenue', 'value' => $money($summary['revenue'] ?? 0), 'note' => 'Completed and delivered'],
            ['label' => 'Orders', 'value' => $number($summary['total_orders'] ?? 0), 'note' => 'All statuses'],
            ['label' => 'Avg Order Value', 'value' => $money($summary['average_order_value'] ?? 0), 'note' => 'Finalized orders'],
            ['label' => 'Return Rate', 'value' => $percent($summary['return_rate'] ?? 0), 'note' => ($summary['total_returns'] ?? 0) . ' returns in total'],
            ['label' => 'Active Products', 'value' => $number($summary['active_products'] ?? 0), 'note' => 'Visible inventory'],
            ['label' => 'Stock Risk', 'value' => $number($summary['inventory_risk_count'] ?? 0), 'note' => 'Low or out of stock'],
        ];
        foreach ($kpis as $kpi):
        ?>
        <div class="analytics-card p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"><?= $escape($kpi['label']) ?></p>
            <p class="mt-3 text-2xl font-bold tracking-normal text-slate-900 dark:text-white"><?= $escape($kpi['value']) ?></p>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400"><?= $escape($kpi['note']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <section class="analytics-card p-5 xl:col-span-2">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Last 7 Days</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Orders and revenue by day.</p>
                </div>
                <label class="sr-only" for="dailyTrendMetric">Daily trend metric</label>
                <select
                    id="dailyTrendMetric"
                    class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/30 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200"
                >
                    <option value="orders">Orders</option>
                    <option value="revenue">Revenue</option>
                </select>
            </div>

            <?php if ($dailyMaxRevenue <= 0 && $dailyMaxOrders <= 0): ?>
                <div class="analytics-empty">No order activity in the last 7 days.</div>
            <?php else: ?>
                <div class="grid h-72 grid-cols-7 items-end gap-3">
                    <?php foreach (($dailyTrend ?? []) as $day):
                        $dayRevenue = (float)($day['revenue'] ?? 0);
                        $dayOrders = (int)($day['orders'] ?? 0);
                        $revenueHeight = ($dailyMaxRevenue > 0 && $dayRevenue > 0) ? max(8, round(($dayRevenue / $dailyMaxRevenue) * 100, 1)) : 0;
                        $ordersHeight = ($dailyMaxOrders > 0 && $dayOrders > 0) ? max(8, round(($dayOrders / $dailyMaxOrders) * 100, 1)) : 0;
                    ?>
                    <div class="flex h-full min-w-0 flex-col justify-end gap-2">
                        <div class="flex flex-1 items-end justify-center">
                            <div
                                class="hidden w-4 rounded-t bg-indigo-500"
                                data-daily-trend-bar="revenue"
                                style="height: <?= $revenueHeight ?>%"
                                title="Revenue <?= $money($dayRevenue) ?>"
                            ></div>
                            <div
                                class="w-4 rounded-t bg-emerald-500"
                                data-daily-trend-bar="orders"
                                style="height: <?= $ordersHeight ?>%"
                                title="<?= $dayOrders ?> orders"
                            ></div>
                        </div>
                        <div class="text-center">
                            <p class="truncate text-xs font-semibold text-slate-600 dark:text-slate-300"><?= $escape($day['label']) ?></p>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500" data-daily-trend-value="orders"><?= $dayOrders ?> ord</p>
                            <p class="hidden text-[11px] text-indigo-500 dark:text-indigo-300" data-daily-trend-value="revenue"><?= $money($dayRevenue) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-4 flex items-center gap-4 text-xs font-medium text-slate-500 dark:text-slate-400">
                    <span class="hidden items-center gap-1.5" data-daily-trend-legend="revenue"><span class="h-2 w-2 rounded-full bg-indigo-500"></span>Revenue</span>
                    <span class="inline-flex items-center gap-1.5" data-daily-trend-legend="orders"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Orders</span>
                </div>
            <?php endif; ?>
        </section>

        <section class="analytics-card p-5">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Order Performance</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Status mix and conversion quality.</p>

            <div class="mt-5 space-y-3">
                <?php if (empty($orderStatus['rows'])): ?>
                    <div class="analytics-empty">No orders found.</div>
                <?php else: ?>
                    <?php foreach ($orderStatus['rows'] as $row):
                        $colors = [
                            'completed' => 'bg-emerald-500',
                            'delivered' => 'bg-teal-500',
                            'processing' => 'bg-sky-500',
                            'shipped' => 'bg-indigo-500',
                            'pending' => 'bg-amber-500',
                            'cancelled' => 'bg-rose-500',
                        ];
                        $color = $colors[$row['status']] ?? 'bg-slate-400';
                    ?>
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3 text-sm">
                            <span class="inline-flex min-w-0 items-center gap-2 font-medium text-slate-700 dark:text-slate-200"><span class="analytics-status-dot <?= $color ?>"></span><?= $escape($statusLabel($row['status'])) ?></span>
                            <span class="shrink-0 text-xs font-bold text-slate-500 dark:text-slate-400"><?= (int)$row['count'] ?> / <?= $percent($row['percentage']) ?></span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                            <div class="h-full rounded-full <?= $color ?>" style="width: <?= max(3, (float)$row['percentage']) ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <section class="analytics-card p-5">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Monthly revenue in the last 6 months</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Finalized order revenue by month.</p>
            <div class="mt-5 space-y-3">
                <?php if ($monthlyMaxRevenue <= 0): ?>
                    <div class="analytics-empty">No finalized revenue yet.</div>
                <?php else: ?>
                    <?php foreach (($monthlyTrend ?? []) as $month):
                        $width = $monthlyMaxRevenue > 0 ? max(4, round(((float)$month['revenue'] / $monthlyMaxRevenue) * 100, 1)) : 4;
                    ?>
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-700 dark:text-slate-200"><?= $escape($month['label']) ?></span>
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400"><?= $money($month['revenue']) ?></span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                            <div class="h-full rounded-full bg-indigo-500" style="width: <?= $width ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="analytics-card p-5">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Highest value orders that were canceled</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Largest orders by total value.</p>
            <div class="mt-4 divide-y divide-slate-100 dark:divide-slate-700">
                <?php if (empty($recentHighValueOrders)): ?>
                    <div class="analytics-empty">No orders found.</div>
                <?php else: ?>
                    <?php foreach ($recentHighValueOrders as $order): ?>
                    <a href="/orders/<?= (int)$order['id'] ?>" class="flex items-center justify-between gap-3 py-3 transition hover:text-indigo-600 dark:hover:text-indigo-400">
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold text-slate-700 dark:text-slate-200">#<?= (int)$order['id'] ?> <?= $escape($order['customer_name'] ?? 'Unknown') ?></span>
                            <span class="block text-xs text-slate-400 dark:text-slate-500"><?= $escape($statusLabel((string)$order['status'])) ?> / <?= $escape(DateTimeHelper::format($order['created_at'] ?? null, 'M d')) ?></span>
                        </span>
                        <span class="shrink-0 text-sm font-bold text-slate-900 dark:text-white"><?= $money($order['total'] ?? 0) ?></span>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="analytics-card p-5">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Inventory Risk</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Products at or below <?= (int)($lowStockThreshold ?? 2) ?> units.</p>
            <div class="mt-4 divide-y divide-slate-100 dark:divide-slate-700">
                <?php if (empty($productAnalytics['inventoryRisk'])): ?>
                    <div class="analytics-empty">Inventory levels look healthy.</div>
                <?php else: ?>
                    <?php foreach ($productAnalytics['inventoryRisk'] as $product):
                        $stock = (int)($product['stock'] ?? 0);
                        $tone = $stock === 0 ? 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300';
                    ?>
                    <div class="flex items-center justify-between gap-3 py-3">
                        <span class="truncate text-sm font-semibold text-slate-700 dark:text-slate-200"><?= $escape($product['name'] ?? 'Unknown Product') ?></span>
                        <span class="shrink-0 rounded-lg px-2 py-1 text-xs font-bold <?= $tone ?>"><?= $stock ?> left</span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <section class="analytics-card p-5">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Product Analytics</h3>
            <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Top selling products by number of orders</p>
                    <div class="mt-2">
                        <?php $rankRows($productAnalytics['topSelling'] ?? [], 'name', 'quantity_sold', $maxTopProductQty, static fn ($value) => (int)$value . ' sold', 'bg-emerald-500'); ?>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Top selling products by amount spent</p>
                    <div class="mt-2">
                        <?php $rankRows($productAnalytics['topRevenue'] ?? [], 'name', 'revenue', $maxTopProductRevenue, $money, 'bg-indigo-500'); ?>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Slow or No-Sale Products</p>
                <div class="mt-2 divide-y divide-slate-100 dark:divide-slate-700">
                    <?php if (empty($productAnalytics['slowMoving'])): ?>
                        <div class="analytics-empty">No active products found.</div>
                    <?php else: ?>
                        <?php foreach ($productAnalytics['slowMoving'] as $product): ?>
                        <div class="flex items-center justify-between gap-3 py-3">
                            <span class="truncate text-sm font-semibold text-slate-700 dark:text-slate-200"><?= $escape($product['name'] ?? 'Unknown Product') ?></span>
                            <span class="shrink-0 text-xs font-bold text-slate-500 dark:text-slate-400"><?= (int)($product['quantity_sold'] ?? 0) ?> sold / <?= (int)($product['stock'] ?? 0) ?> stock</span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="analytics-card p-5">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Customer Analytics</h3>
            <div class="mt-4 rounded-lg bg-slate-50 p-3 text-sm font-semibold text-slate-700 dark:bg-slate-900/40 dark:text-slate-200">
                <?= (int)($customerAnalytics['newCustomersThisMonth'] ?? 0) ?> new customers this month
            </div>
            <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Top Customers by amount spent</p>
                    <div class="mt-2">
                        <?php $rankRows($customerAnalytics['topSpenders'] ?? [], 'customer_name', 'total_spent', $maxCustomerSpend, $money, 'bg-cyan-500'); ?>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Top Customers by orders count</p>
                    <div class="mt-2">
                        <?php $rankRows($customerAnalytics['topOrderCounts'] ?? [], 'customer_name', 'order_count', $maxCustomerOrders, static fn ($value) => (int)$value . ' orders', 'bg-violet-500'); ?>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Top customer by number of returns</p>
                <div class="mt-2">
                    <?php $rankRows($customerAnalytics['customersWithReturns'] ?? [], 'customer_name', 'return_count', max(1, (int)($returnAnalytics['total'] ?? 0)), static fn ($value) => (int)$value . ' returns', 'bg-rose-500'); ?>
                </div>
            </div>
        </section>
    </div>

    <section class="analytics-card p-5">
        <h3 class="text-base font-bold text-slate-900 dark:text-white">Returns Analytics</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Return status mix, products tied to returned orders, and pending work.</p>
        <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">% of approved returns</p>
                <div class="mt-3 space-y-3">
                    <?php if (empty($returnAnalytics['statusRows'])): ?>
                        <div class="analytics-empty">No returns found.</div>
                    <?php else: ?>
                        <?php foreach ($returnAnalytics['statusRows'] as $row):
                            $color = match ($row['status']) {
                                'approved' => 'bg-emerald-500',
                                'rejected' => 'bg-rose-500',
                                default => 'bg-amber-500',
                            };
                        ?>
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="inline-flex items-center gap-2 font-medium text-slate-700 dark:text-slate-200"><span class="analytics-status-dot <?= $color ?>"></span><?= $escape($statusLabel($row['status'])) ?></span>
                                <span class="text-xs font-bold text-slate-500 dark:text-slate-400"><?= (int)$row['count'] ?> / <?= $percent($row['percentage']) ?></span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700">
                                <div class="h-full rounded-full <?= $color ?>" style="width: <?= max(3, (float)$row['percentage']) ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Most Returned Products</p>
                <div class="mt-2">
                    <?php $rankRows($returnAnalytics['mostReturnedProducts'] ?? [], 'name', 'return_count', max(1, (int)($returnAnalytics['total'] ?? 0)), static fn ($value) => (int)$value . ' returns', 'bg-rose-500'); ?>
                </div>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Pending Returns</p>
                <div class="mt-2 divide-y divide-slate-100 dark:divide-slate-700">
                    <?php if (empty($returnAnalytics['pendingReturns'])): ?>
                        <div class="analytics-empty">No pending returns.</div>
                    <?php else: ?>
                        <?php foreach ($returnAnalytics['pendingReturns'] as $return): ?>
                        <a href="/returns/<?= (int)$return['id'] ?>" class="flex items-center justify-between gap-3 py-3 transition hover:text-indigo-600 dark:hover:text-indigo-400">
                            <span class="truncate text-sm font-semibold text-slate-700 dark:text-slate-200">#<?= (int)$return['id'] ?> <?= $escape($return['customer_name'] ?? 'Unknown') ?></span>
                            <span class="shrink-0 text-xs font-bold text-slate-500 dark:text-slate-400"><?= $escape(DateTimeHelper::format($return['created_at'] ?? null, 'M d')) ?></span>
                        </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const metricSelect = document.getElementById('dailyTrendMetric');
    if (!metricSelect) return;

    function setDailyTrendMetric(metric) {
        document.querySelectorAll('[data-daily-trend-bar], [data-daily-trend-value], [data-daily-trend-legend]').forEach(function (element) {
            const isActive = element.dataset.dailyTrendBar === metric
                || element.dataset.dailyTrendValue === metric
                || element.dataset.dailyTrendLegend === metric;

            element.classList.toggle('hidden', !isActive);
            if (element.dataset.dailyTrendLegend) {
                element.classList.toggle('inline-flex', isActive);
            }
        });
    }

    metricSelect.addEventListener('change', function () {
        setDailyTrendMetric(metricSelect.value);
    });

    setDailyTrendMetric(metricSelect.value);
});
</script>
