<x-layout title="Analytics" page="analytics/index">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-sans" x-data="analyticsPage()">

        {{-- Header --}}
        <div class="mb-8 border-b border-gray-200 pb-4">
            <h1 class="text-2xl font-bold text-gray-900">Budget Analytics</h1>
            <p class="text-sm text-gray-500 mt-1">Comprehensive barangay budget performance and utilization analytics.
            </p>
        </div>

        {{-- Loading State --}}
        <div x-show="isLoading" x-cloak class="flex items-center justify-center py-20">
            <div class="text-center">
                <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-[#128a43] mb-4"></div>
                <p class="text-sm text-gray-600 font-medium tracking-wide uppercase">Processing Analytics...</p>
            </div>
        </div>

        {{-- Error State --}}
        <div x-show="hasError && !isLoading" x-cloak
            class="bg-white border-l-4 border-l-red-600 border-y border-r border-gray-200 rounded-sm p-8 text-center shadow-sm">
            <svg class="w-12 h-12 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Analytics Failed to Load</h3>
            <p x-text="errorMessage" class="text-sm text-gray-600 mb-6"></p>
            <button @click="retryLoad()"
                class="px-5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-sm transition flex items-center justify-center mx-auto gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Retry Connection
            </button>
        </div>

        {{-- Empty State --}}
        <div x-show="isEmpty && !isLoading && !hasError" x-cloak
            class="bg-gray-50 border border-gray-200 rounded-sm p-16 text-center">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h3 class="text-lg font-bold text-gray-900 mb-1">No Data Available</h3>
            <p class="text-sm text-gray-600 mb-6">There are no barangay budgets to analyze yet.</p>
            <a href="/budgets"
                class="inline-flex items-center px-5 py-2 bg-[#0d6efd] hover:bg-blue-700 text-white text-sm font-medium rounded-sm transition shadow-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create First Budget
            </a>
        </div>

        {{-- Main Content --}}
        <div x-show="!isLoading && !hasError && !isEmpty" x-cloak class="space-y-6">

            {{-- Summary Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                <div
                    class="bg-white border-y border-r border-gray-200 border-l-4 border-l-[#0d6efd] rounded-sm p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-1">Total Allocated</p>
                    <p x-text="formatCurrency(stats.totalAllocated)"
                        class="text-2xl font-black text-[#0d6efd] tracking-tight">₱0</p>
                    <p class="text-xs text-gray-400 mt-2 font-medium">Across all barangays</p>
                </div>

                <div
                    class="bg-white border-y border-r border-gray-200 border-l-4 border-l-red-600 rounded-sm p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-1">Total Spent</p>
                    <p x-text="formatCurrency(stats.totalSpent)"
                        class="text-2xl font-black text-red-600 tracking-tight">₱0</p>
                    <p class="text-xs text-gray-400 mt-2 font-medium">Total expenses recorded</p>
                </div>

                <div
                    class="bg-white border-y border-r border-gray-200 border-l-4 border-l-[#128a43] rounded-sm p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-1">Avg Utilization</p>
                    <p x-text="stats.avgUtilization.toFixed(2) + '%'"
                        class="text-2xl font-black text-gray-800 tracking-tight">0%</p>
                    <p class="text-xs text-gray-400 mt-2 font-medium">System-wide average</p>
                </div>

                <div
                    class="bg-white border-y border-r border-gray-200 border-l-4 border-l-gray-500 rounded-sm p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-1">Barangays</p>
                    <p x-text="stats.barangayCount" class="text-2xl font-black text-gray-800 tracking-tight">0</p>
                    <p class="text-xs text-gray-400 mt-2 font-medium">Active budget programs</p>
                </div>

            </div>

            {{-- Charts Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Budget Allocation Doughnut --}}
                <div class="bg-white rounded-sm border border-gray-200 shadow-sm p-6">
                    <h3
                        class="text-sm font-bold uppercase tracking-wide text-gray-700 mb-6 border-b border-gray-100 pb-2">
                        Allocation Distribution
                    </h3>
                    <div class="relative" style="height: 300px;">
                        <canvas id="allocation-doughnut"></canvas>
                    </div>
                </div>

                {{-- Utilization Line Chart --}}
                <div class="bg-white rounded-sm border border-gray-200 shadow-sm p-6">
                    <h3
                        class="text-sm font-bold uppercase tracking-wide text-gray-700 mb-6 border-b border-gray-100 pb-2">
                        Utilization Rates
                    </h3>
                    <div style="height: 300px;">
                        <canvas id="utilization-line"></canvas>
                    </div>
                </div>

            </div>

            {{-- Two-Column Layout for Category and Comparison --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Spending Horizontal Bar --}}
                <div class="bg-white rounded-sm border border-gray-200 shadow-sm p-6">
                    <h3
                        class="text-sm font-bold uppercase tracking-wide text-gray-700 mb-6 border-b border-gray-100 pb-2">
                        Spending by Category
                    </h3>
                    <div style="height: 350px;">
                        <canvas id="category-horizontal"></canvas>
                    </div>
                </div>

                {{-- Budget Comparison Bar --}}
                <div class="bg-white rounded-sm border border-gray-200 shadow-sm p-6">
                    <h3
                        class="text-sm font-bold uppercase tracking-wide text-gray-700 mb-6 border-b border-gray-100 pb-2">
                        Budget vs. Spending
                    </h3>
                    <div style="height: 350px;">
                        <canvas id="comparison-chart"></canvas>
                    </div>
                </div>

            </div>

            {{-- Performance Radar --}}
            <div class="bg-white rounded-sm border border-gray-200 shadow-sm p-6">
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-700 mb-6 border-b border-gray-100 pb-2">
                    Barangay Performance Radar
                </h3>
                <div style="height: 400px; max-width: 800px; margin: 0 auto;">
                    <canvas id="performance-radar"></canvas>
                </div>
            </div>

            {{-- Detailed Table --}}
            <div class="bg-white rounded-sm border border-gray-200 shadow-sm overflow-hidden mt-8">

                {{-- Table Header --}}
                <div class="bg-[#128a43] px-6 py-4">
                    <h3 class="text-lg font-bold text-white tracking-wide">Detailed Barangay Summary</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Barangay</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Allocated</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Spent</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Remaining</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Utilization</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(barangay, index) in barangays" :key="index">
                                <tr class="hover:bg-gray-50 transition cursor-pointer group"
                                    @click="window.location.href = '/budgets/' + barangay.budget_id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900" x-text="barangay.barangay_name">
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5" x-text="barangay.budget_name"></div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-[#0d6efd]"
                                        x-text="formatCurrency(barangay.total_allocated)"></td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-red-600"
                                        x-text="formatCurrency(barangay.total_spent)"></td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900"
                                        x-text="formatCurrency(barangay.total_allocated - barangay.total_spent)"></td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-700"
                                        x-text="barangay.utilization_rate.toFixed(2) + '%'"></td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span x-text="barangay.status"
                                            :class="{
                                                'bg-green-100 text-green-800': barangay.status === 'Under Budget',
                                                'bg-yellow-100 text-yellow-800': barangay.status === 'Near Limit' ||
                                                    barangay.status === 'At Limit',
                                                'bg-red-100 text-red-800': barangay.status === 'Over Budget'
                                            }"
                                            class="px-2 py-1 text-xs font-semibold rounded uppercase"></span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a :href="'/budgets/' + barangay.budget_id"
                                            class="text-[#0d6efd] hover:text-blue-800 transition opacity-0 group-hover:opacity-100">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </main>
</x-layout>
