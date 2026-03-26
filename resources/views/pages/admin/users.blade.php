<!-- /resources/views/pages/admin/users.blade.php -->
<x-layout title="User Management" page="admin/users">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="usersManager()">
        <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                <p class="text-sm text-gray-500 mt-1">Manage system users and their roles.</p>
            </div>
            <button @click="openModal()"
                class="px-4 py-2 bg-[#0d6efd] hover:bg-blue-700 text-white text-sm font-medium rounded-sm transition flex items-center gap-2 shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add User
            </button>
        </div>

        <div id="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#128a43]"></div>
        </div>

        <div id="content" class="hidden">
            <div class="bg-white border border-gray-200 rounded-sm shadow-sm overflow-hidden">
                <div id="users-table"></div>
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
                        <h2 class="text-lg font-bold text-white" x-text="editMode ? 'Edit User' : 'Create User'"></h2>
                        <button @click="showModal = false" class="text-white/80 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="saveUser()" class="px-6 py-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" x-model="form.name" required
                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]"
                                placeholder="Juan Dela Cruz">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" x-model="form.email" required
                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]"
                                placeholder="juan.delacruz@gov.ph">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select x-model="form.role" required
                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] cursor-pointer">
                                <option value="">Select role...</option>
                                <option value="admin">Administrator</option>
                                <option value="budget-officer">Budget Officer</option>
                                <option value="auditor">Auditor</option>
                                <option value="user">User</option>
                            </select>
                        </div>

                        <div x-show="!editMode">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" x-model="form.password" :required="!editMode"
                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]"
                                placeholder="Minimum 8 characters">
                        </div>

                        <div x-show="!editMode">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input type="password" x-model="form.password_confirmation" :required="!editMode"
                                class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]"
                                placeholder="Re-enter password">
                        </div>

                        <div x-show="editMode" class="border-t border-gray-200 pt-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" x-model="changePassword"
                                    class="w-4 h-4 text-[#0d6efd] border-gray-300 rounded focus:ring-[#0d6efd]">
                                <span class="ml-2 text-sm text-gray-700">Change password</span>
                            </label>

                            <div x-show="changePassword" class="mt-3 space-y-3">
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">New Password</label>
                                    <input type="password" x-model="form.password"
                                        class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">Confirm New Password</label>
                                    <input type="password" x-model="form.password_confirmation"
                                        class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" @click="showModal = false"
                                class="px-5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-sm cursor-pointer">Cancel</button>
                            <button type="submit" :disabled="saving"
                                class="px-5 py-2 bg-[#0d6efd] hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-sm cursor-pointer">
                                <span x-show="!saving" x-text="editMode ? 'Update' : 'Create'"></span>
                                <span x-show="saving">Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</x-layout>
