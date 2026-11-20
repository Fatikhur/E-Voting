@extends('layouts.app', ['title' => 'Dashboard Admin Panitia Pemilihan'])

@section('content')
<div class="space-y-6">

    <!-- Header & Shortcuts -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Dashboard Panitia Pemilihan</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Manajemen pemungutan suara digital tingkat RW dan RT.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.residents.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-[#252A34] px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold border border-[#E5E7EB] shadow-xs transition">
                <svg class="w-4 h-4 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                <span>Data Warga</span>
            </a>
            <a href="{{ route('admin.elections.create') }}" class="inline-flex items-center gap-2 bg-[#1877F2] hover:bg-[#166fe5] text-white px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold shadow-xs transition">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Buat Pemilihan Baru</span>
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">Total Pemilih (DPT)</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-bold text-[#252A34]">{{ number_format($stats['total_dpt']) }}</span>
                <svg class="w-5 h-5 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
            </div>
            <p class="text-xs text-[#6B7280] mt-2">Terdaftar di seluruh pemilihan</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">Sudah Memberikan Suara</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-bold text-[#269B68]">{{ number_format($stats['total_voted']) }}</span>
                <span class="text-xs px-2 py-0.5 rounded-md bg-emerald-50 text-[#269B68] font-semibold border border-emerald-200">Voted</span>
            </div>
            <p class="text-xs text-[#6B7280] mt-2">Partisipasi aktif warga</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">Belum Memilih</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-bold text-amber-600">{{ number_format($stats['total_unvoted']) }}</span>
                <span class="text-xs px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-semibold border border-amber-200">Unvoted</span>
            </div>
            <p class="text-xs text-[#6B7280] mt-2">Menunggu penggunaan hak suara</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">Pemilihan Aktif</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-bold text-[#1877F2]">{{ number_format($stats['active_elections']) }}</span>
                <span class="text-xs px-2 py-0.5 rounded-md bg-blue-50 text-[#1877F2] font-semibold border border-blue-200">Live</span>
            </div>
            <p class="text-xs text-[#6B7280] mt-2">Pemungutan suara terbuka</p>
        </div>
    </div>

    <!-- Active and Managed Elections -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden">
        <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E5E7EB] flex items-center justify-between">
            <h2 class="text-sm sm:text-base font-semibold text-[#252A34]">Daftar Agenda Pemilihan</h2>
            <a href="{{ route('admin.elections.index') }}" class="text-xs font-semibold text-[#1877F2] hover:underline">Semua Pemilihan</a>
        </div>

        <div class="divide-y divide-[#E5E7EB]">
            @forelse($elections as $el)
                <div class="p-4 sm:p-5 hover:bg-slate-50/70 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase border {{ $el->status_badge['class'] }}">
                                {{ $el->status_badge['text'] }}
                            </span>
                            <span class="text-xs font-mono text-[#6B7280] font-medium">{{ $el->code }}</span>
                            <span class="text-xs text-[#6B7280]">• {{ $el->scope_display }}</span>
                        </div>
                        <h3 class="text-sm sm:text-base font-semibold text-[#252A34]">
                            <a href="{{ route('admin.elections.show', $el) }}" class="hover:text-[#1877F2] transition-colors">{{ $el->title }}</a>
                        </h3>
                        <p class="text-xs text-[#6B7280]">
                            Jadwal: {{ $el->start_time->format('d M Y, H:i') }} s/d {{ $el->end_time->format('d M Y, H:i') }} • {{ $el->candidates->count() }} Kandidat Terdaftar
                        </p>
                    </div>

                    <div class="flex items-center gap-2 self-start md:self-auto shrink-0">
                        <a href="{{ route('admin.dpt.index', $el) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-[#E5E7EB] bg-white hover:bg-slate-50 text-[#252A34] transition">
                            DPT ({{ $el->eligibleVoters()->count() }})
                        </a>
                        <a href="{{ route('admin.elections.show', $el) }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-[#1877F2] hover:bg-[#166fe5] text-white shadow-xs transition">
                            Kelola & Hasil
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-[#6B7280] text-xs sm:text-sm">Belum ada pemilihan yang dibuat. Klik tombol di atas untuk membuat pemilihan baru.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
