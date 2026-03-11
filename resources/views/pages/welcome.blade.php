<x-layout title="Welcome to eAlloc" page="welcome">

    {{-- Animation Styles for SVGs --}}
    <style>
        @keyframes slideInFromLeft {
            from {
                opacity: 0;
                transform: translateX(-100px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInFromRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .svg-animate {
            opacity: 0;
        }

        .svg-animate.slide-in-left {
            animation: slideInFromLeft 0.8s ease-out forwards;
        }

        .svg-animate.slide-in-right {
            animation: slideInFromRight 0.8s ease-out forwards;
        }
    </style>

    <main class="flex-grow font-sans bg-white">

        {{-- HERO SECTION --}}
        <section
            class="relative min-h-screen flex items-center bg-gradient-to-br from-gray-100 via-gray-50 to-white overflow-hidden">
            {{-- Background illustration --}}
            <div class="absolute inset-0 opacity-5">
                <div class="absolute right-0 top-0 w-2/3 h-full bg-[#128a43] transform rotate-12 translate-x-1/2"></div>
            </div>

            <div class="relative w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex flex-col lg:flex-row items-center gap-12">

                    {{-- Left, Text Content --}}
                    <div class="lg:w-1/2 text-center lg:text-left">
                        {{-- Logo --}}
                        <div class="inline-flex items-center gap-3 mb-6">
                            <div class="w-16 h-16 bg-[#128a43] rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-9 h-9 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M11 2.05v10.45h10.45c-.49 5.37-4.99 9.5-10.45 9.5-5.8 0-10.5-4.7-10.5-10.5S5.2 2 11 2.05zm2 0c5.05.5 9 4.45 9.5 9.5h-9.5V2.05z" />
                                </svg>
                            </div>
                            <span class="text-4xl font-black text-[#128a43] italic tracking-tight">
                                <span class="lowercase">e</span>ALLOC
                            </span>
                        </div>

                        {{-- Tagline --}}
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4 tracking-tight">
                            Fast. Reliable. Transparent.
                        </h1>

                        {{-- Description --}}
                        <p class="text-base md:text-lg text-gray-700 mb-8 leading-relaxed max-w-xl mx-auto lg:mx-0">
                            Manage your government budget allocations and track expenditures all in one place.
                        </p>

                        {{-- CTA Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            <a href="/login"
                                class="inline-flex items-center justify-center px-8 py-3.5 bg-[#128a43] hover:bg-[#0f7236] text-white text-sm font-bold uppercase tracking-wider rounded-sm shadow-md transition-all">
                                Go to Login
                            </a>
                            <a href="#about"
                                class="inline-flex items-center justify-center px-8 py-3.5 bg-white hover:bg-gray-50 text-[#128a43] text-sm font-bold uppercase tracking-wider rounded-sm border-2 border-[#128a43] transition-all">
                                Learn More
                            </a>
                        </div>
                    </div>

                    {{-- Right, Illustration --}}
                    <div class="lg:w-1/2 svg-animate" data-animation="slide-in-right">
                        <div class="relative">
                            <img src="/images/welcome.svg" alt="eAlloc System"
                                class="w-full max-w-md mx-auto drop-shadow-xl">
                        </div>
                    </div>

                </div>

                {{-- Scroll Indicator --}}
                <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 hidden lg:block">
                    <a href="#about"
                        class="flex flex-col items-center text-gray-500 hover:text-[#128a43] transition-colors animate-bounce">
                        <span class="text-xs uppercase tracking-wider mb-2">Scroll Down</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        {{-- SYSTEM OVERVIEW --}}
        <section id="about" class="py-16 lg:py-20 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12">

                    {{-- Left, Illustration --}}
                    <div class="lg:w-2/5 flex justify-center svg-animate" data-animation="slide-in-left">
                        <svg class="w-72 lg:w-80 h-auto text-[#128a43]" viewBox="0 0 200 200" fill="none"
                            xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="100" cy="100" r="85" fill="currentColor" fill-opacity="0.06"
                                stroke="currentColor" stroke-width="2" />
                            <g transform="translate(40,40) scale(5)">
                                <path
                                    d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                                <path d="m9 12 2 2 4-4" />
                            </g>
                        </svg>
                    </div>

                    {{-- Right, Content --}}
                    <div class="lg:w-3/5">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 tracking-tight">
                            ABOUT eALLOC
                        </h2>
                        <p class="text-gray-700 text-base leading-relaxed mb-6">
                            The eAlloc Budget Management System is designed to provide a secure, centralized platform
                            for managing government budgets, tracking expenditures in real-time, and ensuring complete
                            transparency in public financial administration.
                        </p>
                        <p class="text-gray-700 text-base leading-relaxed">
                            Built specifically for Local Government Units (LGUs), the platform streamlines budget
                            planning,
                            expenditure monitoring, and financial reporting in full compliance with national accounting
                            standards
                            and Commission on Audit (COA) guidelines.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        {{-- KEY FEATURES --}}
        <section class="py-16 lg:py-20 bg-gradient-to-br from-teal-50 to-green-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row-reverse items-center gap-12">

                    {{-- Right, Illustration --}}
                    <div class="lg:w-2/5 flex justify-center svg-animate" data-animation="slide-in-right">
                        <svg class="w-72 lg:w-80 h-auto text-[#128a43]" viewBox="0 0 200 200" fill="none"
                            xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="100" cy="100" r="85" fill="currentColor" fill-opacity="0.06"
                                stroke="currentColor" stroke-width="2" />
                            <g transform="translate(40,40) scale(5)">
                                <path
                                    d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z" />
                                <path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12" />
                                <path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17" />
                            </g>
                        </svg>
                    </div>

                    {{-- Left, Content --}}
                    <div class="lg:w-3/5">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 tracking-tight">
                            PLATFORM CAPABILITIES
                        </h2>
                        <p class="text-gray-700 text-base leading-relaxed mb-6">
                            The eAlloc system delivers comprehensive financial management tools designed to enhance
                            efficiency, accountability, and transparency across all levels of government operations:
                        </p>
                        <ul class="space-y-3 text-gray-700">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-[#128a43] mt-0.5 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Centralized budget planning and approval workflows</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-[#128a43] mt-0.5 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Real-time expenditure tracking and monitoring</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-[#128a43] mt-0.5 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Automated compliance with COA regulations and standards</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-[#128a43] mt-0.5 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Comprehensive audit trails for all financial transactions</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-[#128a43] mt-0.5 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Advanced analytics and reporting dashboards</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-[#128a43] mt-0.5 flex-shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Multi-level access control and role-based permissions</span>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </section>

        {{-- SECURITY & COMPLIANCE --}}
        <section class="py-16 lg:py-20 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row items-center gap-12">

                    {{-- Left, Illustration --}}
                    <div class="lg:w-2/5 svg-animate" data-animation="slide-in-left">
                        <div class="bg-white p-8 rounded-lg">
                            <svg class="w-full h-auto text-[#128a43]" viewBox="0 0 200 200" fill="none"
                                xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="100" cy="100" r="85" fill="currentColor" fill-opacity="0.06"
                                    stroke="currentColor" stroke-width="2" />
                                <g transform="translate(40,40) scale(5)">
                                    <path
                                        d="M16.75 12h3.632a1 1 0 0 1 .894 1.447l-2.034 4.069a1 1 0 0 1-1.708.134l-2.124-2.97" />
                                    <path
                                        d="M17.106 9.053a1 1 0 0 1 .447 1.341l-3.106 6.211a1 1 0 0 1-1.342.447L3.61 12.3a2.92 2.92 0 0 1-1.3-3.91L3.69 5.6a2.92 2.92 0 0 1 3.92-1.3z" />
                                    <path d="M2 19h3.76a2 2 0 0 0 1.8-1.1L9 15" />
                                    <path d="M2 21v-4" />
                                    <path d="M7 9h.01" />
                                </g>
                            </svg>
                        </div>
                    </div>

                    {{-- Right, Content --}}
                    <div class="lg:w-3/5">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 tracking-tight">
                            SECURITY & COMPLIANCE
                        </h2>
                        <p class="text-gray-700 text-base leading-relaxed mb-6">
                            Security and data protection are at the core of the eAlloc system. The platform implements
                            enterprise-grade security measures to protect sensitive financial information and ensure
                            compliance with national data protection standards.
                        </p>

                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-[#128a43]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-1">Data Privacy Act Compliant</h3>
                                    <p class="text-sm text-gray-600">Full adherence to RA 10173 (Data Privacy Act of
                                        2012)</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-[#128a43]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-1">End-to-End Encryption</h3>
                                    <p class="text-sm text-gray-600">All financial data encrypted in transit and at
                                        rest</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-[#128a43]" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 mb-1">Regular Security Audits</h3>
                                    <p class="text-sm text-gray-600">Continuous monitoring and vulnerability
                                        assessments</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- ACCESS INFORMATION --}}
        <section class="py-16 lg:py-20 bg-gradient-to-br from-teal-50 to-green-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row-reverse items-center gap-12">

                    {{-- Right, Illustration --}}
                    <div class="lg:w-2/5 flex justify-center svg-animate" data-animation="slide-in-right">
                        <svg class="w-72 lg:w-80 h-auto text-[#128a43]" viewBox="0 0 200 200" fill="none"
                            xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="100" cy="100" r="85" fill="white" />
                            <g transform="translate(40,40) scale(5)">
                                <path d="M20 11v6" />
                                <path d="M20 13h2" />
                                <path d="M3 21v-2a4 4 0 0 1 4-4h6a4 4 0 0 1 2.072.578" />
                                <circle cx="10" cy="7" r="4" />
                                <circle cx="20" cy="19" r="2" />
                            </g>
                        </svg>
                    </div>

                    {{-- Left, Content --}}
                    <div class="lg:w-3/5">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 tracking-tight">
                            AUTHORIZED ACCESS
                        </h2>

                        <p class="text-gray-700 text-base leading-relaxed mb-6">
                            Access to the eAlloc Budget Management System is restricted to authorized government
                            personnel only. All users must authenticate through secure credentials to access the
                            platform.
                        </p>

                        <div class="bg-white border-l-4 border-[#128a43] p-6 rounded-r-lg shadow-sm mb-6">
                            <h3 class="font-bold text-gray-900 mb-2">System Access Requirements</h3>

                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-start gap-2">
                                    <span class="text-[#128a43] font-bold">•</span>
                                    <span>Valid government-issued credentials</span>
                                </li>

                                <li class="flex items-start gap-2">
                                    <span class="text-[#128a43] font-bold">•</span>
                                    <span>Assigned role and permissions within your LGU</span>
                                </li>

                                <li class="flex items-start gap-2">
                                    <span class="text-[#128a43] font-bold">•</span>
                                    <span>Completion of mandatory system orientation</span>
                                </li>

                                <li class="flex items-start gap-2">
                                    <span class="text-[#128a43] font-bold">•</span>
                                    <span>Active employment status with authorized government unit</span>
                                </li>
                            </ul>
                        </div>

                        <p class="text-sm text-gray-600 italic">
                            All access attempts are logged and monitored. Unauthorized access attempts are subject to
                            administrative action and may be reported to appropriate authorities.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        {{-- STRATEGIC GOALS --}}
        <section class="py-24 bg-gray-50 border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div
                    class="mb-12 border-b border-gray-200 pb-5 flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight uppercase">Strategic Objectives
                        </h2>
                        <p class="text-sm text-gray-500 mt-2 font-medium">Core performance targets of the eAlloc
                            framework.</p>
                    </div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                        Directives
                    </div>
                </div>

                {{-- Directory Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Transparency --}}
                    <div class="bg-white border border-gray-200 rounded-sm p-8 flex flex-col relative">
                        <div class="absolute top-0 left-0 w-full h-1 bg-[#128a43]"></div>

                        <div class="flex items-center gap-4 mb-4">
                            <div class="text-[#128a43]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Fiscal Transparency
                            </h3>
                        </div>

                        <p class="text-sm text-gray-600 leading-relaxed mb-8 flex-grow">
                            To provide absolute visibility into the allocation and expenditure of public funds, ensuring
                            all stakeholders can track financial activities accurately and openly.
                        </p>

                        <div class="mt-auto border-t border-gray-100 pt-4">
                            <span
                                class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Target
                                Outcome</span>
                            <span class="block text-sm font-semibold text-gray-800">Zero Financial Blind Spots</span>
                        </div>
                    </div>

                    {{-- Efficiency --}}
                    <div class="bg-white border border-gray-200 rounded-sm p-8 flex flex-col relative">
                        <div class="absolute top-0 left-0 w-full h-1 bg-[#128a43]"></div>

                        <div class="flex items-center gap-4 mb-4">
                            <div class="text-[#128a43]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Operational Efficiency
                            </h3>
                        </div>

                        <p class="text-sm text-gray-600 leading-relaxed mb-8 flex-grow">
                            To eliminate administrative bottlenecks in budget distribution by digitizing approval
                            workflows and automating real-time financial reporting metrics.
                        </p>

                        <div class="mt-auto border-t border-gray-100 pt-4">
                            <span
                                class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Target
                                Outcome</span>
                            <span class="block text-sm font-semibold text-gray-800">Accelerated Processing</span>
                        </div>
                    </div>

                    {{-- Compliance --}}
                    <div class="bg-white border border-gray-200 rounded-sm p-8 flex flex-col relative">
                        <div class="absolute top-0 left-0 w-full h-1 bg-[#128a43]"></div>
                        <div class="flex items-center gap-4 mb-4">
                            <div class="text-[#128a43]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Strict Compliance</h3>
                        </div>

                        <p class="text-sm text-gray-600 leading-relaxed mb-8 flex-grow">
                            To enforce rigid adherence to state financial regulations and Commission on Audit (COA)
                            guidelines through hard-coded systemic limit controls.
                        </p>

                        <div class="mt-auto border-t border-gray-100 pt-4">
                            <span
                                class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Target
                                Outcome</span>
                            <span class="block text-sm font-semibold text-gray-800">100% Audit Readiness</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>

    </main>

    {{-- Scroll Animation Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const heroIllustration = document.querySelector('[data-animation="slide-in-right"]');
            if (heroIllustration && heroIllustration.closest('section').classList.contains('min-h-screen')) {
                heroIllustration.classList.add('slide-in-right');
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const animation = entry.target.dataset.animation;
                        entry.target.classList.add(animation);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.2,
                rootMargin: '0px 0px -50px 0px'
            });

            document.querySelectorAll('.svg-animate').forEach(svg => {
                if (!svg.closest('section').classList.contains('min-h-screen')) {
                    observer.observe(svg);
                }
            });
        });
    </script>

</x-layout>
