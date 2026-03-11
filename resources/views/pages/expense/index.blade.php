<x-layout title="Expenses" page="expense/index">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-sans" x-data="expenseManager()">

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Expense Tracking</h1>
            <p class="text-sm text-gray-600 mt-1">Monitor and manage all expenses across budgets.</p>
        </div>

        {{-- Summary Cards with Icons --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total Expenses --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-red-50 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Total Expenses</p>
                <p id="total-expenses" class="text-3xl font-bold text-gray-900">₱0.00</p>
            </div>

            {{-- This Month --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">This Month</p>
                <p id="month-expenses" class="text-3xl font-bold text-blue-600">₱0.00</p>
            </div>

            {{-- Transactions --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-purple-50 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Transactions</p>
                <p id="transaction-count" class="text-3xl font-bold text-gray-900">0</p>
            </div>

            {{-- Average Amount --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 bg-green-50 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Avg. Amount</p>
                <p id="avg-expenses" class="text-3xl font-bold text-gray-900">₱0.00</p>
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Filters</h2>
                <button @click="clearFilters()"
                    class="text-sm font-medium text-[#0d6efd] hover:text-blue-700 hover:underline transition">
                    Clear All
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">Budget</label>
                    <select x-model="filters.budget_id" @change="applyFilters()"
                        class="w-full h-10 border border-gray-300 rounded-sm px-3 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]">
                        <option value="">All Budgets</option>
                        <template x-for="budget in budgets" :key="budget.id">
                            <option :value="budget.id" x-text="budget.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">Budget Item</label>
                    <select x-model="filters.budget_item_id" @change="applyFilters()"
                        class="w-full h-10 border border-gray-300 rounded-sm px-3 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]">
                        <option value="">All Items</option>
                        <template x-for="item in budgetItems" :key="item.id">
                            <option :value="item.id"
                                x-text="item.name + ' (' + (item.budget?.name || 'N/A') + ')'">
                            </option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" x-model="filters.from_date" @change="applyFilters()"
                        class="w-full h-10 border border-gray-300 rounded-sm px-3 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" x-model="filters.to_date" @change="applyFilters()"
                        class="w-full h-10 border border-gray-300 rounded-sm px-3 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]">
                </div>
            </div>
        </div>

        {{-- Loading Spinner --}}
        <div id="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#128a43]"></div>
        </div>

        {{-- Main Content --}}
        <div id="content" class="hidden">
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden mb-6">
                <div class="bg-[#128a43] px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white tracking-wide">Expense Records</h2>
                    <button @click="openModal()" id="create-btn"
                        class="hidden px-4 py-2 bg-white text-[#128a43] hover:bg-gray-50 text-sm font-medium rounded-sm transition shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Record Expense
                    </button>
                </div>
                <div id="expenses-table" class="overflow-x-auto"></div>
            </div>
            <div id="pagination" class="flex justify-center"></div>
        </div>

        {{-- Create Modal --}}
        <div x-show="showModal" x-cloak @keydown.escape.window="showModal = false"
            class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">

            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="showModal = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

                <div
                    class="relative bg-white rounded-sm overflow-hidden shadow-xl border border-gray-200 w-full max-w-2xl">
                    <div class="bg-[#128a43] px-6 py-4 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-white tracking-wide">Record New Expense</h2>
                        <button @click="showModal = false" type="button"
                            class="text-white/80 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="saveExpense()" class="px-6 py-6 space-y-5">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Budget Item <span
                                    class="text-red-500">*</span></label>
                            <select x-model="form.budget_item_id" required
                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white">
                                <option value="" disabled>Select Budget Item...</option>
                                <template x-for="item in budgetItems" :key="item.id">
                                    <option :value="item.id"
                                        x-text="`${item.name} - ${item.budget?.name || 'N/A'} (₱${parseFloat(item.allocated_amount).toLocaleString('en-PH', {minimumFractionDigits: 2})})`">
                                    </option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Description <span
                                    class="text-red-500">*</span></label>
                            <textarea x-model="form.description" required rows="3"
                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white"
                                placeholder="e.g., Office supplies purchase for Q1"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Amount (PHP) <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" x-model="form.amount" step="0.01" min="0.01"
                                        required
                                        class="w-full border border-gray-300 rounded-sm pl-7 pr-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Transaction Date <span
                                        class="text-red-500">*</span></label>
                                <input type="date" x-model="form.transaction_date" required
                                    class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white">
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
