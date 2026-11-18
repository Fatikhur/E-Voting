@extends('layouts.app', ['title' => 'Manajemen Pemilihan'])

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Pemilihan RT/RW</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar seluruh agenda pemilihan, status tahapan, dan verifikasi hasil.</p>
        </div>
        <div>
            <a href="{{ route('admin.elections.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm transition">
                <span>➕ Buat Pemilihan Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.elections.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <select name="type" class="px-3.5 py-2 text-sm rounded-lg border border-slate-300">
                <option value="">Semua Jenis Pemilihan</option>
                <option value="ketua_rw" {{ request('type') === 'ketua_rw' ? 'selected' : '' }}>Ketua RW</option>
                <option value="ketua_rt" {{ request('type') === 'ketua_rt' ? 'selected' : '' }}>Ketua RT</option>
            </select>
            <select name="status" class="px-3.5 py-2 text-sm rounded-lg border border-slate-300">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Buka (Live)</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Ditutup</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Dipublikasikan</option>
            </select>
            <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900">
                Filter
            </button>
            @if(request()->hasAny(['type', 'status']))
                <a href="{{ route('admin.elections.index') }}" class="px-3 py-2 text-sm text-slate-500 hover:text-slate-800">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Election Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($elections as $el)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col hover:border-blue-300 transition">
                <div class="p-5 flex-1 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase border {{ $el->status_badge['class'] }}">
                            {{ $el->status_badge['text'] }}
                        </span>
                        <span class="text-xs font-mono font-semibold text-slate-400">{{ $el->code }}</span>
                    </div>

                    <h2 class="font-extrabold text-slate-900 text-base leading-snug">
                        <a href="{{ route('admin.elections.show', $el) }}" class="hover:text-blue-600 transition">{{ $el->title }}</a>
                    </h2>

                    <div class="text-xs text-slate-500 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span>📍</span>
                            <span>{{ $el->scope_display }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span>🗓️</span>
                            <span>Periode Jabatan: {{ $el->term_period }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span>⏰</span>
                            <span>{{ $el->start_time->format('d/m/Y H:i') }} - {{ $el->end_time->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 text-center text-xs">
                        <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="block text-slate-400 font-medium">Kandidat</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $el->candidates->count() }} Orang</span>
                        </div>
                        <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="block text-slate-400 font-medium">Daftar Pemilih</span>
                            <span class="font-bold text-slate-800 text-sm">{{ $el->eligibleVoters()->count() }} Warga</span>
                        </div>
                    </div>
                </div>

                <div class="p-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.dpt.index', $el) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-600 hover:bg-slate-200/70">
                        Kelola DPT
                    </a>
                    <a href="{{ route('admin.elections.show', $el) }}" class="px-4 py-1.5 text-xs font-bold rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
                        Kelola & Monitoring ➔
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 rounded-2xl border border-slate-200 text-center text-slate-400 space-y-3">
                <span class="text-4xl block">🗳️</span>
                <p class="text-base font-semibold text-slate-700">Belum ada pemilihan yang sesuai dengan filter.</p>
                <p class="text-xs text-slate-400">Silakan buat pemilihan baru melalui tombol di atas.</p>
            </div>
        @endforelse
    </div>

    <div>
        {{ $elections->links() }}
    </div>

</div>
@endsection
