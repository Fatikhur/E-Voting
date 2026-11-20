@extends('layouts.app', ['title' => 'Dashboard Pemilih'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Greeting & Identity Hero -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] p-5 sm:p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-[#269B68] border border-emerald-200">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Pemilih Terverifikasi</span>
                    </span>
                    <span class="text-xs text-[#6B7280]">NIK Terenkripsi Aman</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">
                    Selamat Datang, {{ $user->name }}
                </h1>
                <p class="text-xs sm:text-sm text-[#6B7280]">
                    Wilayah Hak Pilih: <strong class="text-[#252A34]">RT {{ $user->rt?->rt_number ?? '01' }} / RW {{ $user->rw?->rw_number ?? '01' }}</strong>, {{ $user->village?->name ?? 'Kelurahan Sukamaju' }}
                </p>
            </div>

            <div class="flex items-center gap-3 self-start sm:self-auto shrink-0">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#1877F2] border border-blue-200 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Voting Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider block">Agenda Pemilihan</span>
                <span class="text-2xl font-bold text-[#252A34] mt-0.5 block">{{ $voterSummary['total_elections'] }}</span>
                <span class="text-[10px] text-[#6B7280]">Tingkat RT & RW Anda</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-slate-50 text-[#6B7280] flex items-center justify-center shrink-0 border border-[#E5E7EB]">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider block">Sudah Digunakan</span>
                <span class="text-2xl font-bold text-[#269B68] mt-0.5 block">{{ $voterSummary['voted_count'] }}</span>
                <span class="text-[10px] text-[#6B7280]">Suara sah tersimpan</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-[#269B68] flex items-center justify-center shrink-0 border border-emerald-200">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider block">Menunggu Pemilihan</span>
                <span class="text-2xl font-bold {{ $voterSummary['pending_count'] > 0 ? 'text-[#1877F2]' : 'text-[#6B7280]' }} mt-0.5 block">{{ $voterSummary['pending_count'] }}</span>
                <span class="text-[10px] text-[#6B7280]">{{ $voterSummary['pending_count'] > 0 ? 'Bilik suara terbuka' : 'Tidak ada antrean' }}</span>
            </div>
            <div class="w-10 h-10 rounded-lg {{ $voterSummary['pending_count'] > 0 ? 'bg-blue-50 text-[#1877F2] border-blue-200' : 'bg-slate-50 text-[#6B7280] border-[#E5E7EB]' }} flex items-center justify-center shrink-0 border">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Timeline Tahapan Pemilihan -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] p-4 sm:p-5 shadow-xs">
        <h3 class="text-xs font-semibold text-[#252A34] uppercase tracking-wider mb-3">Tahapan Pelaksanaan Pemilihan Digital</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-[11px]">
            <div class="p-2.5 rounded-lg bg-slate-50 border border-[#E5E7EB]">
                <div class="flex items-center gap-1.5 font-semibold text-[#269B68]">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <span>1. Verifikasi DPT</span>
                </div>
                <p class="text-[10px] text-[#6B7280] mt-1">Pendaftaran & penetapan hak pilih</p>
            </div>

            <div class="p-2.5 rounded-lg bg-slate-50 border border-[#E5E7EB]">
                <div class="flex items-center gap-1.5 font-semibold text-[#269B68]">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <span>2. Profil Calon</span>
                </div>
                <p class="text-[10px] text-[#6B7280] mt-1">Publikasi visi, misi, dan kandidat</p>
            </div>

            <div class="p-2.5 rounded-lg bg-blue-50 border border-blue-200">
                <div class="flex items-center gap-1.5 font-semibold text-[#1877F2]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#1877F2]"></span>
                    <span>3. Pemungutan Suara</span>
                </div>
                <p class="text-[10px] text-blue-900 mt-1">Pemberian suara & bukti token</p>
            </div>

            <div class="p-2.5 rounded-lg bg-slate-50 border border-[#E5E7EB]">
                <div class="flex items-center gap-1.5 font-semibold text-[#6B7280]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#94A3B8]"></span>
                    <span>4. Rekapitulasi</span>
                </div>
                <p class="text-[10px] text-[#6B7280] mt-1">Pengesahan berita acara resmi</p>
            </div>
        </div>
    </div>

    <!-- Active Elections Section -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-[#252A34] uppercase tracking-wider">
                    Agenda Pemilihan di Wilayah Anda
                </h2>
                <p class="text-xs text-[#6B7280]">Gunakan hak suara Anda secara bijak, bebas, dan rahasia</p>
            </div>
            <span class="text-xs font-semibold text-[#1877F2]">{{ $electionsData->count() }} Agenda</span>
        </div>

        <div class="space-y-4">
            @forelse($electionsData as $item)
                @php
                    $el = $item['election'];
                    $hasVoted = $item['has_voted'];
                    $canVote = $item['can_vote'];
                    $isEligible = $item['is_eligible'];
                @endphp

                <div class="bg-white rounded-xl border {{ $canVote ? 'border-[#1877F2] shadow-xs' : ($hasVoted ? 'border-emerald-200 bg-emerald-50/10' : 'border-[#E5E7EB]') }} p-5 shadow-xs transition">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-5">

                        <div class="space-y-2 flex-1">
                            <!-- Status Badges -->
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wide border {{ $el->status_badge['class'] }}">
                                    {{ $el->status_badge['text'] }}
                                </span>
                                <span class="text-[11px] font-mono text-[#6B7280] font-medium">{{ $el->code }}</span>

                                @if($hasVoted)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-[#269B68] border border-emerald-200 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        <span>Suara Anda Sudah Masuk</span>
                                    </span>
                                @elseif($canVote)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-[#1877F2] border border-blue-200 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#1877F2]"></span>
                                        <span>Bilik Suara Dibuka</span>
                                    </span>
                                @endif
                            </div>

                            <!-- Title -->
                            <h3 class="text-base font-bold text-[#252A34]">
                                {{ $el->title }}
                            </h3>

                            <!-- Details -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-[#6B7280]">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#6B7280] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                    </svg>
                                    <span>Cakupan: <strong class="text-[#252A34]">{{ $el->scope_display }}</strong></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#6B7280] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Batas Waktu: <strong class="text-[#252A34]">{{ $el->end_time->format('d M Y, H:i') }} WIB</strong></span>
                                </div>
                            </div>

                            <!-- Candidates Preview Thumbnail -->
                            @if($el->candidates->isNotEmpty())
                                <div class="pt-2">
                                    <span class="text-[11px] text-[#6B7280] block mb-1.5 font-medium">Kandidat Terdaftar ({{ $el->candidates->count() }} Calon):</span>
                                    <div class="flex flex-wrap items-center gap-2">
                                        @foreach($el->candidates as $can)
                                            <div class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-slate-50 border border-[#E5E7EB] text-[11px]">
                                                <span class="w-4 h-4 rounded bg-[#1877F2] text-white text-[10px] font-bold flex items-center justify-center shrink-0">
                                                    {{ $can->candidate_number }}
                                                </span>
                                                <span class="font-medium text-[#252A34]">{{ $can->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Action Box -->
                        <div class="w-full md:w-60 shrink-0 flex flex-col justify-center">
                            @if($hasVoted)
                                <div class="p-3 rounded-lg bg-emerald-50/70 border border-emerald-200 text-center space-y-1.5">
                                    <div class="w-7 h-7 mx-auto rounded-full bg-emerald-100 text-[#269B68] flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                    </div>
                                    <p class="text-[11px] font-semibold text-emerald-950">
                                        Hak suara sah tercatat.
                                    </p>
                                    <a href="{{ route('voting.receipt', $el) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-[#1877F2] hover:underline">
                                        <span>Buka Tanda Terima</span>
                                    </a>
                                </div>

                                @if($el->isPublished())
                                    <a href="{{ route('reports.results', $el) }}" class="mt-2 w-full py-2 px-3 text-center text-xs font-semibold rounded-lg bg-[#1877F2] text-white hover:bg-[#166fe5] shadow-xs transition">
                                        Lihat Hasil Resmi
                                    </a>
                                @endif
                            @elseif($canVote)
                                <div class="space-y-1.5">
                                    <a
                                        href="{{ route('voting.candidates', $el) }}"
                                        class="flex items-center justify-center gap-2 w-full py-2.5 px-4 text-center text-xs font-semibold rounded-lg bg-[#1877F2] hover:bg-[#166fe5] text-white shadow-xs transition"
                                    >
                                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Mulai Memberikan Suara</span>
                                    </a>
                                    <p class="text-[10px] text-center text-[#6B7280]">Bilik suara digital sedang dibuka</p>
                                </div>
                            @elseif(!$isEligible)
                                <div class="p-3 rounded-lg bg-slate-50 border border-[#E5E7EB] text-center text-[11px] text-[#6B7280]">
                                    Identitas belum terdaftar dalam DPT agenda pemilihan ini.
                                </div>
                            @else
                                <div class="p-3 rounded-lg bg-slate-50 border border-[#E5E7EB] text-center text-[11px] text-[#6B7280]">
                                    Pemungutan suara belum dibuka.<br>
                                    Mulai: <strong class="text-[#252A34]">{{ $el->start_time->format('d M Y, H:i') }} WIB</strong>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl p-8 text-center text-[#6B7280] border border-[#E5E7EB] shadow-xs">
                    <p class="text-sm font-semibold text-[#252A34]">Tidak ada agenda pemilihan aktif di wilayah Anda saat ini.</p>
                    <p class="text-xs text-[#6B7280] mt-1">Anda akan menerima pemberitahuan ketika panitia menjadwalkan pemilihan baru.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Secret Ballot Guarantee Banner -->
    <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200/60 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-[#1877F2]/10 text-[#1877F2] flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <div class="space-y-0.5 text-xs text-blue-950">
            <h4 class="font-bold">Jaminan Kerahasiaan Hak Suara (Secret Ballot)</h4>
            <p class="text-[11px] text-blue-800 leading-relaxed">
                Pilihan Anda dimasukkan secara acak ke dalam kotak suara kriptografis. Sistem tidak menyimpan relasi antara identitas Anda dengan pilihan kandidat Anda. Pilihan Anda tetap menjadi rahasia mutlak Anda.
            </p>
        </div>
    </div>

    <!-- 3-Step Guide -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] p-5 shadow-xs">
        <h3 class="text-xs font-semibold text-[#252A34] uppercase tracking-wider mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-[#1877F2]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.516 0c.85.493 1.509 1.333 1.509 2.316V18" />
            </svg>
            <span>Panduan Mudah 3 Langkah Memberikan Suara</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
            <div class="p-3.5 rounded-lg bg-slate-50 border border-[#E5E7EB] space-y-1">
                <span class="w-5 h-5 rounded bg-blue-50 text-[#1877F2] font-semibold flex items-center justify-center text-[10px]">1</span>
                <h4 class="font-semibold text-[#252A34]">Pilih Kandidat</h4>
                <p class="text-[11px] text-[#6B7280]">Pelajari profil, foto, serta visi dan misi dari masing-masing calon ketua.</p>
            </div>
            <div class="p-3.5 rounded-lg bg-slate-50 border border-[#E5E7EB] space-y-1">
                <span class="w-5 h-5 rounded bg-blue-50 text-[#1877F2] font-semibold flex items-center justify-center text-[10px]">2</span>
                <h4 class="font-semibold text-[#252A34]">Konfirmasi Pilihan</h4>
                <p class="text-[11px] text-[#6B7280]">Periksa kembali pilihan Anda melalui dialog konfirmasi untuk menghindari salah tekan.</p>
            </div>
            <div class="p-3.5 rounded-lg bg-slate-50 border border-[#E5E7EB] space-y-1">
                <span class="w-5 h-5 rounded bg-blue-50 text-[#1877F2] font-semibold flex items-center justify-center text-[10px]">3</span>
                <h4 class="font-semibold text-[#252A34]">Simpan Bukti Anonim</h4>
                <p class="text-[11px] text-[#6B7280]">Suara masuk ke kotak suara rahasia dan Anda menerima kode tanda terima digital.</p>
            </div>
        </div>
    </div>

</div>
@endsection
