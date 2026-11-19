@extends('layouts.app', ['title' => 'Perbarui Kata Sandi'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-[#252A34] tracking-tight">Perbarui Kata Sandi</h1>
            <p class="text-xs text-[#6B7280] mt-0.5">Pastikan akun Anda menggunakan kata sandi yang aman dan tidak mudah ditebak.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-[#6B7280] hover:text-[#252A34] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    <!-- Password Card -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs p-6">
        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="current_password" class="block text-xs font-semibold text-[#252A34] mb-1.5">
                    Kata Sandi Saat Ini <span class="text-red-500">*</span>
                </label>
                <input
                    type="password"
                    name="current_password"
                    id="current_password"
                    required
                    class="w-full px-3 py-2 text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                >
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-[#252A34] mb-1.5">
                    Kata Sandi Baru <span class="text-red-500">*</span>
                </label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    class="w-full px-3 py-2 text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                >
                <p class="text-[11px] text-[#6B7280] mt-1">Minimal 8 karakter, kombinasi huruf dan angka.</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-[#252A34] mb-1.5">
                    Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span>
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    required
                    class="w-full px-3 py-2 text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                >
            </div>

            <div class="pt-4 border-t border-[#E5E7EB] flex items-center justify-end gap-2.5">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 text-xs font-medium rounded-lg text-[#6B7280] hover:text-[#252A34] hover:bg-slate-100 transition">
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-4 py-2 text-xs font-semibold rounded-lg bg-[#1877F2] text-white hover:bg-[#166fe5] shadow-xs transition"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
