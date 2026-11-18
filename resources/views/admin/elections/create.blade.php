@extends('layouts.app', ['title' => 'Buat Pemilihan Baru'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="{ electionType: '{{ old('type', 'ketua_rw') }}' }">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Buat Pemilihan Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Konfigurasikan jadwal, wilayah, dan aturan pemilihan Ketua RT atau Ketua RW.</p>
        </div>
        <a href="{{ route('admin.elections.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            ← Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('admin.elections.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">Nama / Judul Pemilihan *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Contoh: Pemilihan Ketua RW 01 Dusun Harmoni Periode 2026-2031" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="type" class="block text-sm font-semibold text-slate-700 mb-1">Tingkat Pemilihan *</label>
                    <select name="type" id="type" x-model="electionType" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                        <option value="ketua_rw">Pemilihan Ketua RW</option>
                        <option value="ketua_rt">Pemilihan Ketua RT</option>
                    </select>
                </div>

                <div>
                    <label for="term_period" class="block text-sm font-semibold text-slate-700 mb-1">Periode Masa Jabatan *</label>
                    <input type="text" name="term_period" id="term_period" value="{{ old('term_period', '2026 - 2031') }}" required placeholder="Contoh: 2026 - 2031" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="village_id" class="block text-sm font-semibold text-slate-700 mb-1">Kelurahan *</label>
                    <select name="village_id" id="village_id" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                        @foreach($villages as $vil)
                            <option value="{{ $vil->id }}" {{ old('village_id') == $vil->id ? 'selected' : '' }}>{{ $vil->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="rw_id" class="block text-sm font-semibold text-slate-700 mb-1">Wilayah RW *</label>
                    <select name="rw_id" id="rw_id" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                        @foreach($rws as $rw)
                            <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>RW {{ $rw->rw_number }} ({{ $rw->name ?? 'Dusun' }})</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="electionType === 'ketua_rt'">
                    <label for="rt_id" class="block text-sm font-semibold text-slate-700 mb-1">Wilayah RT *</label>
                    <select name="rt_id" id="rt_id" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                        <option value="">Pilih RT</option>
                        @foreach($rts as $rt)
                            <option value="{{ $rt->id }}" {{ old('rt_id') == $rt->id ? 'selected' : '' }}>RT {{ $rt->rt_number }} (RW {{ $rt->rw?->rw_number }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-3 border-t border-slate-100">
                <div>
                    <label for="start_time" class="block text-sm font-semibold text-slate-700 mb-1">Waktu Mulai Pemungutan Suara *</label>
                    <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time') }}" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-semibold text-slate-700 mb-1">Waktu Selesai Pemungutan Suara *</label>
                    <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                </div>
            </div>

            <div>
                <label for="tie_breaker_rule" class="block text-sm font-semibold text-slate-700 mb-1">Aturan Tertulis Jika Perolehan Suara Seri (Tie-Breaker Rule)</label>
                <textarea name="tie_breaker_rule" id="tie_breaker_rule" rows="2" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm" placeholder="Contoh: Musyawarah mufakat panitia pengawas bersama para kandidat atau pemungutan suara putaran kedua.">{{ old('tie_breaker_rule', 'Musyawarah mufakat panitia pengawas bersama para kandidat atau pemungutan suara putaran kedua.') }}</textarea>
                <p class="text-xs text-slate-500 mt-1">Sesuai pedoman etika pemilihan, sistem tidak memilih pemenang otomatis jika terjadi suara seri.</p>
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi Tambahan (Opsional)</label>
                <textarea name="description" id="description" rows="2" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm" placeholder="Keterangan mengenai tata cara dan informasi pemilihan...">{{ old('description') }}</textarea>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.elections.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                    Simpan & Buat Pemilihan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
