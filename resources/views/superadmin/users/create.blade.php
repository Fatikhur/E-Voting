@extends('layouts.app', ['title' => 'Tambah Pengguna Baru'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Tambah Pengguna Baru</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Daftarkan akun administrator, pengawas, atau akun warga baru.</p>
        </div>
        <a href="{{ route('superadmin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-[#6B7280] hover:text-[#252A34] transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-xs border border-[#E5E7EB] p-5 sm:p-6">
        <form action="{{ route('superadmin.users.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold text-[#252A34] mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                </div>

                <div>
                    <label for="username" class="block text-xs font-semibold text-[#252A34] mb-1">Username Unik *</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] placeholder-[#6B7280] focus:outline-none focus:border-[#1877F2]" placeholder="huruf_kecil_tanpa_spasi">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-semibold text-[#252A34] mb-1">Alamat Email (Opsional)</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold text-[#252A34] mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] placeholder-[#6B7280] focus:outline-none focus:border-[#1877F2]" placeholder="08...">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="role_name" class="block text-xs font-semibold text-[#252A34] mb-1">Peran (Role) *</label>
                    <select name="role_name" id="role_name" required class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                        <option value="">Pilih Peran Pengguna</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role_name') === $role->name ? 'selected' : '' }}>
                                {{ $role->display_name }} ({{ $role->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-[#252A34] mb-1">Kata Sandi Awal *</label>
                    <input type="password" name="password" id="password" required minlength="8" class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] placeholder-[#6B7280] focus:outline-none focus:border-[#1877F2]" placeholder="Minimal 8 karakter">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-[#E5E7EB]">
                <div>
                    <label for="village_id" class="block text-xs font-semibold text-[#252A34] mb-1">Kelurahan</label>
                    <select name="village_id" id="village_id" class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                        <option value="">Pusat / Seluruh Kelurahan</option>
                        @foreach($villages as $vil)
                            <option value="{{ $vil->id }}" {{ old('village_id') == $vil->id ? 'selected' : '' }}>
                                {{ $vil->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="rw_id" class="block text-xs font-semibold text-[#252A34] mb-1">Wilayah RW</label>
                    <select name="rw_id" id="rw_id" class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                        <option value="">Seluruh Wilayah RW</option>
                        @foreach($rws as $rw)
                            <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>
                                RW {{ $rw->rw_number }} ({{ $rw->name ?? 'Dusun' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-[#E5E7EB] flex items-center justify-end gap-2.5">
                <a href="{{ route('superadmin.users.index') }}" class="px-4 py-2 rounded-lg text-xs sm:text-sm font-medium text-[#252A34] hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold text-white bg-[#1877F2] hover:bg-[#166fe5] shadow-xs transition">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
