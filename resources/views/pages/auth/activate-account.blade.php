<x-layout title="Activate Account" page="auth/activate">
    <main class="flex-grow flex items-center justify-center p-6 bg-gray-50">
        <div class="max-w-md w-full bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-[#128a43] to-[#0d6b35] px-8 py-6 text-white">
                <div class="flex justify-center mb-3">
                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-center">Activate Your Account</h1>
                <p class="text-sm text-center mt-2 text-white/90">eAlloc Budget Management System</p>
            </div>

            {{-- Content --}}
            <div class="px-8 py-8">

                {{-- Welcome Message --}}
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded">
                    <p class="text-sm text-blue-900">
                        <strong>Welcome, {{ $user->name }}!</strong><br>
                        <span class="text-blue-700">Set your password to activate your account.</span>
                    </p>
                </div>

                {{-- Account Info --}}
                <div class="mb-6 space-y-2 text-sm">
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span><strong>Email:</strong> {{ $user->email }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span><strong>Role:</strong> {{ ucwords(str_replace('-', ' ', $user->role)) }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-orange-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm"><strong>Link expires:</strong> {{ $expiresAt }}</span>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- Activation Form --}}
                <form method="POST" action="{{ request()->fullUrl() }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Create Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                   focus:outline-none focus:border-[#128a43] focus:ring-2 focus:ring-[#128a43]/20"
                            placeholder="Enter a strong password">
                        <p class="mt-1 text-xs text-gray-500">
                            Must be at least 8 characters with uppercase, lowercase, numbers, and symbols
                        </p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                   focus:outline-none focus:border-[#128a43] focus:ring-2 focus:ring-[#128a43]/20"
                            placeholder="Re-enter your password">
                    </div>

                    {{-- Password Requirements --}}
                    <div class="mb-6 p-3 bg-gray-50 border border-gray-200 rounded text-xs text-gray-600">
                        <p class="font-medium mb-2">Password Requirements:</p>
                        <ul class="space-y-1 pl-4">
                            <li>✓ Minimum 8 characters</li>
                            <li>✓ At least one uppercase letter (A-Z)</li>
                            <li>✓ At least one lowercase letter (a-z)</li>
                            <li>✓ At least one number (0-9)</li>
                            <li>✓ At least one special character (!@#$%^&*)</li>
                        </ul>
                    </div>

                    <button type="submit"
                        class="w-full bg-[#128a43] hover:bg-[#0d6b35] text-white font-medium py-3 rounded-md transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Activate Account
                    </button>
                </form>

                {{-- Security Notice --}}
                <div class="mt-6 p-3 bg-yellow-50 border border-yellow-200 rounded text-xs text-yellow-800">
                    <strong>🔒 Security Notice:</strong> Never share your password with anyone.
                    eAlloc administrators will never ask for your password.
                </div>
            </div>

        </div>
    </main>
</x-layout>
