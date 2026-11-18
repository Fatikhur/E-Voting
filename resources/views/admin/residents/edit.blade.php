@extends('layouts.app', ['title' => 'Edit Data Warga'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Data Warga</h1>
            <p class="text-sm text-slate-500 mt-1">Perbarui informasi kependudukan warga {{ $resident->name }}.</p>
        </div>
        <a href="{{ route('admin.residents.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            ← Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('admin.residents.update', $resident) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">NIK (Terenkripsi)</label>
                    <input type="text" disabled value="{{ $resident->nik_masked }}" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-200 bg-slate-100 text-slate-500 text-sm font-mono cursor-not-allowed">
                    <p class="text-[11px] text-slate-400 mt-1">NIK tidak dapat diubah langsung demi menjaga integritas hash.</p>
                </div>

                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $resident->name) }}" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="gender" class="block text-sm font-semibold text-slate-700 mb-1">Jenis Kelamin *</label>
                    <select name="gender" id="gender" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                        <option value="L" {{ old('gender', $resident->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $resident->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label for="birth_place" class="block text-sm font-semibold text-slate-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_place" id="birth_place" value="{{ old('birth_place', $resident->birth_place) }}" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                </div>

                <div>
                    <label for="birth_date" class="block text-sm font-semibold text-slate-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $resident->birth_date?->format('Y-m-d')) }}" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                </div>
            </div>

            <div>
                <label for="address" class="block text-sm font-semibold text-slate-700 mb-1">Alamat Tempat Tinggal *</label>
                <textarea name="address" id="address" rows="2" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">{{ old('address', $resident->address) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label for="village_id" class="block text-sm font-semibold text-slate-700 mb-1">Kelurahan *</label>
                    <select name="village_id" id="village_id" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                        @foreach($villages as $vil)
                            <option value="{{ $vil->id }}" {{ old('village_id', $resident->village_id) == $vil->id ? 'selected' : '' }}>{{ $vil->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="rw_id" class="block text-sm font-semibold text-slate-700 mb-1">RW *</label>
                    <select name="rw_id" id="rw_id" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                        @foreach($rws as $rw)
                            <option value="{{ $rw->id }}" {{ old('rw_id', $resident->rw_id) == $rw->id ? 'selected' : '' }}>RW {{ $rw->rw_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="rt_id" class="block text-sm font-semibold text-slate-700 mb-1">RT *</label>
                    <select name="rt_id" id="rt_id" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                        @foreach($rts as $rt)
                            <option value="{{ $rt->id }}" {{ old('rt_id', $resident->rt_id) == $rt->id ? 'selected' : '' }}>RT {{ $rt->rt_number }} (RW {{ $rt->rw?->rw_number }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-3 border-t border-slate-100">
                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1">Nomor Telepon</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $resident->phone) }}" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-700 mb-1">Status Kependudukan *</label>
                    <select name="status" id="status" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                        <option value="active" {{ old('status', $resident->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="moved" {{ old('status', $resident->status) === 'moved' ? 'selected' : '' }}>Pindah</option>
                        <option value="deceased" {{ old('status', $resident->status) === 'deceased' ? 'selected' : '' }}>Meninggal</option>
                    </select>
                </div>

                <div>
                    <label for="is_eligible_voter" class="block text-sm font-semibold text-slate-700 mb-1">Status Hak Pilih *</label>
                    <select name="is_eligible_voter" id="is_eligible_voter" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm">
                        <option value="1" {{ old('is_eligible_voter', $resident->is_eligible_voter) == '1' ? 'selected' : '' }}>Memenuhi Syarat (Eligible)</option>
                        <option value="0" {{ old('is_eligible_voter', $resident->is_eligible_voter) == '0' ? 'selected' : '' }}>Tidak Memenuhi Syarat</option>
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.residents.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">
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
