@extends('layouts.app', ['title' => 'Tanda Terima Hak Suara'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="{ copied: false, receiptCode: '{{ $receiptToken }}' }">

    <div class="bg-white rounded-3xl border-2 border-emerald-400 shadow-xl overflow-hidden text-center">

        <!-- Top Celebration Banner -->
        <div class="p-8 bg-gradient-to-b from-emerald-600 to-emerald-700 text-white space-y-3">
            <div class="w-20 h-20 rounded-full bg-white text-emerald-600 text-4xl flex items-center justify-center mx-auto shadow-lg animate-bounce">
                ✓
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Suara Anda Berhasil Dicatat!</h1>
            <p class="text-emerald-100 text-sm sm:text-base max-w-md mx-auto">
                Terima kasih atas partisipasi aktif Anda dalam pesta demokrasi pemilihan {{ $election->title }}.
            </p>
        </div>

        <div class="p-6 sm:p-8 space-y-6">

            <!-- Anonymous Receipt Box -->
            <div class="p-6 rounded-2xl bg-slate-50 border-2 border-dashed border-slate-300 text-left space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Bukti Tanda Terima Anonim (Receipt Token)</span>
                    <span class="text-xs font-mono text-emerald-700 bg-emerald-100 px-2.5 py-0.5 rounded-full font-bold">Sah Terverifikasi</span>
                </div>

                <div class="p-3 bg-white rounded-xl border border-slate-200 font-mono text-xs sm:text-sm text-slate-900 break-all select-all font-semibold">
                    {{ $receiptToken }}
                </div>

                <div class="flex items-center justify-between text-xs text-slate-500">
                    <span>Waktu Tercatat: {{ $voterRecord->voted_at ? $voterRecord->voted_at->format('d M Y, H:i:s') : date('d M Y, H:i:s') }} WIB</span>
                    <button
                        type="button"
                        @click="navigator.clipboard.writeText(receiptCode); copied = true; setTimeout(() => copied = false, 3000)"
                        class="font-bold text-blue-600 hover:text-blue-800 underline focus:outline-none"
                    >
                        <span x-show="!copied">📋 Salin Kode Bukti</span>
                        <span x-show="copied" class="text-emerald-600 font-bold" style="display: none;">✓ Tersalin!</span>
                    </button>
                </div>
            </div>

            <!-- Secret Ballot Guarantee Banner -->
            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-left text-xs sm:text-sm text-blue-900 flex items-start gap-3">
                <span class="text-2xl shrink-0">🛡️</span>
                <div class="space-y-1">
                    <span class="font-bold block">Jaminan Kerahasiaan Pilihan (Secret Ballot):</span>
                    <p class="text-blue-800 leading-relaxed text-xs">
                        Surat suara Anda telah dimasukkan secara acak ke dalam kotak suara kriptografis. Sistem secara arsitektural <strong>tidak menyimpan relasi atau jejak</strong> yang menghubungkan identitas diri Anda dengan nama calon yang Anda pilih. Pilihan Anda tetap menjadi rahasia mutlak Anda.
                    </p>
                </div>
            </div>

            <!-- Home Button -->
            <div class="pt-4 border-t border-slate-100">
                <a
                    href="{{ route('voter.dashboard') }}"
                    class="block w-full py-3.5 px-6 text-center text-sm sm:text-base font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-white shadow-md transition"
                >
                    Kembali ke Halaman Utama Pemilih ➔
                </a>
            </div>

        </div>
    </div>

</div>
@endsection
