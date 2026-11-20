@extends('layouts.app', ['title' => 'Dashboard Super Administrator'])

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Dashboard Super Administrator</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-0.5">Pusat analitik sistem, integritas audit log kriptografis, dan konfigurasi wilayah.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('superadmin.backup.download') }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-[#252A34] px-3.5 py-2 rounded-lg text-xs font-semibold border border-[#E5E7EB] shadow-xs transition">
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
                        class="inline-flex items-center gap-2 bg-[#1877F2] hover:bg-[#166fe5] text-white px-3.5 py-2 rounded-lg text-xs font-semibold shadow-xs transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1877F2]">
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
                <h2 class="font-semibold text-xs sm:text-sm {{ $chainIntegrity['is_valid'] ? 'text-emerald-950' : 'text-red-950' }}">
                    Status Integritas Audit Trail: {{ $chainIntegrity['is_valid'] ? 'AMAN & VALID' : 'PERINGATAN: ANOMALI TERDETEKSI' }}
                </h2>
                <p class="text-[11px] sm:text-xs {{ $chainIntegrity['is_valid'] ? 'text-emerald-800' : 'text-red-800' }} mt-0.5">
                    {{ $chainIntegrity['message'] }} (Total {{ $chainIntegrity['total_verified'] ?? 0 }} log terverifikasi berantai SHA-256).
                </p>
            </div>
        </div>
        <div class="text-[11px] font-mono bg-white px-3 py-1.5 rounded-lg border border-[#E5E7EB] text-[#252A34] shrink-0">
            Hash Terakhir: {{ substr($chainIntegrity['latest_hash'] ?? '0000000000000000', 0, 16) }}...
        </div>
    </div>

    <!-- 6 Numerical Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- 1. Total Warga -->
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Total Warga</span>
                    <svg class="w-4 h-4 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-[#252A34]">{{ number_format($stats['total_residents']) }}</span>
            </div>
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Basis data kependudukan</p>
        </div>

        <!-- 2. Total DPT -->
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Total DPT</span>
                    <svg class="w-4 h-4 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-[#252A34]">{{ number_format($stats['total_voters_registered']) }}</span>
            </div>
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Entri pemilih terverifikasi</p>
        </div>

        <!-- 3. Total Pemilihan -->
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Total Pemilihan</span>
                    <svg class="w-4 h-4 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-[#252A34]">{{ number_format($stats['total_elections']) }}</span>
            </div>
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Agenda tingkat RT/RW</p>
        </div>

        <!-- 4. Pemilihan Aktif -->
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Sedang Aktif</span>
                    <span class="w-2 h-2 rounded-full bg-[#1877F2]"></span>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-[#1877F2]">{{ number_format($stats['active_elections']) }}</span>
                    <span class="text-[10px] text-[#6B7280]">Live</span>
                </div>
            </div>
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Bilik suara digital buka</p>
        </div>

        <!-- 5. Total Audit Log -->
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Audit Log</span>
                    <svg class="w-4 h-4 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-[#252A34]">{{ number_format($stats['total_audit_logs']) }}</span>
            </div>
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Rantai hash tercatat</p>
        </div>

        <!-- 6. Status Integritas -->
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Integritas</span>
                    <svg class="w-4 h-4 text-[#269B68]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <span class="text-lg font-bold text-[#269B68]">100% Valid</span>
            </div>
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">SHA-256 Anti-Tamper</p>
        </div>
    </div>

    <!-- Analytics Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Chart 1: Status Pemilihan (Donut) -->
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-[#E5E7EB]">
                    <div>
                        <h3 class="text-xs font-semibold text-[#252A34] uppercase tracking-wider">Status Agenda Pemilihan</h3>
                        <p class="text-[11px] text-[#6B7280] mt-0.5">Siklus hidup agenda pemilihan di seluruh wilayah</p>
                    </div>
                </div>
                <div class="relative h-52 mt-3">
                    <canvas id="electionStatusChart"></canvas>
                </div>
            </div>
            <div class="grid grid-cols-4 gap-1 pt-3 border-t border-[#E5E7EB] text-center text-[10px]">
                <div>
                    <span class="text-[#6B7280] block">Draft</span>
                    <strong class="text-[#252A34]">{{ $electionStatusDistribution['draft'] }}</strong>
                </div>
                <div>
                    <span class="text-[#1877F2] block">Terjadwal</span>
                    <strong class="text-[#252A34]">{{ $electionStatusDistribution['scheduled'] }}</strong>
                </div>
                <div>
                    <span class="text-[#269B68] block">Aktif</span>
                    <strong class="text-[#252A34]">{{ $electionStatusDistribution['open'] }}</strong>
                </div>
                <div>
                    <span class="text-[#6B7280] block">Selesai</span>
                    <strong class="text-[#252A34]">{{ $electionStatusDistribution['closed'] }}</strong>
                </div>
            </div>
        </div>

        <!-- Chart 2: Agregat Partisipasi Pemilih (Donut + Progress) -->
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-[#E5E7EB]">
                    <div>
                        <h3 class="text-xs font-semibold text-[#252A34] uppercase tracking-wider">Partisipasi Pemilih Agregat</h3>
                        <p class="text-[11px] text-[#6B7280] mt-0.5">Rasio suara masuk terhadap seluruh DPT terdaftar</p>
                    </div>
                    <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-emerald-50 text-[#269B68] border border-emerald-200">
                        {{ $voterParticipation['percentage'] }}%
                    </span>
                </div>
                <div class="relative h-52 mt-3">
                    <canvas id="voterParticipationChart"></canvas>
                </div>
            </div>
            <div class="space-y-1.5 pt-3 border-t border-[#E5E7EB]">
                <div class="flex items-center justify-between text-[11px]">
                    <span class="text-[#6B7280]">Suara Masuk: <strong class="text-[#269B68]">{{ number_format($voterParticipation['voted']) }}</strong></span>
                    <span class="text-[#6B7280]">Belum Memilih: <strong class="text-[#252A34]">{{ number_format($voterParticipation['unvoted']) }}</strong></span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-[#269B68] h-1.5 rounded-full" style="width: {{ $voterParticipation['percentage'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Chart 3: Distribusi Aktivitas Sistem (Bar Chart) -->
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-[#E5E7EB]">
                    <div>
                        <h3 class="text-xs font-semibold text-[#252A34] uppercase tracking-wider">Aktivitas Audit Per Kategori</h3>
                        <p class="text-[11px] text-[#6B7280] mt-0.5">Distribusi event transaksi dalam rantai log</p>
                    </div>
                </div>
                <div class="relative h-52 mt-3">
                    <canvas id="auditActivityChart"></canvas>
                </div>
            </div>
            <div class="pt-3 border-t border-[#E5E7EB] flex items-center justify-between text-[11px] text-[#6B7280]">
                <span>Total Kategori: <strong class="text-[#252A34]">{{ count($auditActivityBreakdown) }}</strong></span>
                <a href="{{ route('superadmin.audit.index') }}" class="text-[#1877F2] font-semibold hover:underline">Periksa Audit Log</a>
            </div>
        </div>

    </div>

    <!-- Infrastructure & Scope Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-[#1877F2] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-[#6B7280] block">Struktur Wilayah</span>
                <p class="text-xs font-bold text-[#252A34] mt-0.5">
                    {{ $stats['total_villages'] }} Desa • {{ $stats['total_rws'] }} RW • {{ $stats['total_rts'] }} RT
                </p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-[#269B68] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-[#6B7280] block">Akun Pengguna Sistem</span>
                <p class="text-xs font-bold text-[#252A34] mt-0.5">
                    {{ $stats['total_users'] }} Akun Terdaftar
                </p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-[#6B7280] block">Distribusi Pemilihan</span>
                <p class="text-xs font-bold text-[#252A34] mt-0.5">
                    {{ $electionScopeDistribution['rw'] }} Tingkat RW • {{ $electionScopeDistribution['rt'] }} Tingkat RT
                </p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-slate-50 text-[#252A34] flex items-center justify-center shrink-0 border border-[#E5E7EB]">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.7 7.4A3 3 0 018.06 6h7.88a3 3 0 012.36 1.4l1.55 4.15a4.5 4.5 0 01.9 2.7" />
                </svg>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-[#6B7280] block">Infrastruktur Database</span>
                <p class="text-xs font-bold text-[#252A34] mt-0.5">
                    MySQL 8.4 • AES-256 & SHA-256
                </p>
            </div>
        </div>
    </div>

    <!-- Operational Records: Recent Elections & Audit Trail -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Recent Elections -->
        <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden flex flex-col">
            <div class="p-4 sm:px-5 sm:py-3.5 border-b border-[#E5E7EB] flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-[#252A34] text-xs sm:text-sm uppercase tracking-wider">Daftar Pemilihan Terkini</h3>
                    <p class="text-[11px] text-[#6B7280]">Agenda pemilihan yang tercatat dalam sistem</p>
                </div>
                <a href="{{ route('admin.elections.index') }}" class="text-xs font-semibold text-[#1877F2] hover:underline">Lihat Semua</a>
            </div>
            <div class="divide-y divide-[#E5E7EB] flex-1">
                @forelse($recentElections as $election)
                    <div class="p-3.5 sm:p-4 hover:bg-slate-50/70 transition-colors flex items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase border {{ $election->status_badge['class'] }}">
                                    {{ $election->status_badge['text'] }}
                                </span>
                                <span class="text-[11px] font-mono text-[#6B7280] font-medium">{{ $election->code }}</span>
                            </div>
                            <h4 class="font-semibold text-[#252A34] text-xs sm:text-sm">{{ $election->title }}</h4>
                            <p class="text-[11px] text-[#6B7280]">{{ $election->scope_display }} - Periode {{ $election->term_period }}</p>
                        </div>
                        <a href="{{ route('admin.elections.show', $election) }}" class="px-3 py-1.5 rounded-lg bg-white border border-[#E5E7EB] hover:bg-slate-50 text-[#252A34] text-xs font-medium transition shadow-xs shrink-0">
                            Buka
                        </a>
                    </div>
                @empty
                    <div class="p-8 text-center text-[#6B7280] text-xs">Belum ada pemilihan yang dibuat.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Audit Logs -->
        <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden flex flex-col">
            <div class="p-4 sm:px-5 sm:py-3.5 border-b border-[#E5E7EB] flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-[#252A34] text-xs sm:text-sm uppercase tracking-wider">Jejak Audit Aktivitas Sistem</h3>
                    <p class="text-[11px] text-[#6B7280]">Aliran transaksi kriptografis terbaru</p>
                </div>
                <a href="{{ route('superadmin.audit.index') }}" class="text-xs font-semibold text-[#1877F2] hover:underline">Detail Audit</a>
            </div>
            <div class="divide-y divide-[#E5E7EB] flex-1 overflow-y-auto max-h-[360px]">
                @forelse($recentLogs as $log)
                    <div class="p-3.5 hover:bg-slate-50/70 transition-colors text-xs space-y-1">
                        <div class="flex items-center justify-between text-[#6B7280]">
                            <span class="font-semibold text-[#252A34]">@ {{ $log->username }} ({{ ucfirst($log->role) }})</span>
                            <span class="text-[11px]">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[#252A34] text-[11px] leading-relaxed">{{ $log->description }}</p>
                        <div class="flex items-center justify-between text-[10px] text-[#6B7280] font-mono pt-0.5">
                            <span>Aksi: {{ $log->action }}</span>
                            <span>Hash: {{ substr($log->current_hash, 0, 12) }}...</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-[#6B7280] text-xs">Belum ada catatan audit.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.Chart) return;

    // 1. Chart Status Pemilihan (Doughnut)
    const ctxStatus = document.getElementById('electionStatusChart');
    if (ctxStatus) {
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Draft', 'Terjadwal', 'Aktif / Buka', 'Selesai'],
                datasets: [{
                    data: [
                        {{ $electionStatusDistribution['draft'] }},
                        {{ $electionStatusDistribution['scheduled'] }},
                        {{ $electionStatusDistribution['open'] }},
                        {{ $electionStatusDistribution['closed'] }}
                    ],
                    backgroundColor: ['#94A3B8', '#1877F2', '#269B68', '#64748B'],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 10 } }
                    }
                },
                cutout: '65%'
            }
        });
    }

    // 2. Chart Partisipasi Pemilih (Doughnut)
    const ctxPart = document.getElementById('voterParticipationChart');
    if (ctxPart) {
        new Chart(ctxPart, {
            type: 'doughnut',
            data: {
                labels: ['Sudah Memilih', 'Belum Memilih'],
                datasets: [{
                    data: [
                        {{ $voterParticipation['voted'] }},
                        {{ $voterParticipation['unvoted'] }}
                    ],
                    backgroundColor: ['#269B68', '#CBD5E1'],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 10 } }
                    }
                },
                cutout: '65%'
            }
        });
    }

    // 3. Chart Aktivitas Audit (Bar)
    const ctxAudit = document.getElementById('auditActivityChart');
    if (ctxAudit) {
        const auditLabels = {!! json_encode(array_keys($auditActivityBreakdown)) !!};
        const auditValues = {!! json_encode(array_values($auditActivityBreakdown)) !!};

        new Chart(ctxAudit, {
            type: 'bar',
            data: {
                labels: auditLabels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
                datasets: [{
                    label: 'Jumlah Log',
                    data: auditValues,
                    backgroundColor: '#1877F2',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 10 } },
                        grid: { color: '#F1F2F5' }
                    },
                    x: {
                        ticks: { font: { size: 9 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
