<x-layout title="Expense Detail" page="expense/details">

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-sans" x-data="expenseDetailManager()">

        {{-- Loading Spinner --}}
        <div id="loading" class="flex justify-center items-center py-20">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#128a43]"></div>
        </div>

        <div id="content" class="hidden space-y-6">

            {{-- Navigation --}}
            <div>
                <a href="/expenses"
                    class="inline-flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-[#0d6efd] transition group">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Expenses
                </a>
            </div>

            {{-- Expense Card --}}
            <div class="bg-white rounded-sm shadow-sm border border-gray-200 flex flex-col">

                <div class="p-6 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-6">
                    {{-- Description & Date --}}
                    <div class="flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">Expense Description
                        </p>
                        <h1 id="expense-description" class="text-2xl font-bold text-gray-900 leading-snug"></h1>

                        <div class="mt-4 flex items-center gap-2 text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span id="expense-date" class="text-sm font-medium uppercase tracking-wide"></span>
                        </div>
                    </div>

                    {{-- Amount Highlight --}}
                    <div class="sm:text-right bg-red-50 px-6 py-4 rounded-sm border border-red-100 min-w-[220px]">
                        <p class="text-xs text-red-600 uppercase tracking-wide font-bold mb-1">Total Expense</p>
                        <p id="expense-amount" class="text-3xl font-black text-red-600 tracking-tight"></p>
                    </div>
                </div>

                {{-- Action Footer --}}
                <div id="actions-section"
                    class="hidden px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-wrap gap-3 sm:justify-end">
                    <button @click="openEditModal()"
                        class="px-5 py-2 bg-[#0d6efd] hover:bg-blue-700 text-white text-sm font-medium rounded-sm transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Edit Expense
                    </button>
                    <button @click="deleteExpenseAction()"
                        class="px-5 py-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-sm font-medium rounded-sm transition flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>

            {{-- Budget Impact Card --}}
            <div class="bg-white rounded-sm shadow-sm border border-gray-200 overflow-hidden">

                <div class="bg-[#128a43] px-6 py-4 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-white tracking-wide">Budget Impact & Details</h2>
                </div>

                <div class="p-6 space-y-8">

                    {{-- Budget Item Source Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Budget
                                Source</label>
                            <p id="budget-name" class="text-base font-medium text-gray-900"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Budget
                                Item</label>
                            <p id="budget-item-name" class="text-base font-medium text-gray-900"></p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Category</label>
                            <span id="budget-category"
                                class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200"></span>
                        </div>
                    </div>

                    {{-- Financial Health Breakdown --}}
                    <div class="bg-gray-50 border border-gray-200 rounded-sm p-5">
                        <h3
                            class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-4 border-b border-gray-200 pb-2">
                            Allocation Status</h3>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <span class="block text-xs font-medium text-gray-500 uppercase">Allocated</span>
                                <span id="allocated-amount" class="block text-lg font-bold text-[#128a43] mt-1"></span>
                            </div>
                            <div>
                                <span class="block text-xs font-medium text-gray-500 uppercase">Total Spent</span>
                                <span id="total-spent" class="block text-lg font-bold text-red-600 mt-1"></span>
                            </div>
                            <div>
                                <span class="block text-xs font-medium text-gray-500 uppercase">Remaining</span>
                                <span id="remaining-budget" class="block text-lg font-bold text-[#128a43] mt-1"></span>
                            </div>
                            <div>
                                <span class="block text-xs font-medium text-gray-500 uppercase">Utilization</span>
                                <span id="utilization-rate" class="block text-lg font-bold text-gray-900 mt-1"></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- Edit Modal --}}
        <div x-show="showEditModal" x-cloak @keydown.escape.window="closeEditModal()"
            class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">

            <div class="flex items-center justify-center min-h-screen px-4">

                {{-- Backdrop --}}
                <div @click="closeEditModal()" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity">
                </div>

                {{-- Modal Panel --}}
                <div
                    class="relative bg-white rounded-sm overflow-hidden shadow-xl border border-gray-200 w-full max-w-2xl">

                    {{-- Header --}}
                    <div class="bg-[#128a43] px-6 py-4 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-white tracking-wide">Edit Expense</h2>
                        <button @click="closeEditModal()" type="button"
                            class="text-white/80 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form @submit.prevent="saveExpense()" class="px-6 py-6 space-y-5">

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Budget Item <span
                                    class="text-red-500">*</span></label>
                            <select x-model="editForm.budget_item_id" required
                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white">
                                <option value="" disabled>Select Budget Item...</option>
                                <template x-for="item in budgetItems" :key="item.id">
                                    <option :value="item.id"
                                        x-text="`${item.name} - ${item.budget?.name || 'N/A'}`"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Description <span
                                    class="text-red-500">*</span></label>
                            <textarea x-model="editForm.description" required rows="3"
                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Amount (PHP) <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <input type="number" x-model="editForm.amount" step="0.01" min="0.01"
                                        required
                                        class="w-full border border-gray-300 rounded-sm pl-7 pr-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white text-red-600 font-semibold">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Transaction Date <span
                                        class="text-red-500">*</span></label>
                                <input type="date" x-model="editForm.transaction_date" required
                                    class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white">
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="pt-6 mt-6 border-t border-gray-100 flex justify-end gap-3">
                            <button type="button" @click="closeEditModal()"
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
                                <span x-show="!saving">Update Expense</span>
                                <span x-show="saving" style="display: none;">Saving...</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </main>
</x-layout>
