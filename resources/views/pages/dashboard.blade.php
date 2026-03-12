<x-layout title="Dashboard" page="dashboard">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-sans">

        {{-- Header --}}
        <div class="mb-8 md:mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">Dashboard</h1>
                <p class="text-sm text-gray-500 mt-2">Welcome back, <span id="user-name"
                        class="font-semibold text-[#128a43]"></span></p>
            </div>
        </div>

        {{-- Loading State --}}
        <div id="dashboard-loading" class="flex flex-col items-center justify-center min-h-[400px]">
            <div class="relative w-10 h-10 mb-4">
                <div class="absolute inset-0 rounded-full border-[3px] border-gray-100"></div>
                <div
                    class="absolute inset-0 rounded-full border-[3px] border-[#128a43] border-t-transparent animate-spin">
                </div>
            </div>
            <p class="text-sm font-medium text-gray-500 animate-pulse">Loading Dashboard...</p>
        </div>

        {{-- Error State --}}
        <div id="dashboard-error" class="hidden">
            <div class="max-w-xl mx-auto mt-12 bg-white border border-red-100 rounded-xl shadow-sm p-8 text-center">
                <div
                    class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-50 ring-8 ring-red-50/50 mb-5">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-2">Dashboard Error</h3>
                <p id="error-message" class="text-sm text-gray-500 mb-6"></p>
                <button onclick="location.reload()"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition shadow-sm gap-2">
                    No Data Available
                </button>
            </div>
        </div>

        {{-- Dashboard Content --}}
        <div id="dashboard-content" class="hidden space-y-6">

            {{-- User Info Card --}}
            <div
                class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 hover:border-[#128a43]/30 transition-all duration-200 group">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-1 h-10 bg-[#128a43] rounded-full opacity-20 group-hover:opacity-100 transition-opacity">
                        </div>
                        <div>
                            <p id="user-name-full" class="text-xl font-bold text-gray-900 tracking-tight"></p>
                            <p id="user-email" class="text-sm text-gray-500 font-medium mt-0.5"></p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span id="user-role-badge"
                            class="inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold uppercase tracking-widest bg-[#128a43]/10 text-[#128a43] ring-1 ring-inset ring-[#128a43]/20"></span>
                    </div>
                </div>
            </div>

            {{-- Overview Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Chart Widget --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 md:p-6">
                    <div class="flex justify-between items-start w-full mb-4">
                        <div>
                            <h5 class="text-lg font-bold text-gray-900 mb-1">Budget Overview</h5>
                            <p class="text-xs text-gray-500">Current fiscal year breakdown</p>
                        </div>
                        <span
                            class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-bold uppercase tracking-wider">Pie
                            Chart</span>
                    </div>
                    <div class="py-4 h-[220px] flex items-center justify-center">
                        <canvas id="budget-overview-chart"></canvas>
                    </div>
                    <div class="grid grid-cols-3 border-t border-gray-100 mt-4 pt-4 text-center">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Allocated</p>
                            <p id="chart-allocated" class="font-bold text-gray-900 text-xs">₱0</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Spent</p>
                            <p id="chart-spent" class="font-bold text-[#128a43] text-xs">₱0</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Remaining</p>
                            <p id="chart-remaining" class="font-bold text-[#f59e0b] text-xs">₱0</p>
                        </div>
                    </div>
                </div>

                {{-- Gauge Widget --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 md:p-6">
                    <div class="flex justify-between items-start w-full mb-4">
                        <div>
                            <h5 class="text-lg font-bold text-gray-900 mb-1">System Utilization</h5>
                            <p class="text-xs text-gray-500">System-wide performance</p>
                        </div>
                    </div>
                    <div class="relative flex items-center justify-center h-[180px]">
                        <canvas id="utilization-chart"></canvas>
                        <div class="absolute bottom-4 flex flex-col items-center">
                            <p id="utilization-text" class="text-3xl font-extrabold text-gray-900 tracking-tight">0%</p>
                            <p class="text-[10px] font-bold uppercase text-gray-400">Utilized</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 mt-8 pt-4">
                        <a href="/analytics"
                            class="flex items-center justify-center text-sm font-semibold text-[#128a43] hover:underline">
                            View Full Report <svg class="w-4 h-4 ms-1" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Quick Stats Cards --}}
                <div class="flex flex-col gap-4">
                    <div
                        class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:border-[#128a43]/30 transition-all duration-200 group flex-1 flex flex-col justify-center">
                        <div class="flex items-center gap-2.5 mb-2">
                            <div
                                class="w-2.5 h-2.5 rounded-full bg-[#128a43]/90 ring-4 ring-[#128a43]/10 group-hover:ring-green-500/20 transition-all">
                            </div>
                            <h3 class="text-sm font-medium text-gray-500">Total Allocated</h3>
                        </div>
                        <p id="total-allocated" class="text-2xl font-bold text-gray-900 tracking-tight">₱0.00</p>
                    </div>

                    <div
                        class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:border-[#128a43]/30 transition-all duration-200 group flex-1 flex flex-col justify-center">
                        <div class="flex items-center gap-2.5 mb-2">
                            <div
                                class="w-2.5 h-2.5 rounded-full bg-[#128a43]/90 ring-4 ring-[#128a43]/10 group-hover:ring-green-500/20 transition-all">
                            </div>
                            <h3 class="text-sm font-medium text-gray-500">Total Spent</h3>
                        </div>
                        <p id="total-spent" class="text-2xl font-bold text-gray-900 tracking-tight">₱0.00</p>
                    </div>

                    <div
                        class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:border-[#128a43]/30 transition-all duration-200 group flex-1 flex flex-col justify-center">
                        <div class="flex items-center gap-2.5 mb-2">
                            <div
                                class="w-2.5 h-2.5 rounded-full bg-[#128a43]/90 ring-4 ring-[#128a43]/10 group-hover:ring-green-500/20 transition-all">
                            </div>
                            <h3 class="text-sm font-medium text-gray-500">Remaining Budget</h3>
                        </div>
                        <p id="remaining-budget" class="text-2xl font-bold text-gray-900 tracking-tight">₱0.00</p>
                    </div>
                </div>

            </div>

            {{-- Category Chart & Recent Budgets --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Spending by Category Bar Chart --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">Category Spending</h3>
                        <div class="flex gap-2">
                            <div class="w-2 h-2 rounded-full bg-[#128a43]"></div>
                        </div>
                    </div>
                    <div class="p-6" style="height: 350px;">
                        <canvas id="category-chart"></canvas>
                    </div>
                </div>

                {{-- Recent Budgets --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-base font-semibold text-gray-900">Recent Budgets</h3>
                        <a href="/budgets"
                            class="text-sm font-semibold text-[#128a43] hover:text-[#0f7236] transition-colors">View
                            All →</a>
                    </div>
                    <div id="recent-budgets" class="flex-1 overflow-auto min-h-[350px]">
                        <div class="flex items-center justify-center h-full p-8">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#128a43]">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </main>
</x-layout>
