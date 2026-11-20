@extends('layouts.app', ['title' => 'Manajemen Wilayah RT/RW'])

@section('content')
<div class="space-y-6" x-data="{ openRwModal: false, openRtModal: false, activeRwId: null }">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Struktur Wilayah RT/RW</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Hierarki Kelurahan, Rukun Warga (RW), dan Rukun Tetangga (RT).</p>
        </div>
    </div>

    <!-- Tree View of Territories -->
    <div class="space-y-6">
        @foreach($villages as $village)
            <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden">
                <div class="p-4 sm:px-5 sm:py-4 bg-[#171B35] text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-[11px] text-[#2ECC71] font-mono font-semibold uppercase tracking-wider block">Kelurahan / Desa</span>
                        <h2 class="text-base sm:text-lg font-bold text-white">{{ $village->name }} (Kode: {{ $village->code }})</h2>
                        <p class="text-xs text-[#C5C9D4] mt-0.5">{{ $village->district }}, {{ $village->regency }} • Kode Pos {{ $village->postal_code ?? '-' }}</p>
                    </div>
                    <button @click="openRwModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-[#1877F2] hover:bg-[#166fe5] text-white transition self-start sm:self-auto shadow-xs">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Tambah RW</span>
                    </button>
                </div>

                <div class="p-5 sm:p-6 divide-y divide-[#E5E7EB]">
                    @forelse($village->rws as $rw)
                        <div class="py-4 first:pt-0 last:pb-0">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg bg-blue-50 text-[#1877F2] border border-blue-200 font-bold text-xs flex items-center justify-center">
                                        {{ $rw->rw_number }}
                                    </span>
                                    <div>
                                        <h3 class="font-semibold text-[#252A34] text-sm sm:text-base">RW {{ $rw->rw_number }} {{ $rw->name ? '- ' . $rw->name : '' }}</h3>
                                        <p class="text-xs text-[#6B7280]">{{ $rw->description ?? 'Tidak ada deskripsi' }}</p>
                                    </div>
                                </div>
                                <button @click="openRtModal = true; activeRwId = {{ $rw->id }}" class="text-xs font-medium px-2.5 py-1 rounded-lg border border-[#E5E7EB] text-[#252A34] hover:bg-slate-50 transition">
                                    + Tambah RT
                                </button>
                            </div>

                            <!-- List of RTs inside this RW -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2.5 pl-11">
                                @forelse($rw->rts as $rt)
                                    <div class="p-2.5 rounded-lg border border-[#E5E7EB] bg-slate-50 hover:bg-white hover:border-[#1877F2] transition-colors flex items-center justify-between">
                                        <div>
                                            <span class="font-semibold text-xs sm:text-sm text-[#252A34]">RT {{ $rt->rt_number }}</span>
                                            <span class="block text-[11px] text-[#6B7280]">{{ $rt->residents()->count() }} Warga</span>
                                        </div>
                                        <svg class="w-4 h-4 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                        </svg>
                                    </div>
                                @empty
                                    <div class="col-span-full text-xs text-[#6B7280] italic">Belum ada RT terdaftar pada RW ini.</div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-[#6B7280] text-xs sm:text-sm">Belum ada RW yang didaftarkan.</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal Tambah RW -->
    <div x-show="openRwModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" role="dialog" aria-modal="true">
        <div @click.away="openRwModal = false" class="bg-white rounded-xl max-w-md w-full p-5 sm:p-6 shadow-xl border border-[#E5E7EB]">
            <h3 class="text-base font-semibold text-[#252A34]">Daftarkan RW Baru</h3>
            <form action="{{ route('superadmin.territories.store-rw') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="village_id" value="{{ $villages->first()?->id }}">
                <div>
                    <label class="block text-xs font-semibold text-[#252A34] mb-1">Nomor RW *</label>
                    <input type="text" name="rw_number" required placeholder="Contoh: 03" class="w-full px-3 py-2 text-xs sm:text-sm border border-[#E5E7EB] rounded-lg bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#252A34] mb-1">Nama Lingkungan / Dusun</label>
                    <input type="text" name="name" placeholder="Contoh: Dusun Cemara" class="w-full px-3 py-2 text-xs sm:text-sm border border-[#E5E7EB] rounded-lg bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#252A34] mb-1">Deskripsi Wilayah</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 text-xs sm:text-sm border border-[#E5E7EB] rounded-lg bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]"></textarea>
                </div>
                <div class="flex justify-end gap-2.5 pt-3 border-t border-[#E5E7EB]">
                    <button type="button" @click="openRwModal = false" class="px-3.5 py-1.5 text-xs font-medium text-[#252A34] rounded-lg border border-[#E5E7EB] hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold text-white bg-[#1877F2] hover:bg-[#166fe5] rounded-lg shadow-xs transition">Simpan RW</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah RT -->
    <div x-show="openRtModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" role="dialog" aria-modal="true">
        <div @click.away="openRtModal = false" class="bg-white rounded-xl max-w-md w-full p-5 sm:p-6 shadow-xl border border-[#E5E7EB]">
            <h3 class="text-base font-semibold text-[#252A34]">Daftarkan RT Baru</h3>
            <form action="{{ route('superadmin.territories.store-rt') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="rw_id" :value="activeRwId">
                <div>
                    <label class="block text-xs font-semibold text-[#252A34] mb-1">Nomor RT *</label>
                    <input type="text" name="rt_number" required placeholder="Contoh: 04" class="w-full px-3 py-2 text-xs sm:text-sm border border-[#E5E7EB] rounded-lg bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[#252A34] mb-1">Nama Lingkungan (Opsional)</label>
                    <input type="text" name="name" placeholder="Contoh: Blok Dahlia" class="w-full px-3 py-2 text-xs sm:text-sm border border-[#E5E7EB] rounded-lg bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                </div>
                <div class="flex justify-end gap-2.5 pt-3 border-t border-[#E5E7EB]">
                    <button type="button" @click="openRtModal = false" class="px-3.5 py-1.5 text-xs font-medium text-[#252A34] rounded-lg border border-[#E5E7EB] hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold text-white bg-[#1877F2] hover:bg-[#166fe5] rounded-lg shadow-xs transition">Simpan RT</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
