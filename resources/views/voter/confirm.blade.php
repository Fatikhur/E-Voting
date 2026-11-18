@extends('layouts.app', ['title' => 'Konfirmasi Pilihan Anda'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Back Link -->
    <a href="{{ route('voting.candidates', $election) }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-slate-500 hover:text-slate-800 transition">
        ← Batal / Pilih Calon Lain
    </a>

    <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-xl overflow-hidden">

        <!-- Top Header -->
        <div class="p-6 sm:p-8 bg-blue-950 text-white text-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-600/40 text-blue-200 mb-2">
                Tahap Terakhir: Konfirmasi Hak Suara
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Periksa Kembali Pilihan Anda</h1>
            <p class="text-sm text-blue-200 mt-1">Pastikan calon di bawah ini adalah pilihan terbaik yang Anda kehendaki.</p>
        </div>

        <div class="p-6 sm:p-8 space-y-6">

            <!-- Selected Candidate Card -->
            <div class="p-6 rounded-2xl bg-blue-50/70 border-2 border-blue-200 text-center space-y-4">
                <span class="w-16 h-16 rounded-2xl bg-blue-600 text-white font-extrabold text-3xl flex items-center justify-center mx-auto shadow-md">
                    {{ $candidate->candidate_number }}
                </span>

                <img
                    src="{{ $candidate->photo_url }}"
                    alt="{{ $candidate->name }}"
                    class="w-32 h-32 rounded-2xl mx-auto object-cover border-4 border-white shadow-lg"
                >

                <div>
                    <span class="text-xs font-bold text-blue-700 uppercase tracking-wider block">Calon Nomor Urut {{ $candidate->candidate_number }}</span>
                    <h2 class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $candidate->name }}</h2>
                    <p class="text-xs text-slate-500 mt-1">{{ $candidate->address }}</p>
                </div>

                <div class="p-3 bg-white rounded-xl border border-blue-100 text-xs text-slate-600 italic">
                    "{{ $candidate->vision }}"
                </div>
            </div>

            <!-- Crucial Final Warning -->
            <div class="p-4 rounded-2xl bg-amber-50 border-l-4 border-amber-500 text-amber-950 text-xs sm:text-sm space-y-1">
                <div class="flex items-center gap-2 font-bold">
                    <span class="text-base">⚠️</span>
                    <span>PENTING UNTUK DIKETAHUI:</span>
                </div>
                <p class="text-amber-800 leading-relaxed pl-6">
                    Sesuai asas kerahasiaan pemilihan, setelah Anda menekan tombol konfirmasi di bawah, surat suara akan dimasukkan secara permanen ke dalam kotak suara rahasia dan <strong>TIDAK DAPAT DIUBAH ATAU DITARIK KEMBALI</strong>.
                </p>
            </div>

            <!-- Confirmation Form -->
            <form action="{{ route('voting.cast', $election) }}" method="POST" class="space-y-4 pt-2">
                @csrf
                <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <a
                        href="{{ route('voting.candidates', $election) }}"
                        class="w-full sm:w-1/3 py-3.5 px-4 text-center text-sm font-bold rounded-xl border-2 border-slate-300 text-slate-700 hover:bg-slate-100 transition"
                    >
                        ← Ganti Pilihan
                    </a>

                    <button
                        type="submit"
                        class="w-full sm:w-2/3 py-3.5 px-6 text-center text-base font-extrabold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-xl shadow-emerald-600/30 hover:shadow-emerald-600/50 transition transform active:scale-[0.98] border-2 border-emerald-400 flex items-center justify-center gap-2"
                    >
                        <span>✓</span>
                        <span>YA, SAYA YAKIN & KONFIRMASI</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
