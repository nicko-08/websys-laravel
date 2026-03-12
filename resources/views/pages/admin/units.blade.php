<x-layout title="Government Units" page="admin/units">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-12 font-sans text-gray-800" x-data="unitsManager()">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight truncate">Government Units</h1>
                <p class="text-sm text-gray-500 mt-1">Manage barangays and government units.</p>
            </div>

            {{-- Primary Action --}}
            <button @click="openModal()"
                class="flex-shrink-0 px-5 py-2.5 bg-[#0d6efd] hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm flex items-center justify-center gap-2 w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Unit
            </button>
        </div>

        {{-- Loading Spinner --}}
        <div id="loading" class="flex justify-center items-center py-32" x-transition>
            <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-gray-100 border-t-[#0d6efd]">
            </div>
        </div>

        {{-- Main Content / Table Wrapper --}}
        <div id="content" class="hidden" x-transition.opacity.duration.500ms>
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 sm:px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <h2 class="text-base font-bold text-gray-900">All Units</h2>
                </div>

                {{-- Table wrapper specifically designed for mobile swipe-to-scroll --}}
                <div class="w-full overflow-x-auto -webkit-overflow-scrolling-touch">
                    <div id="units-table" class="min-w-full inline-block align-middle"></div>
                </div>
            </div>
        </div>

        {{-- Create / Edit Modal --}}
        <div x-show="showModal" x-cloak @keydown.escape.window="showModal = false"
            class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">

            {{-- Responsive modal wrapper with dvh for mobile address bars --}}
            <div
                class="flex items-end sm:items-center justify-center min-h-[100dvh] px-4 pt-4 pb-6 sm:pb-20 text-center sm:p-0">

                {{-- Backdrop --}}
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showModal = false"
                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

                {{-- Modal Panel --}}
                <div x-show="showModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                    class="relative bg-white rounded-xl sm:rounded-2xl text-left overflow-hidden shadow-2xl border border-gray-100 w-full max-w-lg transform transition-all mt-8 sm:my-8">

                    {{-- Header --}}
                    <div
                        class="border-b border-gray-100 px-5 sm:px-6 py-5 flex justify-between items-center bg-gray-50/50">
                        <h2 class="text-lg font-bold text-gray-900" x-text="editMode ? 'Edit Unit' : 'Create Unit'">
                        </h2>
                        <button @click="showModal = false" type="button"
                            class="text-gray-400 hover:text-gray-600 transition-colors rounded-full p-1.5 hover:bg-gray-200/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form @submit.prevent="saveUnit()" class="px-5 sm:px-8 py-6 sm:py-8 space-y-6">

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Unit Name <span
                                    class="text-[#0d6efd]">*</span></label>
                            <input type="text" x-model="form.name" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-3 sm:py-2.5 text-base sm:text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white transition-shadow">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Type <span
                                    class="text-[#0d6efd]">*</span></label>
                            <select x-model="form.type" required
                                class="w-full border border-gray-200 rounded-lg px-4 py-3 sm:py-2.5 text-base sm:text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] bg-white transition-shadow">
                                <option value="" disabled>Select unit type...</option>
                                <option value="barangay">Barangay</option>
                                <option value="city">City</option>
                                <option value="municipality">Municipality</option>
                            </select>
                        </div>

                        {{-- Responsive Actions Stack --}}
                        <div
                            class="pt-6 mt-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 sm:gap-4 border-t sm:border-t-0 border-gray-100">
                            <button type="button" @click="showModal = false"
                                class="w-full sm:w-auto px-5 py-3 sm:py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg transition-colors shadow-sm">
                                Cancel
                            </button>
                            <button type="submit" :disabled="saving"
                                class="w-full sm:w-auto px-5 py-3 sm:py-2.5 bg-[#0d6efd] hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 shadow-sm">
                                {{-- Added missing SVG spinner from the original code --}}
                                <svg x-show="saving" class="animate-spin w-4 h-4 text-white" fill="none"
                                    viewBox="0 0 24 24" style="display: none;">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                        class="opacity-25"></circle>
                                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                        class="opacity-75"></path>
                                </svg>
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
