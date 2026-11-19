<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'E-Voting RT/RW' }} - Sistem Pemilihan Digital RT/RW</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full flex flex-col font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen bg-slate-50 flex">

        <!-- Desktop Sidebar (Hidden on mobile, visible on md:flex) -->
        <aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 z-30 bg-slate-900 border-r border-slate-800 text-slate-200">
            <!-- Sidebar Header / Brand Logo -->
            <div class="h-16 flex items-center gap-3 px-5 border-b border-slate-800 bg-slate-950/40 shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 rounded-lg">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white shadow-sm shadow-blue-500/25 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div class="leading-tight truncate">
                        <span class="text-base font-bold tracking-tight text-white block truncate">E-Voting RT/RW</span>
                        <span class="text-[11px] text-slate-400 font-medium block truncate">Sistem Pemilihan Digital</span>
                    </div>
                </a>
            </div>

            <!-- Sidebar Nav Links Component -->
            @include('layouts.sidebar-nav')
        </aside>

        <!-- Mobile Sidebar Drawer (Overlay) -->
        <div x-cloak x-show="sidebarOpen" class="relative z-50 md:hidden" role="dialog" aria-modal="true" @keydown.window.escape="sidebarOpen = false">
            <!-- Backdrop Overlay -->
            <div x-show="sidebarOpen"
                 x-transition:enter="transition-opacity ease-linear duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs"
                 @click="sidebarOpen = false"
                 aria-hidden="true"></div>

            <div class="fixed inset-0 flex">
                <!-- Drawer Panel -->
                <div x-show="sidebarOpen"
                     x-transition:enter="transition ease-in-out duration-200 transform"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-150 transform"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full"
                     class="relative mr-14 flex w-full max-w-xs flex-1 flex-col bg-slate-900 border-r border-slate-800">
                    
                    <!-- Close Button -->
                    <div class="absolute top-2 right-2">
                        <button type="button" 
                                @click="sidebarOpen = false" 
                                class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-white"
                                aria-label="Tutup menu navigasi">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile Brand Logo -->
                    <div class="h-16 flex items-center gap-3 px-5 border-b border-slate-800 bg-slate-950/40 shrink-0">
                        <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center font-bold text-white shadow-sm shadow-blue-500/25 shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <div class="leading-tight truncate">
                            <span class="text-base font-bold tracking-tight text-white block truncate">E-Voting RT/RW</span>
                            <span class="text-[11px] text-slate-400 font-medium block truncate">Sistem Pemilihan Digital</span>
                        </div>
                    </div>

                    @include('layouts.sidebar-nav')
                </div>
            </div>
        </div>

        <!-- Main Wrapper (Offset by sidebar width on desktop: md:pl-64) -->
        <div class="flex-1 md:pl-64 flex flex-col min-w-0">

            <!-- Top Navigation Header (Sticky, with User Identity & Logout from Gambar 2) -->
            <header class="bg-blue-950 text-white shadow-md border-b border-blue-900 sticky top-0 z-20">
                <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
                    <!-- Left: Mobile Menu Trigger & Section Title -->
                    <div class="flex items-center gap-3">
                        <button type="button"
                                @click="sidebarOpen = true"
                                class="md:hidden p-2 -ml-1 rounded-lg text-blue-200 hover:text-white hover:bg-blue-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                                aria-label="Buka menu navigasi">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

                        <div class="hidden md:flex items-center gap-2 text-sm">
                            <span class="text-xs uppercase tracking-wider font-bold text-blue-300/80">E-Voting</span>
                            <span class="text-blue-500 font-light">/</span>
                            <h1 class="font-semibold text-white tracking-tight text-sm sm:text-base">{{ $title ?? 'Dashboard' }}</h1>
                        </div>

                        <div class="md:hidden flex items-center gap-2">
                            <span class="font-bold text-white text-sm tracking-tight truncate max-w-[140px] sm:max-w-xs">{{ $title ?? 'E-Voting' }}</span>
                        </div>
                    </div>

                    <!-- Right: User Badge & Profile & Logout (GAMBAR 2) -->
                    @auth
                    <div class="flex items-center gap-3">
                        <div class="hidden sm:flex flex-col text-right">
                            <span class="text-sm font-semibold text-white leading-tight">{{ auth()->user()->name }}</span>
                            <div class="flex items-center justify-end gap-1.5 mt-0.5">
                                @php
                                    $roleBadge = match(auth()->user()->role_name) {
                                        'superadmin' => ['bg' => 'bg-purple-900/60 text-purple-200 border-purple-500/40', 'label' => 'Super Admin'],
                                        'admin' => ['bg' => 'bg-emerald-900/60 text-emerald-200 border-emerald-500/40', 'label' => 'Admin Panitia'],
                                        'pengawas' => ['bg' => 'bg-amber-900/60 text-amber-200 border-amber-500/40', 'label' => 'Pengawas'],
                                        default => ['bg' => 'bg-blue-900/60 text-blue-200 border-blue-500/40', 'label' => 'Pemilih'],
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border {{ $roleBadge['bg'] }}">
                                    {{ $roleBadge['label'] }}
                                </span>
                                @if(auth()->user()->rw)
                                    <span class="text-[11px] text-blue-300">RW {{ auth()->user()->rw->rw_number }}</span>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('password.edit') }}" title="Ganti Kata Sandi" class="p-2 text-blue-200 hover:text-white hover:bg-blue-900 rounded-lg transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </a>

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')" class="flex items-center gap-1.5 bg-red-600/90 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                    <!-- Flash Alerts -->
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-emerald-600 text-xl font-bold">✓</span>
                                <p class="text-sm md:text-base font-medium text-emerald-900">{{ session('success') }}</p>
                            </div>
                            <button @click="show = false" class="text-emerald-600 hover:text-emerald-900 font-bold ml-4">✕</button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-red-600 text-xl font-bold">⚠️</span>
                                <p class="text-sm md:text-base font-medium text-red-900">{{ session('error') }}</p>
                            </div>
                            <button @click="show = false" class="text-red-600 hover:text-red-900 font-bold ml-4">✕</button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div x-data="{ show: true }" x-show="show" class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg shadow-sm flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-blue-600 text-xl font-bold">ℹ️</span>
                                <p class="text-sm md:text-base font-medium text-blue-900">{{ session('info') }}</p>
                            </div>
                            <button @click="show = false" class="text-blue-600 hover:text-blue-900 font-bold ml-4">✕</button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div x-data="{ show: true }" x-show="show" class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-bold text-red-800 text-sm md:text-base">Terdapat beberapa kesalahan input:</p>
                                    <ul class="list-disc list-inside mt-2 text-sm text-red-700 space-y-1">
                                        @foreach($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button @click="show = false" class="text-red-600 hover:text-red-900 font-bold ml-4">✕</button>
                            </div>
                        </div>
                    @endif

                    <!-- View Content Injection -->
                    {{ $slot ?? '' }}
                    @yield('content')

                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 py-6 mt-auto text-slate-500 text-xs sm:text-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                    <div>
                        <p class="font-semibold text-slate-700">Sistem E-Voting Digital Pemilihan Ketua RT/RW</p>
                        <p class="text-slate-400 mt-0.5">Menjamin Prinsip Langsung, Umum, Bebas, Rahasia, Jujur, dan Adil.</p>
                    </div>
                    <div class="flex items-center gap-4 text-slate-400">
                        <span>Versi 1.0 (Laravel 13 & MySQL 8.4)</span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1 text-emerald-600 font-medium">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            Sistem Aman & Aktif
                        </span>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    @stack('scripts')
</body>
</html>
