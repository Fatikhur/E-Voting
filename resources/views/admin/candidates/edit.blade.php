@extends('layouts.app', ['title' => 'Edit Profil Kandidat'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-[#252A34] tracking-tight">Edit Profil Kandidat</h1>
            <p class="text-xs text-[#6B7280] mt-0.5">Kandidat No. Urut {{ $candidate->candidate_number }} - {{ $candidate->name }}</p>
        </div>
        <a href="{{ route('admin.elections.show', $candidate->election) }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-[#6B7280] hover:text-[#252A34] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Candidate Edit Form Card -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs p-6">
        <form action="{{ route('admin.candidates.update', $candidate) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-[#252A34] mb-1.5">Nomor Urut</label>
                    <input
                        type="text"
                        disabled
                        value="{{ $candidate->candidate_number }}"
                        class="w-full px-3 py-2 text-sm font-semibold rounded-lg border border-[#E5E7EB] bg-slate-100 text-[#6B7280] cursor-not-allowed"
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
                        value="{{ old('name', $candidate->name) }}"
                        required
                        class="w-full px-3 py-2 text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="address" class="block text-xs font-semibold text-[#252A34] mb-1.5">Alamat Tempat Tinggal</label>
                    <input
                        type="text"
                        name="address"
                        id="address"
                        value="{{ old('address', $candidate->address) }}"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                    >
                </div>

                <div>
                    <label for="photo" class="block text-xs font-semibold text-[#252A34] mb-1.5">Ganti Foto Kandidat</label>
                    <input
                        type="file"
                        name="photo"
                        id="photo"
                        accept="image/jpeg,image/png,image/webp"
                        class="w-full text-xs text-[#6B7280] file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#1877F2]/10 file:text-[#1877F2] hover:file:bg-[#1877F2]/20"
                    >
                    @if($candidate->photo_path)
                        <p class="text-[11px] text-[#6B7280] mt-1">Biarkan kosong jika tidak ingin mengubah foto saat ini.</p>
                    @endif
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
                    class="w-full px-3 py-2 text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                >{{ old('vision', $candidate->vision) }}</textarea>
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
                    class="w-full px-3 py-2 text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                >{{ old('mission', $candidate->mission) }}</textarea>
            </div>

            <div>
                <label for="bio" class="block text-xs font-semibold text-[#252A34] mb-1.5">Profil Singkat / Rekam Jejak</label>
                <textarea
                    name="bio"
                    id="bio"
                    rows="2"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:ring-2 focus:ring-[#1877F2]/20 focus:border-[#1877F2]"
                >{{ old('bio', $candidate->bio) }}</textarea>
            </div>

            <div class="pt-4 border-t border-[#E5E7EB] flex items-center justify-end gap-2.5">
                <a href="{{ route('admin.elections.show', $candidate->election) }}" class="px-4 py-2 text-xs font-medium rounded-lg text-[#6B7280] hover:text-[#252A34] hover:bg-slate-100 transition">
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-4 py-2 text-xs font-semibold rounded-lg bg-[#1877F2] text-white hover:bg-[#166fe5] shadow-xs transition"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
