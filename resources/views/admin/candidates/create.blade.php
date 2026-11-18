@extends('layouts.app', ['title' => 'Tambah Kandidat'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftarkan Kandidat Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Pemilihan: {{ $election->title }}</p>
        </div>
        <a href="{{ route('admin.elections.show', $election) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            ← Kembali ke Pemilihan
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('admin.candidates.store', $election) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="candidate_number" class="block text-sm font-semibold text-slate-700 mb-1">Nomor Urut *</label>
                    <input type="number" name="candidate_number" id="candidate_number" value="{{ old('candidate_number', $nextNumber) }}" required min="1" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 font-bold text-base text-blue-600">
                </div>

                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap & Gelar *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: H. Ahmad Fauzi, S.E." class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="address" class="block text-sm font-semibold text-slate-700 mb-1">Alamat Tempat Tinggal</label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}" placeholder="Contoh: Jl. Harmoni No. 12" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                </div>

                <div>
                    <label for="photo" class="block text-sm font-semibold text-slate-700 mb-1">Foto Formal Kandidat</label>
                    <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-[11px] text-slate-400 mt-1">Maks 2MB (JPG, PNG, WEBP).</p>
                </div>
            </div>

            <div>
                <label for="vision" class="block text-sm font-semibold text-slate-700 mb-1">Visi Kepemimpinan *</label>
                <textarea name="vision" id="vision" rows="3" required placeholder="Tuliskan visi kandidat secara ringkas dan padat..." class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">{{ old('vision') }}</textarea>
            </div>

            <div>
                <label for="mission" class="block text-sm font-semibold text-slate-700 mb-1">Misi & Program Kerja *</label>
                <textarea name="mission" id="mission" rows="4" required placeholder="1. Program A&#10;2. Program B&#10;3. Program C..." class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">{{ old('mission') }}</textarea>
            </div>

            <div>
                <label for="bio" class="block text-sm font-semibold text-slate-700 mb-1">Profil Singkat / Rekam Jejak (Opsional)</label>
                <textarea name="bio" id="bio" rows="2" placeholder="Pengalaman organisasi, profesi, atau latar belakang..." class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">{{ old('bio') }}</textarea>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.elections.show', $election) }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                    Simpan Kandidat
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
