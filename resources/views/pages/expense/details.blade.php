<x-layout title="Expense Detail" page="expense/details">

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 font-sans text-gray-800" x-data="expenseDetailManager()">

        {{-- Loading Spinner --}}
        <div id="loading" class="flex justify-center items-center py-32" x-transition>
            <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-gray-100 border-t-[#128a43]">
            </div>
        </div>

        <div id="content" class="hidden space-y-6" x-transition.opacity.duration.500ms>

            {{-- Navigation --}}
            <nav>
                <a href="/expenses"
                    class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors group">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Expenses
                </a>
            </nav>

            {{-- Main Unified Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                <div class="p-6 sm:p-10">
                    {{-- Header & Actions --}}
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-6">
                        <div>
                            <p class="text-sm font-semibold text-gray-400 tracking-wide mb-1 uppercase">Expense Details
                            </p>
                            <h1 id="expense-description"
                                class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight"></h1>
                            <div class="flex items-center gap-2 text-gray-500 mt-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span id="expense-date" class="text-sm font-medium"></span>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div id="actions-section" class="hidden flex flex-wrap gap-3">
                            <button @click="openEditModal()"
                                class="px-4 py-2 bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-all flex items-center gap-2 shadow-sm">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Edit
                            </button>
                            <button @click="deleteExpenseAction()"
                                class="px-4 py-2 bg-white border border-gray-200 hover:border-red-200 hover:bg-red-50 text-gray-700 hover:text-red-600 text-sm font-medium rounded-lg transition-all flex items-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>

                    {{-- Amount Highlight Banner --}}
                    <div
                        class="mt-8 bg-gray-50 rounded-xl p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border border-gray-100">
                        <span class="text-sm font-bold text-gray-500 uppercase tracking-widest">Total Amount</span>
                        <span id="expense-amount" class="text-4xl font-black text-gray-900 tracking-tight"></span>
                    </div>

                    {{-- Classification Grid --}}
                    <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-8">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Budget
                                Source</label>
                            <p id="budget-name" class="text-base font-medium text-gray-900"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Budget
                                Item</label>
                            <p id="budget-item-name" class="text-base font-medium text-gray-900"></p>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Category</label>
                            <span id="budget-category"
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700"></span>
                        </div>
                    </div>

                    <hr class="my-10 border-gray-100">

                    {{-- Budget Impact Section --}}
                    <div>
                        <h2
                            class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#128a43]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            Budget Impact
                        </h2>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                <span
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Allocated</span>
                                <span id="allocated-amount" class="block text-xl font-bold text-gray-900 mt-2"></span>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Total
                                    Spent</span>
                                <span id="total-spent" class="block text-xl font-bold text-gray-900 mt-2"></span>
                            </div>
                            <div class="bg-[#128a43]/5 p-4 rounded-lg border border-[#128a43]/10">
                                <span
                                    class="block text-xs font-semibold text-[#128a43] uppercase tracking-wide">Remaining</span>
                                <span id="remaining-budget" class="block text-xl font-bold text-[#128a43] mt-2"></span>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                <span
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Utilization</span>
                                <span id="utilization-rate" class="block text-xl font-bold text-gray-900 mt-2"></span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- Edit Modal --}}
        <div x-show="showEditModal" x-cloak @keydown.escape.window="closeEditModal()"
            class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">

            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">

                {{-- Backdrop --}}
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @click="closeEditModal()"
                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity">
                </div>

                {{-- Modal Panel --}}
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative bg-white rounded-xl text-left overflow-hidden shadow-2xl border border-gray-100 w-full max-w-2xl transform transition-all my-8">

                    {{-- Header --}}
                    <div class="border-b border-gray-100 px-6 py-5 flex justify-between items-center bg-gray-50/50">
                        <h2 class="text-lg font-bold text-gray-900">Edit Expense</h2>
                        <button @click="closeEditModal()" type="button"
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
                            <select x-model="editForm.budget_item_id" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white transition-shadow">
                                <option value="" disabled>Select Budget Item...</option>
                                <template x-for="item in budgetItems" :key="item.id">
                                    <option :value="item.id"
                                        x-text="`${item.name} - ${item.budget?.name || 'N/A'}`"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description <span
                                    class="text-[#0d6efd]">*</span></label>
                            <textarea x-model="editForm.description" required rows="3"
                                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white transition-shadow resize-none"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Amount (PHP) <span
                                        class="text-[#0d6efd]">*</span></label>
                                <div class="relative rounded-lg">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm font-medium">₱</span>
                                    </div>
                                    <input type="number" x-model="editForm.amount" step="0.01" min="0.01"
                                        required
                                        class="w-full border border-gray-200 rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white text-gray-900 font-bold transition-shadow">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Transaction Date <span
                                        class="text-[#0d6efd]">*</span></label>
                                <input type="date" x-model="editForm.transaction_date" required
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white transition-shadow">
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="pt-6 mt-4 flex justify-end gap-3">
                            <button type="button" @click="closeEditModal()"
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
                                <span x-show="!saving">Save Changes</span>
                                <span x-show="saving" style="display: none;">Saving...</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </main>
</x-layout>
