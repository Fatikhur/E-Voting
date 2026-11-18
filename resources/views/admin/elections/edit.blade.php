@extends('layouts.app', ['title' => 'Edit Konfigurasi Pemilihan'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Konfigurasi Pemilihan</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $election->title }} ({{ $election->code }})</p>
        </div>
        <a href="{{ route('admin.elections.show', $election) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            ← Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('admin.elections.update', $election) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">Nama / Judul Pemilihan *</label>
                <input type="text" name="title" id="title" value="{{ old('title', $election->title) }}" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Tingkat & Cakupan Wilayah</label>
                    <input type="text" disabled value="{{ $election->scope_display }}" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-200 bg-slate-100 text-slate-500 text-sm cursor-not-allowed">
                </div>

                <div>
                    <label for="term_period" class="block text-sm font-semibold text-slate-700 mb-1">Periode Masa Jabatan *</label>
                    <input type="text" name="term_period" id="term_period" value="{{ old('term_period', $election->term_period) }}" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-3 border-t border-slate-100">
                <div>
                    <label for="start_time" class="block text-sm font-semibold text-slate-700 mb-1">Waktu Mulai Pemungutan Suara *</label>
                    <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time', $election->start_time->format('Y-m-d\TH:i')) }}" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-semibold text-slate-700 mb-1">Waktu Selesai Pemungutan Suara *</label>
                    <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time', $election->end_time->format('Y-m-d\TH:i')) }}" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                </div>
            </div>

            <div>
                <label for="tie_breaker_rule" class="block text-sm font-semibold text-slate-700 mb-1">Aturan Suara Seri (Tie-Breaker Rule)</label>
                <textarea name="tie_breaker_rule" id="tie_breaker_rule" rows="2" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">{{ old('tie_breaker_rule', $election->tie_breaker_rule) }}</textarea>
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi Tambahan</label>
                <textarea name="description" id="description" rows="2" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">{{ old('description', $election->description) }}</textarea>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.elections.show', $election) }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
