@extends('layouts.app', ['title' => 'Tanda Terima Hak Suara'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="{ copied: false, receiptCode: '{{ $receiptToken }}' }">

    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden text-center">

        <!-- Top Status Banner -->
        <div class="p-6 bg-slate-50 border-b border-[#E5E7EB] space-y-2">
            <div class="w-12 h-12 rounded-xl bg-[#269B68]/10 text-[#269B68] flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 class="text-xl font-bold text-[#252A34] tracking-tight">Suara Anda Berhasil Dicatat</h1>
            <p class="text-xs text-[#6B7280] max-w-md mx-auto">
                Terima kasih atas partisipasi aktif Anda dalam pesta demokrasi pemilihan {{ $election->title }}.
            </p>
        </div>

        <div class="p-6 space-y-5">

            <!-- Anonymous Receipt Box -->
            <div class="p-5 rounded-lg bg-slate-50 border border-[#E5E7EB] text-left space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-[#6B7280]">
                        Bukti Tanda Terima Anonim (Receipt Token)
                    </span>
                    <span class="text-[11px] font-mono text-[#269B68] bg-[#269B68]/10 px-2 py-0.5 rounded font-semibold">
                        Sah Terverifikasi
                    </span>
                </div>

                <div class="p-3 bg-white rounded-lg border border-[#E5E7EB] font-mono text-xs text-[#252A34] break-all select-all font-semibold">
                    {{ $receiptToken }}
                </div>

                <div class="flex items-center justify-between text-xs text-[#6B7280]">
                    <span>Waktu Tercatat: {{ $voterRecord->voted_at ? $voterRecord->voted_at->format('d M Y, H:i:s') : date('d M Y, H:i:s') }} WIB</span>
                    <button
                        type="button"
                        @click="navigator.clipboard.writeText(receiptCode); copied = true; setTimeout(() => copied = false, 3000)"
                        class="inline-flex items-center gap-1 font-semibold text-[#1877F2] hover:text-[#166fe5] focus:outline-none transition cursor-pointer"
                    >
                        <template x-if="!copied">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                                <span>Salin Kode Bukti</span>
                            </span>
                        </template>
                        <template x-if="copied">
                            <span class="inline-flex items-center gap-1 text-[#269B68]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Tersalin!</span>
                            </span>
                        </template>
                    </button>
                </div>
            </div>

            <!-- Secret Ballot Guarantee Banner -->
            <div class="p-4 rounded-lg bg-blue-50/70 border border-blue-200/60 text-left text-xs text-blue-900 flex items-start gap-3">
                <div class="w-7 h-7 rounded-md bg-[#1877F2]/10 text-[#1877F2] flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <span class="font-semibold block text-blue-950">Jaminan Kerahasiaan Pilihan (Secret Ballot):</span>
                    <p class="text-blue-800 leading-relaxed text-[11px]">
                        Surat suara Anda telah dimasukkan secara acak ke dalam kotak suara kriptografis. Sistem secara arsitektural tidak menyimpan relasi atau jejak yang menghubungkan identitas diri Anda dengan nama calon yang Anda pilih. Pilihan Anda tetap menjadi rahasia mutlak Anda.
                    </p>
                </div>
            </div>

            <!-- Home Button -->
            <div class="pt-3 border-t border-[#E5E7EB]">
                <a
                    href="{{ route('voter.dashboard') }}"
                    class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 text-xs font-semibold rounded-lg bg-[#1877F2] text-white hover:bg-[#166fe5] shadow-xs transition"
                >
                    <span>Kembali ke Halaman Utama Pemilih</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
