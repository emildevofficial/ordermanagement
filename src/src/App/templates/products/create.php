<div class="max-w-lg space-y-4">
    <div>
        <nav class="mb-2 text-sm text-slate-500 dark:text-slate-400" aria-label="Breadcrumb">
            <a href="/products" class="font-medium text-slate-600 transition hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400">Products</a>
            <span class="mx-2 text-slate-400">/</span>
            <span>Create</span>
        </nav>
        <h1 class="text-xl font-bold text-slate-900">Create Product</h1>
        <p class="text-sm text-slate-500 mt-1">Add new product to inventory</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 text-slate-900 dark:text-slate-100">
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Product Name</label>
                <input name="name" required placeholder="Product name" class="w-full h-10 px-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Product Image URL</label>
                <input id="productImageUrl" name="image_url" type="url" placeholder="Paste image link here" class="w-full h-10 px-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                <div id="productImagePreviewBox" class="mt-3 flex min-h-32 items-center justify-center rounded-lg border border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 px-4 py-3 text-center text-sm text-slate-500 dark:text-slate-400">
                    <span id="productImagePreviewText">Image preview will appear here</span>
                    <img id="productImagePreview" src="" alt="Product image preview" class="hidden max-h-40 max-w-full rounded-lg object-contain">
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Price ($)</label>
                    <input name="price" type="number" step="0.01" min="0" required placeholder="0.00" class="w-full h-10 px-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Stock Quantity</label>
                    <input name="stock" type="number" min="0" required placeholder="0" class="w-full h-10 px-3 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-1">
                <a href="/products" class="inline-flex w-auto items-center justify-center h-9 px-3 text-sm text-slate-600 dark:text-slate-300 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition font-medium">Cancel</a>
                <button type="submit" class="inline-flex w-auto items-center justify-center h-9 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition font-medium text-center shadow-sm">Create Product</button>
            </div>
        </form>
    </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('productImageUrl');
    var preview = document.getElementById('productImagePreview');
    var placeholder = document.getElementById('productImagePreviewText');

    if (!input || !preview || !placeholder) return;

    function hidePreview() {
        preview.removeAttribute('src');
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');
    }

    input.addEventListener('input', function () {
        var url = input.value.trim();
        if (url === '') {
            hidePreview();
            return;
        }

        placeholder.classList.add('hidden');
        preview.classList.remove('hidden');
        preview.src = url;
    });

    preview.addEventListener('error', hidePreview);
});
</script>
