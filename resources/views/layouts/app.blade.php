<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F1F2F5] dark:bg-[#0B1120]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'E-Voting RT/RW' }} - Sistem Pemilihan Digital RT/RW</title>

    <script>
        (function () {
            try {
                if (localStorage.getItem('theme') === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full flex flex-col font-sans text-[#252A34] dark:text-slate-100 bg-[#F1F2F5] dark:bg-[#0B1120] antialiased selection:bg-[#1877F2] selection:text-white transition-colors duration-150"
      x-data="{
          sidebarOpen: false,
          darkMode: document.documentElement.classList.contains('dark'),
          toggleDarkMode() {
              this.darkMode = !this.darkMode;
              document.documentElement.classList.toggle('dark', this.darkMode);
              try {
                  localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
              } catch (e) {}
              window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: this.darkMode } }));
          }
      }">

    <div class="min-h-screen bg-[#F1F2F5] flex">

        <!-- Desktop Sidebar (Hidden on mobile, visible on md:flex) -->
        <aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 z-30 bg-[#171B35] border-r border-[#1F2445] text-[#C5C9D4]">
            <!-- Sidebar Header / Brand Logo -->
            <div class="h-16 flex items-center gap-3 px-5 border-b border-[#1F2445] bg-[#13162C] shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2ECC71] rounded-lg">
                    <div class="w-8 h-8 rounded-lg bg-[#1877F2] flex items-center justify-center font-bold text-white shadow-xs shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div class="leading-tight truncate">
                        <span class="text-sm font-bold tracking-tight text-white block truncate">E-Voting RT/RW</span>
                        <span class="text-[11px] text-[#9AA0B4] font-medium block truncate">Sistem Pemilihan Digital</span>
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
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
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
                     class="relative mr-14 flex w-full max-w-xs flex-1 flex-col bg-[#171B35] border-r border-[#1F2445]">
                    
                    <!-- Close Button -->
                    <div class="absolute top-2.5 right-2.5">
                        <button type="button" 
                                @click="sidebarOpen = false" 
                                class="p-1.5 rounded-lg text-[#9AA0B4] hover:text-white hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-[#2ECC71]"
                                aria-label="Tutup menu navigasi">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile Brand Logo -->
                    <div class="h-16 flex items-center gap-3 px-5 border-b border-[#1F2445] bg-[#13162C] shrink-0">
                        <div class="w-8 h-8 rounded-lg bg-[#1877F2] flex items-center justify-center font-bold text-white shadow-xs shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <div class="leading-tight truncate">
                            <span class="text-sm font-bold tracking-tight text-white block truncate">E-Voting RT/RW</span>
                            <span class="text-[11px] text-[#9AA0B4] font-medium block truncate">Sistem Pemilihan Digital</span>
                        </div>
                    </div>

                    @include('layouts.sidebar-nav')
                </div>
            </div>
        </div>

        <!-- Main Wrapper (Offset by sidebar width on desktop: md:pl-64) -->
        <div class="flex-1 md:pl-64 flex flex-col min-w-0">

            <!-- Top Navigation Header -->
            <header class="bg-white border-b border-[#E5E7EB] sticky top-0 z-20">
                <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
                    <!-- Left: Mobile Menu Trigger & Section Title -->
                    <div class="flex items-center gap-3">
                        <button type="button"
                                @click="sidebarOpen = true"
                                class="md:hidden p-2 -ml-1 rounded-lg text-[#6B7280] hover:text-[#252A34] hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1877F2]"
                                aria-label="Buka menu navigasi">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

                        <div class="hidden md:flex items-center gap-2 text-sm">
                            <span class="text-xs uppercase tracking-wider font-semibold text-[#6B7280]">E-Voting</span>
                            <span class="text-slate-300 font-light">/</span>
                            <h1 class="font-semibold text-[#252A34] tracking-tight text-sm sm:text-base">{{ $title ?? 'Dashboard' }}</h1>
                        </div>

                        <div class="md:hidden flex items-center gap-2">
                            <span class="font-semibold text-[#252A34] text-sm tracking-tight truncate max-w-[150px] sm:max-w-xs">{{ $title ?? 'E-Voting' }}</span>
                        </div>
                    </div>

                    <!-- Right: User Info, Role Badge & Logout Button -->
                    @auth
                    <div class="flex items-center gap-3">
                        <div class="hidden sm:flex flex-col text-right">
                            <span class="text-sm font-semibold text-[#252A34] leading-tight">{{ auth()->user()->name }}</span>
                            <div class="flex items-center justify-end gap-1.5 mt-0.5">
                                @php
                                    $roleBadge = match(auth()->user()->role_name) {
                                        'superadmin' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-200', 'label' => 'Super Admin'],
                                        'admin' => ['bg' => 'bg-blue-50 text-[#1877F2] border-blue-200', 'label' => 'Admin Panitia'],
                                        'pengawas' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Pengawas'],
                                        default => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'label' => 'Pemilih'],
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border {{ $roleBadge['bg'] }}">
                                    {{ $roleBadge['label'] }}
                                </span>
                                @if(auth()->user()->rw)
                                    <span class="text-[11px] text-[#6B7280]">RW {{ auth()->user()->rw->rw_number }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Dark Mode Toggle Button (All Roles) -->
                        <button type="button"
                                @click="toggleDarkMode()"
                                :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'"
                                :aria-label="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'"
                                class="p-2 text-[#6B7280] dark:text-slate-400 hover:text-[#252A34] dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1877F2]">
                            <svg x-cloak x-show="!darkMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                            <svg x-cloak x-show="darkMode" class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </button>

                        <a href="{{ route('password.edit') }}" title="Ganti Kata Sandi" class="p-2 text-[#6B7280] dark:text-slate-400 hover:text-[#252A34] dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1877F2]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </a>

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="button"
                                    data-confirm
                                    data-confirm-type="danger"
                                    data-confirm-title="Keluar dari Sistem"
                                    data-confirm-message="Apakah Anda yakin ingin mengakhiri sesi dan keluar dari sistem E-Voting?"
                                    data-confirm-detail="Anda harus memasukkan kembali kredensial akun untuk mengakses panel ini."
                                    data-confirm-button="Ya, Keluar"
                                    data-confirm-cancel="Batal"
                                    class="flex items-center gap-1.5 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-1.5 rounded-lg text-xs font-semibold transition shadow-2xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-400">
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
                        <div x-data="{ show: true }" x-show="show" class="mb-6 bg-emerald-50 border border-emerald-200 p-4 rounded-xl flex items-start justify-between shadow-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-emerald-100 text-[#269B68] flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>
                                <p class="text-xs sm:text-sm font-medium text-emerald-950">{{ session('success') }}</p>
                            </div>
                            <button @click="show = false" class="text-emerald-700 hover:text-emerald-900 ml-4 p-1 rounded-md hover:bg-emerald-100" aria-label="Tutup notifikasi">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" class="mb-6 bg-red-50 border border-red-200 p-4 rounded-xl flex items-start justify-between shadow-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <p class="text-xs sm:text-sm font-medium text-red-950">{{ session('error') }}</p>
                            </div>
                            <button @click="show = false" class="text-red-700 hover:text-red-900 ml-4 p-1 rounded-md hover:bg-red-100" aria-label="Tutup notifikasi">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div x-data="{ show: true }" x-show="show" class="mb-6 bg-blue-50 border border-blue-200 p-4 rounded-xl flex items-start justify-between shadow-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-blue-100 text-[#1877F2] flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                    </svg>
                                </div>
                                <p class="text-xs sm:text-sm font-medium text-blue-950">{{ session('info') }}</p>
                            </div>
                            <button @click="show = false" class="text-blue-700 hover:text-blue-900 ml-4 p-1 rounded-md hover:bg-blue-100" aria-label="Tutup notifikasi">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div x-data="{ show: true }" x-show="show" class="mb-6 bg-red-50 border border-red-200 p-4 rounded-xl shadow-xs">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-semibold text-red-900 text-xs sm:text-sm">Terdapat beberapa kesalahan input:</p>
                                    <ul class="list-disc list-inside mt-2 text-xs sm:text-sm text-red-800 space-y-1">
                                        @foreach($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button @click="show = false" class="text-red-700 hover:text-red-900 ml-4 p-1 rounded-md hover:bg-red-100" aria-label="Tutup notifikasi">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- View Content Injection -->
                    {{ $slot ?? '' }}
                    @yield('content')

                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-[#E5E7EB] py-5 mt-auto text-xs text-[#6B7280]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
                    <div>
                        <p class="font-semibold text-[#252A34]">Sistem E-Voting Digital Pemilihan Ketua RT/RW</p>
                        <p class="text-[#6B7280] mt-0.5">Menjamin Prinsip Langsung, Umum, Bebas, Rahasia, Jujur, dan Adil.</p>
                    </div>
                    <div class="flex items-center gap-3 text-[#6B7280]">
                        <span>Versi 1.0</span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1.5 text-[#269B68] font-medium">
                            <span class="w-2 h-2 rounded-full bg-[#269B68]"></span>
                            Sistem Aman & Aktif
                        </span>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    {{-- Global Confirmation Dialog Modal --}}
    @include('layouts.confirm-modal')

    @stack('scripts')
</body>
</html>
