<?php
// expects: $products, $success, $error
?>

<div class="w-full space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <nav class="mb-2 text-sm text-slate-500 dark:text-slate-400" aria-label="Breadcrumb">
                <a href="/products" class="font-medium text-slate-600 transition hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400">Products</a>
                <span class="mx-2 text-slate-400">/</span>
                <span>Import / Export</span>
            </nav>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Import / Export</h2>
            <!-- <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Import, export, and bulk edit product inventory using CSV files.</p> -->
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-700 dark:text-emerald-300">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="rounded-lg border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm font-medium text-rose-700 dark:text-rose-300">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <section class="rounded-xl border border-slate-700/70 bg-slate-900 p-5 shadow-lg shadow-slate-950/20 transition hover:border-indigo-500/50">
            <div class="flex h-full flex-col gap-4">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-indigo-400/20 bg-indigo-500/10 text-indigo-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l4-4m-4 4l-4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-white">Export Products</h3>
                        <p class="mt-1 text-xs text-slate-400">Download all real products from inventory.</p>
                        <!-- <p class="mt-1 text-sm text-slate-400">Download all real products from inventory.</p> -->
                    </div>
                </div>
                <div class="grid w-full grid-cols-1 gap-2 sm:w-fit sm:grid-cols-2">
                    <a href="/import-export/export" class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg bg-indigo-600 px-3.5 text-sm font-semibold text-white shadow-sm shadow-indigo-950/30 transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                        Export CSV
                    </a>
                    <a href="/import-export/export-xlsx" class="inline-flex h-10 items-center justify-center whitespace-nowrap rounded-lg border border-slate-600 bg-slate-950/80 px-3.5 text-sm font-semibold text-slate-100 shadow-sm shadow-slate-950/20 transition hover:border-indigo-400 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                        Export XLSX
                    </a>
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-slate-700/70 bg-slate-900 p-5 shadow-lg shadow-slate-950/20 transition hover:border-indigo-500/50">
            <form method="POST" action="/import-export/import" enctype="multipart/form-data" class="flex h-full flex-col gap-4">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-emerald-400/20 bg-emerald-500/10 text-emerald-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21V9m0 0l4 4m-4-4l-4 4M4 7V5a2 2 0 012-2h12a2 2 0 012 2v2"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-white">Import Products</h3>
                        <!-- <p class="mt-1 text-sm text-slate-400">Upload CSV files to create or update inventory products.</p> -->
                        <!-- <p class="mt-1 text-sm text-slate-400">Upload CSV files to create or update inventory products.</p> -->
                        <p class="mt-1 max-w-md text-xs leading-5 text-slate-500">Upload CSV files to create or update inventory products.</p>
                        <!-- <p class="mt-1 max-w-md text-xs leading-5 text-slate-500">Template file is only for import format. To download all real products, use Export CSV or Export XLSX.</p> -->
                    </div>
                </div>
                <div class="flex w-full flex-col gap-2 md:flex-row md:flex-wrap md:items-center">
                    <a href="/import-export/sample" class="inline-flex h-10 shrink-0 items-center justify-center whitespace-nowrap rounded-lg border border-slate-600 bg-slate-950/80 px-3 text-xs font-semibold text-slate-100 shadow-sm shadow-slate-950/20 transition hover:border-indigo-400 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                        Download Import Template
                    </a>
                    <input
                        type="file"
                        name="products_csv"
                        accept=".csv,text/csv"
                        required
                        class="block min-w-0 flex-1 text-sm text-slate-300 file:mr-3 file:h-10 file:rounded-lg file:border file:border-slate-600 file:bg-slate-950 file:px-3 file:text-sm file:font-semibold file:text-slate-100 file:transition hover:file:border-indigo-400 hover:file:bg-slate-800"
                    >
                    <button type="submit" class="inline-flex h-10 shrink-0 items-center justify-center whitespace-nowrap rounded-lg bg-indigo-600 px-4 text-sm font-semibold text-white shadow-sm shadow-indigo-950/30 transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-900">
                        Import CSV
                    </button>
                </div>
            </form>
        </section>
    </div>

    <section class="rounded-xl border border-slate-200 bg-white shadow dark:border-slate-700 dark:bg-slate-800">
        <div class="border-b border-slate-200 px-6 py-5 dark:border-slate-700">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Bulk Edit Products</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Select rows, adjust price, stock, or active state, then apply only those selected changes.</p>
        </div>

        <form method="POST" action="/import-export/bulk-edit">
            <div class="p-5">
                <?php if (!empty($products)): ?>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        <?php foreach ($products as $product): ?>
                            <?php
                                $id = (int)$product['id'];
                                $price = number_format((float)$product['price'], 2, '.', '');
                                $stock = (int)$product['stock'];
                                $isActive = (int)($product['is_active'] ?? 1) === 1;
                                $imageUrl = trim((string)($product['image_url'] ?? ''));
                                $productName = (string)$product['name'];
                                $productInitial = mb_strtoupper(mb_substr($productName, 0, 1, 'UTF-8'), 'UTF-8');
                            ?>
                            <article class="group overflow-hidden rounded-xl border border-slate-700/80 bg-slate-900 shadow-sm shadow-slate-950/20 transition hover:border-indigo-400/70 hover:shadow-lg hover:shadow-indigo-950/20">
                                <div class="relative h-28 overflow-hidden border-b border-slate-800 bg-slate-950">
                                    <?php if ($imageUrl !== ''): ?>
                                        <img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES) ?>" alt="<?= htmlspecialchars($productName, ENT_QUOTES) ?>" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                    <?php else: ?>
                                        <div class="absolute inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-slate-950"></div>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg border border-white/10 bg-slate-900/85 text-xl font-bold text-white shadow-md shadow-slate-950/40">
                                                <?= htmlspecialchars($productInitial) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <label class="absolute left-3 top-3 inline-flex items-center gap-2 rounded-lg border border-indigo-400/30 bg-slate-950/85 px-2 py-1 text-xs font-semibold text-slate-100 shadow-sm shadow-slate-950/30">
                                        <input type="checkbox" name="selected[]" value="<?= $id ?>" class="h-4 w-4 rounded border-slate-500 bg-slate-900 text-indigo-500 focus:ring-indigo-500">
                                        Select
                                    </label>
                                </div>

                                <div class="space-y-3 p-3">
                                    <div class="min-w-0">
                                        <h4 class="truncate text-sm font-semibold text-white"><?= htmlspecialchars($productName) ?></h4>
                                        <p class="mt-0.5 text-xs text-slate-500">ID #<?= $id ?></p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div class="rounded-lg border border-slate-800 bg-slate-950/70 px-2.5 py-2">
                                            <p class="text-slate-500">Current Price</p>
                                            <p class="mt-1 font-mono font-semibold text-slate-100">$<?= $price ?></p>
                                        </div>
                                        <div class="rounded-lg border border-slate-800 bg-slate-950/70 px-2.5 py-2">
                                            <p class="text-slate-500">Current Stock</p>
                                            <p class="mt-1 font-mono font-semibold text-slate-100"><?= $stock ?></p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <label class="block">
                                            <span class="text-xs font-medium text-slate-400">New Price</span>
                                            <input
                                                type="number"
                                                name="products[<?= $id ?>][price]"
                                                value="<?= $price ?>"
                                                min="0"
                                                step="0.01"
                                                class="mt-1 h-8 w-full rounded-lg border border-slate-700 bg-slate-950 px-2.5 text-sm font-medium text-white outline-none transition placeholder:text-slate-600 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/30"
                                            >
                                        </label>
                                        <label class="block">
                                            <span class="text-xs font-medium text-slate-400">New Stock</span>
                                            <input
                                                type="number"
                                                name="products[<?= $id ?>][stock]"
                                                value="<?= $stock ?>"
                                                min="0"
                                                step="1"
                                                class="mt-1 h-8 w-full rounded-lg border border-slate-700 bg-slate-950 px-2.5 text-sm font-medium text-white outline-none transition placeholder:text-slate-600 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/30"
                                            >
                                        </label>
                                    </div>

                                    <label class="flex items-center justify-between rounded-lg border border-slate-800 bg-slate-950/70 px-2.5 py-2 text-sm text-slate-200">
                                        <span class="font-medium">Active</span>
                                        <input type="checkbox" name="products[<?= $id ?>][is_active]" value="1" <?= $isActive ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-indigo-500 focus:ring-indigo-500">
                                    </label>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="rounded-xl border border-slate-700 bg-slate-900 px-6 py-12 text-center text-slate-300">
                        No products found.
                    </div>
                <?php endif; ?>
            </div>

            <div class="sticky bottom-0 flex justify-end border-t border-slate-200 bg-white px-6 py-4 dark:border-slate-700 dark:bg-slate-800">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                    Apply Bulk Changes
                </button>
            </div>
        </form>
    </section>
</div>
