@extends('layouts.app', ['title' => 'Manajemen Wilayah RT/RW'])

@section('content')
<div class="space-y-6" x-data="{ openRwModal: false, openRtModal: false, activeRwId: null }">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Struktur Wilayah RT/RW</h1>
            <p class="text-sm text-slate-500 mt-1">Hierarki Kelurahan, Rukun Warga (RW), dan Rukun Tetangga (RT).</p>
        </div>
    </div>

    <!-- Tree View of Territories -->
    <div class="space-y-6">
        @foreach($villages as $village)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 bg-slate-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-xs text-blue-300 font-mono font-bold uppercase tracking-wider block">Kelurahan / Desa</span>
                        <h2 class="text-xl font-extrabold text-white">{{ $village->name }} (Kode: {{ $village->code }})</h2>
                        <p class="text-xs text-slate-300 mt-0.5">{{ $village->district }}, {{ $village->regency }} • Kode Pos {{ $village->postal_code ?? '-' }}</p>
                    </div>
                    <button @click="openRwModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg bg-blue-600 hover:bg-blue-500 text-white transition self-start sm:self-auto">
                        <span>➕ Tambah RW</span>
                    </button>
                </div>

                <div class="p-6 divide-y divide-slate-100">
                    @forelse($village->rws as $rw)
                        <div class="py-4 first:pt-0 last:pb-0">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-800 font-extrabold text-sm flex items-center justify-center">
                                        {{ $rw->rw_number }}
                                    </span>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-base">RW {{ $rw->rw_number }} {{ $rw->name ? '— ' . $rw->name : '' }}</h3>
                                        <p class="text-xs text-slate-400">{{ $rw->description ?? 'Tidak ada deskripsi' }}</p>
                                    </div>
                                </div>
                                <button @click="openRtModal = true; activeRwId = {{ $rw->id }}" class="text-xs font-semibold px-2.5 py-1 rounded-md border border-slate-300 text-slate-600 hover:bg-slate-100 transition">
                                    + Tambah RT
                                </button>
                            </div>

                            <!-- List of RTs inside this RW -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2.5 pl-11">
                                @forelse($rw->rts as $rt)
                                    <div class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-white hover:border-blue-400 transition flex items-center justify-between">
                                        <div>
                                            <span class="font-bold text-sm text-slate-800">RT {{ $rt->rt_number }}</span>
                                            <span class="block text-[11px] text-slate-400">{{ $rt->residents()->count() }} Warga</span>
                                        </div>
                                        <span class="text-xs">🏡</span>
                                    </div>
                                @empty
                                    <div class="col-span-full text-xs text-slate-400 italic">Belum ada RT terdaftar pada RW ini.</div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-sm">Belum ada RW yang didaftarkan.</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal Tambah RW -->
    <div x-show="openRwModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div @click.away="openRwModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900">Daftarkan RW Baru</h3>
            <form action="{{ route('superadmin.territories.store-rw') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="village_id" value="{{ $villages->first()?->id }}">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor RW *</label>
                    <input type="text" name="rw_number" required placeholder="Contoh: 03" class="w-full px-3 py-2 text-sm border rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lingkungan / Dusun</label>
                    <input type="text" name="name" placeholder="Contoh: Dusun Cemara" class="w-full px-3 py-2 text-sm border rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Wilayah</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border rounded-lg"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" @click="openRwModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 rounded-lg">Simpan RW</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah RT -->
    <div x-show="openRtModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div @click.away="openRtModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900">Daftarkan RT Baru</h3>
            <form action="{{ route('superadmin.territories.store-rt') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="rw_id" :value="activeRwId">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor RT *</label>
                    <input type="text" name="rt_number" required placeholder="Contoh: 04" class="w-full px-3 py-2 text-sm border rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lingkungan (Opsional)</label>
                    <input type="text" name="name" placeholder="Contoh: Blok Dahlia" class="w-full px-3 py-2 text-sm border rounded-lg">
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" @click="openRtModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 rounded-lg">Simpan RT</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
