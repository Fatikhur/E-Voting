<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - E-Voting RT/RW</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen relative overflow-x-hidden text-[#252A34] antialiased selection:bg-[#1877F2] selection:text-white flex flex-col items-center justify-center p-4 py-8 sm:py-12"
      x-data="{ showPassword: false, loginInput: '{{ old('login', '') }}', passwordInput: '' }">

    <!-- Fixed Fullscreen Background with Smaller & More Abundant 3D Floating Spheres -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0"
         style="background: radial-gradient(circle at 50% 50%, #0E1938 0%, #091024 45%, #050814 100%);">

        <!-- Sphere 1: Top-Left (Medium, ~190px) -->
        <div class="absolute -top-10 -left-10 w-48 h-48 rounded-full opacity-85"
             style="background: radial-gradient(circle at 35% 30%, #3B82F6 0%, #1D4ED8 42%, #0E1F4D 75%, #050A1A 100%); box-shadow: 0 16px 32px -8px rgba(0,0,0,0.85);"></div>

        <!-- Sphere 2: Mid-Left (Small, ~130px) -->
        <div class="absolute top-1/4 -left-8 w-32 h-32 rounded-full opacity-75"
             style="background: radial-gradient(circle at 35% 30%, #60A5FA 0%, #2563EB 45%, #0F2356 75%, #050A1A 100%); box-shadow: 0 12px 24px -6px rgba(0,0,0,0.8);"></div>

        <!-- Sphere 3: Lower-Left (Medium-Large, ~220px) -->
        <div class="absolute bottom-20 -left-12 w-56 h-56 rounded-full opacity-85"
             style="background: radial-gradient(circle at 35% 30%, #2563EB 0%, #1D4ED8 45%, #0F1E4A 75%, #050A1A 100%); box-shadow: 0 20px 40px -10px rgba(0,0,0,0.85);"></div>

        <!-- Sphere 4: Bottom-Left Ambient (Small, ~110px) -->
        <div class="absolute -bottom-6 left-1/4 w-28 h-28 rounded-full opacity-65 blur-xs"
             style="background: radial-gradient(circle at 35% 30%, #1E40AF 0%, #172554 60%, #030712 100%);"></div>

        <!-- Sphere 5: Top-Right (Medium, ~180px) -->
        <div class="absolute -top-8 right-16 w-44 h-44 rounded-full opacity-80"
             style="background: radial-gradient(circle at 35% 30%, #3B82F6 0%, #1D4ED8 42%, #0E1F4D 75%, #050A1A 100%); box-shadow: 0 16px 32px -8px rgba(0,0,0,0.85);"></div>

        <!-- Sphere 6: Mid-Right Feature (Balanced, ~260px - significantly smaller than previous 540px) -->
        <div class="absolute top-1/2 -right-12 -translate-y-1/2 w-64 h-64 rounded-full opacity-90"
             style="background: radial-gradient(circle at 35% 30%, #3B82F6 0%, #1D4ED8 40%, #0F2356 72%, #050A1A 100%); box-shadow: 0 24px 48px -12px rgba(0,0,0,0.9);"></div>

        <!-- Sphere 7: Lower-Right (Small-Medium, ~140px) -->
        <div class="absolute bottom-24 right-10 w-36 h-36 rounded-full opacity-80"
             style="background: radial-gradient(circle at 35% 30%, #2563EB 0%, #1D4ED8 45%, #0F2356 75%, #050A1A 100%); box-shadow: 0 12px 24px -6px rgba(0,0,0,0.8);"></div>

        <!-- Sphere 8: Bottom-Right (Medium, ~190px) -->
        <div class="absolute -bottom-10 -right-10 w-48 h-48 rounded-full opacity-75"
             style="background: radial-gradient(circle at 35% 30%, #1D4ED8 0%, #172554 55%, #050811 100%); box-shadow: 0 16px 32px -8px rgba(0,0,0,0.85);"></div>

        <!-- Sphere 9: Top Floating Ambient Orb (~80px) -->
        <div class="absolute top-12 left-1/3 w-20 h-20 rounded-full opacity-45 blur-xs"
             style="background: radial-gradient(circle at 35% 30%, #60A5FA 0%, #1D4ED8 60%, #0F172A 100%);"></div>

        <!-- Sphere 10: Bottom Floating Ambient Orb (~95px) -->
        <div class="absolute bottom-12 right-1/3 w-24 h-24 rounded-full opacity-50 blur-xs"
             style="background: radial-gradient(circle at 35% 30%, #38BDF8 0%, #1E40AF 60%, #0B132B 100%);"></div>

        <!-- Subtle Ambient Glows -->
        <div class="absolute top-1/4 left-1/3 w-96 h-96 rounded-full bg-blue-500/10 blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 rounded-full bg-blue-600/10 blur-3xl"></div>
    </div>

    <!-- Centered Login Container: Safe Margin on Top and Bottom, Perfectly Balanced -->
    <div class="w-full max-w-md my-auto relative z-10 flex flex-col items-center">

        <!-- Frosted Glass Card Container: Exactly matching Reference Card -->
        <div class="w-full bg-white/95 backdrop-blur-2xl border border-white/90 shadow-2xl shadow-black/35 rounded-[30px] p-5 sm:p-6 lg:p-7 relative overflow-hidden">

            <!-- Subtle Glass Shine on Top Edge -->
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white to-transparent pointer-events-none"></div>

            <!-- Header: Logo, E-Voting RT/RW & Concise Selamat Datang -->
            <div class="text-center mb-3.5">
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-[#1877F2] to-[#1258b8] text-white shadow-md shadow-blue-500/25 mb-1.5 border border-white/40">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h1 class="text-xl font-extrabold text-[#1E293B] tracking-tight">E-Voting RT/RW</h1>
                <p class="text-[10px] font-bold text-[#1877F2] uppercase tracking-wider mt-0.5">
                    Selamat Datang
                </p>
                <p class="text-xs text-[#64748B]">
                    Sistem Pemilihan Digital Transparan & Aman
                </p>
            </div>

            <!-- Flash Alerts -->
            @if(session('error'))
                <div class="mb-2.5 bg-red-50/90 border border-red-200/80 p-2 rounded-xl text-xs text-red-800 flex items-center gap-2 backdrop-blur-sm shadow-2xs">
                    <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-2.5 bg-blue-50/90 border border-blue-200/80 p-2 rounded-xl text-xs text-blue-800 flex items-center gap-2 backdrop-blur-sm shadow-2xs">
                    <svg class="w-4 h-4 text-[#1877F2] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-2.5 bg-emerald-50/90 border border-emerald-200/80 p-2 rounded-xl text-xs text-emerald-800 flex items-center gap-2 backdrop-blur-sm shadow-2xs">
                    <svg class="w-4 h-4 text-[#269B68] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-2.5 bg-red-50/90 border border-red-200/80 p-2 rounded-xl text-xs text-red-800 backdrop-blur-sm shadow-2xs">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form with Clean & Compact Spacing -->
            <form action="{{ route('login') }}" method="POST" class="space-y-2.5">
                @csrf

                <!-- Username or NIK: Pill Input -->
                <div>
                    <label for="login" class="block text-xs font-semibold text-[#1E293B] mb-1 ml-1.5">
                        Username atau NIK (16 Digit) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative rounded-full transition-all focus-within:ring-4 focus-within:ring-[#1877F2]/15">
                        <input
                            type="text"
                            name="login"
                            id="login"
                            x-model="loginInput"
                            required
                            autofocus
                            placeholder="Username atau 16 digit NIK"
                            class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm rounded-full border border-blue-100 bg-white/70 hover:bg-white focus:bg-white text-[#1E293B] placeholder-[#94A3B8] focus:outline-none focus:border-[#1877F2] backdrop-blur-md transition shadow-2xs"
                        >
                        <!-- Small User Silhouette Icon -->
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#1877F2] z-10">
                            <svg class="w-4 h-4 text-[#1877F2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-0.5 text-[10px] text-[#64748B] ml-2">Gunakan username akun atau 16 digit NIK terdaftar di DPT.</p>
                </div>

                <!-- Password: Pill Input with Eye Toggle -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-[#1E293B] mb-1 ml-1.5">
                        Kata Sandi <span class="text-red-500">*</span>
                    </label>
                    <div class="relative rounded-full transition-all focus-within:ring-4 focus-within:ring-[#1877F2]/15">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            id="password"
                            x-model="passwordInput"
                            required
                            placeholder="Masukkan kata sandi akun"
                            class="w-full pl-10 pr-10 py-2 text-xs sm:text-sm rounded-full border border-blue-100 bg-white/70 hover:bg-white focus:bg-white text-[#1E293B] placeholder-[#94A3B8] focus:outline-none focus:border-[#1877F2] backdrop-blur-md transition shadow-2xs"
                        >
                        <!-- Small Lock Icon -->
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#1877F2] z-10">
                            <svg class="w-4 h-4 text-[#1877F2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <!-- Eye Icon Toggle -->
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#64748B] hover:text-[#1877F2] transition cursor-pointer select-none z-10"
                            tabindex="-1"
                            :title="showPassword ? 'Sembunyikan sandi' : 'Tampilkan sandi'"
                        >
                            <template x-if="!showPassword">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </template>
                            <template x-if="showPassword">
                                <svg class="w-4 h-4 text-[#1877F2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </template>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-0.5 px-1.5">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#1877F2] border-blue-200 focus:ring-[#1877F2]">
                        <span class="text-xs text-[#64748B]">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button: Pill Shape -->
                <div class="pt-0.5">
                    <button
                        type="submit"
                        class="w-full py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-full text-white bg-gradient-to-r from-[#1877F2] to-[#1258b8] hover:from-[#166fe5] hover:to-[#0f4ea2] focus:outline-none focus:ring-4 focus:ring-[#1877F2]/30 shadow-lg shadow-blue-500/25 active:scale-[0.99] transition cursor-pointer flex items-center justify-center gap-2"
                    >
                        <span>Masuk ke Sistem</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative my-3">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-blue-100"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-3 bg-white/90 text-[#64748B] text-[10px] font-semibold uppercase tracking-wider rounded-full border border-blue-100 backdrop-blur-sm shadow-2xs">
                        Akun Percobaan Demo
                    </span>
                </div>
            </div>

            <!-- Demo Accounts Quick Clickers -->
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button
                    type="button"
                    @click="loginInput = 'pemilih'; passwordInput = 'Pemilih123!'"
                    class="p-2 rounded-2xl border border-blue-200/70 bg-blue-50/50 hover:bg-blue-100/70 hover:border-blue-400 text-[#1E293B] text-left transition-all duration-150 flex flex-col gap-0.5 cursor-pointer group shadow-2xs backdrop-blur-xs"
                >
                    <div class="flex items-center gap-1.5 font-semibold text-[11px] text-blue-900 group-hover:text-[#1877F2]">
                        <svg class="w-3.5 h-3.5 text-[#1877F2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Pemilih / Warga</span>
                    </div>
                    <span class="text-[9px] text-blue-700/80 font-mono">pemilih / Pemilih123!</span>
                </button>

                <button
                    type="button"
                    @click="loginInput = 'admin'; passwordInput = 'Admin123!'"
                    class="p-2 rounded-2xl border border-emerald-200/70 bg-emerald-50/50 hover:bg-emerald-100/70 hover:border-emerald-400 text-[#1E293B] text-left transition-all duration-150 flex flex-col gap-0.5 cursor-pointer group shadow-2xs backdrop-blur-xs"
                >
                    <div class="flex items-center gap-1.5 font-semibold text-[11px] text-emerald-900 group-hover:text-[#269B68]">
                        <svg class="w-3.5 h-3.5 text-[#269B68]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Admin Panitia</span>
                    </div>
                    <span class="text-[9px] text-emerald-700/80 font-mono">admin / Admin123!</span>
                </button>

                <button
                    type="button"
                    @click="loginInput = 'pengawas'; passwordInput = 'Pengawas123!'"
                    class="p-2 rounded-2xl border border-amber-200/70 bg-amber-50/50 hover:bg-amber-100/70 hover:border-amber-400 text-[#1E293B] text-left transition-all duration-150 flex flex-col gap-0.5 cursor-pointer group shadow-2xs backdrop-blur-xs"
                >
                    <div class="flex items-center gap-1.5 font-semibold text-[11px] text-amber-900 group-hover:text-amber-700">
                        <svg class="w-3.5 h-3.5 text-[#D97706]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>Pengawas</span>
                    </div>
                    <span class="text-[9px] text-amber-800/80 font-mono">pengawas / Pengawas123!</span>
                </button>

                <button
                    type="button"
                    @click="loginInput = 'superadmin'; passwordInput = 'SuperAdmin123!'"
                    class="p-2 rounded-2xl border border-purple-200/70 bg-purple-50/50 hover:bg-purple-100/70 hover:border-purple-400 text-[#1E293B] text-left transition-all duration-150 flex flex-col gap-0.5 cursor-pointer group shadow-2xs backdrop-blur-xs"
                >
                    <div class="flex items-center gap-1.5 font-semibold text-[11px] text-purple-900 group-hover:text-purple-700">
                        <svg class="w-3.5 h-3.5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        <span>Super Admin</span>
                    </div>
                    <span class="text-[9px] text-purple-800/80 font-mono">superadmin / SuperAdmin123!</span>
                </button>
            </div>

        </div>

        <!-- Footer Copyright (Spaced cleanly below card) -->
        <p class="mt-4 mb-2 text-center text-[11px] text-blue-200/80 tracking-wide select-none">
            Hak Cipta &copy; {{ date('Y') }} Rukun Warga 01 Dusun Harmoni Indah. Seluruh hak cipta dilindungi.
        </p>

    </div>

</body>
</html>
