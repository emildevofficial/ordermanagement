<?php use App\Helper\Permission; ?>
<!-- STATS ROW -->
<?php if (Permission::isAllowed('admin')): ?>
<div class='grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_minmax(0,1.55fr)_minmax(0,1fr)_minmax(0,1fr)] gap-4 mb-6 items-stretch'>
<?php else: ?>
<div class='grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6 items-stretch'>
<?php endif; ?>

    <!-- Orders Metric Selector -->
    <div class='bg-white dark:bg-slate-800 rounded-xl p-4 h-[11.5rem] border border-slate-200 dark:border-slate-700 shadow-sm relative flex flex-col overflow-hidden'>

        <div class='relative inline-block shrink-0'>
            <button
                id='ordersMetricTrigger'
                class='flex items-center gap-1 text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide hover:text-slate-700 dark:hover:text-slate-200 transition-colors cursor-pointer select-none'
                aria-haspopup='listbox'
                aria-expanded='false'
            >
                <span id='ordersMetricLabel'>Total Orders</span>
                <svg id='ordersMetricChevron' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'
                     class='w-3 h-3 transition-transform duration-200'>
                    <path fill-rule='evenodd' d='M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06z' clip-rule='evenodd'/>
                </svg>
            </button>

            <ul
                id='ordersMetricMenu'
                role='listbox'
                class='hidden absolute left-0 top-full mt-1.5 w-44 z-50
                       bg-white dark:bg-slate-800
                       border border-slate-200 dark:border-slate-600
                       rounded-lg shadow-lg overflow-hidden'
            >
                <li role='option' data-value='total'
                    class='orders-metric-option px-3 py-2 text-xs text-slate-700 dark:text-slate-200
                           hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer
                           flex items-center justify-between font-medium'>
                    Total Orders
                    <svg id='ordersCheck_total' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'
                         class='w-3.5 h-3.5 text-emerald-500'>
                        <path fill-rule='evenodd' d='M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143z' clip-rule='evenodd'/>
                    </svg>
                </li>
                <li role='option' data-value='today'
                    class='orders-metric-option px-3 py-2 text-xs text-slate-700 dark:text-slate-200
                           hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer
                           flex items-center justify-between'>
                    Orders Today
                    <svg id='ordersCheck_today' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'
                         class='w-3.5 h-3.5 text-emerald-500 hidden'>
                        <path fill-rule='evenodd' d='M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143z' clip-rule='evenodd'/>
                    </svg>
                </li>
                <li role='option' data-value='week'
                    class='orders-metric-option px-3 py-2 text-xs text-slate-700 dark:text-slate-200
                           hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer
                           flex items-center justify-between'>
                    Orders This Week
                    <svg id='ordersCheck_week' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'
                         class='w-3.5 h-3.5 text-emerald-500 hidden'>
                        <path fill-rule='evenodd' d='M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143z' clip-rule='evenodd'/>
                    </svg>
                </li>
                <li role='option' data-value='month'
                    class='orders-metric-option px-3 py-2 text-xs text-slate-700 dark:text-slate-200
                           hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer
                           flex items-center justify-between'>
                    Orders This Month
                    <svg id='ordersCheck_month' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'
                         class='w-3.5 h-3.5 text-emerald-500 hidden'>
                        <path fill-rule='evenodd' d='M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143z' clip-rule='evenodd'/>
                    </svg>
                </li>
            </ul>
        </div>

        <div class='flex-1 min-h-0 flex flex-col justify-center'>
            <p id='ordersMetricValue' class='text-[2rem] leading-none font-bold text-slate-800 dark:text-white shrink-0'>
                <?= $totalOrders ?>
            </p>

            <p id='ordersMetricDesc' class='text-sm text-emerald-600 dark:text-emerald-400 mt-4 shrink-0'>
                All-time orders
            </p>
        </div>
    </div>

    <!-- Returns -->
    <?php
    $returnColor = 'text-emerald-500';
    $returnBadge = 'Healthy';

    if ($returnRate > 20) {
        $returnColor = 'text-red-500';
        $returnBadge = 'Critical';
    } elseif ($returnRate > 5) {
        $returnColor = 'text-amber-500';
        $returnBadge = 'Warning';
    }
    ?>

    <div class='bg-white dark:bg-slate-800 rounded-xl p-4 h-[11.5rem] border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col overflow-hidden'>
        <p class='text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide shrink-0'>
            Returns Rate
        </p>

        <div class='flex-1 min-h-0 flex flex-col justify-center'>
            <p class='text-[2rem] leading-none font-bold <?= $returnColor ?> shrink-0'>
                <?= number_format($returnRate, 1) ?>%
            </p>

            <p class='text-sm text-slate-500 dark:text-slate-400 mt-4 shrink-0'>
                <?= $totalReturns ?> return<?= $totalReturns !== 1 ? 's' : '' ?> of <?= $totalOrders ?> order<?= $totalOrders !== 1 ? 's' : '' ?>
            </p>
        </div>
    </div>

    <!-- Order Status Donut -->

