@extends('layouts.app', ['title' => 'Manajemen Pengguna'])

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Pengguna</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola akun Super Admin, Admin Panitia, Pengawas, dan Warga Pemilih.</p>
        </div>
        <div>
            <a href="{{ route('superadmin.users.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm transition">
                <span>➕ Tambah Pengguna Baru</span>
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
        <form method="GET" action="{{ route('superadmin.users.index') }}" class="flex flex-wrap items-center gap-3 w-full">
            <div class="flex-1 min-w-[200px]">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama, username, atau email..."
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500"
                >
            </div>
            <div>
                <select name="role" class="px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Peran (Role)</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                            {{ $role->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900">
                Filter
            </button>
            @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('superadmin.users.index') }}" class="px-3 py-2 text-sm text-slate-500 hover:text-slate-800">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Nama & Username</th>
                        <th class="px-5 py-3.5">Peran (Role)</th>
                        <th class="px-5 py-3.5">Wilayah</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Login Terakhir</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                <div class="text-xs text-slate-400">@ {{ $user->username }} • {{ $user->email ?? 'Tanpa email' }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $roleBadge = match($user->role_name) {
                                        'superadmin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'admin' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'pengawas' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        default => 'bg-blue-100 text-blue-800 border-blue-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $roleBadge }}">
                                    {{ ucfirst($user->role_name) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-500">
                                @if($user->rw)
                                    RW {{ $user->rw->rw_number }} {{ $user->rt ? '/ RT ' . $user->rt->rt_number : '' }}
                                @else
                                    <span class="text-slate-400">Pusat/Semua</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user->status === 'active' ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-xs text-slate-400">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah' }}
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2" x-data="{ openReset: false }">
                                    <!-- Toggle Active Button -->
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('superadmin.users.toggle-status', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            @if($user->status === 'active')
                                                <button type="button"
                                                        data-confirm
                                                        data-confirm-type="warning"
                                                        data-confirm-title="Nonaktifkan Akun Pengguna"
                                                        data-confirm-message="Apakah Anda yakin ingin menonaktifkan akun {{ $user->name }} ({{ '@' . $user->username }})?"
                                                        data-confirm-detail="Pengguna ini tidak akan dapat masuk ke sistem sampai akun diaktifkan kembali oleh Super Administrator."
                                                        data-confirm-button="Ya, Nonaktifkan Akun"
                                                        data-confirm-cancel="Batal"
                                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-red-200 text-red-600 hover:bg-red-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-400 transition">
                                                    Nonaktifkan
                                                </button>
                                            @else
                                                <button type="button"
                                                        data-confirm
                                                        data-confirm-type="info"
                                                        data-confirm-title="Aktifkan Akun Pengguna"
                                                        data-confirm-message="Aktifkan kembali akun pengguna {{ $user->name }} ({{ '@' . $user->username }})?"
                                                        data-confirm-detail="Pengguna ini akan segera memiliki hak akses untuk login kembali ke sistem."
                                                        data-confirm-button="Ya, Aktifkan Akun"
                                                        data-confirm-cancel="Batal"
                                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-emerald-200 text-emerald-600 hover:bg-emerald-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 transition">
                                                    Aktifkan
                                                </button>
                                            @endif
                                        </form>

                                        <!-- Reset Password Modal Trigger -->
                                        <button @click="openReset = true" class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-100 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">
                                            Reset Sandi
                                        </button>

                                        <!-- Styled Reset Password Modal -->
                                        <div x-cloak x-show="openReset" @keydown.window.escape="openReset = false" class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
                                            <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" @click="openReset = false" aria-hidden="true"></div>
                                            <div @click.stop class="relative bg-white rounded-2xl max-w-sm w-full p-6 text-left shadow-2xl border border-slate-200 z-10 transform transition-all">
                                                <div class="flex items-center gap-3 mb-4">
                                                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h3 class="text-base font-bold text-slate-900">Reset Kata Sandi</h3>
                                                        <p class="text-xs text-slate-500">{{ $user->name }} ({{ '@' . $user->username }})</p>
                                                    </div>
                                                </div>
                                                <form action="{{ route('superadmin.users.reset-password', $user) }}" method="POST" class="space-y-4">
                                                    @csrf
                                                    <div>
                                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru</label>
                                                        <input type="password" name="new_password" required minlength="8" placeholder="Minimal 8 karakter" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    </div>
                                                    <div class="flex justify-end gap-2.5 pt-2 border-t border-slate-100">
                                                        <button type="button" @click="openReset = false" class="px-3.5 py-2 text-xs font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-slate-50 transition">Batal</button>
                                                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm transition">Simpan Sandi Baru</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium">Akun Anda</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">Tidak ada data pengguna yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

</div>
@endsection
