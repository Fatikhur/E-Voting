@extends('layouts.app', ['title' => 'Dashboard Pemilih'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <!-- Greeting Card (Senior Friendly) -->
    <div class="bg-gradient-to-r from-blue-900 to-indigo-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-500/30 text-blue-200 border border-blue-400/30">
                    Akun Pemilih Terdaftar
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Selamat Datang, Bapak/Ibu {{ $user->name }}
                </h1>
                <p class="text-sm sm:text-base text-blue-200 leading-relaxed">
                    Wilayah Anda: <span class="font-bold text-white">RT {{ $user->rt?->rt_number ?? '-' }} / RW {{ $user->rw?->rw_number ?? '-' }}</span>, {{ $user->village?->name ?? 'Kelurahan Sukamaju' }}
                </p>
            </div>
            <div class="text-4xl sm:text-5xl shrink-0">
                🗳️
            </div>
        </div>
    </div>

    <!-- Active Elections Section -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                Agenda Pemilihan Anda
            </h2>
            <span class="text-xs sm:text-sm text-slate-500 font-medium">Hak Suara Dijamin Rahasia</span>
        </div>

        <div class="space-y-6">
            @forelse($electionsData as $item)
                @php
                    $el = $item['election'];
                    $hasVoted = $item['has_voted'];
                    $canVote = $item['can_vote'];
                    $isEligible = $item['is_eligible'];
                @endphp

                <div class="bg-white rounded-3xl border-2 {{ $canVote ? 'border-blue-500 shadow-xl shadow-blue-500/10' : ($hasVoted ? 'border-emerald-400 bg-emerald-50/20 shadow-md' : 'border-slate-200 shadow-sm') }} p-6 sm:p-8 transition">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">

                        <div class="space-y-3 flex-1">
                            <!-- Status Badges -->
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wide border {{ $el->status_badge['class'] }}">
                                    {{ $el->status_badge['text'] }}
                                </span>

                                @if($hasVoted)
                                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-600 text-white shadow-sm flex items-center gap-1">
                                        <span>✓</span> SUDAH MENGGUNAKAN HAK SUARA
                                    </span>
                                @elseif($canVote)
                                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-blue-600 text-white animate-pulse">
                                        🔴 PEMUNGUTAN SUARA DIBUKA
                                    </span>
                                @endif
                            </div>

                            <!-- Title -->
                            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 leading-snug">
                                {{ $el->title }}
                            </h3>

                            <!-- Details -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-slate-600 pt-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">📍</span>
                                    <span>Cakupan: <strong>{{ $el->scope_display }}</strong></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-base">⏰</span>
                                    <span>Batas Waktu: <strong>{{ $el->end_time->format('d M Y, H:i') }} WIB</strong></span>
                                </div>
                            </div>

                            @if($el->description)
                                <p class="text-xs sm:text-sm text-slate-500 pt-1 leading-relaxed">
                                    {{ $el->description }}
                                </p>
                            @endif
                        </div>

                        <!-- Action Box -->
                        <div class="w-full md:w-72 shrink-0 flex flex-col justify-center">
                            @if($hasVoted)
                                <div class="p-4 rounded-2xl bg-emerald-100/70 border border-emerald-300 text-center space-y-2">
                                    <span class="text-3xl block">✅</span>
                                    <p class="text-xs sm:text-sm font-bold text-emerald-950">
                                        Anda telah menggunakan hak suara untuk pemilihan ini. Terima kasih atas partisipasi Anda!
                                    </p>
                                    <a href="{{ route('voting.receipt', $el) }}" class="inline-block mt-1 text-xs font-bold text-emerald-800 hover:text-emerald-950 underline">
                                        📄 Buka Tanda Terima Anonim
                                    </a>
                                </div>

                                @if($el->isPublished())
                                    <a href="{{ route('reports.results', $el) }}" class="mt-3 w-full py-2.5 px-4 text-center text-xs font-bold rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition">
                                        📊 Lihat Hasil Resmi Pemilihan
                                    </a>
                                @endif
                            @elseif($canVote)
                                <div class="space-y-2">
                                    <a
                                        href="{{ route('voting.candidates', $el) }}"
                                        class="block w-full py-4 px-6 text-center text-base sm:text-lg font-extrabold rounded-2xl bg-blue-600 hover:bg-blue-700 text-white shadow-xl shadow-blue-600/30 hover:shadow-blue-600/50 transition transform active:scale-[0.98] border-2 border-blue-400"
                                    >
                                        🗳️ MULAI MEMILIH ➔
                                    </a>
                                    <p class="text-[11px] text-center text-slate-500 font-medium">Klik tombol di atas untuk melihat kandidat dan menentukan pilihan Anda.</p>
                                </div>
                            @elseif(!$isEligible)
                                <div class="p-4 rounded-2xl bg-slate-100 text-center text-xs text-slate-500">
                                    Identitas Anda belum terdaftar dalam DPT untuk pemilihan ini. Silakan hubungi Panitia.
                                </div>
                            @else
                                <div class="p-4 rounded-2xl bg-slate-100 text-center text-xs text-slate-600">
                                    Pemungutan suara belum dibuka. Jadwal mulai: <br>
                                    <strong>{{ $el->start_time->format('d M Y, H:i') }} WIB</strong>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl p-12 text-center text-slate-400 border border-slate-200">
                    <span class="text-4xl block mb-2">📭</span>
                    <p class="text-base font-bold text-slate-700">Tidak ada agenda pemilihan aktif di wilayah RT/RW Anda saat ini.</p>
                    <p class="text-xs text-slate-400 mt-1">Anda akan menerima pemberitahuan ketika panitia menjadwalkan pemilihan baru.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Senior Friendly 3-Step Guide -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span>💡</span> Panduan Mudah 3 Langkah Melakukan Pemilihan Digital
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs sm:text-sm">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                <span class="w-8 h-8 rounded-full bg-blue-600 text-white font-extrabold flex items-center justify-center text-sm">1</span>
                <h4 class="font-bold text-slate-900 text-sm">Pilih Kandidat</h4>
                <p class="text-slate-600">Pelajari foto, nama, nomor urut, serta visi dan misi dari masing-masing calon ketua.</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                <span class="w-8 h-8 rounded-full bg-blue-600 text-white font-extrabold flex items-center justify-center text-sm">2</span>
                <h4 class="font-bold text-slate-900 text-sm">Konfirmasi Pilihan</h4>
                <p class="text-slate-600">Periksa kembali calon yang Anda pilih sebelum konfirmasi final untuk mencegah salah tekan.</p>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                <span class="w-8 h-8 rounded-full bg-blue-600 text-white font-extrabold flex items-center justify-center text-sm">3</span>
                <h4 class="font-bold text-slate-900 text-sm">Simpan Bukti Anonim</h4>
                <p class="text-slate-600">Suara Anda masuk ke kotak suara rahasia. Anda akan menerima kode bukti tanda terima sah.</p>
            </div>
        </div>
    </div>

</div>
@endsection
