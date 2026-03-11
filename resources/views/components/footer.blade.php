<footer class="mt-auto bg-[#dee2e6] text-sm text-gray-600 pt-10 font-sans">

    {{-- Main Footer --}}
    <div class="max-w-7xl mx-auto px-4 pb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">

            {{-- About Section --}}
            <div class="md:col-span-2 flex gap-4 items-start">
                <div class="w-12 h-12 bg-[#128a43] rounded flex items-center justify-center shrink-0">
                    {{-- Budgeting, Finance Icon --}}
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M11 2.05v10.45h10.45c-.49 5.37-4.99 9.5-10.45 9.5-5.8 0-10.5-4.7-10.5-10.5S5.2 2 11 2.05zm2 0c5.05.5 9 4.45 9.5 9.5h-9.5V2.05z" />
                    </svg>
                </div>
                <div>
                    {{-- eAlloc branding --}}
                    <h4 class="text-xl font-black italic tracking-tighter uppercase text-gray-800 leading-tight">
                        <span class="lowercase">e</span>ALLOC
                    </h4>
                    <div class="text-xs text-gray-500 mb-2 font-medium uppercase tracking-wider">Budget Management
                        System</div>
                    <p class="text-xs text-gray-500 leading-relaxed max-w-sm">
                        Securely manage budget allocations to promote transparency and accountability in public fund
                        management.
                        Empowering citizens with access to government budget information.
                    </p>
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="md:col-span-1">
                <h4 class="font-semibold text-gray-700 mb-2">Contact Information</h4>
                <ul class="text-xs space-y-1">
                    <li>📧 <a href="mailto:support@ealloc.gov.ph"
                            class="text-[#0d6efd] hover:underline">support@ealloc.gov.ph</a></li>
                    <li>📞 <a href="tel:+6281234567" class="text-[#0d6efd] hover:underline">(02) 8123-4567</a></li>
                    <li>📱 <a href="tel:+639171234567" class="text-[#0d6efd] hover:underline">0917-123-4567</a></li>
                    <li class="text-[#0d6efd]">🕐 Mon-Fri, 8:00 AM - 5:00 PM</li>
                </ul>
            </div>

            {{-- System Information --}}
            <div class="md:col-span-1">
                <h4 class="font-semibold text-gray-700 mb-2">System Information</h4>
                <ul class="text-xs space-y-1">
                    <li class="text-[#0d6efd]">Version 1.0</li>
                    <li class="text-[#0d6efd]">Last Updated: March 2026</li>
                    <li class="text-[#0d6efd]">Status: Operational</li>
                    <li class="text-[#0d6efd]">Maintained by: ITS122L Baddies</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Bottom Bar --}}
    <div class="border-t border-gray-300">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Republic of the Philippines</span>
                </div>
                <div>
                    © {{ date('Y') }} eAlloc - Budget Management System. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</footer>
