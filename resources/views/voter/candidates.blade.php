@extends('layouts.app', ['title' => 'Pilih Kandidat - ' . $election->title])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Navigation & Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('voter.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-[#6B7280] hover:text-[#252A34] transition mb-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Halaman Utama</span>
            </a>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Daftar Calon Ketua</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-0.5">{{ $election->title }} ({{ $election->scope_display }})</p>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-2 bg-blue-50/80 border border-blue-200/60 rounded-lg text-xs text-blue-900 font-medium">
            <svg class="w-4 h-4 text-[#1877F2] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <span>Pilihan Anda dijamin rahasia, aman, dan langsung tercatat secara anonim.</span>
        </div>
    </div>

    <!-- Instructions Banner -->
    <div class="p-4 rounded-xl bg-white border border-[#E5E7EB] shadow-xs flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-[#1877F2]/10 text-[#1877F2] flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <p class="text-xs sm:text-sm text-[#252A34]">
            Silakan membaca visi dan misi calon di bawah ini. Klik tombol <span class="font-semibold text-[#1877F2]">"Pilih Kandidat"</span> pada calon yang Anda kehendaki untuk melanjutkan ke tahap konfirmasi.
        </p>
    </div>

    <!-- Candidate Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($candidates as $candidate)
            <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs hover:border-[#1877F2] hover:shadow-sm transition flex flex-col justify-between overflow-hidden">
                <div class="p-5 space-y-4">

                    <!-- Candidate Number -->
                    <div class="flex items-center justify-between pb-3 border-b border-[#E5E7EB]">
                        <span class="w-10 h-10 rounded-lg bg-[#1877F2] text-white font-bold text-lg flex items-center justify-center">
                            {{ $candidate->candidate_number }}
                        </span>
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-[#6B7280]">
                            Calon No. Urut {{ $candidate->candidate_number }}
                        </span>
                    </div>

                    <!-- Photo & Name -->
                    <div class="text-center pt-1 pb-2">
                        <img
                            src="{{ $candidate->photo_url }}"
                            alt="{{ $candidate->name }}"
                            class="w-28 h-28 rounded-xl mx-auto object-cover border border-[#E5E7EB] shadow-xs mb-3"
                        >
                        <h2 class="text-base font-bold text-[#252A34] leading-snug">{{ $candidate->name }}</h2>
                        <p class="text-xs text-[#6B7280] mt-0.5">{{ $candidate->address ?? 'Warga RT ' . ($candidate->election->rt?->rt_number ?? '01') }}</p>
                    </div>

                    <!-- Vision -->
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 space-y-1">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-[#1877F2] block">Visi Utama</span>
                        <p class="text-xs text-[#252A34] italic leading-relaxed">
                            "{{ $candidate->vision }}"
                        </p>
                    </div>

                    <!-- Mission -->
                    <div class="space-y-1">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-[#6B7280] block">Misi & Program Kerja</span>
                        <div class="text-xs text-[#6B7280] whitespace-pre-line leading-relaxed pl-0.5 max-h-32 overflow-y-auto">
                            {{ $candidate->mission }}
                        </div>
                    </div>

                    @if($candidate->bio)
                        <div class="pt-2 border-t border-[#E5E7EB] text-xs text-[#6B7280]">
                            <span class="font-semibold text-[#252A34]">Profil:</span> {{ $candidate->bio }}
                        </div>
                    @endif
                </div>

                <!-- Select Button -->
                <div class="p-4 bg-slate-50 border-t border-[#E5E7EB]">
                    <a
                        href="{{ route('voting.confirm', [$election, 'candidate_id' => $candidate->id]) }}"
                        class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 text-xs font-semibold rounded-lg bg-[#1877F2] text-white hover:bg-[#166fe5] shadow-xs transition"
                    >
                        <span>Pilih Kandidat No. {{ $candidate->candidate_number }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-10 rounded-xl border border-[#E5E7EB] text-center text-[#6B7280]">
                <p class="text-xs font-medium">Tidak ada kandidat aktif pada pemilihan ini.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
