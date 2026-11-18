@extends('layouts.app', ['title' => 'Dashboard Pengawas Pemilihan'])

@section('content')
<div class="space-y-8">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Pengawas Independen</h1>
            <p class="text-sm sm:text-base text-slate-500 mt-1">Pengawasan independen jalannya pemungutan suara digital, audit log, dan kepatuhan prosedur.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pengawas.reports.create') }}" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm transition">
                <span>📝 Ajukan Laporan Temuan</span>
            </a>
            <a href="{{ route('pengawas.audit.index') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm transition">
                <span>🛡️ Periksa Rantai Audit</span>
            </a>
        </div>
    </div>

    <!-- Audit Integrity Banner -->
    <div class="p-4 rounded-2xl border {{ $integrity['is_valid'] ? 'bg-emerald-50 border-emerald-300 text-emerald-950' : 'bg-red-50 border-red-300 text-red-950' }} shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-2xl">{{ $integrity['is_valid'] ? '🛡️' : '🚨' }}</span>
            <div>
                <h3 class="font-bold text-sm sm:text-base {{ $integrity['is_valid'] ? 'text-emerald-900' : 'text-red-900' }}">
                    Status Integritas Audit Trail: {{ $integrity['is_valid'] ? 'VALID & AMAN' : 'PERINGATAN MANIPULASI' }}
                </h3>
                <p class="text-xs {{ $integrity['is_valid'] ? 'text-emerald-700' : 'text-red-700' }} mt-0.5">
                    {{ $integrity['message'] }}
                </p>
            </div>
        </div>
        <a href="{{ route('pengawas.audit.index') }}" class="text-xs font-bold text-blue-700 hover:underline shrink-0">Buka Rantai ➔</a>
    </div>

    <!-- Pending Approvals Alert (Two-person rule) -->
    @if($pendingApprovals->isNotEmpty())
        <div class="bg-amber-50 rounded-2xl border border-amber-300 p-5 shadow-sm space-y-3">
            <div class="flex items-center gap-2 text-amber-900 font-bold text-sm">
                <span>⚠️</span>
                <span>Terdapat {{ $pendingApprovals->count() }} pemilihan ditutup yang membutuhkan tanda tangan persetujuan pengawas:</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($pendingApprovals as $ap)
                    <div class="bg-white p-3.5 rounded-xl border border-amber-200 flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-slate-800 text-xs">{{ $ap->election->title }}</h4>
                            <p class="text-[11px] text-slate-400">Status Panitia Admin: {{ $ap->admin_approved ? '✓ Disetujui' : '⏳ Belum' }}</p>
                        </div>
                        <a href="{{ route('pengawas.monitoring.show', $ap->election) }}" class="px-3 py-1.5 text-xs font-bold bg-amber-600 text-white rounded-lg hover:bg-amber-700">
                            Periksa & Setujui ➔
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Monitoring Elections List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Pemantauan Pemilihan Aktif & Terjadwal</h2>
            <span class="text-xs text-slate-400 font-medium">Pemantauan waktu nyata</span>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($elections as $el)
                <div class="p-5 hover:bg-slate-50 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md text-xs font-bold uppercase border {{ $el->status_badge['class'] }}">
                                {{ $el->status_badge['text'] }}
                            </span>
                            <span class="text-xs font-mono font-bold text-slate-400">{{ $el->code }}</span>
                            <span class="text-xs text-slate-400">• {{ $el->scope_display }}</span>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">
                            <a href="{{ route('pengawas.monitoring.show', $el) }}" class="hover:text-blue-600 transition">{{ $el->title }}</a>
                        </h3>
                        <p class="text-xs text-slate-500">
                            Jadwal: {{ $el->start_time->format('d/m/Y H:i') }} s/d {{ $el->end_time->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="text-right text-xs">
                            <span class="block text-slate-400">Total Pemilih</span>
                            <span class="font-extrabold text-slate-800 text-sm">{{ $el->eligibleVoters()->count() }} Warga</span>
                        </div>
                        <a href="{{ route('pengawas.monitoring.show', $el) }}" class="px-4 py-2 text-xs font-bold rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
                            Pantau Real-Time ➔
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-sm">Belum ada pemilihan yang terdaftar.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
