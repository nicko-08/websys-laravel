<x-layout title="Audit Logs" page="admin/audit-logs">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="auditLogsManager()">
        <div class="mb-6 border-b border-gray-200 pb-4">
            <h1 class="text-2xl font-bold text-gray-900">Audit Logs</h1>
            <p class="text-sm text-gray-500 mt-1">System activity and change history</p>
        </div>

        {{-- Filters --}}
        <div class="mb-6 bg-white border border-gray-200 rounded-sm shadow-sm p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                    <select x-model="filters.action" @change="loadLogs()"
                        class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] cursor-pointer">
                        <option value="">All Actions</option>
                        <option value="created">Created</option>
                        <option value="updated">Updated</option>
                        <option value="deleted">Deleted</option>
                        <option value="account_activated">Account Activated</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Resource</label>
                    <select x-model="filters.resource" @change="loadLogs()"
                        class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] cursor-pointer">
                        <option value="">All Resources</option>
                        <option value="User">Users</option>
                        <option value="Budget">Budgets</option>
                        <option value="BudgetItem">Budget Items</option>
                        <option value="Expense">Expenses</option>
                        <option value="GovernmentUnit">Government Units</option>
                        <option value="FiscalYear">Fiscal Years</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" x-model="filters.from" @change="loadLogs()"
                        class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] cursor-pointer">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" x-model="filters.to" @change="loadLogs()"
                        class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd] cursor-pointer">
                </div>
            </div>

            <div class="mt-4 flex items-center gap-3">
                <button @click="resetFilters()"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-sm transition cursor-pointer">
                    Clear Filters
                </button>
                <span x-show="logs.length > 0" class="text-sm text-gray-600"
                    x-text="`${logs.length} records found`"></span>
            </div>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#128a43]"></div>
        </div>

        {{-- Table --}}
        <div x-show="!loading" class="bg-white border border-gray-200 rounded-sm shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Timestamp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Resource</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IP Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Details</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="log in logs" :key="log.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                    x-text="formatDate(log.performed_at)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                    x-text="log.performed_by?.name || 'System'"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="getActionClass(log.action)" x-text="log.action"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span x-text="log.resource_display || `${log.resource} #${log.resource_id}`"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    x-text="log.ip_address || 'N/A'"></td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <button @click="viewDetails(log)"
                                        class="text-[#0d6efd] hover:underline cursor-pointer">
                                        View
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="logs.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                No audit logs found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Details Modal --}}
        <div x-show="showDetails" x-cloak @keydown.escape.window="showDetails = false"
            class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="showDetails = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>
                <div
                    class="relative bg-white rounded-sm overflow-hidden shadow-xl border border-gray-200 w-full max-w-2xl">
                    <div class="bg-[#128a43] px-6 py-4 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-white">Audit Log Details</h2>
                        <button @click="showDetails = false" class="text-white/80 hover:text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="px-6 py-6">
                        <pre class="bg-gray-50 border border-gray-200 rounded p-4 text-xs overflow-auto max-h-96"
                            x-text="JSON.stringify(selectedLog, null, 2)"></pre>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-layout>
