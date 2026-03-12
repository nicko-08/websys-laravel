<x-layout title="Analytics" page="analytics/index">
    <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 font-sans" x-data="analyticsPage()">

        {{-- Header --}}
        <div class="mb-8 md:mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">Budget Analytics</h1>
                <p class="text-sm text-gray-500 mt-2">Comprehensive budget performance and utilization
                    analytics.</p>
            </div>
        </div>

        {{-- Loading State --}}
        <div x-show="isLoading" x-cloak class="flex flex-col items-center justify-center min-h-[400px]">
            <div class="relative w-10 h-10 mb-4">
                <div class="absolute inset-0 rounded-full border-[3px] border-gray-100"></div>
                {{-- Spinning indicator --}}
                <div
                    class="absolute inset-0 rounded-full border-[3px] border-[#128a43] border-t-transparent animate-spin">
                </div>
            </div>
            <p class="text-sm font-medium text-gray-500 animate-pulse">Loading analytics...</p>
        </div>

        {{-- Error State --}}
        <div x-show="hasError && !isLoading" x-cloak
            class="max-w-xl mx-auto mt-12 bg-white border border-red-100 rounded-xl shadow-sm p-8 text-center">

            <div
                class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-50 ring-8 ring-red-50/50 mb-5">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <h3 class="text-base font-semibold text-gray-900 mb-2">Analytics Failed to Load</h3>
            <p x-text="errorMessage" class="text-sm text-gray-500 mb-6"></p>

            <button @click="retryLoad()"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 hover:text-gray-900 text-gray-700 text-sm font-medium rounded-lg transition-colors shadow-sm gap-2 focus:outline-none focus:ring-2 focus:ring-gray-200">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                No Data Available
            </button>
        </div>

        {{-- Main Content --}}
        <div x-show="!isLoading && !hasError && !isEmpty" x-cloak class="space-y-6">

            {{-- Summary Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                <div
                    class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:border-[#128a43]/30 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div
                            class="w-2.5 h-2.5 rounded-full bg-[#128a43]/90 ring-4 ring-[#128a43]/10 group-hover:ring-green-500/20 transition-all">
                        </div>
                        <h3 class="text-sm font-medium text-gray-500">Total Allocated</h3>
                    </div>
                    <p x-text="formatCurrency(stats.totalAllocated)"
                        class="text-3xl font-bold text-gray-900 tracking-tight">₱0</p>
                    <p class="text-xs text-gray-400 mt-2">Across all barangays</p>
                </div>

                <div
                    class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:border-[#128a43]/30 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div
                            class="w-2.5 h-2.5 rounded-full bg-[#128a43]/90 ring-4 ring-[#128a43]/10 group-hover:ring-green-500/20 transition-all">
                        </div>
                        <h3 class="text-sm font-medium text-gray-500">Total Spent</h3>
                    </div>
                    <p x-text="formatCurrency(stats.totalSpent)"
                        class="text-3xl font-bold text-gray-900 tracking-tight">₱0</p>
                    <p class="text-xs text-gray-400 mt-2">Total expenses recorded</p>
                </div>

                <div
                    class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:border-[#128a43]/30 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div
                            class="w-2.5 h-2.5 rounded-full bg-[#128a43]/90 ring-4 ring-[#128a43]/10 group-hover:ring-green-500/20 transition-all">
                        </div>
                        <h3 class="text-sm font-medium text-gray-500">Avg Utilization</h3>
                    </div>
                    <p x-text="stats.avgUtilization.toFixed(2) + '%'"
                        class="text-3xl font-bold text-gray-900 tracking-tight">0%</p>
                    <p class="text-xs text-gray-400 mt-2">System-wide average</p>
                </div>

                <div
                    class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:border-[#128a43]/30 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-center gap-2.5 mb-3">
                        <div
                            class="w-2.5 h-2.5 rounded-full bg-[#128a43]/90 ring-4 ring-[#128a43]/10 group-hover:ring-green-500/20 transition-all">
                        </div>
                        <h3 class="text-sm font-medium text-gray-500">Barangays</h3>
                    </div>
                    <p x-text="stats.barangayCount" class="text-3xl font-bold text-gray-900 tracking-tight">0</p>
                    <p class="text-xs text-gray-400 mt-2">Active budget programs</p>
                </div>

            </div>

            {{-- Charts Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Utilization Line Chart --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Utilization Rate</h3>
                            <p class="text-sm text-gray-500 mt-1">Budget utilization across barangays</p>
                        </div>
                        <div class="flex items-center">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-[#128a43]/10 text-[#128a43] ring-1 ring-inset ring-[#128a43]/20 rounded-md text-xs font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                Active
                            </span>
                        </div>
                    </div>
                    <div style="height: 280px;" class="w-full relative">
                        <canvas id="utilization-line"></canvas>
                    </div>
                </div>

                {{-- Budget Allocation Doughnut --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Budget Allocation</h3>
                            <p class="text-sm text-gray-500 mt-1">Distribution across barangays</p>
                        </div>
                    </div>
                    <div class="relative w-full" style="height: 280px;">
                        <canvas id="allocation-doughnut"></canvas>
                    </div>
                </div>

            </div>

            {{-- Two-Column Layout for Category and Comparison --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Spending Horizontal Bar --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Category Spending</h3>
                            <p class="text-sm text-gray-500 mt-1">Expenses by budget category</p>
                        </div>
                    </div>
                    <div class="relative w-full" style="height: 320px;">
                        <canvas id="category-horizontal"></canvas>
                    </div>
                </div>

                {{-- Budget Comparison Bar --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Budget vs Spending</h3>
                            <p class="text-sm text-gray-500 mt-1">Allocated budget and actual spending</p>
                        </div>
                    </div>
                    <div class="relative w-full" style="height: 320px;">
                        <canvas id="comparison-chart"></canvas>
                    </div>
                </div>

            </div>

            {{-- Performance Radar --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Performance Radar</h3>
                        <p class="text-sm text-gray-500 mt-1">Multi-dimensional performance analysis of top barangays
                        </p>
                    </div>
                    <button type="button"
                        class="text-gray-400 hover:text-gray-600 bg-transparent hover:bg-gray-50 rounded-lg p-1.5 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                </div>
                <div class="relative w-full mx-auto" style="height: 420px; max-width: 900px;">
                    <canvas id="performance-radar"></canvas>
                </div>
            </div>

            {{-- Detailed Table --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-8">

                {{-- Table Header --}}
                <div class="px-6 py-5 border-b border-gray-200 bg-white">
                    <h3 class="text-base font-semibold text-gray-900">Detailed Barangay Summary</h3>
                    <p class="text-sm text-gray-500 mt-1">Complete budget performance breakdown</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Barangay</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Allocated</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Spent</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Remaining</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Utilization</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <template x-for="(barangay, index) in barangays" :key="index">
                                <tr class="hover:bg-gray-50/80 transition-colors group cursor-pointer"
                                    @click="window.location.href = '/budgets/' + barangay.budget_id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"
                                            x-text="barangay.barangay_name"></div>
                                        <div class="text-xs text-gray-500 mt-1" x-text="barangay.budget_name"></div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900"
                                        x-text="formatCurrency(barangay.total_allocated)"></td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-500"
                                        x-text="formatCurrency(barangay.total_spent)"></td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900"
                                        x-text="formatCurrency(barangay.total_allocated - barangay.total_spent)"></td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900"
                                        x-text="barangay.utilization_rate.toFixed(2) + '%'"></td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span x-text="barangay.status"
                                            :class="{
                                                'bg-[#128a43]/10 text-[#128a43] ring-[#128a43]/20': barangay
                                                    .status === 'Under Budget',
                                                'bg-yellow-50 text-yellow-700 ring-yellow-600/20': barangay
                                                    .status === 'Near Limit' || barangay.status === 'At Limit',
                                                'bg-red-50 text-red-700 ring-red-600/10': barangay
                                                    .status === 'Over Budget'
                                            }"
                                            class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-md ring-1 ring-inset"></span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a :href="'/budgets/' + barangay.budget_id"
                                            class="inline-flex items-center text-[#128a43] hover:text-[#0f7236] transition-opacity opacity-0 group-hover:opacity-100">
                                            View
                                            <svg class="w-4 h-4 ms-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
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
