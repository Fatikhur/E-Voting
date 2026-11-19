@extends('layouts.app', ['title' => 'DPT - ' . $election->title])

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-mono font-medium text-[#6B7280]">{{ $election->code }}</span>
                <span class="text-xs text-[#6B7280]">• {{ $election->scope_display }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Daftar Pemilih Tetap (DPT)</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-0.5">Pemilihan: {{ $election->title }}</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.elections.show', $election) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-lg border border-[#E5E7EB] bg-white hover:bg-slate-50 text-[#252A34] transition">
                <svg class="w-4 h-4 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Kembali ke Pemilihan</span>
            </a>
            @if(in_array($election->status, ['draft', 'scheduled', 'open']))
                <form action="{{ route('admin.dpt.sync', $election) }}" method="POST" class="inline">
                    @csrf
                    <button type="button"
                            data-confirm
                            data-confirm-type="info"
                            data-confirm-title="Sinkronisasi Daftar Pemilih Tetap"
                            data-confirm-message="Sinkronkan DPT untuk pemilihan '{{ $election->title }}' sekarang?"
                            data-confirm-detail="Sistem akan memvalidasi warga yang memenuhi syarat umur minimal 17 tahun dan domisili, lalu mendaftarkannya ke DPT."
                            data-confirm-button="Ya, Sinkronkan DPT"
                            data-confirm-cancel="Batal"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-[#1877F2] hover:bg-[#166fe5] text-white shadow-xs transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1877F2]">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span>Sinkronkan DPT Terbaru</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] text-center shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block">Total DPT</span>
            <span class="text-xl sm:text-2xl font-bold text-[#252A34]">{{ number_format($stats['total']) }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] text-center shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block">Sudah Memilih</span>
            <span class="text-xl sm:text-2xl font-bold text-[#269B68]">{{ number_format($stats['voted']) }}</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] text-center shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block">Belum Memilih</span>
            <span class="text-xl sm:text-2xl font-bold text-amber-600">{{ number_format($stats['unvoted']) }}</span>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.dpt.index', $election) }}" class="flex flex-wrap items-center gap-2.5 w-full">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pemilih atau NIK..." class="px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] placeholder-[#6B7280] flex-1 min-w-[200px] focus:outline-none focus:border-[#1877F2]">
            <select name="status" class="px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                <option value="">Semua Status Hak Suara</option>
                <option value="voted" {{ request('status') === 'voted' ? 'selected' : '' }}>Sudah Voting</option>
                <option value="unvoted" {{ request('status') === 'unvoted' ? 'selected' : '' }}>Belum Voting</option>
            </select>
            <button type="submit" class="px-3.5 py-2 text-xs sm:text-sm font-semibold bg-[#252A34] text-white rounded-lg hover:bg-slate-800 transition">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.dpt.index', $election) }}" class="px-3 py-2 text-xs sm:text-sm text-[#6B7280] hover:text-[#252A34] transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Voters Table -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-[#252A34]">
                <thead class="bg-slate-50 text-[#6B7280] uppercase font-semibold text-[11px] sm:text-xs border-b border-[#E5E7EB]">
                    <tr>
                        <th class="px-5 py-3">Nama Pemilih & NIK</th>
                        <th class="px-5 py-3">Alamat & Wilayah</th>
                        <th class="px-5 py-3">Akun Terhubung</th>
                        <th class="px-5 py-3">Status Hak Suara</th>
                        <th class="px-5 py-3">Waktu Memilih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse($voters as $voter)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-[#252A34] text-sm">{{ $voter->resident->name }}</div>
                                <div class="font-mono text-xs text-[#6B7280]">{{ $voter->resident->nik_masked }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-[#252A34]">
                                <div>{{ $voter->resident->address }}</div>
                                <div class="text-[11px] text-[#6B7280]">RT {{ $voter->resident->rt?->rt_number }} / RW {{ $voter->resident->rw?->rw_number }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-xs">
                                @if($voter->user)
                                    <span class="inline-flex items-center gap-1.5 text-[#252A34] font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#269B68]"></span>
                                        @ {{ $voter->user->username }}
                                    </span>
                                @else
                                    <span class="text-[#6B7280] italic">Belum buat akun login</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if($voter->has_voted)
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-[#269B68] border border-emerald-200 inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        <span>Sudah Memilih</span>
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200 inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Belum Memilih</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-xs text-[#6B7280] whitespace-nowrap">
                                {{ $voter->voted_at ? $voter->voted_at->format('d/m/Y H:i:s') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-[#6B7280] text-xs sm:text-sm">Belum ada pemilih dalam DPT. Klik "Sinkronkan DPT Terbaru" di atas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[#E5E7EB]">
            {{ $voters->links() }}
        </div>
    </div>

</div>
@endsection
