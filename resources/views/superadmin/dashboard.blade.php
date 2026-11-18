@extends('layouts.app', ['title' => 'Dashboard Super Administrator'])

@section('content')
<div class="space-y-8">

    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Super Administrator</h1>
            <p class="text-sm sm:text-base text-slate-500 mt-1">Pemantauan kesehatan sistem, integritas audit log kriptografis, dan konfigurasi wilayah.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('superadmin.backup.download') }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 px-4 py-2 rounded-xl text-sm font-semibold border border-slate-300 shadow-sm transition">
                <span>💾 Unduh Backup JSON</span>
            </a>
            <form action="{{ route('superadmin.audit.verify') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-sm transition">
                    <span>🛡️ Pindai Integritas Audit</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Integrity Status Banner -->
    <div class="p-4 rounded-2xl border {{ $chainIntegrity['is_valid'] ? 'bg-emerald-50 border-emerald-300 text-emerald-950' : 'bg-red-50 border-red-300 text-red-950' }} shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl {{ $chainIntegrity['is_valid'] ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white' }} flex items-center justify-center font-bold text-xl shrink-0">
                {{ $chainIntegrity['is_valid'] ? '✓' : '✗' }}
            </div>
            <div>
                <h2 class="font-bold text-base {{ $chainIntegrity['is_valid'] ? 'text-emerald-900' : 'text-red-900' }}">
                    Status Integritas Audit Trail: {{ $chainIntegrity['is_valid'] ? 'AMAN & VALID' : 'PERINGATAN: ANOMALI TERDETEKSI' }}
                </h2>
                <p class="text-xs sm:text-sm {{ $chainIntegrity['is_valid'] ? 'text-emerald-700' : 'text-red-700' }} mt-0.5">
                    {{ $chainIntegrity['message'] }} (Total {{ $chainIntegrity['total_verified'] }} log terverifikasi berantai SHA-256).
                </p>
            </div>
        </div>
        <div class="text-xs font-mono bg-white/70 px-3 py-1.5 rounded-lg border border-slate-200">
            Hash Terakhir: {{ substr($chainIntegrity['latest_hash'], 0, 16) }}...
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Warga</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-slate-800">{{ number_format($stats['total_residents']) }}</span>
                <span class="text-xl">👥</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Terdaftar di basis data</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">DPT Pemilihan</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-slate-800">{{ number_format($stats['total_voters_registered']) }}</span>
                <span class="text-xl">🗳️</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Entri pemilih terverifikasi</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Pemilihan Aktif</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-blue-600">{{ number_format($stats['active_elections']) }}</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-bold">Sedang Buka</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Dari {{ $stats['total_elections'] }} total pemilihan</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Audit Log</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-slate-800">{{ number_format($stats['total_audit_logs']) }}</span>
                <span class="text-xl">🔗</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Rantai hash anti-tampering</p>
        </div>
    </div>

    <!-- Two Columns: Recent Elections & Recent Audit Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Elections -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-base">Daftar Pemilihan Terkini</h3>
                <a href="{{ route('admin.elections.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">Lihat Semua ➔</a>
            </div>
            <div class="divide-y divide-slate-100 flex-1">
                @forelse($recentElections as $election)
                    <div class="p-4 hover:bg-slate-50 transition flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase border {{ $election->status_badge['class'] }}">
                                    {{ $election->status_badge['text'] }}
                                </span>
                                <span class="text-xs text-slate-400">{{ $election->code }}</span>
                            </div>
                            <h4 class="font-bold text-slate-800 text-sm mt-1">{{ $election->title }}</h4>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $election->scope_display }} • Periode {{ $election->term_period }}</p>
                        </div>
                        <a href="{{ route('admin.elections.show', $election) }}" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold">
                            Buka ➔
                        </a>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-sm">Belum ada pemilihan yang dibuat.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Audit Logs -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-base">Jejak Audit Aktivitas Sistem</h3>
                <a href="{{ route('superadmin.audit.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">Detail Audit ➔</a>
            </div>
            <div class="divide-y divide-slate-100 flex-1 overflow-y-auto max-h-[380px]">
                @forelse($recentLogs as $log)
                    <div class="p-3.5 hover:bg-slate-50 transition text-xs">
                        <div class="flex items-center justify-between text-slate-400 mb-1">
                            <span class="font-semibold text-slate-700">@ {{ $log->username }} ({{ ucfirst($log->role) }})</span>
                            <span>{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-slate-800 font-medium leading-relaxed">{{ $log->description }}</p>
                        <div class="mt-1 flex items-center justify-between text-[10px] text-slate-400 font-mono">
                            <span>Aksi: {{ $log->action }}</span>
                            <span>Hash: {{ substr($log->current_hash, 0, 12) }}...</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-sm">Belum ada catatan audit.</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
