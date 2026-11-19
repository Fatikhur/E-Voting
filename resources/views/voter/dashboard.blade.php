@extends('layouts.app', ['title' => 'Dashboard Pemilih'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Greeting Card -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-[#1877F2] border border-blue-200">
                    Akun Pemilih Terdaftar
                </span>
                <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">
                    Selamat Datang, {{ $user->name }}
                </h1>
                <p class="text-xs sm:text-sm text-[#6B7280]">
                    Wilayah: <span class="font-semibold text-[#252A34]">RT {{ $user->rt?->rt_number ?? '-' }} / RW {{ $user->rw?->rw_number ?? '-' }}</span>, {{ $user->village?->name ?? 'Kelurahan Sukamaju' }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#1877F2] border border-blue-200 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Active Elections Section -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-[#252A34] tracking-tight">
                Agenda Pemilihan Anda
            </h2>
            <span class="text-xs text-[#6B7280]">Hak Suara Dijamin Rahasia</span>
        </div>

        <div class="space-y-4">
            @forelse($electionsData as $item)
                @php
                    $el = $item['election'];
                    $hasVoted = $item['has_voted'];
                    $canVote = $item['can_vote'];
                    $isEligible = $item['is_eligible'];
                @endphp

                <div class="bg-white rounded-xl border {{ $canVote ? 'border-[#1877F2]' : ($hasVoted ? 'border-emerald-300 bg-emerald-50/15' : 'border-[#E5E7EB]') }} p-5 sm:p-6 shadow-xs transition">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">

                        <div class="space-y-2.5 flex-1">
                            <!-- Status Badges -->
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold uppercase tracking-wide border {{ $el->status_badge['class'] }}">
                                    {{ $el->status_badge['text'] }}
                                </span>

                                @if($hasVoted)
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-[#269B68] border border-emerald-200 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        <span>Sudah Memilih</span>
                                    </span>
                                @elseif($canVote)
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-[#1877F2] border border-blue-200 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-[#1877F2]"></span>
                                        <span>Pemungutan Suara Dibuka</span>
                                    </span>
                                @endif
                            </div>

                            <!-- Title -->
                            <h3 class="text-base sm:text-lg font-bold text-[#252A34] leading-snug">
                                {{ $el->title }}
                            </h3>

                            <!-- Details -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs sm:text-sm text-[#6B7280] pt-1">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#6B7280] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                    </svg>
                                    <span>Cakupan: <strong class="text-[#252A34]">{{ $el->scope_display }}</strong></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#6B7280] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Batas Waktu: <strong class="text-[#252A34]">{{ $el->end_time->format('d M Y, H:i') }} WIB</strong></span>
                                </div>
                            </div>

                            @if($el->description)
                                <p class="text-xs text-[#6B7280] pt-1 leading-relaxed">
                                    {{ $el->description }}
                                </p>
                            @endif
                        </div>

                        <!-- Action Box -->
                        <div class="w-full md:w-64 shrink-0 flex flex-col justify-center">
                            @if($hasVoted)
                                <div class="p-3.5 rounded-lg bg-emerald-50/70 border border-emerald-200 text-center space-y-1.5">
                                    <div class="w-7 h-7 mx-auto rounded-full bg-emerald-100 text-[#269B68] flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-emerald-950">
                                        Anda telah memberikan hak suara.
                                    </p>
                                    <a href="{{ route('voting.receipt', $el) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-[#1877F2] hover:underline">
                                        <span>Buka Tanda Terima</span>
                                    </a>
                                </div>

                                @if($el->isPublished())
                                    <a href="{{ route('reports.results', $el) }}" class="mt-2.5 w-full py-2 px-3 text-center text-xs font-semibold rounded-lg bg-[#1877F2] text-white hover:bg-[#166fe5] shadow-xs transition">
                                        Lihat Hasil Resmi
                                    </a>
                                @endif
                            @elseif($canVote)
                                <div class="space-y-2">
                                    <a
                                        href="{{ route('voting.candidates', $el) }}"
                                        class="flex items-center justify-center gap-2 w-full py-2.5 px-4 text-center text-sm font-semibold rounded-lg bg-[#1877F2] hover:bg-[#166fe5] text-white shadow-xs transition"
                                    >
                                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Mulai Memilih</span>
                                    </a>
                                    <p class="text-[11px] text-center text-[#6B7280]">Lihat profil kandidat dan tentukan pilihan.</p>
                                </div>
                            @elseif(!$isEligible)
                                <div class="p-3.5 rounded-lg bg-slate-50 border border-[#E5E7EB] text-center text-xs text-[#6B7280]">
                                    Identitas belum terdaftar dalam DPT untuk pemilihan ini.
                                </div>
                            @else
                                <div class="p-3.5 rounded-lg bg-slate-50 border border-[#E5E7EB] text-center text-xs text-[#6B7280]">
                                    Pemungutan suara belum dibuka. Jadwal mulai:<br>
                                    <strong class="text-[#252A34]">{{ $el->start_time->format('d M Y, H:i') }} WIB</strong>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl p-10 text-center text-[#6B7280] border border-[#E5E7EB] shadow-xs">
                    <p class="text-sm font-semibold text-[#252A34]">Tidak ada agenda pemilihan aktif di wilayah RT/RW Anda saat ini.</p>
                    <p class="text-xs text-[#6B7280] mt-1">Anda akan menerima pemberitahuan ketika panitia menjadwalkan pemilihan baru.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Senior Friendly 3-Step Guide -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] p-5 sm:p-6 shadow-xs">
        <h3 class="text-sm sm:text-base font-semibold text-[#252A34] mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-[#1877F2]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.516 0c.85.493 1.509 1.333 1.509 2.316V18" />
            </svg>
            <span>Panduan Mudah 3 Langkah Melakukan Pemilihan Digital</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs sm:text-sm">
            <div class="p-3.5 rounded-lg bg-slate-50 border border-[#E5E7EB] space-y-1.5">
                <span class="w-6 h-6 rounded-md bg-blue-50 text-[#1877F2] font-semibold flex items-center justify-center text-xs">1</span>
                <h4 class="font-semibold text-[#252A34] text-xs sm:text-sm">Pilih Kandidat</h4>
                <p class="text-xs text-[#6B7280]">Pelajari foto, nama, nomor urut, serta visi dan misi dari masing-masing calon ketua.</p>
            </div>
            <div class="p-3.5 rounded-lg bg-slate-50 border border-[#E5E7EB] space-y-1.5">
                <span class="w-6 h-6 rounded-md bg-blue-50 text-[#1877F2] font-semibold flex items-center justify-center text-xs">2</span>
                <h4 class="font-semibold text-[#252A34] text-xs sm:text-sm">Konfirmasi Pilihan</h4>
                <p class="text-xs text-[#6B7280]">Periksa kembali calon yang Anda pilih sebelum konfirmasi final untuk mencegah salah tekan.</p>
            </div>
            <div class="p-3.5 rounded-lg bg-slate-50 border border-[#E5E7EB] space-y-1.5">
                <span class="w-6 h-6 rounded-md bg-blue-50 text-[#1877F2] font-semibold flex items-center justify-center text-xs">3</span>
                <h4 class="font-semibold text-[#252A34] text-xs sm:text-sm">Simpan Bukti Anonim</h4>
                <p class="text-xs text-[#6B7280]">Suara Anda masuk ke kotak suara rahasia. Anda akan menerima kode bukti tanda terima sah.</p>
            </div>
        </div>
    </div>

</div>
@endsection
