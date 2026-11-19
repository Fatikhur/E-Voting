@extends('layouts.app', ['title' => 'Detail Laporan Temuan'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Detail Laporan Pengawasan</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Diajukan pada {{ $report->created_at->format('d M Y, H:i') }}</p>
        </div>
        <a href="{{ route('pengawas.reports.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-[#6B7280] hover:text-[#252A34] transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-xs border border-[#E5E7EB] p-5 sm:p-6 space-y-5">
        <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 rounded text-[11px] font-medium uppercase bg-amber-50 text-amber-700 border border-amber-200">
                {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
            </span>
            <span class="text-xs text-[#6B7280] font-mono">{{ $report->election->code }}</span>
        </div>

        <div>
            <h2 class="text-base sm:text-lg font-bold text-[#252A34]">{{ $report->title }}</h2>
            <p class="text-xs text-[#6B7280] mt-1">
                Agenda Pemilihan: <span class="font-semibold text-[#252A34]">{{ $report->election->title }}</span> • Pelapor: <span class="font-semibold text-[#252A34]">@ {{ $report->supervisor->username }}</span>
            </p>
        </div>

        <div class="p-4 rounded-lg bg-slate-50 border border-[#E5E7EB] text-[#252A34] text-xs sm:text-sm whitespace-pre-line leading-relaxed">
            {{ $report->content }}
        </div>

        @if($report->evidence_path)
            <div class="pt-4 border-t border-[#E5E7EB]">
                <span class="text-xs font-semibold text-[#252A34] block mb-2">Lampiran Bukti Pengawasan:</span>
                <a href="{{ Storage::disk('public')->url($report->evidence_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-blue-200 bg-blue-50 text-[#1877F2] text-xs font-semibold hover:bg-blue-100 transition">
                    <svg class="w-3.5 h-3.5 text-[#1877F2]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                    </svg>
                    <span>Buka Lampiran Dokumen</span>
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
