@extends('layouts.app', ['title' => 'Edit Profil Kandidat'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Profil Kandidat</h1>
            <p class="text-sm text-slate-500 mt-1">Kandidat No. Urut {{ $candidate->candidate_number }} — {{ $candidate->name }}</p>
        </div>
        <a href="{{ route('admin.elections.show', $candidate->election) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            ← Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('admin.candidates.update', $candidate) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor Urut</label>
                    <input type="text" disabled value="{{ $candidate->candidate_number }}" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-200 bg-slate-100 font-bold text-base text-slate-500 cursor-not-allowed">
                </div>

                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap & Gelar *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $candidate->name) }}" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="address" class="block text-sm font-semibold text-slate-700 mb-1">Alamat Tempat Tinggal</label>
                    <input type="text" name="address" id="address" value="{{ old('address', $candidate->address) }}" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                </div>

                <div>
                    <label for="photo" class="block text-sm font-semibold text-slate-700 mb-1">Ganti Foto Kandidat</label>
                    <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
            </div>

            <div>
                <label for="vision" class="block text-sm font-semibold text-slate-700 mb-1">Visi Kepemimpinan *</label>
                <textarea name="vision" id="vision" rows="3" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">{{ old('vision', $candidate->vision) }}</textarea>
            </div>

            <div>
                <label for="mission" class="block text-sm font-semibold text-slate-700 mb-1">Misi & Program Kerja *</label>
                <textarea name="mission" id="mission" rows="4" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">{{ old('mission', $candidate->mission) }}</textarea>
            </div>

            <div>
                <label for="bio" class="block text-sm font-semibold text-slate-700 mb-1">Profil Singkat / Rekam Jejak</label>
                <textarea name="bio" id="bio" rows="2" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">{{ old('bio', $candidate->bio) }}</textarea>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.elections.show', $candidate->election) }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
