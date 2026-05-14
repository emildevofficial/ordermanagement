<?php
// expects: $products (array)
use App\Helper\Permission;
use App\Helper\DateTimeHelper;

$isAdmin = Permission::isAllowed('admin');

$formatDateTime = static function ($value): array {
    return DateTimeHelper::formatDateTimeParts($value);
};

$formatProductName = static function (string $name): string {
    $lower = mb_strtolower(trim($name), 'UTF-8');
    return mb_strtoupper(mb_substr($lower, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($lower, 1, null, 'UTF-8');
};
?>

<?php if ($isAdmin): ?>
<div class="w-full max-w-6xl px-6 py-8 space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">
            Products
        </h2>
        <div class="flex items-center gap-2">
            <a href="/dashboard" class="inline-flex items-center gap-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                &larr; Back to Dashboard
            </a>
            <a href="/products/create" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition">
                + Add Product
            </a>
        </div>
    </div>

    <div class="w-full bg-white dark:bg-slate-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="w-28 px-5 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Image</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Price ($)</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Current Stock</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Product Created At</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Product Updated At</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-200 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <?php
                                $displayName = $formatProductName((string)$product['name']);
                                $imageUrl = trim((string)($product['image_url'] ?? ''));
                                [$createdDate, $createdTime] = $formatDateTime($product['created_at'] ?? null);
                                [$updatedDate, $updatedTime] = $formatDateTime($product['updated_at'] ?? null);
                            ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                <td class="px-5 py-4">
                                    <?php if ($imageUrl !== ''): ?>
                                        <img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES) ?>" alt="<?= htmlspecialchars($displayName, ENT_QUOTES) ?>" class="h-20 w-20 rounded-xl border border-slate-200 dark:border-slate-700 object-cover">
                                    <?php else: ?>
                                        <div class="flex h-20 w-20 items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 text-2xl font-semibold text-slate-600 dark:text-slate-300">
                                            <?= htmlspecialchars(mb_strtoupper(mb_substr($displayName, 0, 1, 'UTF-8'), 'UTF-8')) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-5 font-medium text-slate-900 dark:text-slate-100"><?= htmlspecialchars($displayName) ?></td>
                                <td class="px-4 py-5 text-center font-mono text-slate-700 dark:text-slate-200"><?= number_format((float)$product['price'], 2) ?></td>
                                <td class="px-4 py-5 text-center">
                                    <span class="font-mono text-slate-700 dark:text-slate-200" data-product-stock data-product-id="<?= (int)$product['id'] ?>"><?= (int)$product['stock'] ?></span>
                                </td>
                                <td class="px-4 py-5">
                                    <div class="text-sm font-medium text-slate-900 dark:text-slate-100"><?= $createdDate ?></div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400"><?= $createdTime ?></div>
                                </td>
                                <td class="px-4 py-5">
                                    <div class="text-sm font-medium text-slate-900 dark:text-slate-100"><?= $updatedDate ?></div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400"><?= $updatedTime ?></div>
                                </td>
                                <td class="px-4 py-5 text-right">
                                    <a href="/products/<?= (int)$product['id'] ?>/edit" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition" title="Edit product">
                                        <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h6M4 20h16M3 7l1-1a2 2 0 012 0l1 1M7 11l7-7 4 4-7 7H7v-4z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-300">
                                No products found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<style>
    .shop-grid {
        perspective: 1200px;
    }

    .shop-card {
        transition: border-color 180ms ease, box-shadow 180ms ease;
    }
</style>

<div class="w-full px-2 sm:px-4 lg:px-6 py-6 space-y-6">
    <div class="flex flex-col gap-2">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Shop</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Browse available products and choose a quantity during checkout.</p>
    </div>

    <?php if (!empty($products)): ?>
        <div class="shop-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5">
            <?php foreach ($products as $product): ?>
                <?php
                    $productId = (int)$product['id'];
                    $stock = (int)$product['stock'];
                    $isAvailable = !empty($product['is_active']) && $stock > 0;
                    $displayName = $formatProductName((string)$product['name']);
                    $productInitial = mb_strtoupper(mb_substr($displayName, 0, 1, 'UTF-8'), 'UTF-8');
                    $imageUrl = trim((string)($product['image_url'] ?? ''));
                    $price = number_format((float)$product['price'], 2);

                    $statusBadgeClass = 'bg-emerald-500/10 text-emerald-300 border-emerald-400/20';
                    $statusDotClass = 'bg-emerald-400';
                    $statusLabel = 'In Stock';

                    if (!$isAvailable) {
                        $statusBadgeClass = 'bg-rose-500/10 text-rose-300 border-rose-400/20';
                        $statusDotClass = 'bg-rose-400';
                        $statusLabel = 'Out of Stock';
                    } elseif ($stock <= 2) {
                        $statusBadgeClass = 'bg-amber-500/10 text-amber-300 border-amber-400/20';
                        $statusDotClass = 'bg-amber-400';
                        $statusLabel = 'Low Stock';
                    }
                ?>
                <article class="shop-card flex h-full min-h-[19rem] flex-col rounded-lg border border-slate-700/80 bg-slate-900 p-4 shadow-sm shadow-slate-950/20 hover:border-indigo-400/60 hover:shadow-xl hover:shadow-indigo-950/20">
                    <div class="shop-card-surface flex h-full flex-col justify-between">
                        <div class="shop-card-media group relative mb-4 h-40 overflow-hidden rounded-lg border border-slate-700/70 bg-slate-950">
                            <?php if ($imageUrl !== ''): ?>
                                <img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES) ?>" alt="<?= htmlspecialchars($displayName, ENT_QUOTES) ?>" class="h-full w-full rounded-lg object-cover transition-transform duration-300 group-hover:scale-105">
                            <?php else: ?>
                                <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(99,102,241,0.30),transparent_36%),radial-gradient(circle_at_80%_10%,rgba(16,185,129,0.18),transparent_30%),linear-gradient(145deg,rgba(15,23,42,0.25),rgba(2,6,23,0.96))]"></div>
                                <div class="absolute inset-x-4 bottom-4 top-5 rounded-lg border border-white/10 bg-white/[0.04] shadow-2xl shadow-slate-950/40"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-slate-900/80 text-3xl font-bold text-white shadow-xl shadow-slate-950/40">
                                        <?= htmlspecialchars($productInitial) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div>
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <div class="min-w-0 text-left">
                                    <h3 class="shop-card-title truncate text-sm font-semibold leading-5 text-white">
                                        <?= htmlspecialchars($displayName) ?>
                                    </h3>
                                </div>
                                <p class="shop-card-price shrink-0 text-right text-lg font-bold leading-5 text-white">$<?= $price ?></p>
                            </div>

                            <div class="mb-3 flex justify-center">
                                <p class="shop-card-status inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-center text-xs font-semibold <?= $statusBadgeClass ?>">
                                    <span class="h-1.5 w-1.5 rounded-full <?= $statusDotClass ?>"></span>
                                    <?= $statusLabel ?>
                                </p>
                            </div>

                            <div class="shop-card-actions mt-2 flex items-center justify-center">
                                <button
                                    type="button"
                                    <?= $isAvailable ? '' : 'disabled' ?>
                                    class="w-24 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-900 disabled:cursor-not-allowed disabled:bg-slate-700 disabled:text-slate-400"
                                    data-shop-buy
                                    data-product-id="<?= $productId ?>"
                                    data-product-name="<?= htmlspecialchars($displayName, ENT_QUOTES) ?>"
                                    data-product-price="<?= $price ?>"
                                    data-product-stock="<?= $stock ?>"
                                >
                                    Buy
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="rounded-lg border border-slate-700 bg-slate-900 px-6 py-12 text-center text-slate-300">
            No products found.
        </div>
    <?php endif; ?>
