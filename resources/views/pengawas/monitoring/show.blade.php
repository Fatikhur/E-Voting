@extends('layouts.app', ['title' => 'Pengawasan: ' . $election->title])

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase border {{ $election->status_badge['class'] }}">
                    {{ $election->status_badge['text'] }}
                </span>
                <span class="text-xs font-mono font-medium text-[#6B7280]">{{ $election->code }}</span>
                <span class="text-xs text-[#6B7280]">• {{ $election->scope_display }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Pengawasan: {{ $election->title }}</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Pemantauan data partisipasi warga dan pemeriksaan surat suara.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pengawas.reports.create', ['election_id' => $election->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-amber-600 hover:bg-amber-700 text-white shadow-xs transition">
                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
                <span>Buat Laporan Catatan</span>
            </a>
            <a href="{{ route('reports.results', $election) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border border-[#E5E7EB] bg-white text-[#1877F2] hover:bg-slate-50 transition">
                <svg class="w-3.5 h-3.5 text-[#1877F2]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
                <span>Hasil & Grafik</span>
            </a>
        </div>
    </div>

    <!-- Supervisor Approval Box if Closed -->
    @if($election->isClosed())
        @php
            $approval = $election->approval;
        @endphp
        <div class="bg-white rounded-xl border {{ $approval?->supervisor_approved ? 'border-emerald-200 bg-emerald-50/20' : 'border-amber-200 bg-amber-50/20' }} p-5 sm:p-6 shadow-xs">
            <h3 class="font-semibold text-sm sm:text-base text-[#252A34] mb-2">Persetujuan Hasil Pengawasan (Two-Person Rule)</h3>
            @if($approval?->supervisor_approved)
                <div class="p-3.5 rounded-lg bg-emerald-50 border border-emerald-200 text-xs">
                    <p class="font-semibold text-emerald-950 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#269B68]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Anda telah menyetujui hasil pemilihan ini.</span>
                    </p>
                    <p class="text-[#252A34] italic mt-1">"{{ $approval->supervisor_notes }}"</p>
                    <p class="text-[11px] text-[#6B7280] mt-2 font-mono">Ditandatangani pada {{ $approval->supervisor_approved_at->format('d/m/Y H:i') }}</p>
                </div>
            @else
                <form action="{{ route('pengawas.elections.approve', $election) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-[#252A34] mb-1">Catatan Verifikasi Pengawas Independen *</label>
                        <textarea name="notes" rows="2" required placeholder="Contoh: Proses penghitungan telah diawasi langsung. Jumlah surat suara sesuai dengan pemilih terdaftar..." class="w-full px-3 py-2 text-xs sm:text-sm border border-[#E5E7EB] rounded-lg bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]"></textarea>
                    </div>
                    <button type="button"
                            data-confirm
                            data-confirm-type="warning"
                            data-confirm-title="Tandatangani Persetujuan Berita Acara"
                            data-confirm-message="Tandatangani dan sahkan hasil pengawasan pemilihan ini (Two-Person Rule)?"
                            data-confirm-detail="Catatan verifikasi Anda akan disimpan permanen dan dibubuhi stempel integritas audit trail kriptografis SHA-256."
                            data-confirm-button="Ya, Tanda Tangani & Sahkan"
                            data-confirm-cancel="Batal"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-amber-600 hover:bg-amber-700 text-white shadow-xs transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-400">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        <span>Tanda Tangani & Sahkan Hasil Pengawasan</span>
                    </button>
                </form>
            @endif
        </div>
    @endif

    <!-- Turnout Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block">Daftar Pemilih Tetap</span>
            <span class="text-2xl font-bold text-[#252A34]">{{ number_format($counting['statistics']['total_eligible_voters']) }}</span>
            <span class="block text-xs text-[#6B7280] mt-1">Hak suara terverifikasi</span>
        </div>
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block">Tingkat Partisipasi</span>
            <span class="text-2xl font-bold text-[#269B68]">{{ $counting['statistics']['turnout_percentage'] }}%</span>
            <span class="block text-xs text-[#6B7280] mt-1">{{ $counting['statistics']['total_voted'] }} Pemilih telah memilih</span>
        </div>
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block">Surat Suara Sah</span>
            <span class="text-2xl font-bold text-[#1877F2]">{{ number_format($counting['statistics']['total_valid_ballots']) }}</span>
            <span class="block text-xs text-[#6B7280] mt-1">Dalam kotak suara rahasia</span>
        </div>
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block">Pemeriksaan Integritas</span>
            <span class="text-sm sm:text-base font-bold block mt-1 {{ $counting['statistics']['is_count_consistent'] ? 'text-[#269B68]' : 'text-red-600' }}">
                {{ $counting['statistics']['is_count_consistent'] ? 'Konsisten 100%' : 'Terdapat Selisih' }}
            </span>
            <span class="block text-xs text-[#6B7280] mt-1">Voters vs Ballots</span>
        </div>
    </div>

    <!-- Voter Stream Table -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden">
        <div class="p-4 border-b border-[#E5E7EB] flex items-center justify-between">
            <h3 class="font-semibold text-[#252A34] text-xs sm:text-sm">Aliran Partisipasi Pemilih (Audit Trail Real-Time)</h3>
            <span class="text-xs text-[#6B7280] italic">Pilihan suara dirahasiakan sepenuhnya sesuai prinsip Secret Ballot</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#252A34]">
                <thead class="bg-slate-50 text-[#6B7280] uppercase font-semibold text-[11px] sm:text-xs border-b border-[#E5E7EB]">
                    <tr>
                        <th class="px-4 py-3">Nama Pemilih & NIK Masked</th>
                        <th class="px-4 py-3">Wilayah</th>
                        <th class="px-4 py-3">Status Voting</th>
                        <th class="px-4 py-3">Waktu Pemberian Suara</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse($voterRecords as $vr)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-semibold text-[#252A34]">{{ $vr->resident->name }}</span>
                                <span class="block font-mono text-[11px] text-[#6B7280]">{{ $vr->resident->nik_masked }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-[#252A34]">
                                RT {{ $vr->resident->rt?->rt_number }} / RW {{ $vr->resident->rw?->rw_number }}
                            </td>
                            <td class="px-4 py-3">
                                @if($vr->has_voted)
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-[#269B68] border border-emerald-200">
                                        Sudah Memilih
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-[#6B7280] border border-slate-200">
                                        Belum Memilih
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-[#6B7280] font-mono">
                                {{ $vr->voted_at ? $vr->voted_at->format('d/m/Y H:i:s') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-[#6B7280] text-xs sm:text-sm">Belum ada data pemilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[#E5E7EB]">
            {{ $voterRecords->links() }}
        </div>
    </div>

</div>
@endsection
