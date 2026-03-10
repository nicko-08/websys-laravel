<x-layout title="Fiscal Years" page="admin/fiscal-years">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="fiscalYearsManager()">
        <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Fiscal Years</h1>
                <p class="text-sm text-gray-500 mt-1">Manage fiscal year periods.</p>
            </div>
            <button @click="openModal()"
                class="px-4 py-2 bg-[#0d6efd] hover:bg-blue-700 text-white text-sm font-medium rounded-sm transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Fiscal Year
            </button>
        </div>

        <div id="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#128a43]"></div>
        </div>

        <div id="content" class="hidden">
            <div class="bg-white border border-gray-200 rounded-sm shadow-sm overflow-hidden">
                <div id="years-table"></div>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="showModal" x-cloak @keydown.escape.window="showModal = false"
            class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="showModal = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>
                <div
                    class="relative bg-white rounded-sm overflow-hidden shadow-xl border border-gray-200 w-full max-w-lg">
                    <div class="bg-[#128a43] px-6 py-4 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-white"
                            x-text="editMode ? 'Edit Fiscal Year' : 'Create Fiscal Year'"></h2>
                        <button @click="showModal = false" class="text-white/80 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="saveYear()" class="px-6 py-6 space-y-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Year</label>
                            <input type="number" x-model="form.year" required min="2020" max="2100"
                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Start Date</label>
                                <input type="date" x-model="form.start_date" required
                                    class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">End Date</label>
                                <input type="date" x-model="form.end_date" required
                                    class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]">
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" x-model="form.is_active" id="is_active"
                                class="w-4 h-4 text-[#0d6efd] border-gray-300 rounded focus:ring-[#0d6efd]">
                            <label for="is_active" class="ml-2 text-sm text-gray-700">Set as Active Fiscal Year</label>
                        </div>

                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" @click="showModal = false"
                                class="px-5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-sm">Cancel</button>
                            <button type="submit" :disabled="saving"
                                class="px-5 py-2 bg-[#0d6efd] hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-sm">
                                <span x-show="!saving">Save</span>
                                <span x-show="saving">Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</x-layout>
