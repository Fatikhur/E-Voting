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
</head>
<body class="h-full flex flex-col font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <!-- Top Navigation Header -->
    <header class="bg-blue-950 text-white shadow-md border-b border-blue-900 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 focus:outline-none focus:ring-2 focus:ring-blue-400 rounded-lg p-1">
                        <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-xl shadow-inner text-white">
                            🗳️
                        </div>
                        <div>
                            <span class="text-lg font-bold tracking-tight text-white block leading-tight">E-Voting RT/RW</span>
                            <span class="text-xs text-blue-300 font-medium block">Sistem Pemilihan Digital Terpercaya</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links based on Role -->
                @auth
                <nav class="hidden md:flex items-center gap-1 text-sm font-medium">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('*.dashboard') || request()->routeIs('dashboard') ? 'bg-blue-800 text-white font-semibold' : 'text-blue-100 hover:bg-blue-900 hover:text-white' }}">
                        Dashboard
                    </a>

                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('superadmin.users.index') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('superadmin.users.*') ? 'bg-blue-800 text-white font-semibold' : 'text-blue-100 hover:bg-blue-900 hover:text-white' }}">
                            Kelola Akun
                        </a>
                        <a href="{{ route('superadmin.territories.index') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('superadmin.territories.*') ? 'bg-blue-800 text-white font-semibold' : 'text-blue-100 hover:bg-blue-900 hover:text-white' }}">
                            Wilayah RT/RW
                        </a>
                        <a href="{{ route('superadmin.audit.index') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('superadmin.audit.*') ? 'bg-blue-800 text-white font-semibold' : 'text-blue-100 hover:bg-blue-900 hover:text-white' }}">
                            Audit & Integritas
                        </a>
                        <a href="{{ route('superadmin.settings.index') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('superadmin.settings.*') ? 'bg-blue-800 text-white font-semibold' : 'text-blue-100 hover:bg-blue-900 hover:text-white' }}">
                            Pengaturan
                        </a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.residents.index') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('admin.residents.*') ? 'bg-blue-800 text-white font-semibold' : 'text-blue-100 hover:bg-blue-900 hover:text-white' }}">
                            Data Warga
                        </a>
                        <a href="{{ route('admin.elections.index') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('admin.elections.*') || request()->routeIs('admin.candidates.*') || request()->routeIs('admin.dpt.*') ? 'bg-blue-800 text-white font-semibold' : 'text-blue-100 hover:bg-blue-900 hover:text-white' }}">
                            Pemilihan
                        </a>
                    @elseif(auth()->user()->isPengawas())
                        <a href="{{ route('pengawas.audit.index') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('pengawas.audit.*') ? 'bg-blue-800 text-white font-semibold' : 'text-blue-100 hover:bg-blue-900 hover:text-white' }}">
                            Jejak Audit
                        </a>
                        <a href="{{ route('pengawas.reports.index') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('pengawas.reports.*') ? 'bg-blue-800 text-white font-semibold' : 'text-blue-100 hover:bg-blue-900 hover:text-white' }}">
                            Laporan Temuan
                        </a>
                    @endif
                </nav>

                <!-- User Badge & Profile & Logout -->
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

                    <a href="{{ route('password.edit') }}" title="Ganti Kata Sandi" class="p-2 text-blue-200 hover:text-white hover:bg-blue-900 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')" class="flex items-center gap-1.5 bg-red-600/90 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition shadow-sm focus:outline-none focus:ring-2 focus:ring-red-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </div>

        <!-- Mobile Nav Sub-bar -->
        @auth
        <div class="md:hidden border-t border-blue-900 bg-blue-950 px-4 py-2 flex items-center gap-2 overflow-x-auto text-xs font-medium">
            <a href="{{ route('dashboard') }}" class="px-2.5 py-1.5 rounded whitespace-nowrap {{ request()->routeIs('*.dashboard') || request()->routeIs('dashboard') ? 'bg-blue-800 text-white font-bold' : 'text-blue-200' }}">Dashboard</a>
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('superadmin.users.index') }}" class="px-2.5 py-1.5 rounded whitespace-nowrap {{ request()->routeIs('superadmin.users.*') ? 'bg-blue-800 text-white font-bold' : 'text-blue-200' }}">Akun</a>
                <a href="{{ route('superadmin.territories.index') }}" class="px-2.5 py-1.5 rounded whitespace-nowrap {{ request()->routeIs('superadmin.territories.*') ? 'bg-blue-800 text-white font-bold' : 'text-blue-200' }}">Wilayah</a>
                <a href="{{ route('superadmin.audit.index') }}" class="px-2.5 py-1.5 rounded whitespace-nowrap {{ request()->routeIs('superadmin.audit.*') ? 'bg-blue-800 text-white font-bold' : 'text-blue-200' }}">Audit</a>
                <a href="{{ route('superadmin.settings.index') }}" class="px-2.5 py-1.5 rounded whitespace-nowrap {{ request()->routeIs('superadmin.settings.*') ? 'bg-blue-800 text-white font-bold' : 'text-blue-200' }}">Pengaturan</a>
            @elseif(auth()->user()->isAdmin())
                <a href="{{ route('admin.residents.index') }}" class="px-2.5 py-1.5 rounded whitespace-nowrap {{ request()->routeIs('admin.residents.*') ? 'bg-blue-800 text-white font-bold' : 'text-blue-200' }}">Data Warga</a>
                <a href="{{ route('admin.elections.index') }}" class="px-2.5 py-1.5 rounded whitespace-nowrap {{ request()->routeIs('admin.elections.*') ? 'bg-blue-800 text-white font-bold' : 'text-blue-200' }}">Pemilihan</a>
            @elseif(auth()->user()->isPengawas())
                <a href="{{ route('pengawas.audit.index') }}" class="px-2.5 py-1.5 rounded whitespace-nowrap {{ request()->routeIs('pengawas.audit.*') ? 'bg-blue-800 text-white font-bold' : 'text-blue-200' }}">Audit</a>
                <a href="{{ route('pengawas.reports.index') }}" class="px-2.5 py-1.5 rounded whitespace-nowrap {{ request()->routeIs('pengawas.reports.*') ? 'bg-blue-800 text-white font-bold' : 'text-blue-200' }}">Laporan</a>
            @endif
        </div>
        @endauth
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

    @stack('scripts')
</body>
</html>