</div>

<div id="shopBuyModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/75 px-4 opacity-0 transition-opacity duration-150">
    <div id="shopBuyPanel" class="w-full max-w-sm scale-95 rounded-lg border border-slate-700 bg-slate-900 p-5 shadow-2xl shadow-slate-950/60 transition-transform duration-150">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-white">Confirm purchase</h3>
            </div>
            <button type="button" id="shopBuyClose" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-800 hover:text-white" aria-label="Close">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form method="POST" action="/orders/create" class="mt-6 flex flex-col items-center gap-5">
            <input type="hidden" id="shopBuyProductId" name="product_id" value="">
            <div class="flex w-full flex-col items-center">
                <label for="shopBuyQuantity" class="block text-center text-sm font-medium text-slate-200">How many units would you like to purchase?</label>
                <input
                    id="shopBuyQuantity"
                    name="quantity"
                    type="number"
                    min="1"
                    value="1"
                    required
                    class="mt-4 h-11 w-24 rounded-xl border border-slate-700 bg-slate-950 px-3 text-center text-base font-semibold text-white outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/30"
                >
            </div>

            <div class="flex w-full justify-center">
                <button type="submit" class="w-36 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-950/30 transition hover:from-indigo-500 hover:to-purple-500 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                    Buy
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('shopBuyModal');
    var panel = document.getElementById('shopBuyPanel');
    var closeBtn = document.getElementById('shopBuyClose');
    var productIdInput = document.getElementById('shopBuyProductId');
    var quantityInput = document.getElementById('shopBuyQuantity');

    function openModal(button) {
        var productId = button.dataset.productId || '';
        var stock = parseInt(button.dataset.productStock || '1', 10);
        var quantity = 1;

        if (!Number.isInteger(quantity) || quantity < 1) quantity = 1;
        if (Number.isInteger(stock) && stock > 0 && quantity > stock) quantity = stock;

        productIdInput.value = productId;
        quantityInput.max = String(Math.max(1, stock));
        quantityInput.value = String(quantity);

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(function () {
            modal.classList.remove('opacity-0');
            panel.classList.remove('scale-95');
        });
        quantityInput.focus();
        quantityInput.select();
    }

    function closeModal() {
        modal.classList.add('opacity-0');
        panel.classList.add('scale-95');
        setTimeout(function () {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 150);
    }

    document.querySelectorAll('[data-shop-buy]').forEach(function (button) {
        button.addEventListener('click', function () {
            openModal(button);
        });
    });

    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function (event) {
        if (event.target === modal) closeModal();
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });
});
</script>
<?php endif; ?>
