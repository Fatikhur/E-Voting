@extends('layouts.app', ['title' => 'Dashboard Pengawas Pemilihan'])

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Dashboard Pengawas Independen</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Pengawasan independen jalannya pemungutan suara digital, audit log, dan kepatuhan prosedur.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('pengawas.reports.create') }}" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold shadow-xs transition">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
                <span>Ajukan Laporan Temuan</span>
            </a>
            <a href="{{ route('pengawas.audit.index') }}" class="inline-flex items-center gap-2 bg-[#171B35] hover:bg-[#13162C] text-white px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold shadow-xs transition">
                <svg class="w-4 h-4 text-[#C5C9D4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                <span>Periksa Rantai Audit</span>
            </a>
        </div>
    </div>

    <!-- Audit Integrity Banner -->
    <div class="p-4 rounded-xl border {{ $integrity['is_valid'] ? 'bg-emerald-50/70 border-emerald-200 text-emerald-950' : 'bg-red-50/70 border-red-200 text-red-950' }} shadow-xs flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg {{ $integrity['is_valid'] ? 'bg-[#269B68] text-white' : 'bg-[#DC2626] text-white' }} flex items-center justify-center shrink-0">
                @if($integrity['is_valid'])
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                @else
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                @endif
            </div>
            <div>
                <h2 class="font-semibold text-sm sm:text-base {{ $integrity['is_valid'] ? 'text-emerald-950' : 'text-red-950' }}">
                    Status Integritas Audit Trail: {{ $integrity['is_valid'] ? 'VALID & AMAN' : 'PERINGATAN MANIPULASI' }}
                </h2>
                <p class="text-xs sm:text-sm {{ $integrity['is_valid'] ? 'text-emerald-800' : 'text-red-800' }} mt-0.5">
                    {{ $integrity['message'] }}
                </p>
            </div>
        </div>
        <a href="{{ route('pengawas.audit.index') }}" class="text-xs font-semibold text-[#1877F2] hover:underline shrink-0">Buka Rantai</a>
    </div>

    <!-- Pending Approvals Alert (Two-person rule) -->
    @if($pendingApprovals->isNotEmpty())
        <div class="bg-amber-50/80 rounded-xl border border-amber-200 p-4 sm:p-5 shadow-xs space-y-3">
            <div class="flex items-center gap-2 text-amber-900 font-semibold text-xs sm:text-sm">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                <span>Terdapat {{ $pendingApprovals->count() }} pemilihan ditutup yang membutuhkan tanda tangan persetujuan pengawas:</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($pendingApprovals as $ap)
                    <div class="bg-white p-3.5 rounded-lg border border-amber-200 flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-[#252A34] text-xs sm:text-sm">{{ $ap->election->title }}</h4>
                            <p class="text-[11px] text-[#6B7280]">Status Panitia Admin: {{ $ap->admin_approved ? 'Disetujui' : 'Belum Disetujui' }}</p>
                        </div>
                        <a href="{{ route('pengawas.monitoring.show', $ap->election) }}" class="px-3 py-1.5 text-xs font-semibold bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition">
                            Periksa & Setujui
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Monitoring Elections List -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden">
        <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E5E7EB] flex items-center justify-between">
            <h2 class="text-sm sm:text-base font-semibold text-[#252A34]">Pemantauan Pemilihan Aktif & Terjadwal</h2>
            <span class="text-xs text-[#6B7280] font-medium">Pemantauan waktu nyata</span>
        </div>

        <div class="divide-y divide-[#E5E7EB]">
            @forelse($elections as $el)
                <div class="p-4 sm:p-5 hover:bg-slate-50/70 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase border {{ $el->status_badge['class'] }}">
                                {{ $el->status_badge['text'] }}
                            </span>
                            <span class="text-xs font-mono font-medium text-[#6B7280]">{{ $el->code }}</span>
                            <span class="text-xs text-[#6B7280]">• {{ $el->scope_display }}</span>
                        </div>
                        <h3 class="font-semibold text-[#252A34] text-sm sm:text-base">
                            <a href="{{ route('pengawas.monitoring.show', $el) }}" class="hover:text-[#1877F2] transition-colors">{{ $el->title }}</a>
                        </h3>
                        <p class="text-xs text-[#6B7280]">
                            Jadwal: {{ $el->start_time->format('d/m/Y H:i') }} s/d {{ $el->end_time->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="text-right text-xs">
                            <span class="block text-[#6B7280]">Total Pemilih</span>
                            <span class="font-bold text-[#252A34] text-sm">{{ $el->eligibleVoters()->count() }} Warga</span>
                        </div>
                        <a href="{{ route('pengawas.monitoring.show', $el) }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-[#1877F2] hover:bg-[#166fe5] text-white shadow-xs transition">
                            Pantau Real-Time
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-[#6B7280] text-xs sm:text-sm">Belum ada pemilihan yang terdaftar.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
