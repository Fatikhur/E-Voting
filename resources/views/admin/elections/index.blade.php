@extends('layouts.app', ['title' => 'Manajemen Pemilihan'])

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Manajemen Pemilihan RT/RW</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Daftar seluruh agenda pemilihan, status tahapan, dan verifikasi hasil.</p>
        </div>
        <div>
            <a href="{{ route('admin.elections.create') }}" class="inline-flex items-center gap-2 bg-[#1877F2] hover:bg-[#166fe5] text-white px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold shadow-xs transition">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Buat Pemilihan Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.elections.index') }}" class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
            <select name="type" class="px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:border-[#1877F2] focus:ring-1 focus:ring-[#1877F2] focus:outline-none">
                <option value="">Semua Jenis Pemilihan</option>
                <option value="ketua_rw" {{ request('type') === 'ketua_rw' ? 'selected' : '' }}>Ketua RW</option>
                <option value="ketua_rt" {{ request('type') === 'ketua_rt' ? 'selected' : '' }}>Ketua RT</option>
            </select>
            <select name="status" class="px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:border-[#1877F2] focus:ring-1 focus:ring-[#1877F2] focus:outline-none">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Buka (Live)</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Ditutup</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Dipublikasikan</option>
            </select>
            <button type="submit" class="px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-[#252A34] text-white hover:bg-slate-800 transition">
                Filter
            </button>
            @if(request()->hasAny(['type', 'status']))
                <a href="{{ route('admin.elections.index') }}" class="px-3 py-2 text-xs sm:text-sm text-[#6B7280] hover:text-[#252A34] transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Election Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($elections as $el)
            <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden flex flex-col hover:border-slate-300 transition-colors">
                <div class="p-5 flex-1 space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase border {{ $el->status_badge['class'] }}">
                            {{ $el->status_badge['text'] }}
                        </span>
                        <span class="text-xs font-mono font-medium text-[#6B7280]">{{ $el->code }}</span>
                    </div>

                    <h2 class="font-semibold text-[#252A34] text-base leading-snug">
                        <a href="{{ route('admin.elections.show', $el) }}" class="hover:text-[#1877F2] transition-colors">{{ $el->title }}</a>
                    </h2>

                    <div class="text-xs text-[#6B7280] space-y-1.5">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#6B7280] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span>{{ $el->scope_display }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#6B7280] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253 3.75m3 0h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 21h15a2.25 2.25 0 002.25-2.25V9a2.25 2.25 0 00-2.25-2.25z" />
                            </svg>
                            <span>Periode Jabatan: {{ $el->term_period }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#6B7280] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $el->start_time->format('d/m/Y H:i') }} - {{ $el->end_time->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-3 border-t border-[#E5E7EB] text-center text-xs">
                        <div class="p-2 rounded-lg bg-slate-50 border border-[#E5E7EB]">
                            <span class="block text-[#6B7280] font-medium text-[11px]">Kandidat</span>
                            <span class="font-bold text-[#252A34] text-sm">{{ $el->candidates->count() }} Orang</span>
                        </div>
                        <div class="p-2 rounded-lg bg-slate-50 border border-[#E5E7EB]">
                            <span class="block text-[#6B7280] font-medium text-[11px]">Daftar Pemilih</span>
                            <span class="font-bold text-[#252A34] text-sm">{{ $el->eligibleVoters()->count() }} Warga</span>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-slate-50 border-t border-[#E5E7EB] flex items-center justify-between gap-2">
                    <a href="{{ route('admin.dpt.index', $el) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg text-[#252A34] hover:bg-slate-200/70 transition">
                        Kelola DPT
                    </a>
                    <a href="{{ route('admin.elections.show', $el) }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-[#1877F2] hover:bg-[#166fe5] text-white shadow-xs transition">
                        Kelola & Monitoring
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 rounded-xl border border-[#E5E7EB] text-center text-[#6B7280] space-y-2">
                <p class="text-sm font-semibold text-[#252A34]">Belum ada pemilihan yang sesuai dengan filter.</p>
                <p class="text-xs text-[#6B7280]">Silakan buat pemilihan baru melalui tombol di atas.</p>
            </div>
        @endforelse
    </div>

    <div>
        {{ $elections->links() }}
    </div>

</div>
@endsection
