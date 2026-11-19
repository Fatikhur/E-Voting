@extends('layouts.app', ['title' => 'Edit Konfigurasi Pemilihan'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Edit Konfigurasi Pemilihan</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">{{ $election->title }} ({{ $election->code }})</p>
        </div>
        <a href="{{ route('admin.elections.show', $election) }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-[#6B7280] hover:text-[#252A34] transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-xs border border-[#E5E7EB] p-5 sm:p-6">
        <form action="{{ route('admin.elections.update', $election) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-xs font-semibold text-[#252A34] mb-1">Nama / Judul Pemilihan *</label>
                <input type="text" name="title" id="title" value="{{ old('title', $election->title) }}" required class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#252A34] mb-1">Tingkat & Cakupan Wilayah</label>
                    <input type="text" disabled value="{{ $election->scope_display }}" class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-slate-50 text-[#6B7280] cursor-not-allowed">
                </div>

                <div>
                    <label for="term_period" class="block text-xs font-semibold text-[#252A34] mb-1">Periode Masa Jabatan *</label>
                    <input type="text" name="term_period" id="term_period" value="{{ old('term_period', $election->term_period) }}" required class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-[#E5E7EB]">
                <div>
                    <label for="start_time" class="block text-xs font-semibold text-[#252A34] mb-1">Waktu Mulai Pemungutan Suara *</label>
                    <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time', $election->start_time->format('Y-m-d\TH:i')) }}" required class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                </div>

                <div>
                    <label for="end_time" class="block text-xs font-semibold text-[#252A34] mb-1">Waktu Selesai Pemungutan Suara *</label>
                    <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time', $election->end_time->format('Y-m-d\TH:i')) }}" required class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                </div>
            </div>

            <div>
                <label for="tie_breaker_rule" class="block text-xs font-semibold text-[#252A34] mb-1">Aturan Suara Seri (Tie-Breaker Rule)</label>
                <textarea name="tie_breaker_rule" id="tie_breaker_rule" rows="2" class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">{{ old('tie_breaker_rule', $election->tie_breaker_rule) }}</textarea>
            </div>

            <div>
                <label for="description" class="block text-xs font-semibold text-[#252A34] mb-1">Deskripsi Tambahan</label>
                <textarea name="description" id="description" rows="2" class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">{{ old('description', $election->description) }}</textarea>
            </div>

            <div class="pt-4 border-t border-[#E5E7EB] flex items-center justify-end gap-2.5">
                <a href="{{ route('admin.elections.show', $election) }}" class="px-4 py-2 rounded-lg text-xs sm:text-sm font-medium text-[#252A34] hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold text-white bg-[#1877F2] hover:bg-[#166fe5] shadow-xs transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
