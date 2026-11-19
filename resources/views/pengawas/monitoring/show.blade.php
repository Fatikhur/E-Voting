@extends('layouts.app', ['title' => 'Pengawasan: ' . $election->title])

@section('content')
<div class="space-y-8">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase border {{ $election->status_badge['class'] }}">
                    {{ $election->status_badge['text'] }}
                </span>
                <span class="text-xs font-mono font-bold text-slate-400">{{ $election->code }}</span>
                <span class="text-xs text-slate-400">• {{ $election->scope_display }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pengawasan: {{ $election->title }}</h1>
            <p class="text-sm text-slate-500 mt-1">Pemantauan data partisipasi warga dan pemeriksaan surat suara.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pengawas.reports.create', ['election_id' => $election->id]) }}" class="px-3.5 py-2 text-xs font-bold rounded-xl bg-amber-600 hover:bg-amber-700 text-white shadow-sm transition">
                📝 Buat Laporan Catatan
            </a>
            <a href="{{ route('reports.results', $election) }}" class="px-3.5 py-2 text-xs font-bold rounded-xl border border-blue-300 bg-blue-50 text-blue-900 hover:bg-blue-100">
                📊 Hasil & Grafik
            </a>
        </div>
    </div>

    <!-- Supervisor Approval Box if Closed -->
    @if($election->isClosed())
        @php
            $approval = $election->approval;
        @endphp
        <div class="bg-white rounded-2xl border {{ $approval?->supervisor_approved ? 'border-emerald-300 bg-emerald-50/20' : 'border-amber-300 bg-amber-50/20' }} p-6 shadow-sm">
            <h3 class="font-bold text-base text-slate-900 mb-2">Persetujuan Hasil Pengawasan (Two-Person Rule)</h3>
            @if($approval?->supervisor_approved)
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs">
                    <p class="font-bold text-emerald-900">✓ Anda telah menyetujui hasil pemilihan ini.</p>
                    <p class="text-slate-600 italic mt-1">"{{ $approval->supervisor_notes }}"</p>
                    <p class="text-[11px] text-slate-400 mt-2 font-mono">Ditandatangani pada {{ $approval->supervisor_approved_at->format('d/m/Y H:i') }}</p>
                </div>
            @else
                <form action="{{ route('pengawas.elections.approve', $election) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Verifikasi Pengawas Independen *</label>
                        <textarea name="notes" rows="2" required placeholder="Contoh: Proses penghitungan telah diawasi langsung. Jumlah surat suara sesuai dengan pemilih terdaftar..." class="w-full px-3 py-2 text-xs border rounded-lg"></textarea>
                    </div>
                    <button type="button"
                            data-confirm
                            data-confirm-type="warning"
                            data-confirm-title="Tandatangani Persetujuan Berita Acara"
                            data-confirm-message="Tandatangani dan sahkan hasil pengawasan pemilihan ini (Two-Person Rule)?"
                            data-confirm-detail="Catatan verifikasi Anda akan disimpan permanen dan dibubuhi stempel integritas audit trail kriptografis SHA-256."
                            data-confirm-button="Ya, Tanda Tangani & Sahkan"
                            data-confirm-cancel="Batal"
                            class="px-5 py-2 text-xs font-bold rounded-xl bg-amber-600 hover:bg-amber-700 text-white shadow-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-400">
                        ✍️ Tanda Tangani & Sahkan Hasil Pengawasan
                    </button>
                </form>
            @endif
        </div>
    @endif

    <!-- Turnout Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Daftar Pemilih Tetap</span>
            <span class="text-2xl font-extrabold text-slate-800">{{ number_format($counting['statistics']['total_eligible_voters']) }}</span>
            <span class="block text-xs text-slate-500 mt-1">Hak suara terverifikasi</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Tingkat Partisipasi</span>
            <span class="text-2xl font-extrabold text-emerald-600">{{ $counting['statistics']['turnout_percentage'] }}%</span>
            <span class="block text-xs text-slate-500 mt-1">{{ $counting['statistics']['total_voted'] }} Pemilih telah memilih</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Surat Suara Sah</span>
            <span class="text-2xl font-extrabold text-blue-600">{{ number_format($counting['statistics']['total_valid_ballots']) }}</span>
            <span class="block text-xs text-slate-500 mt-1">Dalam kotak suara rahasia</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pemeriksaan Integritas</span>
            <span class="text-base font-bold block mt-1 {{ $counting['statistics']['is_count_consistent'] ? 'text-emerald-700' : 'text-red-700' }}">
                {{ $counting['statistics']['is_count_consistent'] ? '✓ Konsisten 100%' : '⚠️ Terdapat Selisih' }}
            </span>
            <span class="block text-xs text-slate-400 mt-1">Voters vs Ballots</span>
        </div>
    </div>

    <!-- Voter Stream Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Aliran Partisipasi Pemilih (Audit Trail Real-Time)</h3>
            <span class="text-xs text-slate-400 italic">Pilihan suara dirahasiakan sepenuhnya sesuai prinsip Secret Ballot</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Nama Pemilih & NIK Masked</th>
                        <th class="px-4 py-3">Wilayah</th>
                        <th class="px-4 py-3">Status Voting</th>
                        <th class="px-4 py-3">Waktu Pemberian Suara</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($voterRecords as $vr)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3">
                                <span class="font-bold text-slate-800">{{ $vr->resident->name }}</span>
                                <span class="block font-mono text-[11px] text-slate-400">{{ $vr->resident->nik_masked }}</span>
                            </td>
                            <td class="px-4 py-3">
                                RT {{ $vr->resident->rt?->rt_number }} / RW {{ $vr->resident->rw?->rw_number }}
                            </td>
                            <td class="px-4 py-3">
                                @if($vr->has_voted)
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Sudah Memilih
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        Belum Memilih
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500 font-mono">
                                {{ $vr->voted_at ? $vr->voted_at->format('d/m/Y H:i:s') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400">Belum ada data pemilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $voterRecords->links() }}
        </div>
    </div>

</div>
@endsection
