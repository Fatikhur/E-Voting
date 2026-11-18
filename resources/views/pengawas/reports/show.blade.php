@extends('layouts.app', ['title' => 'Detail Laporan Temuan'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Detail Laporan Pengawasan</h1>
            <p class="text-sm text-slate-500 mt-1">Diajukan pada {{ $report->created_at->format('d M Y, H:i') }}</p>
        </div>
        <a href="{{ route('pengawas.reports.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            ← Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 space-y-6">
        <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase bg-amber-100 text-amber-800 border border-amber-200">
                {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
            </span>
            <span class="text-xs text-slate-400 font-mono">{{ $report->election->code }}</span>
        </div>

        <div>
            <h2 class="text-xl font-bold text-slate-900">{{ $report->title }}</h2>
            <p class="text-xs text-slate-500 mt-1">
                Agenda Pemilihan: <span class="font-semibold text-slate-700">{{ $report->election->title }}</span> • Pelapor: <span class="font-semibold text-slate-700">@ {{ $report->supervisor->username }}</span>
            </p>
        </div>

        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-sm whitespace-pre-line leading-relaxed">
            {{ $report->content }}
        </div>

        @if($report->evidence_path)
            <div class="pt-4 border-t border-slate-100">
                <span class="text-xs font-bold text-slate-700 block mb-2">Lampiran Bukti Pengawasan:</span>
                <a href="{{ Storage::disk('public')->url($report->evidence_path) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-blue-200 bg-blue-50 text-blue-700 text-xs font-semibold hover:bg-blue-100">
                    <span>📎 Buka Lampiran Dokumen</span>
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