<!-- Order Status Donut -->
<?php
$total = max((int)$totalOrders, 1);
$completedPct = round(($completedOrders / $total) * 100, 1);
$pendingPct   = round(($pendingOrders   / $total) * 100, 1);
$cancelledPct = round(($cancelledOrders / $total) * 100, 1);

// r=38, circumference = 2*pi*38 ~= 238.76
$c = 238.76;
$completedDash = round(($completedOrders / $total) * $c, 2);
$pendingDash   = round(($pendingOrders   / $total) * $c, 2);
$cancelledDash = round(($cancelledOrders / $total) * $c, 2);

$completedOffset = 0;
$pendingOffset   = -$completedDash;
$cancelledOffset = -($completedDash + $pendingDash);
?>

<style>
    .donut-value-text       { fill: #1e293b; }
    .donut-label-text       { fill: #94a3b8; }
    .dark .donut-value-text { fill: #f1f5f9; }
    .dark .donut-label-text { fill: #94a3b8; }
    .legend-pct             { font-size: 13px; font-weight: 900; color: #1e293b; line-height: 1; }
    .dark .legend-pct       { color: #f1f5f9; }
    .status-legend-label    { color: #475569; }
    .dark .status-legend-label { color: #cbd5e1; }
</style>

<div class='bg-white dark:bg-slate-800 rounded-xl p-4 h-[11.5rem] border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col overflow-hidden sm:col-span-2 lg:col-span-1'>

    <p class='text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide leading-none shrink-0'>
        Order Status
    </p>

    <div style='flex:1;min-height:0;display:grid;grid-template-columns:minmax(0,0.96fr) minmax(0,1.04fr);align-items:center;gap:10px;width:100%;padding-top:4px'>
        <svg
            viewBox='0 0 100 100'
            xmlns='http://www.w3.org/2000/svg'
            style='width:134px;height:134px;max-width:100%;flex-shrink:0;transform:rotate(-90deg);display:block;margin:0 auto'
        >
            <circle cx='50' cy='50' r='38'
                fill='none'
                stroke='#e2e8f0'
                stroke-width='10'
                class='dark:stroke-slate-700'
            />
            <circle cx='50' cy='50' r='38'
                fill='none'
                stroke='#10b981'
                stroke-width='10'
                stroke-dasharray='<?= $completedDash ?> <?= $c ?>'
                stroke-dashoffset='<?= $completedOffset ?>'
                stroke-linecap='butt'
            />
            <circle cx='50' cy='50' r='38'
                fill='none'
                stroke='#f59e0b'
                stroke-width='10'
                stroke-dasharray='<?= $pendingDash ?> <?= $c ?>'
                stroke-dashoffset='<?= $pendingOffset ?>'
                stroke-linecap='butt'
            />
            <circle cx='50' cy='50' r='38'
                fill='none'
                stroke='#ef4444'
                stroke-width='10'
                stroke-dasharray='<?= $cancelledDash ?> <?= $c ?>'
                stroke-dashoffset='<?= $cancelledOffset ?>'
                stroke-linecap='butt'
            />
            <text
                x='50' y='47'
                text-anchor='middle'
                dominant-baseline='middle'
                font-size='17'
                font-weight='700'
                class='donut-value-text'
                transform='rotate(90 50 50)'
            ><?= (int)$totalOrders ?></text>
            <text
                x='50' y='57'
                text-anchor='middle'
                dominant-baseline='middle'
                font-size='8'
                class='donut-label-text'
                transform='rotate(90 50 50)'
            >Total</text>
        </svg>

        <div style='width:100%;display:flex;flex-direction:column;justify-content:center;align-items:flex-start;gap:10px;flex-shrink:0'>
            <div style='display:grid;grid-template-columns:max-content auto;align-items:center;column-gap:12px'>
                <span style='display:flex;align-items:center;gap:7px;font-size:13px;line-height:1' class='status-legend-label'>
                    <span style='width:7px;height:7px;border-radius:50%;background:#10b981;flex-shrink:0;display:inline-block'></span>
                    Completed
                </span>
                <span class='legend-pct' style='text-align:right'><?= $completedPct ?>% (<?= $completedOrders ?>)</span>
            </div>

            <div style='display:grid;grid-template-columns:max-content auto;align-items:center;column-gap:12px'>
                <span style='display:flex;align-items:center;gap:7px;font-size:13px;line-height:1' class='status-legend-label'>
                    <span style='width:7px;height:7px;border-radius:50%;background:#f59e0b;flex-shrink:0;display:inline-block'></span>
                    Pending
                </span>
                <span class='legend-pct' style='text-align:right'><?= $pendingPct ?>% (<?= $pendingOrders ?>)</span>
            </div>

            <div style='display:grid;grid-template-columns:max-content auto;align-items:center;column-gap:12px'>
                <span style='display:flex;align-items:center;gap:7px;font-size:13px;line-height:1' class='status-legend-label'>
                    <span style='width:7px;height:7px;border-radius:50%;background:#ef4444;flex-shrink:0;display:inline-block'></span>
                    Cancelled
                </span>
                <span class='legend-pct' style='text-align:right'><?= $cancelledPct ?>% (<?= $cancelledOrders ?>)</span>
            </div>
        </div>
    </div>

</div>

    <?php if (Permission::isAllowed('admin')): ?>
    <!-- Revenue -->
    <div class='bg-white dark:bg-slate-800 rounded-xl p-4 h-[11.5rem] border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col overflow-hidden'>
        <p class='text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide shrink-0'>Revenue</p>
        <div class='flex-1 min-h-0 flex flex-col justify-center'>
            <p class='text-[2rem] leading-none font-bold text-slate-800 dark:text-white shrink-0'>$<?= number_format($revenue, 2) ?></p>
            <p class='inline-flex w-fit items-center gap-1.5 rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300 mt-4 shrink-0'>
                <span class='text-[9px] leading-none'>&bull;</span> Completed Only
            </p>
        </div>
    </div>

    <!-- Customers -->
    <div class='bg-white dark:bg-slate-800 rounded-xl p-4 h-[11.5rem] border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col overflow-hidden'>
        <p class='text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide shrink-0'>Total Customers</p>
        <div class='flex-1 min-h-0 flex flex-col justify-center'>
            <p class='text-[2rem] leading-none font-bold text-slate-800 dark:text-white shrink-0'><?= $totalCustomers ?? 0 ?></p>
            <p class='text-sm text-slate-500 dark:text-slate-400 mt-4 shrink-0'>Registered customers</p>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php if (Permission::isAllowed('admin')): ?>
<?php
$maxPurchaseCount = 0;
foreach (($topPurchasingCustomers ?? []) as $customer) {
    $maxPurchaseCount = max($maxPurchaseCount, (int)($customer['order_count'] ?? 0));
}

$maxSpentAmount = 0.0;
foreach (($topSpendingCustomers ?? []) as $customer) {
    $maxSpentAmount = max($maxSpentAmount, (float)($customer['total_spent'] ?? 0));
}
?>

<!-- CUSTOMER ANALYTICS -->
<style>
    .rank-bar-track {
        position: relative;
        height: 6px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
        flex: 1;
    }
    .dark .rank-bar-track {
        background: #1e293b;
    }
    .rank-bar-fill {
        position: absolute;
        inset: 0 auto 0 0;
        border-radius: 999px;
        height: 100%;
        width: 0%;
        transition: width 0.9s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .rank-bar-fill.sky {
        background: linear-gradient(90deg, #0ea5e9 0%, #22d3ee 100%);
        box-shadow: 0 0 8px 0 rgba(14, 165, 233, 0.35);
    }
    .dark .rank-bar-fill.sky {
        background: linear-gradient(90deg, #38bdf8 0%, #67e8f9 100%);
        box-shadow: 0 0 10px 0 rgba(56, 189, 248, 0.4);
    }
    .rank-bar-fill.emerald {
        background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
        box-shadow: 0 0 8px 0 rgba(16, 185, 129, 0.35);
    }
    .dark .rank-bar-fill.emerald {
        background: linear-gradient(90deg, #34d399 0%, #6ee7b7 100%);
        box-shadow: 0 0 10px 0 rgba(52, 211, 153, 0.4);
    }
    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 800;
        line-height: 1;
        flex-shrink: 0;
        letter-spacing: -0.02em;
    }
    .rank-badge.r1 { background: #fef9c3; color: #a16207; }
    .rank-badge.r2 { background: #f1f5f9; color: #475569; }
    .rank-badge.r3 { background: #fff7ed; color: #9a3412; }
    .dark .rank-badge.r1 { background: rgba(234,179,8,0.15); color: #fde047; }
    .dark .rank-badge.r2 { background: rgba(148,163,184,0.12); color: #94a3b8; }
    .dark .rank-badge.r3 { background: rgba(249,115,22,0.12); color: #fb923c; }
    .analytics-row {
        display: grid;
        grid-template-columns: 22px 1fr auto;
        align-items: center;
        gap: 12px;
        padding: 9px 0;
    }
    .analytics-row-inner {
        display: grid;
        grid-template-columns: minmax(0, 140px) 1fr;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }
    .analytics-row + .analytics-row {
        border-top: 1px solid #f1f5f9;
    }
    .dark .analytics-row + .analytics-row {
        border-top-color: rgba(51, 65, 85, 0.6);
    }
    .analytics-value-chip {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: -0.01em;
        line-height: 1;
        padding: 4px 8px;
        border-radius: 7px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .analytics-value-chip.sky {
        background: #f0f9ff;
        color: #0369a1;
    }
    .dark .analytics-value-chip.sky {
        background: rgba(14, 165, 233, 0.1);
        color: #38bdf8;
    }
    .analytics-value-chip.emerald {
        background: #ecfdf5;
        color: #065f46;
    }
    .dark .analytics-value-chip.emerald {
        background: rgba(16, 185, 129, 0.1);
        color: #34d399;
    }
    .analytics-value {
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        color: #334155;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .dark .analytics-value {
        color: #e2e8f0;
    }
    .analytics-name {
        font-size: 13px;
        font-weight: 600;
        line-height: 1.2;
        color: #334155;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .dark .analytics-name {
        color: #e2e8f0;
    }
    .analytics-widget-inner {
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        padding: 6px 14px 6px;
    }
    .dark .analytics-widget-inner {
        background: rgba(15, 23, 42, 0.45);
        border-color: rgba(51, 65, 85, 0.55);
    }
</style>

<div class='grid grid-cols-1 xl:grid-cols-2 gap-4 mb-6'>

    <!-- Top Purchasing Customers -->
    <div class='bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm'>
        <div class='flex items-start justify-between mb-4'>
            <div>
                <p class='text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest'>
                    TOP PURCHASING CUSTOMERS
                </p>
                <p class='text-sm text-slate-400 dark:text-slate-500 mt-0.5'>By number of orders</p>
            </div>
        </div>

        <?php if (empty($topPurchasingCustomers)): ?>
            <div class='rounded-lg border border-dashed border-slate-200 dark:border-slate-700 px-4 py-8 text-sm text-slate-400 dark:text-slate-500 text-center'>
                No customer purchase data yet.
            </div>
        <?php else: ?>
            <div class='analytics-widget-inner'>
                <?php
                $rankClasses = ['r1', 'r2', 'r3'];
                $rankLabels  = ['1', '2', '3'];
                $i = 0;
                foreach ($topPurchasingCustomers as $customer):
                    $orderCount = (int)($customer['order_count'] ?? 0);
                    $barWidth   = $maxPurchaseCount > 0 ? round(max(4, ($orderCount / $maxPurchaseCount) * 100), 1) : 4;
                    $rc         = $rankClasses[$i] ?? 'r3';
                    $rl         = $rankLabels[$i]  ?? ($i + 1);
                    $i++;
                ?>
                <div class='analytics-row'>
                    <span class='rank-badge <?= $rc ?>'><?= $rl ?></span>
                    <div class='analytics-row-inner'>
                        <span class='analytics-name'><?= htmlspecialchars((string)($customer['customer_name'] ?? 'Unknown')) ?></span>
                        <div class='rank-bar-track' data-width='<?= $barWidth ?>'>
                            <div class='rank-bar-fill sky'></div>
                        </div>
                    </div>
                    <span class='analytics-value'><?= $orderCount ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Top Spending Customers -->
    <div class='bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm'>
        <div class='flex items-start justify-between mb-4'>
            <div>
                <p class='text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest'>
                    TOP SPENDING CUSTOMERS
                </p>
                <p class='text-sm text-slate-400 dark:text-slate-500 mt-0.5'>By total amount spent</p>
            </div>
            <span class='inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 tracking-wide'>
                $ Spend
            </span>
        </div>

        <?php if (empty($topSpendingCustomers)): ?>
            <div class='rounded-lg border border-dashed border-slate-200 dark:border-slate-700 px-4 py-8 text-sm text-slate-400 dark:text-slate-500 text-center'>
                No completed spending data yet.
            </div>
        <?php else: ?>
            <div class='analytics-widget-inner'>
                <?php
                $rankClasses = ['r1', 'r2', 'r3'];
                $rankLabels  = ['1', '2', '3'];
                $i = 0;
                foreach ($topSpendingCustomers as $customer):
                    $spentAmount = (float)($customer['total_spent'] ?? 0);
                    $barWidth    = $maxSpentAmount > 0 ? round(max(4, ($spentAmount / $maxSpentAmount) * 100), 1) : 4;
                    $rc          = $rankClasses[$i] ?? 'r3';
                    $rl          = $rankLabels[$i]  ?? ($i + 1);
                    $i++;
                ?>
                <div class='analytics-row'>
                    <span class='rank-badge <?= $rc ?>'><?= $rl ?></span>
                    <div class='analytics-row-inner'>
                        <span class='analytics-name'><?= htmlspecialchars((string)($customer['customer_name'] ?? 'Unknown')) ?></span>
                        <div class='rank-bar-track' data-width='<?= $barWidth ?>'>
                            <div class='rank-bar-fill emerald'></div>
                        </div>
                    </div>
                    <span class='analytics-value'><?= number_format($spentAmount, 2) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php endif; ?>

<?php if (Permission::isAllowed('admin')): ?>
<!-- INVENTORY ALERTS -->
<?php
$outOfStockAlertCount = 0;
$lowStockWarningCount = 0;

foreach (($lowStockProducts ?? []) as $product) {
    $stock = (int)($product['stock'] ?? 0);
    if ($stock === 0) {
        $outOfStockAlertCount++;
    } elseif ($stock <= (int)($lowStockThreshold ?? 2)) {
        $lowStockWarningCount++;
    }
}

?>
<style>
    .inv-alert-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 5px 12px;
        border-radius: 8px;
        background: #fafafa;
        border: 1px solid #f1f5f9;
        transition: all 0.15s;
    }
    .inv-alert-row:hover {
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    .dark .inv-alert-row {
        background: rgba(15, 23, 42, 0.4);
        border-color: rgba(51, 65, 85, 0.5);
    }
    .dark .inv-alert-row:hover {
        background: rgba(15, 23, 42, 0.6);
        border-color: rgba(51, 65, 85, 0.7);
    }

    .inv-alert-content {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
        min-width: 0;
    }

    .inv-alert-name {
        font-size: 13.5px;
        font-weight: 600;
        color: #334155;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex: 1;
        min-width: 0;
    }
    .dark .inv-alert-name {
        color: #e2e8f0;
    }

    .inv-alert-action-group {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .inv-stock-indicator {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        flex-shrink: 0;
        display: inline-block;
    }

    .inv-stock-indicator.is-alert {
        background: #ef4444;
    }

    .inv-stock-indicator.is-warning {
        background: #f59e0b;
    }

    .inv-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #475569;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s, border-color 0.15s, color 0.15s;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .inv-action-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #1e293b;
    }
    .dark .inv-action-btn {
        background: rgba(255,255,255,0.05);
        border-color: rgba(100,116,139,0.4);
        color: #94a3b8;
    }
    .dark .inv-action-btn:hover {
        background: rgba(255,255,255,0.09);
        border-color: rgba(100,116,139,0.6);
        color: #e2e8f0;
    }

    .inv-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 12px 16px;
    }
    .inv-empty-icon {
        width: 28px; height: 28px;
        border-radius: 50%;
        background: #f0fdf4;
        display: flex; align-items: center; justify-content: center;
    }
    .dark .inv-empty-icon { background: rgba(16,185,129,0.1); }
</style>

<div class='w-full xl:w-[calc(50%-0.5rem)] bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm mb-6 overflow-hidden'>

    <!-- Card Header -->
    <div class='flex items-start justify-between px-4 py-1.5 border-b border-slate-100 dark:border-slate-700'>
        <div>
            <p class='text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest'>
                Inventory Alerts
            </p>
            <p class='text-sm text-slate-400 dark:text-slate-500 mt-0.5'>
                Products requiring immediate attention
            </p>
        </div>
        <?php if (!empty($lowStockProducts)): ?>
        <div data-inventory-alert-count class='inline-flex items-center gap-2 shrink-0 mt-0.5'>
            <?php if ($outOfStockAlertCount > 0): ?>
            <span data-alert-count-badge class='inline-flex items-center gap-1.5 rounded-lg bg-red-50 dark:bg-red-500/10 px-2.5 py-1 text-[11px] font-semibold text-red-600 dark:text-red-400 tracking-wide'>
                <?= $outOfStockAlertCount ?> Alert<?= $outOfStockAlertCount === 1 ? '' : 's' ?>
            </span>
            <?php endif; ?>
            <?php if ($lowStockWarningCount > 0): ?>
            <span data-warning-count-badge class='inline-flex items-center gap-1.5 rounded-lg bg-amber-50 dark:bg-amber-500/10 px-2.5 py-1 text-[11px] font-semibold text-amber-700 dark:text-amber-300 tracking-wide'>
                <?= $lowStockWarningCount ?> Warning<?= $lowStockWarningCount === 1 ? '' : 's' ?>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Alert List -->
    <?php if (empty($lowStockProducts)): ?>

        <div class='inv-empty-state'>
            <div class='inv-empty-icon'>
                <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' class='w-5 h-5 text-emerald-500'>
                    <path fill-rule='evenodd' d='M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143z' clip-rule='evenodd'/>
                </svg>
            </div>
            <p class='text-sm font-semibold text-slate-600 dark:text-slate-300'>All products sufficiently stocked</p>
            <p class='text-xs text-slate-400 dark:text-slate-500'>No products with stock ≤ <?= (int)($lowStockThreshold ?? 2) ?> were found.</p>
        </div>

    <?php else: ?>

        <div class='px-4 py-1.5 space-y-1' data-inventory-alert-list>
            <?php foreach ($lowStockProducts as $product):
                $stock = (int)($product['stock'] ?? 0);
                $productId = (int)($product['id'] ?? 0);
                $productName = (string)($product['name'] ?? 'Unknown Product');
            ?>
            <div class='inv-alert-row' data-alert-row data-product-id='<?= $productId ?>' data-stock='<?= $stock ?>'>
                <div class='inv-alert-content'>
                    <span class='inv-alert-name'>
                        <?= htmlspecialchars($productName) ?>
                    </span>
                </div>
                <div class='inv-alert-action-group'>
                    <span class='inv-stock-indicator <?= $stock === 0 ? 'is-alert' : 'is-warning' ?>' aria-hidden='true'></span>
                    <button type='button' class='inv-action-btn restock-alert-btn dashboard-restock-trigger' data-product-id='<?= $productId ?>' data-product-name='<?= htmlspecialchars($productName, ENT_QUOTES) ?>'>
                        <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='currentColor' style='width:12px;height:12px;flex-shrink:0'>
                            <path d='M13.488 2.513a1.75 1.75 0 0 0-2.475 0L6.75 6.774a2.75 2.75 0 0 0-.596.892l-.848 2.047a.75.75 0 0 0 .98.98l2.047-.848a2.75 2.75 0 0 0 .892-.596l4.261-4.263a1.75 1.75 0 0 0 0-2.474ZM4.75 14.25a.75.75 0 0 0 0-1.5H3.5a.5.5 0 0 1-.5-.5V4a.5.5 0 0 1 .5-.5h5.25a.75.75 0 0 0 0-1.5H3.5A2 2 0 0 0 1.5 4v8.25a2 2 0 0 0 2 2h1.25Z'/>
                        </svg>
                        Restock
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>
<?php endif; ?>

<?php if (Permission::isAllowed('admin')): ?>
<div id="dashboardBuyModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 px-4 opacity-0 transition-opacity duration-150 ease-out" data-low-stock-threshold="<?= (int)($lowStockThreshold ?? 2) ?>">
    <div id="dashboardBuyPanel" class="relative w-full max-w-sm scale-95 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-2xl transition-transform duration-150 ease-out">
        <button type="button" id="dashboardBuyClose" class="absolute right-3 top-3 inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white transition" aria-label="Close">
            <span class="text-xl leading-none">&times;</span>
        </button>

        <div class="pt-6 text-center">
            <p class="text-sm font-semibold leading-6 text-slate-900 dark:text-slate-100">
                Please enter the number of units you want to buy
            </p>
            <p id="dashboardBuyProductName" class="mt-2 text-xs font-medium text-slate-500 dark:text-slate-400"></p>

            <form id="dashboardBuyForm" class="mt-5 space-y-4" data-product-id="">
                <input id="dashboardBuyProductId" name="product_id" type="hidden" value="">
                <div class="flex flex-col items-center gap-2">
                    <label for="dashboardBuyQuantity" class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Units to add</label>
                    <input id="dashboardBuyQuantity" name="quantity" type="number" min="1" step="1" required class="h-11 w-[120px] rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 px-3 text-center text-base font-semibold text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                </div>

                <p id="dashboardBuyMessage" class="hidden text-sm font-medium"></p>

                <div class="flex items-center justify-center gap-3 pt-1">
                    <button type="button" id="dashboardBuyCancel" class="inline-flex items-center justify-center rounded-lg border border-slate-300 dark:border-slate-600 px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        Cancel
                    </button>
                    <button type="submit" id="dashboardBuySubmit" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition">
                        Buy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function initDashboardRestockModal() {
    const modal = document.getElementById('dashboardBuyModal');
    if (!modal || window.__dashboardRestockModalElement === modal) return;
    window.__dashboardRestockModalElement = modal;

    const panel = document.getElementById('dashboardBuyPanel');
    const closeBtn = document.getElementById('dashboardBuyClose');
    const cancelBtn = document.getElementById('dashboardBuyCancel');
    const form = document.getElementById('dashboardBuyForm');
    const productInput = document.getElementById('dashboardBuyProductId');
    const productNameText = document.getElementById('dashboardBuyProductName');
    const quantityInput = document.getElementById('dashboardBuyQuantity');
    const message = document.getElementById('dashboardBuyMessage');
    const submitBtn = document.getElementById('dashboardBuySubmit');
    const submitDefaultText = submitBtn ? submitBtn.textContent : 'Buy';
    const lowStockThreshold = modal ? parseInt(modal.dataset.lowStockThreshold || '2', 10) : 2;

    window.openRestockModal = function(productId = '', productName = '') {
        if (!modal || !form || !productInput) return;
        if (!productId) return;

        form.dataset.productId = productId;
        form.dataset.productName = productName || '';
        productInput.value = productId;
        form.reset();
        productInput.value = productId;
        if (productNameText) {
            productNameText.textContent = productName ? 'Restocking: ' + productName : '';
        }
        message.classList.add('hidden');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        requestAnimationFrame(function () {
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
            if (panel) {
                panel.classList.remove('scale-95');
                panel.classList.add('scale-100');
            }
            quantityInput.focus();
        });
    };

    function closeBuyModal() {
        if (!modal) return;

        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        if (panel) {
            panel.classList.remove('scale-100');
            panel.classList.add('scale-95');
        }

        setTimeout(function () {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            if (form) form.dataset.productId = '';
            if (form) form.dataset.productName = '';
            if (productInput) productInput.value = '';
            if (productNameText) productNameText.textContent = '';
            if (submitBtn) submitBtn.textContent = submitDefaultText;
        }, 150);
    }

    function showBuyMessage(text, type) {
        message.textContent = text;
        message.classList.remove('hidden', 'text-red-600', 'dark:text-red-400', 'text-emerald-600', 'dark:text-emerald-400');
        if (type === 'success') {
            message.classList.add('text-emerald-600', 'dark:text-emerald-400');
        } else {
            message.classList.add('text-red-600', 'dark:text-red-400');
        }
    }

    if (closeBtn) closeBtn.addEventListener('click', closeBuyModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeBuyModal);
    if (modal) {
        modal.addEventListener('click', function (event) {
            if (event.target === modal) closeBuyModal();
        });
    }

    if (!window.__dashboardRestockClickBound) {
        window.__dashboardRestockClickBound = true;
        document.addEventListener('click', function (event) {
            const button = event.target.closest('.restock-alert-btn');
            if (!button) return;

            event.preventDefault();
            window.openRestockModal(button.dataset.productId || '', button.dataset.productName || '');
        });
    }

    function updateProductStockInDom(productId, newStock) {
        document.querySelectorAll('[data-product-stock][data-product-id="' + productId + '"]').forEach(function (stockNode) {
            stockNode.textContent = String(newStock);
        });
    }

    function buildInventoryAlertCounts(rows) {
        let alertCount = 0;
        let warningCount = 0;

        rows.forEach(function (row) {
            const stock = parseInt(row.dataset.stock || '0', 10);
            if (stock === 0) {
                alertCount += 1;
            } else if (stock <= lowStockThreshold) {
                warningCount += 1;
            }
        });

        return { alerts: alertCount, warnings: warningCount };
    }

    function setInventoryCountBadge(container, selector, count, type) {
        let badge = container.querySelector(selector);
        if (count === 0) {
            if (badge) badge.remove();
            return;
        }

        if (!badge) {
            badge = document.createElement('span');
            badge.dataset[type === 'Alert' ? 'alertCountBadge' : 'warningCountBadge'] = '';
            badge.className = type === 'Alert'
                ? 'inline-flex items-center gap-1.5 rounded-lg bg-red-50 dark:bg-red-500/10 px-2.5 py-1 text-[11px] font-semibold text-red-600 dark:text-red-400 tracking-wide'
                : 'inline-flex items-center gap-1.5 rounded-lg bg-amber-50 dark:bg-amber-500/10 px-2.5 py-1 text-[11px] font-semibold text-amber-700 dark:text-amber-300 tracking-wide';
            container.appendChild(badge);
        }

        badge.textContent = count + ' ' + type + (count === 1 ? '' : 's');
    }

    function refreshInventoryAlert(productId, newStock) {
        const row = document.querySelector('[data-alert-row][data-product-id="' + productId + '"]');
        updateProductStockInDom(productId, newStock);
        if (!row) return;

        row.dataset.stock = String(newStock);

        if (newStock > lowStockThreshold) {
            row.remove();
        }

        const rows = document.querySelectorAll('[data-alert-row]');
        const countBadge = document.querySelector('[data-inventory-alert-count]');
        if (countBadge) {
            const counts = buildInventoryAlertCounts(Array.from(rows));
            setInventoryCountBadge(countBadge, '[data-alert-count-badge]', counts.alerts, 'Alert');
            setInventoryCountBadge(countBadge, '[data-warning-count-badge]', counts.warnings, 'Warning');
            if (rows.length === 0) countBadge.remove();
        }

        if (rows.length === 0) {
            const list = document.querySelector('[data-inventory-alert-list]');
            if (list) {
                list.innerHTML = '<div class="inv-empty-state"><div class="inv-empty-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-emerald-500"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143z" clip-rule="evenodd"/></svg></div><p class="text-sm font-semibold text-slate-600 dark:text-slate-300">All products sufficiently stocked</p><p class="text-xs text-slate-400 dark:text-slate-500">No products with low stock were found.</p></div>';
            }
        }
    }

    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const productId = productInput.value || form.dataset.productId;
            const quantity = parseInt(quantityInput.value, 10);

            if (!productId) {
                showBuyMessage('Product was not selected for restock.', 'error');
                return;
            }

            if (!Number.isInteger(quantity) || quantity <= 0) {
                showBuyMessage('Please enter a valid quantity greater than 0.', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
            submitBtn.textContent = 'Saving...';

            const body = new URLSearchParams();
            body.set('stock', String(quantity));

            fetch('/products/' + encodeURIComponent(productId) + '/stock', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: body.toString()
            })
                .then(function (response) {
                    return response.json().then(function (json) {
                        if (!response.ok || !json.success) {
                            throw new Error(json.error || 'Could not update stock.');
                        }
                        return json;
                    });
                })
                .then(function (json) {
                    showBuyMessage(json.message || 'Stock updated successfully.', 'success');
                    refreshInventoryAlert(productId, parseInt(json.current_stock || quantity, 10));
                    form.reset();
                    setTimeout(closeBuyModal, 550);
                })
                .catch(function (error) {
                    showBuyMessage(error.message || 'Could not update stock.', 'error');
                })
                .finally(function () {
                    submitBtn.disabled = false;
                    submitBtn.textContent = submitDefaultText;
                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                });
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboardRestockModal);
} else {
    initDashboardRestockModal();
}

if (!window.__dashboardRestockObserverBound) {
    window.__dashboardRestockObserverBound = true;
    new MutationObserver(function () {
        if (document.getElementById('dashboardBuyModal')) {
            initDashboardRestockModal();
        }
    }).observe(document.body, { childList: true, subtree: true });
}
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    requestAnimationFrame(function () {
        document.querySelectorAll('.rank-bar-track').forEach(function (track) {
            var w = track.getAttribute('data-width');
            var fill = track.querySelector('.rank-bar-fill');
            if (fill && w) {
                setTimeout(function () { fill.style.width = w + '%'; }, 80);
            }
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const trigger  = document.getElementById('ordersMetricTrigger');
    const menu     = document.getElementById('ordersMetricMenu');
    const chevron  = document.getElementById('ordersMetricChevron');
    const label    = document.getElementById('ordersMetricLabel');
    const value    = document.getElementById('ordersMetricValue');
    const desc     = document.getElementById('ordersMetricDesc');

    const metrics = {
        total: { label: 'Total Orders',      value: <?= (int)$totalOrders ?>,            desc: 'All-time orders' },
        today: { label: 'Orders Today',      value: <?= (int)$ordersToday ?>,            desc: 'Orders placed today' },
        week:  { label: 'Orders This Week',  value: <?= (int)($ordersThisWeek ?? 0) ?>,  desc: 'Orders placed this week' },
        month: { label: 'Orders This Month', value: <?= (int)($ordersThisMonth ?? 0) ?>, desc: 'Orders placed this month' }
    };

    let current = 'total';

    function openMenu() {
        menu.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
        trigger.setAttribute('aria-expanded', 'true');
    }

    function closeMenu() {
        menu.classList.add('hidden');
        chevron.style.transform = '';
        trigger.setAttribute('aria-expanded', 'false');
    }

    function selectMetric(key) {
        const metric = metrics[key];
        label.textContent = metric.label;
        value.textContent = metric.value;
        desc.textContent  = metric.desc;
        document.getElementById('ordersCheck_' + current).classList.add('hidden');
        document.getElementById('ordersCheck_' + key).classList.remove('hidden');
        current = key;
        closeMenu();
    }

    trigger.addEventListener('click', function (e) {
        e.stopPropagation();
        menu.classList.contains('hidden') ? openMenu() : closeMenu();
    });

    document.querySelectorAll('.orders-metric-option').forEach(function (item) {
        item.addEventListener('click', function () {
            selectMetric(this.dataset.value);
        });
    });

    document.addEventListener('click', closeMenu);
});
</script>
