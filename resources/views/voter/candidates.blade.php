@extends('layouts.app', ['title' => 'Pilih Kandidat - ' . $election->title])

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    <!-- Top Navigation & Warning -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('voter.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-slate-500 hover:text-slate-800 transition mb-2">
                ← Kembali ke Halaman Utama
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Daftar Calon Ketua</h1>
            <p class="text-sm sm:text-base text-slate-500 mt-1">{{ $election->title }} ({{ $election->scope_display }})</p>
        </div>
        <div class="p-3 bg-blue-50 border border-blue-200 rounded-2xl text-xs sm:text-sm text-blue-900 font-medium">
            🔒 Pilihan Anda dijamin 100% Rahasia dan Bebas.
        </div>
    </div>

    <!-- Instructions Banner -->
    <div class="p-4 rounded-2xl bg-slate-900 text-white flex items-center gap-3 shadow-md">
        <span class="text-2xl">👉</span>
        <p class="text-sm sm:text-base font-medium">
            Silakan membaca visi dan misi masing-masing kandidat di bawah ini, lalu klik tombol biru <strong>"PILIH KANDIDAT"</strong> pada calon pilihan Anda.
        </p>
    </div>

    <!-- Candidate Cards Grid (Senior Friendly, High Contrast) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($candidates as $candidate)
            <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-md hover:border-blue-500 hover:shadow-xl transition flex flex-col justify-between overflow-hidden">
                <div class="p-6 space-y-4">

                    <!-- Candidate Number Big Circle -->
                    <div class="flex items-center justify-between">
                        <span class="w-14 h-14 rounded-2xl bg-blue-600 text-white font-extrabold text-2xl flex items-center justify-center shadow-lg shadow-blue-600/30">
                            {{ $candidate->candidate_number }}
                        </span>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Calon No. Urut {{ $candidate->candidate_number }}</span>
                    </div>

                    <!-- Photo & Name -->
                    <div class="text-center pt-2 pb-2">
                        <img
                            src="{{ $candidate->photo_url }}"
                            alt="{{ $candidate->name }}"
                            class="w-32 h-32 rounded-2xl mx-auto object-cover border-4 border-slate-100 shadow-md mb-3"
                        >
                        <h2 class="text-xl font-extrabold text-slate-900 leading-tight">{{ $candidate->name }}</h2>
                        <p class="text-xs text-slate-500 mt-1">{{ $candidate->address ?? 'Warga RT ' . ($candidate->election->rt?->rt_number ?? '01') }}</p>
                    </div>

                    <!-- Vision -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-blue-700 block">Visi Utama:</span>
                        <p class="text-xs sm:text-sm text-slate-700 font-medium leading-relaxed italic">
                            "{{ $candidate->vision }}"
                        </p>
                    </div>

                    <!-- Mission -->
                    <div class="space-y-1.5">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 block">Misi & Program Kerja:</span>
                        <div class="text-xs text-slate-600 whitespace-pre-line leading-relaxed pl-1">
                            {{ $candidate->mission }}
                        </div>
                    </div>

                    @if($candidate->bio)
                        <div class="pt-2 border-t border-slate-100 text-xs text-slate-500">
                            <span class="font-semibold text-slate-700">Profil:</span> {{ $candidate->bio }}
                        </div>
                    @endif
                </div>

                <!-- Big Select Button -->
                <div class="p-5 bg-slate-50 border-t border-slate-100">
                    <a
                        href="{{ route('voting.confirm', [$election, 'candidate_id' => $candidate->id]) }}"
                        class="block w-full py-3.5 px-4 text-center text-sm sm:text-base font-extrabold rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-600/20 hover:shadow-blue-600/40 transition transform active:scale-[0.98]"
                    >
                        PILIH KANDIDAT NO. {{ $candidate->candidate_number }} ➔
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 rounded-3xl border border-slate-200 text-center text-slate-400">
                Tidak ada kandidat aktif pada pemilihan ini.
            </div>
        @endforelse
    </div>

</div>
@endsection
