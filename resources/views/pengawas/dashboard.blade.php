@extends('layouts.app', ['title' => 'Dashboard Pengawas Pemilihan'])

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Dashboard Pengawas Independen</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-0.5">Pengawasan independen jalannya pemungutan suara digital, audit log kriptografis, dan transparansi proses.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('pengawas.reports.create') }}" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-3.5 py-2 rounded-lg text-xs font-semibold shadow-xs transition">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
                <span>Ajukan Laporan Temuan</span>
            </a>
            <a href="{{ route('pengawas.audit.index') }}" class="inline-flex items-center gap-2 bg-[#171B35] hover:bg-[#13162C] text-white px-3.5 py-2 rounded-lg text-xs font-semibold shadow-xs transition">
                <svg class="w-4 h-4 text-[#C5C9D4]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                <span>Periksa Rantai Audit</span>
            </a>
        </div>
    </div>

    <!-- Audit Integrity Banner -->
    <div class="p-4 rounded-xl border {{ $integrity['is_valid'] ? 'bg-emerald-50/70 border-emerald-200 text-emerald-950' : 'bg-red-50/70 border-red-200 text-red-950' }} shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
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
                <h2 class="font-semibold text-xs sm:text-sm {{ $integrity['is_valid'] ? 'text-emerald-950' : 'text-red-950' }}">
                    Status Integritas Audit Trail: {{ $integrity['is_valid'] ? 'VALID & AMAN' : 'PERINGATAN MANIPULASI' }}
                </h2>
                <p class="text-[11px] sm:text-xs {{ $integrity['is_valid'] ? 'text-emerald-800' : 'text-red-800' }} mt-0.5">
                    {{ $integrity['message'] }} (Terverifikasi secara kriptografis SHA-256).
                </p>
            </div>
        </div>
        <a href="{{ route('pengawas.audit.index') }}" class="text-xs font-semibold text-[#1877F2] hover:underline shrink-0">
            Buka Rantai Audit
        </a>
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
                        <a href="{{ route('pengawas.monitoring.show', $ap->election) }}" class="px-3 py-1.5 text-xs font-semibold bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition shadow-xs">
                            Periksa & Setujui
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 6 Numerical Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- 1. Total Pemilih DPT -->
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Total DPT</span>
                    <svg class="w-4 h-4 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-[#252A34]">{{ number_format($stats['total_dpt']) }}</span>
            </div>
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Pemilih dalam DPT</p>
        </div>

        <!-- 2. Jumlah Pemilih Sudah Memilih -->
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Sudah Memilih</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-[#269B68] border border-emerald-200">
                        {{ $turnoutPercentage }}%
                    </span>
                </div>
                <span class="text-2xl font-bold text-[#269B68]">{{ number_format($stats['total_voted']) }}</span>
            </div>
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Suara sah terverifikasi</p>
        </div>

        <!-- 3. Jumlah Pemilih Belum Memilih -->
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Belum Memilih</span>
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-amber-600">{{ number_format($stats['total_unvoted']) }}</span>
            </div>
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Hak suara belum dipakai</p>
        </div>

        <!-- 4. Total Audit Log -->
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
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Rantai transaksi digital</p>
        </div>

        <!-- 5. Aktivitas Pengawasan -->
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Pengawasan</span>
                    <svg class="w-4 h-4 text-[#1877F2]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-[#1877F2]">{{ number_format($stats['supervisor_audits']) }}</span>
            </div>
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Catatan aksi pengawas</p>
        </div>

        <!-- 6. Laporan Temuan -->
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Laporan Temuan</span>
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-[#252A34]">{{ number_format($stats['total_reports']) }}</span>
            </div>
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Temuan pengawasan</p>
        </div>
    </div>

    <!-- Analytics Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Chart 1: Partisipasi Pemilih Terawasi (Donut) -->
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-[#E5E7EB]">
                    <div>
                        <h3 class="text-xs font-semibold text-[#252A34] uppercase tracking-wider">Integritas Partisipasi DPT</h3>
                        <p class="text-[11px] text-[#6B7280] mt-0.5">Rasio suara sah terhadap total pemilih terdaftar</p>
                    </div>
                    <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-emerald-50 text-[#269B68] border border-emerald-200">
                        {{ $turnoutPercentage }}%
                    </span>
                </div>
                <div class="relative h-52 mt-3">
                    <canvas id="pengawasTurnoutChart"></canvas>
                </div>
            </div>
            <div class="space-y-1.5 pt-3 border-t border-[#E5E7EB]">
                <div class="flex items-center justify-between text-[11px]">
                    <span class="text-[#6B7280]">Suara Masuk: <strong class="text-[#269B68]">{{ number_format($stats['total_voted']) }}</strong></span>
                    <span class="text-[#6B7280]">Belum Memilih: <strong class="text-[#252A34]">{{ number_format($stats['total_unvoted']) }}</strong></span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-[#269B68] h-1.5 rounded-full" style="width: {{ $turnoutPercentage }}%"></div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Aliran Suara Masuk Berdasarkan Waktu (Line Chart) -->
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between lg:col-span-2">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-[#E5E7EB]">
                    <div>
                        <h3 class="text-xs font-semibold text-[#252A34] uppercase tracking-wider">Aliran Suara Masuk Terpantau (Agregat)</h3>
                        <p class="text-[11px] text-[#6B7280] mt-0.5">Volume pemungutan suara per slot waktu tanpa mengungkap identitas pilihan</p>
                    </div>
                    <span class="text-[11px] text-[#6B7280]">Real-Time Agregat</span>
                </div>
                <div class="relative h-52 mt-3">
                    <canvas id="votingTimelineChart"></canvas>
                </div>
            </div>
            <div class="pt-3 border-t border-[#E5E7EB] flex items-center justify-between text-[11px] text-[#6B7280]">
                <span>Setiap suara dienkripsi secara anonim dan terverifikasi tanda terima digital</span>
                <span class="text-emerald-700 font-semibold">Kerahasiaan Terjamin</span>
            </div>
        </div>

    </div>

    <!-- Active Elections & Audit Feed -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Status Pemilihan yang Sedang Diawasi (2 Cols) -->
        <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden lg:col-span-2 flex flex-col">
            <div class="p-4 sm:px-5 sm:py-3.5 border-b border-[#E5E7EB] flex items-center justify-between">
                <div>
                    <h2 class="text-xs sm:text-sm font-semibold text-[#252A34] uppercase tracking-wider">Pemantauan Pemilihan Aktif & Terjadwal</h2>
                    <p class="text-[11px] text-[#6B7280]">Pengawasan kepatuhan jadwal, DPT, dan integritas hitungan</p>
                </div>
                <span class="text-xs font-medium text-[#6B7280]">Read-Only Mode</span>
            </div>

            <div class="divide-y divide-[#E5E7EB] flex-1">
                @forelse($elections as $el)
                    @php
                        $elDpt = $el->eligibleVoters()->count();
                        $elVoted = $el->eligibleVoters()->where('has_voted', true)->count();
                        $elTurnout = $elDpt > 0 ? round(($elVoted / $elDpt) * 100, 1) : 0;
                    @endphp
                    <div class="p-4 sm:p-5 hover:bg-slate-50/70 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase border {{ $el->status_badge['class'] }}">
                                    {{ $el->status_badge['text'] }}
                                </span>
                                <span class="text-[11px] font-mono text-[#6B7280] font-medium">{{ $el->code }}</span>
                                <span class="text-[11px] text-[#6B7280]">- {{ $el->scope_display }}</span>
                            </div>
                            <h3 class="text-sm font-bold text-[#252A34]">
                                <a href="{{ route('pengawas.monitoring.show', $el) }}" class="hover:text-[#1877F2] transition-colors">{{ $el->title }}</a>
                            </h3>
                            <div class="flex flex-wrap items-center gap-3 text-[11px] text-[#6B7280]">
                                <span>Jadwal: {{ $el->start_time->format('d M, H:i') }} s/d {{ $el->end_time->format('d M Y, H:i') }}</span>
                                <span>•</span>
                                <span>DPT Terdaftar: <strong class="text-[#252A34]">{{ $elDpt }} Warga</strong></span>
                                <span>•</span>
                                <span>Partisipasi: <strong class="text-[#269B68]">{{ $elTurnout }}%</strong></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                            <a href="{{ route('pengawas.monitoring.show', $el) }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-[#1877F2] hover:bg-[#166fe5] text-white shadow-xs transition">
                                Pantau Real-Time
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-[#6B7280] text-xs">Belum ada pemilihan yang terdaftar untuk diawasi.</div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Laporan Temuan & Riwayat Audit (1 Col) -->
        <div class="space-y-5">

            <!-- Laporan Temuan Pengawas Terbaru -->
            <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden flex flex-col">
                <div class="p-4 sm:px-5 sm:py-3.5 border-b border-[#E5E7EB] flex items-center justify-between">
                    <div>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#252A34] uppercase tracking-wider">Laporan Temuan Anda</h3>
                        <p class="text-[11px] text-[#6B7280]">Catatan pengawasan & berita acara</p>
                    </div>
                    <a href="{{ route('pengawas.reports.index') }}" class="text-xs font-semibold text-[#1877F2] hover:underline">Semua</a>
                </div>

                <div class="divide-y divide-[#E5E7EB] flex-1">
                    @forelse($myReports as $rep)
                        <div class="p-3.5 hover:bg-slate-50/70 transition-colors text-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-[#252A34]">{{ $rep->title }}</span>
                                <span class="text-[10px] text-[#6B7280]">{{ $rep->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[#6B7280] text-[11px] line-clamp-2 leading-relaxed">{{ $rep->description }}</p>
                        </div>
                    @empty
                        <div class="p-6 text-center text-[#6B7280] text-xs">Belum ada laporan temuan yang diajukan.</div>
                    @endforelse
                </div>
            </div>

            <!-- Aktivitas Audit Log Terbaru -->
            <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden flex flex-col">
                <div class="p-4 sm:px-5 sm:py-3.5 border-b border-[#E5E7EB] flex items-center justify-between">
                    <div>
                        <h3 class="text-xs sm:text-sm font-semibold text-[#252A34] uppercase tracking-wider">Audit Log Real-Time</h3>
                        <p class="text-[11px] text-[#6B7280]">Jejak transaksi digital</p>
                    </div>
                    <a href="{{ route('pengawas.audit.index') }}" class="text-xs font-semibold text-[#1877F2] hover:underline">Detail</a>
                </div>

                <div class="divide-y divide-[#E5E7EB] flex-1 overflow-y-auto max-h-[220px]">
                    @forelse($recentAudits as $log)
                        <div class="p-3 hover:bg-slate-50/70 transition-colors text-xs space-y-0.5">
                            <div class="flex items-center justify-between text-[#6B7280] text-[10px]">
                                <span class="font-semibold text-[#252A34]">@ {{ $log->username }} ({{ ucfirst($log->role) }})</span>
                                <span>{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-[#252A34] text-[11px] leading-snug">{{ $log->description }}</p>
                        </div>
                    @empty
                        <div class="p-6 text-center text-[#6B7280] text-xs">Belum ada catatan audit log.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.Chart) return;

    // 1. Chart Partisipasi Pemilih Pengawas (Doughnut)
    const ctxTurnout = document.getElementById('pengawasTurnoutChart');
    if (ctxTurnout) {
        new Chart(ctxTurnout, {
            type: 'doughnut',
            data: {
                labels: ['Sudah Memilih', 'Belum Memilih'],
                datasets: [{
                    data: [
                        {{ $stats['total_voted'] }},
                        {{ $stats['total_unvoted'] }}
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

    // 2. Chart Aliran Suara Masuk (Line / Area Chart)
    const ctxTimeline = document.getElementById('votingTimelineChart');
    if (ctxTimeline) {
        const timelineData = {!! json_encode($votingTimeline) !!};
        const labels = Object.keys(timelineData);
        const values = Object.values(timelineData);

        new Chart(ctxTimeline, {
            type: 'line',
            data: {
                labels: labels.length > 0 ? labels : ['08:00', '10:00', '12:00', '14:00', '16:00'],
                datasets: [{
                    label: 'Volume Suara Masuk (Agregat)',
                    data: values.length > 0 ? values : [0, 0, 0, 0, 0],
                    borderColor: '#1877F2',
                    backgroundColor: 'rgba(24, 119, 242, 0.08)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#1877F2',
                    pointRadius: 4,
                    borderWidth: 2
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
