@extends('layouts.app', ['title' => 'DPT - ' . $election->title])

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-mono font-bold text-slate-400">{{ $election->code }}</span>
                <span class="text-xs text-slate-400">• {{ $election->scope_display }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Pemilih Tetap (DPT)</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pemilihan: {{ $election->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.elections.show', $election) }}" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700">
                ← Kembali ke Pemilihan
            </a>
            @if(in_array($election->status, ['draft', 'scheduled', 'open']))
                <form action="{{ route('admin.dpt.sync', $election) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" onclick="return confirm('Sinkronkan DPT sekarang dari basis data warga wilayah ini?')" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
                        <span>🔄 Sinkronkan DPT Terbaru</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 text-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total DPT</span>
            <span class="text-2xl font-extrabold text-slate-800">{{ number_format($stats['total']) }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 text-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Sudah Memilih</span>
            <span class="text-2xl font-extrabold text-emerald-600">{{ number_format($stats['voted']) }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 text-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Belum Memilih</span>
            <span class="text-2xl font-extrabold text-amber-600">{{ number_format($stats['unvoted']) }}</span>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.dpt.index', $election) }}" class="flex flex-wrap items-center gap-3 w-full">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pemilih atau NIK..." class="px-3.5 py-2 text-xs rounded-lg border flex-1 min-w-[200px]">
            <select name="status" class="px-3 py-2 text-xs rounded-lg border">
                <option value="">Semua Status Hak Suara</option>
                <option value="voted" {{ request('status') === 'voted' ? 'selected' : '' }}>Sudah Voting</option>
                <option value="unvoted" {{ request('status') === 'unvoted' ? 'selected' : '' }}>Belum Voting</option>
            </select>
            <button type="submit" class="px-4 py-2 text-xs font-bold bg-slate-800 text-white rounded-lg">Filter</button>
        </form>
    </div>

    <!-- Voters Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Nama Pemilih & NIK</th>
                        <th class="px-5 py-3.5">Alamat & Wilayah</th>
                        <th class="px-5 py-3.5">Akun Terhubung</th>
                        <th class="px-5 py-3.5">Status Hak Suara</th>
                        <th class="px-5 py-3.5">Waktu Memilih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-sans">
                    @forelse($voters as $voter)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900 text-sm">{{ $voter->resident->name }}</div>
                                <div class="font-mono text-slate-400">{{ $voter->resident->nik_masked }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div>{{ $voter->resident->address }}</div>
                                <div class="text-[11px] text-slate-400">RT {{ $voter->resident->rt?->rt_number }} / RW {{ $voter->resident->rw?->rw_number }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($voter->user)
                                    <span class="inline-flex items-center gap-1 text-slate-700 font-medium">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        @ {{ $voter->user->username }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Belum buat akun login</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if($voter->has_voted)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1">
                                        <span>✓</span> Sudah Memilih
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200 inline-flex items-center gap-1">
                                        <span>⏳</span> Belum Memilih
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 whitespace-nowrap">
                                {{ $voter->voted_at ? $voter->voted_at->format('d/m/Y H:i:s') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">Belum ada pemilih dalam DPT. Klik "Sinkronkan DPT Terbaru" di atas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $voters->links() }}
        </div>
    </div>

</div>
@endsection
