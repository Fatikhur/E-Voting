@extends('layouts.app', ['title' => 'Ajukan Laporan Temuan'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Ajukan Laporan Pengawasan</h1>
            <p class="text-sm text-slate-500 mt-1">Catat temuan, rekomendasi perbaikan, atau indikasi pelanggaran selama tahapan pemilihan.</p>
        </div>
        <a href="{{ route('pengawas.reports.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            ← Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('pengawas.reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="election_id" class="block text-sm font-semibold text-slate-700 mb-1">Agenda Pemilihan Terkait *</label>
                    <select name="election_id" id="election_id" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                        <option value="">Pilih Pemilihan</option>
                        @foreach($elections as $el)
                            <option value="{{ $el->id }}" {{ (old('election_id') == $el->id || $selectedElectionId == $el->id) ? 'selected' : '' }}>
                                {{ $el->title }} ({{ $el->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="report_type" class="block text-sm font-semibold text-slate-700 mb-1">Kategori Laporan *</label>
                    <select name="report_type" id="report_type" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                        <option value="catatan">Catatan Observasi Umum</option>
                        <option value="rekomendasi">Rekomendasi Panitia</option>
                        <option value="klarifikasi">Permintaan Klarifikasi</option>
                        <option value="indikasi_pelanggaran">Indikasi Pelanggaran Prosedur</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">Judul Ringkas Temuan *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Contoh: Selisih data absensi fisik dengan DPT..." class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
            </div>

            <div>
                <label for="content" class="block text-sm font-semibold text-slate-700 mb-1">Uraian Detail Temuan / Kronologi *</label>
                <textarea name="content" id="content" rows="5" required placeholder="Jelaskan fakta, waktu, pihak terkait, dan bukti yang ditemukan secara obyektif..." class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">{{ old('content') }}</textarea>
            </div>

            <div>
                <label for="evidence" class="block text-sm font-semibold text-slate-700 mb-1">Bukti Lampiran Pendukung (Foto / Dokumen PDF)</label>
                <input type="file" name="evidence" id="evidence" accept=".pdf,image/jpeg,image/png" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                <p class="text-[11px] text-slate-400 mt-1">Maksimal 4MB (PDF, JPG, PNG).</p>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('pengawas.reports.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 shadow-sm transition">
                    Kirim Laporan Pengawasan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
