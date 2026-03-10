<x-layout title="Dashboard" page="dashboard">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-sans">

        {{-- Header --}}
        <div class="mb-8 border-b border-gray-200 pb-4 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Welcome back, <span id="user-name"
                        class="font-bold text-gray-800"></span></p>
            </div>
        </div>

        {{-- Loading State --}}
        <div id="dashboard-loading" class="flex justify-center items-center py-20">
            <div class="text-center">
                <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-[#128a43] mb-4"></div>
                <p class="text-sm text-gray-600 font-medium tracking-wide uppercase">Loading Dashboard...</p>
            </div>
        </div>

        {{-- Error State --}}
        <div id="dashboard-error" class="hidden">
            <div
                class="bg-white border-l-4 border-l-red-600 border-y border-r border-gray-200 rounded-sm p-8 text-center shadow-sm">
                <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p id="error-message" class="text-gray-900 font-bold mb-4"></p>
                <button onclick="location.reload()"
                    class="px-5 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-sm transition">Retry
                    Connection</button>
            </div>
        </div>

        {{-- Dashboard Content --}}
        <div id="dashboard-content" class="hidden space-y-6">

            {{-- User Info Card --}}
            <div class="bg-white border border-gray-200 rounded-sm shadow-sm p-6">
                <div class="flex items-center gap-4">
                    <div
                        class="w-14 h-14 bg-[#128a43] text-white rounded-sm flex items-center justify-center text-xl font-bold shadow-inner">
                        <span id="user-initials"></span>
                    </div>
                    <div>
                        <p id="user-name-full" class="text-lg font-bold text-gray-900"></p>
                        <p id="user-email" class="text-sm text-gray-500 font-medium"></p>
                        <span id="user-role-badge"
                            class="inline-block mt-2 px-2 py-0.5 border border-gray-200 rounded text-[11px] font-bold uppercase tracking-wider text-gray-600 bg-gray-50"></span>
                    </div>
                </div>
            </div>

            {{-- Stats Overview with Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Budget Overview Doughnut Chart --}}
                <div class="bg-white rounded-sm border border-gray-200 shadow-sm p-6">
                    <h3
                        class="text-sm font-bold uppercase tracking-wide text-gray-700 mb-6 border-b border-gray-100 pb-2">
                        Budget Overview</h3>
                    <div class="relative mx-auto" style="height: 200px; max-width: 200px;">
                        <canvas id="budget-overview-chart"></canvas>
                    </div>
                    <div class="mt-6 grid grid-cols-3 gap-2 text-center">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Allocated</p>
                            <p id="chart-allocated" class="font-bold text-[#0d6efd] text-xs">₱0</p>
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

                {{-- Utilization Gauge Chart --}}
                <div class="bg-white rounded-sm border border-gray-200 shadow-sm p-6">
                    <h3
                        class="text-sm font-bold uppercase tracking-wide text-gray-700 mb-6 border-b border-gray-100 pb-2">
                        System Utilization</h3>
                    <div class="relative mx-auto mt-8" style="height: 140px; max-width: 220px;">
                        <canvas id="utilization-chart"></canvas>
                    </div>
                    <div class="mt-2 text-center">
                        <p id="utilization-text" class="text-4xl font-black text-gray-900 tracking-tight">0%</p>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400 mt-1">Total Budget Utilized
                        </p>
                    </div>
                </div>

                {{-- Quick Stats Cards --}}
                <div class="flex flex-col gap-4 justify-between h-full">

                    <div
                        class="bg-white border-y border-r border-gray-200 border-l-4 border-l-[#0d6efd] rounded-sm p-5 shadow-sm flex-1 flex flex-col justify-center">
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-1">Total Allocated</p>
                        <p id="total-allocated" class="text-2xl font-black text-[#0d6efd] tracking-tight">₱0.00</p>
                    </div>

                    <div
                        class="bg-white border-y border-r border-gray-200 border-l-4 border-l-red-600 rounded-sm p-5 shadow-sm flex-1 flex flex-col justify-center">
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-1">Total Spent</p>
                        <p id="total-spent" class="text-2xl font-black text-red-600 tracking-tight">₱0.00</p>
                    </div>

                    <div
                        class="bg-white border-y border-r border-gray-200 border-l-4 border-l-[#128a43] rounded-sm p-5 shadow-sm flex-1 flex flex-col justify-center">
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-1">Remaining Budget</p>
                        <p id="remaining-budget" class="text-2xl font-black text-[#128a43] tracking-tight">₱0.00</p>
                    </div>

                </div>

            </div>

            {{-- Two Column Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Spending by Category Bar Chart --}}
                <div class="bg-white rounded-sm border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-sm font-bold uppercase tracking-wide text-gray-700">Category Spending</h3>
                        <button class="text-gray-400 hover:text-gray-900"><svg class="w-4 h-4" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                            </svg></button>
                    </div>
                    <div class="p-6" style="height: 320px;">
                        <canvas id="category-chart"></canvas>
                    </div>
                </div>

                {{-- Recent Budgets --}}
                <div class="bg-white rounded-sm border border-gray-200 shadow-sm flex flex-col h-full">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-sm font-bold uppercase tracking-wide text-gray-700">Recent Budgets</h3>
                        <a href="/budgets"
                            class="text-xs font-bold text-[#0d6efd] hover:text-blue-800 uppercase tracking-wide transition">View
                            All →</a>
                    </div>
                    <div id="recent-budgets" class="flex-1 overflow-auto">
                        <div class="flex items-center justify-center h-full p-4">
                            <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-[#128a43]">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </main>
</x-layout>
