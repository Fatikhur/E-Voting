@extends('layouts.app', ['title' => 'Konfirmasi Pilihan Anda'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Back Link -->
    <a href="{{ route('voting.candidates', $election) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-[#6B7280] hover:text-[#252A34] transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Batal / Pilih Calon Lain</span>
    </a>

    <!-- Confirmation Card -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden">

        <!-- Top Header -->
        <div class="p-6 bg-slate-50 border-b border-[#E5E7EB] text-center space-y-1">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#1877F2]/10 text-[#1877F2]">
                Tahap Terakhir: Konfirmasi Hak Suara
            </span>
            <h1 class="text-xl font-bold text-[#252A34] tracking-tight">Periksa Kembali Pilihan Anda</h1>
            <p class="text-xs text-[#6B7280]">Pastikan calon di bawah ini adalah pilihan terbaik yang Anda kehendaki.</p>
        </div>

        <div class="p-6 space-y-5">

            <!-- Selected Candidate Card -->
            <div class="p-5 rounded-lg bg-slate-50 border border-[#E5E7EB] text-center space-y-3">
                <span class="w-12 h-12 rounded-lg bg-[#1877F2] text-white font-bold text-xl flex items-center justify-center mx-auto">
                    {{ $candidate->candidate_number }}
                </span>

                <img
                    src="{{ $candidate->photo_url }}"
                    alt="{{ $candidate->name }}"
                    class="w-28 h-28 rounded-xl mx-auto object-cover border border-[#E5E7EB] shadow-xs"
                >

                <div>
                    <span class="text-[11px] font-semibold text-[#1877F2] uppercase tracking-wider block">Calon Nomor Urut {{ $candidate->candidate_number }}</span>
                    <h2 class="text-lg font-bold text-[#252A34] mt-0.5">{{ $candidate->name }}</h2>
                    <p class="text-xs text-[#6B7280] mt-0.5">{{ $candidate->address }}</p>
                </div>

                <div class="p-3 bg-white rounded-lg border border-[#E5E7EB] text-xs text-[#6B7280] italic">
                    "{{ $candidate->vision }}"
                </div>
            </div>

            <!-- Crucial Final Warning -->
            <div class="p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
                <div class="flex items-center gap-2 font-semibold">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Penting untuk Diketahui:</span>
                </div>
                <p class="text-amber-800 leading-relaxed pl-6">
                    Sesuai asas kerahasiaan pemilihan, setelah Anda menekan tombol konfirmasi, surat suara akan dimasukkan secara permanen ke dalam kotak suara rahasia dan <strong class="font-semibold text-amber-950">tidak dapat diubah atau ditarik kembali</strong>.
                </p>
            </div>

            <!-- Confirmation Form -->
            <form action="{{ route('voting.cast', $election) }}" method="POST" class="space-y-4 pt-1">
                @csrf
                <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <a
                        href="{{ route('voting.candidates', $election) }}"
                        class="w-full sm:w-1/3 py-2.5 px-4 text-center text-xs font-medium rounded-lg border border-[#E5E7EB] text-[#6B7280] hover:text-[#252A34] hover:bg-slate-100 transition"
                    >
                        Ganti Pilihan
                    </a>

                    <button
                        type="button"
                        data-confirm
                        data-confirm-type="success"
                        data-confirm-title="Konfirmasi Akhir Pemberian Suara"
                        data-confirm-message="Kirim suara Anda untuk Kandidat No. {{ $candidate->candidate_number }} ({{ $candidate->name }})?"
                        data-confirm-detail="Pilihan Anda akan dienkripsi secara anonim dan dimasukkan ke dalam kotak suara digital. Surat suara tidak dapat diubah atau ditarik kembali setelah ini."
                        data-confirm-button="Ya, Masukkan ke Kotak Suara"
                        data-confirm-cancel="Periksa Kembali"
                        class="w-full sm:w-2/3 py-2.5 px-5 text-center text-xs font-semibold rounded-lg bg-[#269B68] hover:bg-[#218559] text-white shadow-xs transition flex items-center justify-center gap-1.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#269B68]/30"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Ya, Saya Yakin & Konfirmasi</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
