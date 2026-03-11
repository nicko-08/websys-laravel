<x-layout title="Budget Detail" page="budget/details">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-sans" x-data="budgetDetailManager()">

        <div id="loading" class="flex justify-center items-center py-20">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#128a43]"></div>
        </div>

        <div id="content" class="hidden space-y-8">

            <div>
                <a href="/budgets"
                    class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-[#0d6efd] transition group">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Budgets
                </a>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <h1 id="budget-name" class="text-3xl font-bold text-gray-900 leading-tight"></h1>
                    <div class="mt-2 flex items-center gap-2">
                        <span id="budget-meta"
                            class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 uppercase tracking-wide border border-gray-200"></span>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-sm px-6 py-4 min-w-[220px]">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Total Budget</p>
                    <p id="budget-amount" class="text-3xl font-black text-[#128a43] tracking-tight"></p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-sm shadow-sm overflow-hidden">
                <div class="bg-[#128a43] px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white tracking-wide">Budget Items</h2>
                    <button @click="openModal()" id="add-item-btn"
                        class="hidden px-4 py-1.5 bg-white text-[#128a43] hover:bg-gray-50 text-sm font-medium rounded-sm transition shadow-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Item
                    </button>
                </div>
                <div id="items-table" class="overflow-x-auto"></div>
            </div>

        </div>

        <!-- Modal -->
        <div x-show="showModal" x-cloak @keydown.escape.window="showModal = false"
            class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="showModal = false"
                    class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>

                <div
                    class="relative bg-white rounded-sm text-left overflow-hidden shadow-xl border border-gray-200 w-full max-w-2xl">
                    <div class="bg-[#128a43] px-6 py-4 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-white tracking-wide"
                            x-text="editMode ? 'Edit Budget Item' : 'Create Budget Item'"></h2>
                        <button @click="showModal = false" type="button"
                            class="text-white/80 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="saveItem()" class="px-6 py-6 space-y-5">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Item Name</label>
                            <input type="text" x-model="form.name" required
                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Item Code</label>
                                <input type="text" x-model="form.code" required
                                    class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white">
                            </div>

                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Category</label>
                                <select x-model="form.budget_category_id" required
                                    class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white">
                                    <option value="" disabled>Select Category...</option>
                                    <template x-for="cat in categories" :key="cat.id">
                                        <option :value="cat.id" x-text="cat.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Allocated Amount (PHP)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" x-model="form.allocated_amount" step="0.01" required
                                    class="w-full border border-gray-300 rounded-sm pl-7 pr-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white">
                            </div>
                        </div>

                        <div class="pt-6 mt-6 border-t border-gray-100 flex justify-end gap-3">
                            <button type="button" @click="showModal = false"
                                class="px-5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-sm transition">
                                Cancel
                            </button>
                            <button type="submit" :disabled="saving"
                                class="px-5 py-2 bg-[#0d6efd] hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white text-sm font-medium rounded-sm transition flex items-center gap-2">
                                <svg x-show="saving" class="animate-spin w-4 h-4 text-white" fill="none"
                                    viewBox="0 0 24 24" style="display: none;">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" class="opacity-25"></circle>
                                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                        class="opacity-75"></path>
                                </svg>
                                <span x-show="!saving">Save Item</span>
                                <span x-show="saving" style="display: none;">Processing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>
</x-layout>
