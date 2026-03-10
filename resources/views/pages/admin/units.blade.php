<x-layout title="Government Units" page="admin/units">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="unitsManager()">

        {{-- Header Layout --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 border-b border-gray-200 pb-4">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-bold text-gray-900 truncate">Government Units</h1>
                <p class="text-sm text-gray-500 mt-1">Manage barangays and government units.</p>
            </div>

            <button @click="openModal()"
                class="flex-shrink-0 px-4 py-2 bg-[#0d6efd] hover:bg-blue-700 text-white text-sm font-medium rounded-sm transition flex items-center justify-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Unit
            </button>
        </div>

        <div id="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#128a43]"></div>
        </div>

        <div id="content" class="hidden">
            <div class="bg-white border border-gray-200 rounded-sm shadow-sm overflow-hidden">
                <div id="units-table"></div>
            </div>
        </div>

        <div x-show="showModal" x-cloak @keydown.escape.window="showModal = false"
            class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">

                {{-- Backdrop --}}
                <div @click="showModal = false"
                    class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>

                {{-- Modal Panel --}}
                <div
                    class="relative bg-white rounded-sm overflow-hidden shadow-xl border border-gray-200 w-full max-w-lg">
                    <div class="bg-[#128a43] px-6 py-4 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-white tracking-wide"
                            x-text="editMode ? 'Edit Unit' : 'Create Unit'"></h2>
                        <button @click="showModal = false" class="text-white/80 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="saveUnit()" class="px-6 py-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit Name</label>
                            <input type="text" x-model="form.name" required
                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select x-model="form.type" required
                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white">
                                <option value="" disabled>Select unit type...</option>
                                <option value="barangay">Barangay</option>
                                <option value="city">City</option>
                                <option value="municipality">Municipality</option>
                            </select>
                        </div>

                        <div class="pt-4 mt-2 border-t border-gray-100 flex justify-end gap-3">
                            <button type="button" @click="showModal = false"
                                class="px-5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-sm transition">
                                Cancel
                            </button>
                            <button type="submit" :disabled="saving"
                                class="px-5 py-2 bg-[#0d6efd] hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white text-sm font-medium rounded-sm transition flex items-center gap-2">
                                <span x-show="!saving">Save Unit</span>
                                <span x-show="saving" style="display: none;">Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</x-layout>
