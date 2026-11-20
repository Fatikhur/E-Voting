@extends('layouts.app', ['title' => 'Dashboard Admin Panitia Pemilihan'])

@section('content')
<div class="space-y-6">

    <!-- Header & Shortcuts -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Dashboard Panitia Pemilihan</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-0.5">Manajemen pemungutan suara digital, DPT, dan kandidat tingkat RW dan RT.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.residents.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-[#252A34] px-3.5 py-2 rounded-lg text-xs font-semibold border border-[#E5E7EB] shadow-xs transition">
                <svg class="w-4 h-4 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                <span>Data Warga</span>
            </a>
            <a href="{{ route('admin.elections.create') }}" class="inline-flex items-center gap-2 bg-[#1877F2] hover:bg-[#166fe5] text-white px-3.5 py-2 rounded-lg text-xs font-semibold shadow-xs transition">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Buat Pemilihan Baru</span>
            </a>
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
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Warga terdaftar di wilayah</p>
        </div>

        <!-- 2. Total DPT -->
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Total DPT</span>
                    <svg class="w-4 h-4 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-[#252A34]">{{ number_format($stats['total_dpt']) }}</span>
            </div>
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Pemilih berhak suara</p>
        </div>

        <!-- 3. Sudah Memilih -->
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Sudah Memilih</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-[#269B68] border border-emerald-200">
                        {{ $stats['turnout_percentage'] }}%
                    </span>
                </div>
                <span class="text-2xl font-bold text-[#269B68]">{{ number_format($stats['total_voted']) }}</span>
            </div>
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Suara masuk terverifikasi</p>
        </div>

        <!-- 4. Belum Memilih -->
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
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Menunggu pemberian suara</p>
        </div>

        <!-- 5. Total Kandidat -->
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Total Kandidat</span>
                    <svg class="w-4 h-4 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-[#252A34]">{{ number_format($stats['total_candidates']) }}</span>
            </div>
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Calon ketua terdaftar</p>
        </div>

        <!-- 6. Pemilihan Aktif -->
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider">Pemilihan Aktif</span>
                    <span class="w-2 h-2 rounded-full bg-[#1877F2]"></span>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold text-[#1877F2]">{{ number_format($stats['active_elections']) }}</span>
                    <span class="text-[10px] text-[#6B7280]">/ {{ $stats['total_elections'] }} Agenda</span>
                </div>
            </div>
            <p class="text-[10px] text-[#6B7280] mt-2 pt-2 border-t border-[#F1F2F5]">Bilik pemungutan buka</p>
        </div>
    </div>

    <!-- Analytics Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Chart 1: Komposisi Partisipasi Pemilih (Donut Chart) -->
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-[#E5E7EB]">
                    <div>
                        <h3 class="text-xs font-semibold text-[#252A34] uppercase tracking-wider">Partisipasi Pemilih Wilayah</h3>
                        <p class="text-[11px] text-[#6B7280] mt-0.5">Proporsi warga yang telah menyalurkan hak suara</p>
                    </div>
                    <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-emerald-50 text-[#269B68] border border-emerald-200">
                        {{ $stats['turnout_percentage'] }}%
                    </span>
                </div>
                <div class="relative h-52 mt-3">
                    <canvas id="adminParticipationChart"></canvas>
                </div>
            </div>
            <div class="space-y-1.5 pt-3 border-t border-[#E5E7EB]">
                <div class="flex items-center justify-between text-[11px]">
                    <span class="text-[#6B7280]">Sudah Memilih: <strong class="text-[#269B68]">{{ number_format($stats['total_voted']) }}</strong></span>
                    <span class="text-[#6B7280]">Belum Memilih: <strong class="text-[#252A34]">{{ number_format($stats['total_unvoted']) }}</strong></span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-[#269B68] h-1.5 rounded-full" style="width: {{ $stats['turnout_percentage'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Partisipasi Per Agenda Pemilihan (Bar Chart) -->
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-col justify-between lg:col-span-2">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-[#E5E7EB]">
                    <div>
                        <h3 class="text-xs font-semibold text-[#252A34] uppercase tracking-wider">Performa Partisipasi Per Pemilihan</h3>
                        <p class="text-[11px] text-[#6B7280] mt-0.5">Perbandingan suara masuk vs target DPT per agenda</p>
                    </div>
                    <span class="text-[11px] text-[#6B7280] font-medium">{{ $electionTurnouts->count() }} Agenda Terpantau</span>
                </div>
                <div class="relative h-52 mt-3">
                    <canvas id="adminElectionTurnoutChart"></canvas>
                </div>
            </div>
            <div class="pt-3 border-t border-[#E5E7EB] flex items-center justify-between text-[11px] text-[#6B7280]">
                <span>Grafik menampilkan pemilih berhak suara dan jumlah suara sah yang telah masuk</span>
                <a href="{{ route('admin.elections.index') }}" class="text-[#1877F2] font-semibold hover:underline">Kelola Seluruh Agenda</a>
            </div>
        </div>

    </div>

    <!-- Active Elections & Operational Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Daftar Agenda Pemilihan (2 Cols) -->
        <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden lg:col-span-2 flex flex-col">
            <div class="p-4 sm:px-5 sm:py-3.5 border-b border-[#E5E7EB] flex items-center justify-between">
                <div>
                    <h2 class="text-xs sm:text-sm font-semibold text-[#252A34] uppercase tracking-wider">Daftar Agenda Pemilihan</h2>
                    <p class="text-[11px] text-[#6B7280]">Status pelaksanaan dan tahapan pemungutan suara</p>
                </div>
                <a href="{{ route('admin.elections.index') }}" class="text-xs font-semibold text-[#1877F2] hover:underline">Semua Pemilihan</a>
            </div>

            <div class="divide-y divide-[#E5E7EB] flex-1">
                @forelse($elections as $el)
                    @php
                        $elDptCount = $el->eligibleVoters()->count();
                        $elVotedCount = $el->eligibleVoters()->where('has_voted', true)->count();
                        $elTurnout = $elDptCount > 0 ? round(($elVotedCount / $elDptCount) * 100, 1) : 0;
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
                                <a href="{{ route('admin.elections.show', $el) }}" class="hover:text-[#1877F2] transition-colors">{{ $el->title }}</a>
                            </h3>
                            <div class="flex flex-wrap items-center gap-3 text-[11px] text-[#6B7280]">
                                <span>Jadwal: {{ $el->start_time->format('d M, H:i') }} s/d {{ $el->end_time->format('d M Y, H:i') }}</span>
                                <span>•</span>
                                <span>{{ $el->candidates->count() }} Kandidat</span>
                                <span>•</span>
                                <span>Partisipasi: <strong class="text-[#252A34]">{{ $elVotedCount }}/{{ $elDptCount }} ({{ $elTurnout }}%)</strong></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-start sm:self-auto shrink-0">
                            <a href="{{ route('admin.dpt.index', $el) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-[#E5E7EB] bg-white hover:bg-slate-50 text-[#252A34] transition shadow-xs">
                                DPT ({{ $elDptCount }})
                            </a>
                            <a href="{{ route('admin.elections.show', $el) }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-[#1877F2] hover:bg-[#166fe5] text-white shadow-xs transition">
                                Kelola & Hasil
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-[#6B7280] text-xs">Belum ada pemilihan yang dibuat. Klik tombol di atas untuk membuat pemilihan baru.</div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Aktivitas Pengelolaan Terbaru (1 Col) -->
        <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden flex flex-col">
            <div class="p-4 sm:px-5 sm:py-3.5 border-b border-[#E5E7EB] flex items-center justify-between">
                <div>
                    <h3 class="text-xs sm:text-sm font-semibold text-[#252A34] uppercase tracking-wider">Aktivitas Terkini</h3>
                    <p class="text-[11px] text-[#6B7280]">Catatan kegiatan panitia pemilihan</p>
                </div>
            </div>

            <div class="divide-y divide-[#E5E7EB] flex-1 overflow-y-auto max-h-[420px]">
                @forelse($recentActivities as $act)
                    <div class="p-3.5 hover:bg-slate-50/70 transition-colors text-xs space-y-1">
                        <div class="flex items-center justify-between text-[#6B7280]">
                            <span class="font-semibold text-[#252A34]">@ {{ $act->username }}</span>
                            <span class="text-[11px]">{{ $act->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[#252A34] text-[11px] leading-relaxed">{{ $act->description }}</p>
                        <div class="flex items-center justify-between text-[10px] text-[#6B7280] font-mono pt-0.5">
                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-[#252A34] uppercase font-sans font-semibold text-[9px]">{{ $act->event_type }}</span>
                            <span>{{ substr($act->current_hash, 0, 10) }}...</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-[#6B7280] text-xs">Belum ada aktivitas pengelolaan tercatat.</div>
                @endforelse
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.Chart) return;

    // 1. Chart Komposisi Partisipasi Pemilih Admin (Doughnut)
    const ctxPart = document.getElementById('adminParticipationChart');
    if (ctxPart) {
        new Chart(ctxPart, {
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

    // 2. Chart Turnout Per Pemilihan (Bar)
    const ctxTurnout = document.getElementById('adminElectionTurnoutChart');
    if (ctxTurnout) {
        const turnoutData = {!! json_encode($electionTurnouts) !!};
        const labels = turnoutData.map(d => d.title.length > 20 ? d.title.substring(0, 20) + '...' : d.title);
        const votedData = turnoutData.map(d => d.voted);
        const unvotedData = turnoutData.map(d => d.unvoted);

        new Chart(ctxTurnout, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Suara Masuk',
                        data: votedData,
                        backgroundColor: '#269B68',
                        borderRadius: 4
                    },
                    {
                        label: 'Belum Memilih',
                        data: unvotedData,
                        backgroundColor: '#CBD5E1',
                        borderRadius: 4
                    }
                ]
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
                scales: {
                    x: {
                        stacked: true,
                        ticks: { font: { size: 9 } },
                        grid: { display: false }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 10 } },
                        grid: { color: '#F1F2F5' }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
