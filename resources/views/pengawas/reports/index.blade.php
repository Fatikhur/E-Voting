@extends('layouts.app', ['title' => 'Laporan Temuan Pengawas'])

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Laporan & Temuan Pengawas</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Daftar laporan pengawasan independen, rekomendasi, dan indikasi pelanggaran.</p>
        </div>
        <div>
            <a href="{{ route('pengawas.reports.create') }}" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold shadow-xs transition">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Ajukan Laporan Baru</span>
            </a>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-[#252A34]">
                <thead class="bg-slate-50 text-[#6B7280] uppercase font-semibold text-[11px] sm:text-xs border-b border-[#E5E7EB]">
                    <tr>
                        <th class="px-5 py-3">Judul & Pemilihan</th>
                        <th class="px-5 py-3">Jenis Laporan</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Tanggal Lapor</th>
                        <th class="px-5 py-3 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse($reports as $rep)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-[#252A34] text-xs sm:text-sm">{{ $rep->title }}</div>
                                <div class="text-xs text-[#6B7280]">{{ $rep->election->title }}</div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @php
                                    $badge = match($rep->report_type) {
                                        'indikasi_pelanggaran' => 'bg-red-50 text-red-700 border-red-200',
                                        'rekomendasi' => 'bg-blue-50 text-[#1877F2] border-blue-200',
                                        'klarifikasi' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default => 'bg-slate-100 text-[#252A34] border-slate-200',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium border {{ $badge }}">
                                    {{ ucwords(str_replace('_', ' ', $rep->report_type)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-[#6B7280] border border-slate-200">
                                    {{ ucfirst($rep->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-[#6B7280] whitespace-nowrap">
                                {{ $rep->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('pengawas.reports.show', $rep) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-[#E5E7EB] text-[#252A34] hover:bg-slate-50 transition">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-[#6B7280] text-xs sm:text-sm">Belum ada laporan temuan yang diajukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[#E5E7EB]">
            {{ $reports->links() }}
        </div>
    </div>

</div>
@endsection
