<header class="sticky top-0 z-50 shadow-sm font-sans">
    <nav class="bg-[#128a43] text-white relative z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-14">

                {{-- Logo --}}
                <a href="/" class="flex items-center gap-1.5 hover:opacity-80 transition z-50">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M11 2.05v10.45h10.45c-.49 5.37-4.99 9.5-10.45 9.5-5.8 0-10.5-4.7-10.5-10.5S5.2 2 11 2.05zm2 0c5.05.5 9 4.45 9.5 9.5h-9.5V2.05z" />
                    </svg>
                    <span class="text-[22px] font-black italic tracking-tighter uppercase mt-0.5">
                        <span class="lowercase">e</span>ALLOC
                    </span>
                </a>

                {{-- Conditional Navigation based on route --}}
                @if (request()->routeIs('welcome'))
                    {{-- Welcome Page --}}
                    <div class="flex items-center gap-4">
                        <span class="hidden md:inline text-sm font-medium text-white/80 uppercase tracking-wider">
                            Government Budget Management System
                        </span>
                        <a href="/login"
                            class="px-5 py-2 bg-white text-[#128a43] hover:bg-gray-100 text-sm font-bold rounded-sm shadow-sm transition uppercase tracking-wide">
                            Sign In
                        </a>
                    </div>
                @elseif (!request()->routeIs('login'))
                    {{-- Authenticated Pages, Full Navigation --}}
                    <div class="hidden md:flex items-center gap-1 relative">
                        <a href="/dashboard"
                            class="px-3 py-1.5 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded transition">Dashboard</a>
                        <a href="/expenses"
                            class="px-3 py-1.5 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded transition">Expenses</a>
                        <a href="/budgets"
                            class="px-3 py-1.5 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded transition">Budgets</a>
                        <a href="/analytics"
                            class="px-3 py-1.5 text-sm font-medium text-white/90 hover:text-white hover:bg-white/10 rounded transition">Analytics</a>

                        {{-- USER MENU --}}
                        <div id="user-menu" class="hidden relative ml-2 border-l border-white/20 pl-2"
                            x-data="{ open: false }">

                            {{-- Toggle Button --}}
                            <button @click="open = !open" @click.away="open = false" type="button"
                                class="flex items-center gap-2 px-3 py-1.5 hover:bg-white/10 rounded transition outline-none cursor-pointer">
                                <div
                                    class="w-6 h-6 bg-white text-[#128a43] rounded-full flex items-center justify-center text-xs font-bold shadow-sm pointer-events-none">
                                    <span id="user-initials">--</span>
                                </div>
                                <span id="user-name-display"
                                    class="text-sm font-bold text-white tracking-wide pointer-events-none">Account</span>
                                <svg class="w-4 h-4 text-white/70 pointer-events-none" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Dropdown Panel --}}
                            <div x-show="open" x-cloak style="display: none;"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-sm shadow-xl border border-gray-200 py-1 z-[100] text-gray-800">

                                {{-- User Metadata populated by JS --}}
                                <div class="px-4 py-3 border-b border-gray-100 mb-1 bg-gray-50">
                                    <p id="user-email-display" class="text-xs text-gray-600 truncate font-medium"></p>
                                    <p id="user-role-display"
                                        class="text-[10px] font-bold text-[#128a43] uppercase tracking-widest mt-1"></p>
                                </div>

                                <a href="/admin/audit-logs" id="audit-link"
                                    class="hidden block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#0d6efd] transition">
                                    Audit Logs
                                </a>

                                {{-- Admin Links --}}
                                <a href="/admin/users" id="users-link"
                                    class="hidden block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#0d6efd] transition">
                                    User Management
                                </a>
                                <a href="/admin/units" id="admin-link"
                                    class="hidden block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#0d6efd] transition">
                                    Government Units
                                </a>
                                <a href="/admin/fiscal-years" id="fiscal-link"
                                    class="hidden block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#0d6efd] transition">
                                    Fiscal Years
                                </a>

                                {{-- Sign Out Button --}}
                                <button id="logout-btn-nav" type="button"
                                    class="w-full text-left px-4 py-2 mt-1 text-sm text-red-600 hover:bg-red-50 font-bold transition flex items-center gap-2 border-t border-gray-100 pt-2 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Sign Out
                                </button>
                            </div>
                        </div>

                        {{-- Fallback login link --}}
                        <a href="/login" id="login-link"
                            class="ml-3 px-4 py-1.5 bg-white text-[#128a43] hover:bg-gray-100 text-sm font-medium rounded shadow-sm transition">
                            Sign In
                        </a>
                    </div>

                    {{-- Mobile menu button --}}
                    <button id="mobile-menu-btn" class="md:hidden p-2 text-white hover:bg-white/10 rounded">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </nav>
</header>
