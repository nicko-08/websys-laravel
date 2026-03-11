<x-layout title="Budgets" page="budget/index">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-sans" x-data="budgetManager()">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Budgets</h1>
                <p class="text-sm text-gray-500 mt-1">Manage and allocate government unit budgets.</p>
            </div>
            <button @click="openModal()" id="create-btn"
                class="hidden px-4 py-2 bg-[#0d6efd] hover:bg-blue-700 text-white text-sm font-medium rounded-sm transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Budget
            </button>
        </div>

        {{-- Loading Spinner --}}
        <div id="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#128a43]"></div>
        </div>

        {{-- Main Content / Table Wrapper --}}
        <div id="content" class="hidden">
            <div class="bg-white border border-gray-200 rounded-sm shadow-sm overflow-hidden">
                <div id="budgets-table"></div>
            </div>
            <div id="pagination" class="mt-6 flex justify-center"></div>
        </div>

        <div x-show="showModal" x-cloak @keydown.escape.window="showModal = false"
            class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">

            <div class="flex items-center justify-center min-h-screen px-4">

                {{-- Backdrop --}}
                <div @click="showModal = false"
                    class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>

                {{-- Modal Panel --}}
                <div
                    class="relative bg-white rounded-sm text-left overflow-hidden shadow-xl border border-gray-200 w-full max-w-2xl">

                    {{-- Solid Green Modal Header --}}
                    <div class="bg-[#128a43] px-6 py-4 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-white tracking-wide"
                            x-text="editMode ? 'Edit Budget' : 'Create New Budget'"></h2>
                        <button @click="showModal = false" type="button"
                            class="text-white/80 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form @submit.prevent="saveBudget()" class="px-6 py-6 space-y-5">

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Budget Name</label>
                            <input type="text" x-model="form.name" required
                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Government Unit</label>
                                <select x-model="form.government_unit_id" required
                                    class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white">
                                    <option value="" disabled>Select Unit...</option>
                                    <template x-for="unit in governmentUnits" :key="unit.id">
                                        <option :value="unit.id" x-text="unit.name"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Fiscal Year</label>
                                <select x-model="form.fiscal_year_id" required
                                    class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white">
                                    <option value="" disabled>Select Year...</option>
                                    <template x-for="year in fiscalYears" :key="year.id">
                                        <option :value="year.id" x-text="'FY ' + year.year"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Total Amount (PHP)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" x-model="form.total_amount" step="0.01" required
                                    class="w-full border border-gray-300 rounded-sm pl-7 pr-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white">
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="pt-6 mt-6 border-t border-gray-100 flex justify-end gap-3">
                            <button type="button" @click="showModal = false"
                                class="px-5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-sm transition">
                                Cancel
                            </button>
                            <button type="submit" :disabled="saving"
                                class="px-5 py-2 bg-[#0d6efd] hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white text-sm font-medium rounded-sm transition flex items-center gap-2">
                                <svg x-show="saving" class="animate-spin w-4 h-4 text-white" fill="none"
                                    viewBox="0 0 24 24" style="display: none;">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                        class="opacity-25"></circle>
                                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                        class="opacity-75"></path>
                                </svg>
                                <span x-show="!saving">Save Budget</span>
                                <span x-show="saving" style="display: none;">Processing...</span>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </main>
</x-layout>
