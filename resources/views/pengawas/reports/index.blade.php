@extends('layouts.app', ['title' => 'Laporan Temuan Pengawas'])

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Laporan & Temuan Pengawas</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar laporan pengawasan independen, rekomendasi, dan indikasi pelanggaran.</p>
        </div>
        <div>
            <a href="{{ route('pengawas.reports.create') }}" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm transition">
                <span>➕ Ajukan Laporan Baru</span>
            </a>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Judul & Pemilihan</th>
                        <th class="px-5 py-3.5">Jenis Laporan</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Tanggal Lapor</th>
                        <th class="px-5 py-3.5 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reports as $rep)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900 text-sm">{{ $rep->title }}</div>
                                <div class="text-xs text-slate-400 font-medium">{{ $rep->election->title }}</div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @php
                                    $badge = match($rep->report_type) {
                                        'indikasi_pelanggaran' => 'bg-red-100 text-red-800 border-red-200',
                                        'rekomendasi' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'klarifikasi' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        default => 'bg-slate-100 text-slate-800 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badge }}">
                                    {{ ucwords(str_replace('_', ' ', $rep->report_type)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ ucfirst($rep->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500 whitespace-nowrap">
                                {{ $rep->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('pengawas.reports.show', $rep) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg border hover:bg-slate-100">
                                    Lihat ➔
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">Belum ada laporan temuan yang diajukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $reports->links() }}
        </div>
    </div>

</div>
@endsection
