<x-layout title="Expenses" page="expense/index">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 font-sans text-gray-800" x-data="expenseManager()">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Expense Tracking</h1>
                <p class="text-sm text-gray-500 mt-1">Monitor and manage all expenses across budgets.</p>
            </div>

            {{-- Primary Action --}}
            <button @click="openModal()" id="create-btn"
                class="hidden px-5 py-2.5 bg-[#0d6efd] hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm flex items-center justify-center gap-2 w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Record Expense
            </button>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            {{-- Total Expenses --}}
            <div
                class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-200 group">
                <div class="flex items-center gap-3 mb-3">
                    <div
                        class="w-2.5 h-2.5 rounded-full bg-[#128a43]/90 ring-4 ring-[#128a43]/10 group-hover:ring-green-500/20 transition-all">
                    </div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Expenses</h3>
                </div>
                <p id="total-expenses" class="text-3xl font-black text-gray-900 tracking-tight">₱0.00</p>
                <p class="text-xs font-medium text-gray-400 mt-2">All-time recorded expenses</p>
            </div>

            {{-- This Month --}}
            <div
                class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-200 group">
                <div class="flex items-center gap-3 mb-3">
                    <div
                        class="w-2.5 h-2.5 rounded-full bg-[#128a43] ring-4 ring-[#128a43]/10 group-hover:ring-green-500/20 transition-all">
                    </div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">This Month</h3>
                </div>
                <p id="month-expenses" class="text-3xl font-black text-gray-900 tracking-tight">₱0.00</p>
                <p class="text-xs font-medium text-gray-400 mt-2">Current month spending</p>
            </div>

            {{-- Transactions --}}
            <div
                class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-200 group">
                <div class="flex items-center gap-3 mb-3">
                    <div
                        class="w-2.5 h-2.5 rounded-full bg-[#128a43]/90 ring-4 ring-[#128a43]/10 group-hover:ring-green-500/20 transition-all">
                    </div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Transactions</h3>
                </div>
                <p id="transaction-count" class="text-3xl font-black text-gray-900 tracking-tight">0</p>
                <p class="text-xs font-medium text-gray-400 mt-2">Total number of records</p>
            </div>

            {{-- Average Amount --}}
            <div
                class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md hover:border-gray-300 transition-all duration-200 group">
                <div class="flex items-center gap-3 mb-3">
                    <div
                        class="w-2.5 h-2.5 rounded-full bg-[#128a43]/90 ring-4 ring-[#128a43]/10 group-hover:ring-green-500/20 transition-all">
                    </div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Avg. Amount</h3>
                </div>
                <p id="avg-expenses" class="text-3xl font-black text-gray-900 tracking-tight">₱0.00</p>
                <p class="text-xs font-medium text-gray-400 mt-2">Per transaction average</p>
            </div>

        </div>

        {{-- Filters Section --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 sm:p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter Records
                </h2>
                <button @click="clearFilters()"
                    class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                    Clear All
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Budget</label>
                    <select x-model="filters.budget_id" @change="applyFilters()"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white transition-shadow">
                        <option value="">All Budgets</option>
                        <template x-for="budget in budgets" :key="budget.id">
                            <option :value="budget.id" x-text="budget.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Budget
                        Item</label>
                    <select x-model="filters.budget_item_id" @change="applyFilters()"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white transition-shadow">
                        <option value="">All Items</option>
                        <template x-for="item in budgetItems" :key="item.id">
                            <option :value="item.id"
                                x-text="item.name + ' (' + (item.budget?.name || 'N/A') + ')'"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">From
                        Date</label>
                    <input type="date" x-model="filters.from_date" @change="applyFilters()"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white transition-shadow">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">To
                        Date</label>
                    <input type="date" x-model="filters.to_date" @change="applyFilters()"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white transition-shadow">
                </div>
            </div>
        </div>

        {{-- Loading Spinner --}}
        <div id="loading" class="flex justify-center items-center py-20" x-transition>
            <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-gray-100 border-t-[#0d6efd]">
            </div>
        </div>

        {{-- Main Content --}}
        <div id="content" class="hidden" x-transition.opacity.duration.500ms>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-base font-bold text-gray-900">Recent Transactions</h2>
                </div>
                <div id="expenses-table" class="overflow-x-auto"></div>
            </div>
            <div id="pagination" class="flex justify-center mt-6"></div>
        </div>

        {{-- Create Modal --}}
        <div x-show="showModal" x-cloak @keydown.escape.window="showModal = false"
            class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">

            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">

                {{-- Backdrop --}}
                <div x-show="showModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="showModal = false"
                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity">
                </div>

                {{-- Modal Panel --}}
                <div x-show="showModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative bg-white rounded-xl text-left overflow-hidden shadow-2xl border border-gray-100 w-full max-w-2xl transform transition-all my-8">

                    {{-- Header --}}
                    <div class="border-b border-gray-100 px-6 py-5 flex justify-between items-center bg-gray-50/50">
                        <h2 class="text-lg font-bold text-gray-900">Record New Expense</h2>
                        <button @click="showModal = false" type="button"
                            class="text-gray-400 hover:text-gray-600 transition-colors rounded-full p-1 hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form @submit.prevent="saveExpense()" class="px-6 py-6 sm:p-8 space-y-6">

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Budget Item <span
                                    class="text-[#0d6efd]">*</span></label>
                            <select x-model="form.budget_item_id" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white transition-shadow">
                                <option value="" disabled>Select Budget Item...</option>
                                <template x-for="item in budgetItems" :key="item.id">
                                    <option :value="item.id"
                                        x-text="`${item.name} - ${item.budget?.name || 'N/A'} (₱${parseFloat(item.allocated_amount).toLocaleString('en-PH', {minimumFractionDigits: 2})})`">
                                    </option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description <span
                                    class="text-[#0d6efd]">*</span></label>
                            <textarea x-model="form.description" required rows="3"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white transition-shadow resize-none"
                                placeholder="e.g., Office supplies purchase for Q1"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Amount (PHP) <span
                                        class="text-[#0d6efd]">*</span></label>
                                <div class="relative rounded-lg">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm font-medium">₱</span>
                                    </div>
                                    <input type="number" x-model="form.amount" step="0.01" min="0.01"
                                        required
                                        class="w-full border border-gray-200 rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white text-gray-900 font-bold transition-shadow">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Transaction Date <span
                                        class="text-[#0d6efd]">*</span></label>
                                <input type="date" x-model="form.transaction_date" required
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white transition-shadow">
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="pt-6 mt-4 flex justify-end gap-3">
                            <button type="button" @click="showModal = false"
                                class="px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                                Cancel
                            </button>
                            <button type="submit" :disabled="saving"
                                class="px-5 py-2.5 bg-[#0d6efd] hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2 shadow-sm">
                                <svg x-show="saving" class="animate-spin w-4 h-4 text-white" fill="none"
                                    viewBox="0 0 24 24" style="display: none;">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" class="opacity-25"></circle>
                                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                        class="opacity-75"></path>
                                </svg>
                                <span x-show="!saving">Save Expense</span>
                                <span x-show="saving" style="display: none;">Processing...</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </main>
</x-layout>
