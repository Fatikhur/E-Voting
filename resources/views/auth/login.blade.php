<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - E-Voting RT/RW</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-b from-blue-950 via-slate-900 to-slate-900 text-slate-800">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
        <!-- Logo -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-blue-600 text-white text-4xl shadow-xl shadow-blue-900/50 mb-4 border border-blue-400/30">
            🗳️
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">E-Voting RT/RW</h1>
        <p class="mt-2 text-sm sm:text-base text-blue-200">
            Sistem Digital Pemilihan Ketua RT dan Ketua RW
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="bg-white py-8 px-6 sm:px-10 shadow-2xl rounded-2xl border border-slate-200 sm:rounded-2xl" x-data="{ showPassword: false, loginInput: '{{ old('login', '') }}', passwordInput: '' }">

            <!-- Flash Error / Success -->
            @if(session('error'))
                <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-3.5 rounded-r text-sm text-red-800 font-medium flex items-center gap-2">
                    <span class="text-lg">⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 bg-blue-50 border-l-4 border-blue-500 p-3.5 rounded-r text-sm text-blue-800 font-medium flex items-center gap-2">
                    <span class="text-lg">ℹ️</span>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 bg-emerald-50 border-l-4 border-emerald-500 p-3.5 rounded-r text-sm text-emerald-800 font-medium flex items-center gap-2">
                    <span class="text-lg">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Username or NIK -->
                <div>
                    <label for="login" class="block text-sm sm:text-base font-bold text-slate-700 mb-1.5">
                        Username atau NIK (16 Digit)
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            name="login"
                            id="login"
                            x-model="loginInput"
                            required
                            autofocus
                            placeholder="Contoh: pemilih atau 327601..."
                            class="w-full px-4 py-3 text-base rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition shadow-sm bg-slate-50 focus:bg-white"
                        >
                    </div>
                    <p class="mt-1.5 text-xs text-slate-500">Warga dapat masuk menggunakan nama pengguna atau 16 digit NIK terdaftar.</p>
                </div>

                <!-- Password with Show/Hide toggle -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm sm:text-base font-bold text-slate-700">
                            Kata Sandi
                        </label>
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 font-semibold focus:outline-none"
                            tabindex="-1"
                        >
                            <span x-show="!showPassword">👁️ Tampilkan Sandi</span>
                            <span x-show="showPassword" style="display: none;">🔒 Sembunyikan</span>
                        </button>
                    </div>
                    <div class="relative">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            id="password"
                            x-model="passwordInput"
                            required
                            placeholder="Masukkan kata sandi akun Anda"
                            class="w-full px-4 py-3 text-base rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition shadow-sm bg-slate-50 focus:bg-white"
                        >
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                        <span class="text-sm text-slate-600 select-none">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button
                        type="submit"
                        class="w-full py-3.5 px-4 text-base sm:text-lg font-bold rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/40 shadow-lg shadow-blue-600/30 transition transform active:scale-[0.99]"
                    >
                        Masuk ke Sistem Pemilihan ➔
                    </button>
                </div>
            </form>

            <!-- Demo Accounts Quick Clickers (Developer & Tester Convenience) -->
            <div class="mt-8 pt-6 border-t border-slate-200">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider text-center mb-3">
                    🧪 Akun Percobaan Demo (Klik untuk Isi Cepat)
                </p>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <button
                        type="button"
                        @click="loginInput = 'pemilih'; passwordInput = 'Pemilih123!'"
                        class="p-2.5 rounded-lg border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-900 font-semibold text-left transition flex flex-col"
                    >
                        <span>🗳️ Pemilih / Warga</span>
                        <span class="text-[10px] text-blue-600 font-normal">pemilih / Pemilih123!</span>
                    </button>

                    <button
                        type="button"
                        @click="loginInput = 'admin'; passwordInput = 'Admin123!'"
                        class="p-2.5 rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-900 font-semibold text-left transition flex flex-col"
                    >
                        <span>📋 Admin Panitia</span>
                        <span class="text-[10px] text-emerald-600 font-normal">admin / Admin123!</span>
                    </button>

                    <button
                        type="button"
                        @click="loginInput = 'pengawas'; passwordInput = 'Pengawas123!'"
                        class="p-2.5 rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-900 font-semibold text-left transition flex flex-col"
                    >
                        <span>🔍 Pengawas Pemilihan</span>
                        <span class="text-[10px] text-amber-600 font-normal">pengawas / Pengawas123!</span>
                    </button>

                    <button
                        type="button"
                        @click="loginInput = 'superadmin'; passwordInput = 'SuperAdmin123!'"
                        class="p-2.5 rounded-lg border border-purple-200 bg-purple-50 hover:bg-purple-100 text-purple-900 font-semibold text-left transition flex flex-col"
                    >
                        <span>⚡ Super Admin</span>
                        <span class="text-[10px] text-purple-600 font-normal">superadmin / SuperAdmin123!</span>
                    </button>
                </div>
            </div>

        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            Hak Cipta &copy; {{ date('Y') }} Rukun Warga 01 Dusun Harmoni Indah. Seluruh hak cipta dilindungi.
        </p>
    </div>

</body>
</html>
