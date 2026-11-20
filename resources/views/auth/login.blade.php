<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F1F2F5]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - E-Voting RT/RW</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-[#F1F2F5] text-[#252A34] antialiased">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
        <!-- Logo -->
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-[#171B35] text-white shadow-xs mb-3 border border-[#1F2445]">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
        </div>
        <h1 class="text-xl font-bold text-[#252A34] tracking-tight">E-Voting RT/RW</h1>
        <p class="mt-1 text-xs text-[#6B7280]">
            Sistem Digital Pemilihan Ketua RT dan Ketua RW
        </p>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="bg-white py-6 px-5 sm:px-8 shadow-xs rounded-xl border border-[#E5E7EB]" x-data="{ showPassword: false, loginInput: '{{ old('login', '') }}', passwordInput: '' }">

            <!-- Flash Error / Success -->
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 p-3 rounded-lg text-xs text-red-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-4 bg-blue-50 border border-blue-200 p-3 rounded-lg text-xs text-blue-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#1877F2] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 p-3 rounded-lg text-xs text-emerald-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#269B68] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Username or NIK -->
                <div>
                    <label for="login" class="block text-xs font-semibold text-[#252A34] mb-1.5">
                        Username atau NIK (16 Digit) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="login"
                        id="login"
                        x-model="loginInput"
                        required
                        autofocus
                        placeholder="Contoh: pemilih atau 327601..."
                        class="w-full px-3 py-2 text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] placeholder-[#9CA3AF] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2] transition"
                    >
                    <p class="mt-1 text-[11px] text-[#6B7280]">Warga dapat masuk menggunakan username atau 16 digit NIK terdaftar.</p>
                </div>

                <!-- Password with Show/Hide toggle -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-[#252A34]">
                            Kata Sandi <span class="text-red-500">*</span>
                        </label>
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="inline-flex items-center gap-1 text-[11px] text-[#1877F2] hover:text-[#166fe5] font-medium focus:outline-none transition cursor-pointer"
                            tabindex="-1"
                        >
                            <template x-if="!showPassword">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>Tampilkan</span>
                                </span>
                            </template>
                            <template x-if="showPassword">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                    <span>Sembunyikan</span>
                                </span>
                            </template>
                        </button>
                    </div>
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        id="password"
                        x-model="passwordInput"
                        required
                        placeholder="Masukkan kata sandi akun Anda"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] placeholder-[#9CA3AF] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2] transition"
                    >
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#1877F2] border-[#E5E7EB] focus:ring-[#1877F2]">
                        <span class="text-xs text-[#6B7280] select-none">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button
                        type="submit"
                        class="w-full py-2.5 px-4 text-xs font-semibold rounded-lg text-white bg-[#1877F2] hover:bg-[#166fe5] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/30 shadow-xs transition"
                    >
                        Masuk ke Sistem Pemilihan
                    </button>
                </div>
            </form>

            <!-- Demo Accounts Quick Clickers -->
            <div class="mt-6 pt-5 border-t border-[#E5E7EB]">
                <p class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider text-center mb-2.5">
                    Akun Percobaan Demo (Klik untuk Isi Cepat)
                </p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <button
                        type="button"
                        @click="loginInput = 'pemilih'; passwordInput = 'Pemilih123!'"
                        class="p-2.5 rounded-lg border border-[#E5E7EB] bg-slate-50 hover:bg-slate-100 text-[#252A34] text-left transition flex flex-col gap-0.5 cursor-pointer"
                    >
                        <div class="flex items-center gap-1.5 font-semibold text-xs text-[#252A34]">
                            <svg class="w-3.5 h-3.5 text-[#1877F2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Pemilih / Warga</span>
                        </div>
                        <span class="text-[11px] text-[#6B7280]">pemilih / Pemilih123!</span>
                    </button>

                    <button
                        type="button"
                        @click="loginInput = 'admin'; passwordInput = 'Admin123!'"
                        class="p-2.5 rounded-lg border border-[#E5E7EB] bg-slate-50 hover:bg-slate-100 text-[#252A34] text-left transition flex flex-col gap-0.5 cursor-pointer"
                    >
                        <div class="flex items-center gap-1.5 font-semibold text-xs text-[#252A34]">
                            <svg class="w-3.5 h-3.5 text-[#269B68]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Admin Panitia</span>
                        </div>
                        <span class="text-[11px] text-[#6B7280]">admin / Admin123!</span>
                    </button>

                    <button
                        type="button"
                        @click="loginInput = 'pengawas'; passwordInput = 'Pengawas123!'"
                        class="p-2.5 rounded-lg border border-[#E5E7EB] bg-slate-50 hover:bg-slate-100 text-[#252A34] text-left transition flex flex-col gap-0.5 cursor-pointer"
                    >
                        <div class="flex items-center gap-1.5 font-semibold text-xs text-[#252A34]">
                            <svg class="w-3.5 h-3.5 text-[#D97706]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Pengawas Pemilihan</span>
                        </div>
                        <span class="text-[11px] text-[#6B7280]">pengawas / Pengawas123!</span>
                    </button>

                    <button
                        type="button"
                        @click="loginInput = 'superadmin'; passwordInput = 'SuperAdmin123!'"
                        class="p-2.5 rounded-lg border border-[#E5E7EB] bg-slate-50 hover:bg-slate-100 text-[#252A34] text-left transition flex flex-col gap-0.5 cursor-pointer"
                    >
                        <div class="flex items-center gap-1.5 font-semibold text-xs text-[#252A34]">
                            <svg class="w-3.5 h-3.5 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            <span>Super Admin</span>
                        </div>
                        <span class="text-[11px] text-[#6B7280]">superadmin / SuperAdmin123!</span>
                    </button>
                </div>
            </div>

        </div>

        <p class="mt-4 text-center text-[11px] text-[#6B7280]">
            Hak Cipta &copy; {{ date('Y') }} Rukun Warga 01 Dusun Harmoni Indah. Seluruh hak cipta dilindungi.
        </p>
    </div>

</body>
</html>
