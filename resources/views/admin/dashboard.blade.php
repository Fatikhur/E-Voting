@extends('layouts.app', ['title' => 'Dashboard Admin Panitia Pemilihan'])

@section('content')
<div class="space-y-8">

    <!-- Header & Shortcuts -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Panitia Pemilihan</h1>
            <p class="text-sm sm:text-base text-slate-500 mt-1">Manajemen pemungutan suara digital tingkat RW dan RT.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <a href="{{ route('admin.residents.index') }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 px-3.5 py-2 rounded-xl text-sm font-semibold border border-slate-300 shadow-sm transition">
                <span>👥 Data Warga</span>
            </a>
            <a href="{{ route('admin.elections.create') }}" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm transition">
                <span>➕ Buat Pemilihan Baru</span>
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Pemilih (DPT)</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-slate-800">{{ number_format($stats['total_dpt']) }}</span>
                <span class="text-xl">📋</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Terdaftar di seluruh pemilihan</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Sudah Memberikan Suara</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ number_format($stats['total_voted']) }}</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">Voted</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Partisipasi aktif warga</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Belum Memilih</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-amber-600">{{ number_format($stats['total_unvoted']) }}</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold">Unvoted</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Menunggu penggunaan hak suara</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Pemilihan Aktif</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-blue-600">{{ number_format($stats['active_elections']) }}</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-bold">Live</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Pemungutan suara terbuka</p>
        </div>
    </div>

    <!-- Active and Managed Elections -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Daftar Agenda Pemilihan</h2>
            <a href="{{ route('admin.elections.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">Semua Pemilihan ➔</a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($elections as $el)
                <div class="p-5 hover:bg-slate-50/70 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md text-xs font-bold uppercase border {{ $el->status_badge['class'] }}">
                                {{ $el->status_badge['text'] }}
                            </span>
                            <span class="text-xs font-mono text-slate-400 font-semibold">{{ $el->code }}</span>
                            <span class="text-xs text-slate-400">• {{ $el->scope_display }}</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">
                            <a href="{{ route('admin.elections.show', $el) }}" class="hover:text-blue-600 transition">{{ $el->title }}</a>
                        </h3>
                        <p class="text-xs text-slate-500">
                            Jadwal: {{ $el->start_time->format('d M Y, H:i') }} s/d {{ $el->end_time->format('d M Y, H:i') }} • {{ $el->candidates->count() }} Kandidat Terdaftar
                        </p>
                    </div>

                    <div class="flex items-center gap-2 self-start md:self-auto shrink-0">
                        <a href="{{ route('admin.dpt.index', $el) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-700">
                            DPT ({{ $el->eligibleVoters()->count() }})
                        </a>
                        <a href="{{ route('admin.elections.show', $el) }}" class="px-4 py-1.5 text-xs font-bold rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
                            Kelola & Hasil ➔
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-sm">Belum ada pemilihan yang dibuat. Klik tombol di atas untuk membuat pemilihan baru.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
