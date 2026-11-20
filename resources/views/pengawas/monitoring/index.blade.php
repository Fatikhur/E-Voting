@extends('layouts.app', ['title' => 'Pemantauan Pemilihan'])

@section('content')
<div class="space-y-6">

    <!-- Header & Information -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Pemantauan Pemilihan Aktif & Terjadwal</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-0.5">Pengawasan independen terhadap kepatuhan jadwal, DPT, dan integritas pemungutan suara.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-[#6B7280] text-xs font-semibold border border-[#E5E7EB]">
                <span class="w-2 h-2 rounded-full bg-[#1877F2]"></span>
                <span>Read-Only Mode</span>
            </span>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] p-4 shadow-xs">
        <form method="GET" action="{{ route('pengawas.monitoring.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-[#6B7280] absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari berdasarkan judul atau kode pemilihan..."
                    class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] placeholder-[#9CA3AF] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                >
            </div>

            <div class="flex items-center gap-2">
                <select
                    name="status"
                    class="px-3 py-2 text-xs rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                >
                    <option value="">Semua Status</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Sedang Berlangsung</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Ditutup</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Hasil Dipublikasi</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>

                <button
                    type="submit"
                    class="px-3.5 py-2 text-xs font-semibold rounded-lg bg-[#1877F2] text-white hover:bg-[#166fe5] shadow-xs transition"
                >
                    Filter
                </button>

                @if(request()->hasAny(['search', 'status']))
                    <a
                        href="{{ route('pengawas.monitoring.index') }}"
                        class="px-3 py-2 text-xs font-medium rounded-lg border border-[#E5E7EB] text-[#6B7280] hover:text-[#252A34] hover:bg-slate-50 transition"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Elections Monitoring List -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden">
        <div class="p-4 sm:px-5 sm:py-3.5 border-b border-[#E5E7EB] flex items-center justify-between">
            <h2 class="text-xs sm:text-sm font-semibold text-[#252A34] uppercase tracking-wider">
                Daftar Agenda Pemilihan ({{ $elections->total() }})
            </h2>
            <span class="text-xs text-[#6B7280]">Pembaruan data waktu nyata</span>
        </div>

        <div class="divide-y divide-[#E5E7EB]">
            @forelse($elections as $el)
                @php
                    $elDpt = $el->eligibleVoters()->count();
                    $elVoted = $el->eligibleVoters()->where('has_voted', true)->count();
                    $elTurnout = $elDpt > 0 ? round(($elVoted / $elDpt) * 100, 1) : 0;
                @endphp
                <div class="p-4 sm:p-5 hover:bg-slate-50/70 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1.5 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase border {{ $el->status_badge['class'] }}">
                                {{ $el->status_badge['text'] }}
                            </span>
                            <span class="text-[11px] font-mono text-[#6B7280] font-medium">{{ $el->code }}</span>
                            <span class="text-[11px] text-[#6B7280]">- {{ $el->scope_display }}</span>
                        </div>
                        <h3 class="text-sm sm:text-base font-bold text-[#252A34]">
                            <a href="{{ route('pengawas.monitoring.show', $el) }}" class="hover:text-[#1877F2] transition-colors">{{ $el->title }}</a>
                        </h3>
                        <div class="flex flex-wrap items-center gap-3 text-[11px] text-[#6B7280]">
                            <span>Jadwal: {{ $el->start_time->format('d M, H:i') }} s/d {{ $el->end_time->format('d M Y, H:i') }}</span>
                            <span>•</span>
                            <span>DPT Terdaftar: <strong class="text-[#252A34]">{{ $elDpt }} Warga</strong></span>
                            <span>•</span>
                            <span>Partisipasi: <strong class="{{ $elTurnout > 0 ? 'text-[#269B68]' : 'text-[#252A34]' }}">{{ $elTurnout }}%</strong></span>
                            <span>•</span>
                            <span>{{ $el->candidates->count() }} Kandidat</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-start md:self-auto shrink-0">
                        <a href="{{ route('pengawas.monitoring.show', $el) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-[#1877F2] hover:bg-[#166fe5] text-white shadow-xs transition">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Pantau Real-Time</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-[#6B7280] text-xs">
                    Tidak ada agenda pemilihan yang sesuai dengan kriteria pencarian.
                </div>
            @endforelse
        </div>

        @if($elections->hasPages())
            <div class="p-4 border-t border-[#E5E7EB]">
                {{ $elections->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
