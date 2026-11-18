@extends('layouts.app', ['title' => 'Tambah Pengguna Baru'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tambah Pengguna Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Daftarkan akun administrator, pengawas, atau akun warga baru.</p>
        </div>
        <a href="{{ route('superadmin.users.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            ← Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form action="{{ route('superadmin.users.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-700 mb-1">Username Unik *</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 text-sm" placeholder="huruf_kecil_tanpa_spasi">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Alamat Email (Opsional)</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 text-sm" placeholder="08...">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="role_name" class="block text-sm font-semibold text-slate-700 mb-1">Peran (Role) *</label>
                    <select name="role_name" id="role_name" required class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">Pilih Peran Pengguna</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role_name') === $role->name ? 'selected' : '' }}>
                                {{ $role->display_name }} ({{ $role->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Kata Sandi Awal *</label>
                    <input type="password" name="password" id="password" required minlength="8" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 text-sm" placeholder="Minimal 8 karakter">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-3 border-t border-slate-100">
                <div>
                    <label for="village_id" class="block text-sm font-semibold text-slate-700 mb-1">Kelurahan</label>
                    <select name="village_id" id="village_id" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">Pusat / Seluruh Kelurahan</option>
                        @foreach($villages as $vil)
                            <option value="{{ $vil->id }}" {{ old('village_id') == $vil->id ? 'selected' : '' }}>
                                {{ $vil->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="rw_id" class="block text-sm font-semibold text-slate-700 mb-1">Wilayah RW</label>
                    <select name="rw_id" id="rw_id" class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">Seluruh Wilayah RW</option>
                        @foreach($rws as $rw)
                            <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>
                                RW {{ $rw->rw_number }} ({{ $rw->name ?? 'Dusun' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('superadmin.users.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
