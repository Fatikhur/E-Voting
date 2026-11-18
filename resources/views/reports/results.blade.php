@extends('layouts.app', ['title' => 'Hasil Pemilihan - ' . $election->title])

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase border {{ $election->status_badge['class'] }}">
                    {{ $election->status_badge['text'] }}
                </span>
                <span class="text-xs font-mono font-bold text-slate-400">{{ $election->code }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Laporan & Hasil Perhitungan Suara</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $election->title }} ({{ $election->scope_display }})</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.print', $election) }}" target="_blank" class="px-3.5 py-2 text-xs font-bold rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 shadow-sm transition">
                🖨️ Cetak Hasil
            </a>
            <a href="{{ route('reports.pdf', $election) }}" class="px-4 py-2 text-xs font-bold rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
                📄 Unduh Berita Acara PDF
            </a>
        </div>
    </div>

    <!-- Winner or Tie Banner -->
    @if($results['winner'])
        <div class="p-6 rounded-3xl bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-lg flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4 text-center sm:text-left">
                <span class="text-5xl">🏆</span>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-200">Kandidat Peraih Suara Terbanyak</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold mt-0.5">
                        No. {{ $results['winner']['candidate_number'] }} — {{ $results['winner']['name'] }}
                    </h2>
                    <p class="text-sm text-emerald-100 mt-1">
                        Memperoleh <strong class="text-white">{{ number_format($results['winner']['votes']) }} Suara</strong> ({{ $results['winner']['percentage'] }}% dari total suara sah).
                    </p>
                </div>
            </div>
            <div class="px-4 py-2 rounded-xl bg-white/20 backdrop-blur-sm text-center shrink-0">
                <span class="text-xs font-semibold block text-emerald-100">Status</span>
                <span class="text-sm font-bold">Terpilih</span>
            </div>
        </div>
    @elseif($results['is_tie'])
        <div class="p-6 rounded-3xl bg-amber-500 text-slate-900 shadow-lg space-y-3">
            <div class="flex items-center gap-3">
                <span class="text-3xl">⚖️</span>
                <div>
                    <span class="text-xs font-extrabold uppercase tracking-wider text-amber-900">Perhatian: Hasil Pemilihan Seri (Tie Vote)</span>
                    <h2 class="text-xl sm:text-2xl font-extrabold">Terjadi Perolehan Suara Sama Kuat</h2>
                </div>
            </div>
            <div class="p-4 rounded-2xl bg-white/80 border border-amber-600/30 text-xs sm:text-sm space-y-1">
                <span class="font-bold block text-slate-800">Aturan Penyelesaian Sesuai Ketetapan Panitia:</span>
                <p class="text-slate-700 italic">"{{ $election->tie_breaker_rule ?? 'Musyawarah mufakat panitia pengawas dan para kandidat.' }}"</p>
            </div>
        </div>
    @endif

    <!-- Key Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Daftar Pemilih (DPT)</span>
            <span class="text-2xl sm:text-3xl font-extrabold text-slate-800">{{ number_format($results['statistics']['total_eligible_voters']) }}</span>
            <p class="text-xs text-slate-400 mt-1">Pemilih berhak suara</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Partisipasi Suara</span>
            <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ $results['statistics']['turnout_percentage'] }}%</span>
            <p class="text-xs text-slate-400 mt-1">{{ number_format($results['statistics']['total_voted']) }} suara masuk</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Surat Suara Sah</span>
            <span class="text-2xl sm:text-3xl font-extrabold text-blue-600">{{ number_format($results['statistics']['total_valid_ballots']) }}</span>
            <p class="text-xs text-slate-400 mt-1">Dalam kotak suara rahasia</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Integritas Hitungan</span>
            <span class="text-lg font-bold block mt-1 {{ $results['statistics']['is_count_consistent'] ? 'text-emerald-700' : 'text-red-700' }}">
                {{ $results['statistics']['is_count_consistent'] ? '100% Konsisten' : 'Ada Selisih' }}
            </span>
            <p class="text-xs text-slate-400 mt-1">DPT vs Kotak Suara</p>
        </div>
    </div>

    <!-- Chart.js Visualization & Candidates Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Bar Chart Card -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4" x-data="resultsChart()">
            <h3 class="font-bold text-slate-900 text-base">Grafik Perolehan Suara</h3>
            <div class="relative h-64">
                <canvas id="electionResultsChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Participation Chart -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4" x-data="turnoutChart()">
            <h3 class="font-bold text-slate-900 text-base">Komposisi Partisipasi Pemilih</h3>
            <div class="relative h-64">
                <canvas id="turnoutDoughnutChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Candidate Detailed Results Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 text-lg">Tabel Rincian Perolehan Suara Kandidat</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-xs border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">No. Urut</th>
                        <th class="px-6 py-4">Kandidat</th>
                        <th class="px-6 py-4 text-center">Jumlah Suara Sah</th>
                        <th class="px-6 py-4 text-center">Persentase</th>
                        <th class="px-6 py-4">Diagram</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($results['candidates'] as $can)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4">
                                <span class="w-8 h-8 rounded-xl bg-blue-600 text-white font-extrabold text-sm flex items-center justify-center">
                                    {{ $can['candidate_number'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-base">{{ $can['name'] }}</div>
                                <span class="text-xs text-slate-400">{{ $can['status'] === 'active' ? 'Kandidat Sah' : 'Mengundurkan Diri' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center font-extrabold text-slate-900 text-lg">
                                {{ number_format($can['votes_count']) }}
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-blue-600 text-base">
                                {{ $can['percentage'] }}%
                            </td>
                            <td class="px-6 py-4 w-48">
                                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                                    <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $can['percentage'] }}%"></div>
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
                    backgroundColor: ['#2563eb', '#059669', '#d97706', '#7c3aed'],
                    borderRadius: 8,
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
                    backgroundColor: ['#059669', '#cbd5e1'],
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
