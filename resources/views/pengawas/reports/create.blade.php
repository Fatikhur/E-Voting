@extends('layouts.app', ['title' => 'Ajukan Laporan Temuan'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Ajukan Laporan Pengawasan</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Catat temuan, rekomendasi perbaikan, atau indikasi pelanggaran selama tahapan pemilihan.</p>
        </div>
        <a href="{{ route('pengawas.reports.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-[#6B7280] hover:text-[#252A34] transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-xs border border-[#E5E7EB] p-5 sm:p-6">
        <form action="{{ route('pengawas.reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="election_id" class="block text-xs font-semibold text-[#252A34] mb-1">Agenda Pemilihan Terkait *</label>
                    <select name="election_id" id="election_id" required class="w-full px-3 py-2 rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] text-xs sm:text-sm focus:outline-none focus:border-[#1877F2]">
                        <option value="">Pilih Pemilihan</option>
                        @foreach($elections as $el)
                            <option value="{{ $el->id }}" {{ (old('election_id') == $el->id || $selectedElectionId == $el->id) ? 'selected' : '' }}>
                                {{ $el->title }} ({{ $el->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="report_type" class="block text-xs font-semibold text-[#252A34] mb-1">Kategori Laporan *</label>
                    <select name="report_type" id="report_type" required class="w-full px-3 py-2 rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] text-xs sm:text-sm focus:outline-none focus:border-[#1877F2]">
                        <option value="catatan">Catatan Observasi Umum</option>
                        <option value="rekomendasi">Rekomendasi Panitia</option>
                        <option value="klarifikasi">Permintaan Klarifikasi</option>
                        <option value="indikasi_pelanggaran">Indikasi Pelanggaran Prosedur</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="title" class="block text-xs font-semibold text-[#252A34] mb-1">Judul Ringkas Temuan *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Contoh: Selisih data absensi fisik dengan DPT..." class="w-full px-3 py-2 rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] text-xs sm:text-sm placeholder-[#6B7280] focus:outline-none focus:border-[#1877F2]">
            </div>

            <div>
                <label for="content" class="block text-xs font-semibold text-[#252A34] mb-1">Uraian Detail Temuan / Kronologi *</label>
                <textarea name="content" id="content" rows="5" required placeholder="Jelaskan fakta, waktu, pihak terkait, dan bukti yang ditemukan secara obyektif..." class="w-full px-3 py-2 rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] text-xs sm:text-sm placeholder-[#6B7280] focus:outline-none focus:border-[#1877F2]">{{ old('content') }}</textarea>
            </div>

            <div>
                <label for="evidence" class="block text-xs font-semibold text-[#252A34] mb-1">Bukti Lampiran Pendukung (Foto / Dokumen PDF)</label>
                <input type="file" name="evidence" id="evidence" accept=".pdf,image/jpeg,image/png" class="w-full text-xs text-[#6B7280] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                <p class="text-[11px] text-[#6B7280] mt-1">Maksimal 4MB (PDF, JPG, PNG).</p>
            </div>

            <div class="pt-4 border-t border-[#E5E7EB] flex items-center justify-end gap-2.5">
                <a href="{{ route('pengawas.reports.index') }}" class="px-4 py-2 rounded-lg text-xs sm:text-sm font-medium text-[#252A34] hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold text-white bg-amber-600 hover:bg-amber-700 shadow-xs transition">
                    Kirim Laporan Pengawasan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
