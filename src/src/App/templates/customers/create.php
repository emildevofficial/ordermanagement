<div class="space-y-6">
    <div>
        <nav class="mb-2 text-sm text-slate-500 dark:text-slate-400" aria-label="Breadcrumb">
            <a href="/customers" class="font-medium text-slate-600 transition hover:text-indigo-600 dark:text-slate-300 dark:hover:text-indigo-400">Customers</a>
            <span class="mx-2 text-slate-400">/</span>
            <span>Add</span>
        </nav>
        <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">
            Add Customer
        </h2>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200 dark:border-slate-700 p-6">
        <form method="POST" action="/customers/create" class="space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Name</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    required
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="Customer name"
                >
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    required
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="customer@example.com"
                >
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2.5 text-sm font-medium transition"
                >
                    Save Customer
                </button>
            </div>
        </form>
    </div>
</div>
