<x-layout title="Activation Link Expired" page="auth/expired">
    <main class="flex-grow flex items-center justify-center p-6 bg-gray-50">
        <div class="max-w-md w-full bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">

            <div class="px-8 py-12 text-center">
                <div class="flex justify-center mb-6">
                    <svg class="w-20 h-20 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-gray-900 mb-3">Activation Link Expired</h1>

                <p class="text-gray-600 mb-6">
                    {{ $reason ?? 'This activation link is no longer valid.' }}
                </p>

                <div class="p-4 bg-blue-50 border border-blue-200 rounded text-sm text-blue-900 mb-6">
                    <p class="font-medium mb-2">What you can do:</p>
                    <ul class="text-left space-y-1 pl-4">
                        <li>• Contact your system administrator</li>
                        <li>• Request a new activation email</li>
                        <li>• Email support@ealloc.gov.ph for assistance</li>
                    </ul>
                </div>

                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-[#128a43] hover:bg-[#0d6b35] text-white font-medium rounded-md transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Login
                </a>
            </div>

        </div>
    </main>
</x-layout>
