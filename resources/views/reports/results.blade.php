@extends('layouts.app', ['title' => 'Hasil Pemilihan - ' . $election->title])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase border {{ $election->status_badge['class'] }}">
                    {{ $election->status_badge['text'] }}
                </span>
                <span class="text-xs font-mono font-medium text-[#6B7280]">{{ $election->code }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Laporan & Hasil Perhitungan Suara</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-0.5">{{ $election->title }} ({{ $election->scope_display }})</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.print', $election) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] hover:bg-slate-50 shadow-xs transition">
                <svg class="w-3.5 h-3.5 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Hasil</span>
            </a>
            <a href="{{ route('reports.pdf', $election) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-[#1877F2] text-white hover:bg-[#166fe5] shadow-xs transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Unduh Berita Acara PDF</span>
            </a>
        </div>
    </div>

    <!-- Winner or Tie Banner -->
    @if($results['winner'])
        <div class="p-5 rounded-xl bg-[#269B68]/5 border border-[#269B68]/30 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3.5 text-center sm:text-left">
                <div class="w-12 h-12 rounded-xl bg-[#269B68]/15 text-[#269B68] flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-[#269B68]">Kandidat Peraih Suara Terbanyak</span>
                    <h2 class="text-lg font-bold text-[#252A34] mt-0.5">
                        No. {{ $results['winner']['candidate_number'] }} - {{ $results['winner']['name'] }}
                    </h2>
                    <p class="text-xs text-[#6B7280] mt-0.5">
                        Memperoleh <strong class="text-[#252A34] font-semibold">{{ number_format($results['winner']['votes']) }} Suara</strong> ({{ $results['winner']['percentage'] }}% dari total suara sah).
                    </p>
                </div>
            </div>
            <div class="px-3.5 py-1.5 rounded-lg bg-[#269B68]/15 text-[#269B68] text-center shrink-0">
                <span class="text-[10px] font-semibold uppercase block">Status</span>
                <span class="text-xs font-bold">Terpilih</span>
            </div>
        </div>
    @elseif($results['is_tie'])
        <div class="p-5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 space-y-2">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800">Perhatian: Hasil Pemilihan Seri (Tie Vote)</span>
                    <h2 class="text-base font-bold text-amber-950">Terjadi Perolehan Suara Sama Kuat</h2>
                </div>
            </div>
            <div class="p-3 rounded-lg bg-white border border-amber-200 text-xs space-y-1">
                <span class="font-semibold text-slate-800 block">Aturan Penyelesaian Sesuai Ketetapan Panitia:</span>
                <p class="text-[#6B7280] italic">"{{ $election->tie_breaker_rule ?? 'Musyawarah mufakat panitia pengawas dan para kandidat.' }}"</p>
            </div>
        </div>
    @endif

    <!-- Key Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">Daftar Pemilih (DPT)</span>
            <span class="text-2xl font-bold text-[#252A34]">{{ number_format($results['statistics']['total_eligible_voters']) }}</span>
            <p class="text-[11px] text-[#6B7280] mt-1">Pemilih berhak suara</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">Partisipasi Suara</span>
            <span class="text-2xl font-bold text-[#269B68]">{{ $results['statistics']['turnout_percentage'] }}%</span>
            <p class="text-[11px] text-[#6B7280] mt-1">{{ number_format($results['statistics']['total_voted']) }} suara masuk</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">Surat Suara Sah</span>
            <span class="text-2xl font-bold text-[#1877F2]">{{ number_format($results['statistics']['total_valid_ballots']) }}</span>
            <p class="text-[11px] text-[#6B7280] mt-1">Dalam kotak suara rahasia</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">Integritas Hitungan</span>
            <span class="text-base font-bold block mt-1 {{ $results['statistics']['is_count_consistent'] ? 'text-[#269B68]' : 'text-[#DC2626]' }}">
                {{ $results['statistics']['is_count_consistent'] ? '100% Konsisten' : 'Ada Selisih' }}
            </span>
            <p class="text-[11px] text-[#6B7280] mt-1">DPT vs Kotak Suara</p>
        </div>
    </div>

    <!-- Chart.js Visualization -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <!-- Bar Chart Card -->
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs space-y-3">
            <h3 class="text-xs font-semibold text-[#252A34] uppercase tracking-wider">Grafik Perolehan Suara</h3>
            <div class="relative h-64">
                <canvas id="electionResultsChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Participation Chart -->
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs space-y-3">
            <h3 class="text-xs font-semibold text-[#252A34] uppercase tracking-wider">Komposisi Partisipasi Pemilih</h3>
            <div class="relative h-64">
                <canvas id="turnoutDoughnutChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Candidate Detailed Results Table -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden">
        <div class="p-4 border-b border-[#E5E7EB]">
            <h3 class="text-xs font-semibold text-[#252A34] uppercase tracking-wider">Tabel Rincian Perolehan Suara Kandidat</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#6B7280]">
                <thead class="bg-slate-50 text-[#252A34] uppercase font-semibold text-[11px] border-b border-[#E5E7EB]">
                    <tr>
                        <th class="px-5 py-3">No. Urut</th>
                        <th class="px-5 py-3">Kandidat</th>
                        <th class="px-5 py-3 text-center">Jumlah Suara Sah</th>
                        <th class="px-5 py-3 text-center">Persentase</th>
                        <th class="px-5 py-3">Diagram</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @foreach($results['candidates'] as $can)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3">
                                <span class="w-7 h-7 rounded-lg bg-[#1877F2] text-white font-bold text-xs flex items-center justify-center">
                                    {{ $can['candidate_number'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="font-semibold text-[#252A34] text-xs">{{ $can['name'] }}</div>
                                <span class="text-[11px] text-[#6B7280]">{{ $can['status'] === 'active' ? 'Kandidat Sah' : 'Mengundurkan Diri' }}</span>
                            </td>
                            <td class="px-5 py-3 text-center font-bold text-[#252A34] text-sm">
                                {{ number_format($can['votes_count']) }}
                            </td>
                            <td class="px-5 py-3 text-center font-semibold text-[#1877F2] text-xs">
                                {{ $can['percentage'] }}%
                            </td>
                            <td class="px-5 py-3 w-48">
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-[#1877F2] h-2 rounded-full" style="width: {{ $can['percentage'] }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Bar Chart Hasil Suara
    const ctxBar = document.getElementById('electionResultsChart');
    if (ctxBar && window.Chart) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_map(fn($c) => 'No. ' . $c['candidate_number'] . ' ' . $c['name'], $results['candidates'])) !!},
                datasets: [{
                    label: 'Perolehan Suara Sah',
                    data: {!! json_encode(array_column($results['candidates'], 'votes_count')) !!},
                    backgroundColor: ['#1877F2', '#269B68', '#D97706', '#7C3AED'],
                    borderRadius: 6,
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
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // 2. Doughnut Chart Partisipasi
    const ctxPie = document.getElementById('turnoutDoughnutChart');
    if (ctxPie && window.Chart) {
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Sudah Memilih', 'Belum Memilih'],
                datasets: [{
                    data: [{{ $results['statistics']['total_voted'] }}, {{ $results['statistics']['total_unvoted'] }}],
                    backgroundColor: ['#269B68', '#CBD5E1'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
