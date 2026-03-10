<x-layout title="Login" page="auth/login">

    <main class="flex-grow flex items-center justify-center p-6 bg-transparent">

        <div class="max-w-4xl w-full bg-white border border-gray-200 shadow-sm rounded-sm overflow-hidden">

            <div class="grid md:grid-cols-2">

                {{-- Illustration --}}
                <div class="hidden md:flex items-center justify-center p-10">
                    <img src="/images/login-illustration.svg" alt="Illustration" class="w-full max-w-sm">
                </div>

                {{-- Login Form --}}
                <div class="p-10 lg:p-14">

                    {{-- Logo --}}
                    <div class="mb-6 flex justify-center border-b border-gray-300 pb-6">
                        <img src="/images/ealloc-logo.png" alt="System Logo" class="h-28 w-auto object-contain">
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-2">
                        eAlloc
                    </h2>

                    <p class="text-sm text-gray-600 mb-8">
                        Securely manage budget allocations to promote transparency and accountability in public fund
                        management.
                    </p>

                    <div id="login-error"
                        class="hidden mb-4 px-3 py-2 text-sm text-red-700 bg-red-50 border border-red-200 rounded">
                    </div>

                    <form id="login-form" method="post">

                        {{-- Email --}}
                        <div class="mb-4">
                            <label class="block text-sm text-gray-700 mb-1">
                                Email Address
                            </label>

                            <input id="email" name="email" type="email" required
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm
                                   focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]">
                        </div>

                        {{-- Password --}}
                        <div class="mb-4">
                            <label class="block text-sm text-gray-700 mb-1">
                                Password
                            </label>

                            <input id="password" name="password" type="password" required
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm
                                   focus:outline-none focus:border-[#0d6efd] focus:ring-1 focus:ring-[#0d6efd]">
                        </div>

                        {{-- Sign In --}}
                        <button id="login-submit" type="submit"
                            class="w-full bg-[#0d6efd] hover:bg-blue-700 text-white
                               text-sm font-medium py-2 rounded transition
                               flex items-center justify-center gap-2 mb-3">

                            <svg id="submit-spinner" class="hidden animate-spin w-4 h-4" fill="none"
                                viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>

                            <span id="submit-text">
                                Sign In
                            </span>

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </main>

</x-layout>
