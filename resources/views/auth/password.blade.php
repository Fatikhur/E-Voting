@extends('layouts.app', ['title' => 'Perbarui Kata Sandi'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <h1 class="text-xl font-bold text-slate-800">Perbarui Kata Sandi</h1>
            <p class="text-sm text-slate-500 mt-1">Pastikan akun Anda menggunakan kata sandi yang aman dan tidak mudah ditebak.</p>
        </div>

        <form action="{{ route('password.update') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-1">
                    Kata Sandi Saat Ini
                </label>
                <input
                    type="password"
                    name="current_password"
                    id="current_password"
                    required
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">
                    Kata Sandi Baru
                </label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                <p class="text-xs text-slate-500 mt-1">Minimal 8 karakter, kombinasi huruf dan angka.</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1">
                    Konfirmasi Kata Sandi Baru
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    required
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="px-4 py-2.5 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
