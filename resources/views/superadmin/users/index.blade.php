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
                                            <button type="submit" onclick="return confirm('Ubah status aktif akun ini?')" class="px-2.5 py-1 text-xs font-medium rounded-lg border {{ $user->status === 'active' ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}">
                                                {{ $user->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>

                                        <!-- Reset Password Modal Trigger -->
                                        <button @click="openReset = true" class="px-2.5 py-1 text-xs font-medium rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100">
                                            Reset Sandi
                                        </button>

                                        <!-- Simple Reset Modal -->
                                        <div x-show="openReset" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
                                            <div @click.away="openReset = false" class="bg-white rounded-2xl max-w-sm w-full p-6 text-left shadow-2xl">
                                                <h3 class="text-base font-bold text-slate-800">Reset Sandi: @ {{ $user->username }}</h3>
                                                <form action="{{ route('superadmin.users.reset-password', $user) }}" method="POST" class="mt-4 space-y-3">
                                                    @csrf
                                                    <input type="password" name="new_password" required minlength="8" placeholder="Sandi baru (min 8 char)" class="w-full px-3 py-2 text-sm border rounded-lg">
                                                    <div class="flex justify-end gap-2 pt-2">
                                                        <button type="button" @click="openReset = false" class="px-3 py-1.5 text-xs text-slate-500">Batal</button>
                                                        <button type="submit" class="px-3.5 py-1.5 text-xs font-bold text-white bg-blue-600 rounded-lg">Reset</button>
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
