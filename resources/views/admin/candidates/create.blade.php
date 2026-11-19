@extends('layouts.app', ['title' => 'Tambah Kandidat'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-[#252A34] tracking-tight">Daftarkan Kandidat Baru</h1>
            <p class="text-xs text-[#6B7280] mt-0.5">Pemilihan: {{ $election->title }}</p>
        </div>
        <a href="{{ route('admin.elections.show', $election) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-[#6B7280] hover:text-[#252A34] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Pemilihan</span>
        </a>
    </div>

    <!-- Candidate Form Card -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs p-6">
        <form action="{{ route('admin.candidates.store', $election) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="candidate_number" class="block text-xs font-semibold text-[#252A34] mb-1.5">
                        Nomor Urut <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="candidate_number"
                        id="candidate_number"
                        value="{{ old('candidate_number', $nextNumber) }}"
                        required
                        min="1"
                        class="w-full px-3 py-2 text-sm font-semibold rounded-lg border border-[#E5E7EB] bg-white text-[#1877F2] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                    >
                </div>

                <div class="sm:col-span-2">
                    <label for="name" class="block text-xs font-semibold text-[#252A34] mb-1.5">
                        Nama Lengkap & Gelar <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        required
                        placeholder="Contoh: H. Ahmad Fauzi, S.E."
                        class="w-full px-3 py-2 text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] placeholder-[#9CA3AF] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="address" class="block text-xs font-semibold text-[#252A34] mb-1.5">
                        Alamat Tempat Tinggal
                    </label>
                    <input
                        type="text"
                        name="address"
                        id="address"
                        value="{{ old('address') }}"
                        placeholder="Contoh: Jl. Harmoni No. 12"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] placeholder-[#9CA3AF] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                    >
                </div>

                <div>
                    <label for="photo" class="block text-xs font-semibold text-[#252A34] mb-1.5">
                        Foto Formal Kandidat
                    </label>
                    <input
                        type="file"
                        name="photo"
                        id="photo"
                        accept="image/jpeg,image/png,image/webp"
                        class="w-full text-xs text-[#6B7280] file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#1877F2]/10 file:text-[#1877F2] hover:file:bg-[#1877F2]/20"
                    >
                    <p class="text-[11px] text-[#6B7280] mt-1">Maks 2MB (JPG, PNG, WEBP).</p>
                </div>
            </div>

            <div>
                <label for="vision" class="block text-xs font-semibold text-[#252A34] mb-1.5">
                    Visi Kepemimpinan <span class="text-red-500">*</span>
                </label>
                <textarea
                    name="vision"
                    id="vision"
                    rows="3"
                    required
                    placeholder="Tuliskan visi kandidat secara ringkas dan padat..."
                    class="w-full px-3 py-2 text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] placeholder-[#9CA3AF] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                >{{ old('vision') }}</textarea>
            </div>

            <div>
                <label for="mission" class="block text-xs font-semibold text-[#252A34] mb-1.5">
                    Misi & Program Kerja <span class="text-red-500">*</span>
                </label>
                <textarea
                    name="mission"
                    id="mission"
                    rows="4"
                    required
                    placeholder="1. Program A&#10;2. Program B&#10;3. Program C..."
                    class="w-full px-3 py-2 text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] placeholder-[#9CA3AF] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                >{{ old('mission') }}</textarea>
            </div>

            <div>
                <label for="bio" class="block text-xs font-semibold text-[#252A34] mb-1.5">
                    Profil Singkat / Rekam Jejak (Opsional)
                </label>
                <textarea
                    name="bio"
                    id="bio"
                    rows="2"
                    placeholder="Pengalaman organisasi, profesi, atau latar belakang..."
                    class="w-full px-3 py-2 text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] placeholder-[#9CA3AF] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                >{{ old('bio') }}</textarea>
            </div>

            <div class="pt-4 border-t border-[#E5E7EB] flex items-center justify-end gap-2.5">
                <a href="{{ route('admin.elections.show', $election) }}" class="px-4 py-2 text-xs font-medium rounded-lg text-[#6B7280] hover:text-[#252A34] hover:bg-slate-100 transition">
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-4 py-2 text-xs font-semibold rounded-lg bg-[#1877F2] text-white hover:bg-[#166fe5] shadow-xs transition"
                >
                    Simpan Kandidat
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
