@extends('layouts.app', ['title' => 'Dashboard Super Administrator'])

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Dashboard Super Administrator</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Pemantauan kesehatan sistem, integritas audit log kriptografis, dan konfigurasi wilayah.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('superadmin.backup.download') }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-[#252A34] px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold border border-[#E5E7EB] shadow-xs transition">
                <svg class="w-4 h-4 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <span>Unduh Backup JSON</span>
            </a>
            <form action="{{ route('superadmin.audit.verify') }}" method="POST" class="inline">
                @csrf
                <button type="button"
                        data-confirm
                        data-confirm-type="info"
                        data-confirm-title="Pindai Integritas Audit Trail"
                        data-confirm-message="Mulai pemindaian dan verifikasi seluruh rantai hash audit log?"
                        data-confirm-detail="Sistem akan memeriksa integritas kriptografis SHA-256 baris per baris untuk mendeteksi apabila ada data yang dimanipulasi langsung di database."
                        data-confirm-button="Mulai Pemindaian"
                        data-confirm-cancel="Batal"
                        class="inline-flex items-center gap-2 bg-[#1877F2] hover:bg-[#166fe5] text-white px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold shadow-xs transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1877F2]">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    <span>Pindai Integritas Audit</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Integrity Status Banner -->
    <div class="p-4 rounded-xl border {{ $chainIntegrity['is_valid'] ? 'bg-emerald-50/70 border-emerald-200 text-emerald-950' : 'bg-red-50/70 border-red-200 text-red-950' }} shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg {{ $chainIntegrity['is_valid'] ? 'bg-[#269B68] text-white' : 'bg-[#DC2626] text-white' }} flex items-center justify-center shrink-0">
                @if($chainIntegrity['is_valid'])
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                @else
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                @endif
            </div>
            <div>
                <h2 class="font-semibold text-sm sm:text-base {{ $chainIntegrity['is_valid'] ? 'text-emerald-950' : 'text-red-950' }}">
                    Status Integritas Audit Trail: {{ $chainIntegrity['is_valid'] ? 'AMAN & VALID' : 'PERINGATAN: ANOMALI TERDETEKSI' }}
                </h2>
                <p class="text-xs sm:text-sm {{ $chainIntegrity['is_valid'] ? 'text-emerald-800' : 'text-red-800' }} mt-0.5">
                    {{ $chainIntegrity['message'] }} (Total {{ $chainIntegrity['total_verified'] ?? 0 }} log terverifikasi berantai SHA-256).
                </p>
            </div>
        </div>
        <div class="text-xs font-mono bg-white px-3 py-1.5 rounded-lg border border-[#E5E7EB] text-[#252A34]">
            Hash Terakhir: {{ substr($chainIntegrity['latest_hash'] ?? '0000000000000000', 0, 16) }}...
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">Total Warga</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-bold text-[#252A34]">{{ number_format($stats['total_residents']) }}</span>
                <svg class="w-5 h-5 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
            <p class="text-xs text-[#6B7280] mt-2">Terdaftar di basis data</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">DPT Pemilihan</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-bold text-[#252A34]">{{ number_format($stats['total_voters_registered']) }}</span>
                <svg class="w-5 h-5 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                </svg>
            </div>
            <p class="text-xs text-[#6B7280] mt-2">Entri pemilih terverifikasi</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">Pemilihan Aktif</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-bold text-[#1877F2]">{{ number_format($stats['active_elections']) }}</span>
                <span class="text-xs px-2 py-0.5 rounded-md bg-blue-50 text-[#1877F2] font-semibold border border-blue-200">Sedang Buka</span>
            </div>
            <p class="text-xs text-[#6B7280] mt-2">Dari {{ $stats['total_elections'] }} total pemilihan</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">Total Audit Log</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-bold text-[#252A34]">{{ number_format($stats['total_audit_logs']) }}</span>
                <svg class="w-5 h-5 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                </svg>
            </div>
            <p class="text-xs text-[#6B7280] mt-2">Rantai hash anti-tampering</p>
        </div>
    </div>

    <!-- Two Columns: Recent Elections & Recent Audit Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Elections -->
        <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden flex flex-col">
            <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E5E7EB] flex items-center justify-between">
                <h3 class="font-semibold text-[#252A34] text-sm sm:text-base">Daftar Pemilihan Terkini</h3>
                <a href="{{ route('admin.elections.index') }}" class="text-xs font-semibold text-[#1877F2] hover:underline">Lihat Semua</a>
            </div>
            <div class="divide-y divide-[#E5E7EB] flex-1">
                @forelse($recentElections as $election)
                    <div class="p-4 hover:bg-slate-50/70 transition-colors flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase border {{ $election->status_badge['class'] }}">
                                    {{ $election->status_badge['text'] }}
                                </span>
                                <span class="text-xs text-[#6B7280]">{{ $election->code }}</span>
                            </div>
                            <h4 class="font-semibold text-[#252A34] text-sm mt-1">{{ $election->title }}</h4>
                            <p class="text-xs text-[#6B7280] mt-0.5">{{ $election->scope_display }} • Periode {{ $election->term_period }}</p>
                        </div>
                        <a href="{{ route('admin.elections.show', $election) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-[#252A34] text-xs font-medium transition">
                            Buka
                        </a>
                    </div>
                @empty
                    <div class="p-8 text-center text-[#6B7280] text-xs sm:text-sm">Belum ada pemilihan yang dibuat.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Audit Logs -->
        <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden flex flex-col">
            <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E5E7EB] flex items-center justify-between">
                <h3 class="font-semibold text-[#252A34] text-sm sm:text-base">Jejak Audit Aktivitas Sistem</h3>
                <a href="{{ route('superadmin.audit.index') }}" class="text-xs font-semibold text-[#1877F2] hover:underline">Detail Audit</a>
            </div>
            <div class="divide-y divide-[#E5E7EB] flex-1 overflow-y-auto max-h-[380px]">
                @forelse($recentLogs as $log)
                    <div class="p-3.5 hover:bg-slate-50/70 transition-colors text-xs">
                        <div class="flex items-center justify-between text-[#6B7280] mb-1">
                            <span class="font-semibold text-[#252A34]">@ {{ $log->username }} ({{ ucfirst($log->role) }})</span>
                            <span>{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[#252A34] font-medium leading-relaxed">{{ $log->description }}</p>
                        <div class="mt-1 flex items-center justify-between text-[10px] text-[#6B7280] font-mono">
                            <span>Aksi: {{ $log->action }}</span>
                            <span>Hash: {{ substr($log->current_hash, 0, 12) }}...</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-[#6B7280] text-xs sm:text-sm">Belum ada catatan audit.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
