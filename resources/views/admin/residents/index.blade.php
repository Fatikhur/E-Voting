@extends('layouts.app', ['title' => 'Manajemen Data Warga'])

@section('content')
<div class="space-y-6" x-data="{ openImportModal: false }">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Data Warga & Calon Pemilih</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Pengelolaan data kependudukan terenkripsi dan penentuan kelayakan hak suara.</p>
        </div>
        <div class="flex items-center gap-2 sm:gap-2.5 flex-wrap">
            <a href="{{ route('admin.residents.export', request()->only(['rw_id', 'rt_id'])) }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-[#252A34] px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold border border-[#E5E7EB] shadow-xs transition">
                <svg class="w-4 h-4 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <span>Ekspor CSV</span>
            </a>
            <button @click="openImportModal = true" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-[#252A34] px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold border border-[#E5E7EB] shadow-xs transition">
                <svg class="w-4 h-4 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                <span>Impor CSV</span>
            </button>
            <a href="{{ route('admin.residents.create') }}" class="inline-flex items-center gap-1.5 bg-[#1877F2] hover:bg-[#166fe5] text-white px-3.5 py-2 rounded-lg text-xs sm:text-sm font-semibold shadow-xs transition">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Tambah Warga</span>
            </a>
        </div>
    </div>

    <!-- Import Errors Banner if any -->
    @if(session('import_errors') && count(session('import_errors')) > 0)
        <div class="bg-amber-50 border border-amber-200 p-4 rounded-xl text-xs space-y-1 shadow-xs">
            <p class="font-semibold text-amber-900">Catatan Baris Impor yang Dilewati:</p>
            <ul class="list-disc list-inside text-amber-800 space-y-0.5">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-xl border border-[#E5E7EB] shadow-xs">
        <form method="GET" action="{{ route('admin.residents.index') }}" class="flex flex-wrap items-center gap-2.5">
            <div class="flex-1 min-w-[220px]">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama, NIK (16 digit), atau alamat..."
                    class="w-full px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] placeholder-[#6B7280] focus:border-[#1877F2] focus:ring-1 focus:ring-[#1877F2] focus:outline-none"
                >
            </div>
            <div>
                <select name="rw_id" class="px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:border-[#1877F2] focus:ring-1 focus:ring-[#1877F2] focus:outline-none">
                    <option value="">Semua RW</option>
                    @foreach($rws as $rw)
                        <option value="{{ $rw->id }}" {{ request('rw_id') == $rw->id ? 'selected' : '' }}>
                            RW {{ $rw->rw_number }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:border-[#1877F2] focus:ring-1 focus:ring-[#1877F2] focus:outline-none">
                    <option value="">Semua Status Warga</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="moved" {{ request('status') === 'moved' ? 'selected' : '' }}>Pindah</option>
                    <option value="deceased" {{ request('status') === 'deceased' ? 'selected' : '' }}>Meninggal</option>
                </select>
            </div>
            <button type="submit" class="px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-[#252A34] text-white hover:bg-slate-800 transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'rw_id', 'status']))
                <a href="{{ route('admin.residents.index') }}" class="px-3 py-2 text-xs sm:text-sm text-[#6B7280] hover:text-[#252A34] transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-[#252A34]">
                <thead class="bg-slate-50 text-[#6B7280] text-[11px] sm:text-xs uppercase font-semibold border-b border-[#E5E7EB]">
                    <tr>
                        <th class="px-5 py-3">Nama & NIK (Masked)</th>
                        <th class="px-5 py-3">L/P & Usia</th>
                        <th class="px-5 py-3">Alamat & Wilayah</th>
                        <th class="px-5 py-3">Status Warga</th>
                        <th class="px-5 py-3">Hak Pilih</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse($residents as $res)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-[#252A34]">{{ $res->name }}</div>
                                <div class="text-xs font-mono text-[#6B7280] tracking-wider">{{ $res->nik_masked }}</div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium border {{ $res->gender === 'L' ? 'bg-blue-50 text-[#1877F2] border-blue-200' : 'bg-pink-50 text-pink-700 border-pink-200' }}">
                                    {{ $res->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                                <div class="text-xs text-[#6B7280] mt-0.5">{{ $res->age ? $res->age . ' Tahun' : '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="text-xs text-[#252A34] font-medium">{{ $res->address }}</div>
                                <div class="text-[11px] text-[#6B7280]">
                                    RT {{ $res->rt?->rt_number ?? '-' }} / RW {{ $res->rw?->rw_number ?? '-' }}
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium border {{ $res->status === 'active' ? 'bg-emerald-50 text-[#269B68] border-emerald-200' : 'bg-slate-100 text-[#6B7280] border-slate-200' }}">
                                    {{ ucfirst($res->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if($res->is_eligible_voter && $res->status === 'active' && (!$res->age || $res->age >= 17))
                                    <span class="inline-flex items-center gap-1.5 text-xs text-[#269B68] font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                        </svg>
                                        <span>Memenuhi Syarat</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs text-red-600 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span>Tidak Memenuhi</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.residents.edit', $res) }}" class="px-2.5 py-1 rounded-lg border border-[#E5E7EB] text-[#252A34] hover:bg-slate-50 text-xs font-medium transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.residents.destroy', $res) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                data-confirm
                                                data-confirm-type="danger"
                                                data-confirm-title="Hapus Data Warga"
                                                data-confirm-message="Apakah Anda yakin ingin menghapus data warga {{ $res->name }}?"
                                                data-confirm-detail="NIK: {{ $res->nik }} (Tindakan ini bersifat permanen dan akan menghapus warga dari basis data kependudukan)."
                                                data-confirm-button="Ya, Hapus Permanen"
                                                data-confirm-cancel="Batal"
                                                class="px-2.5 py-1 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-400 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-[#6B7280] text-xs sm:text-sm">Tidak ada data warga yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[#E5E7EB]">
            {{ $residents->links() }}
        </div>
    </div>

    <!-- Modal Import CSV -->
    <div x-show="openImportModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" role="dialog" aria-modal="true">
        <div @click.away="openImportModal = false" class="bg-white rounded-xl max-w-lg w-full p-5 sm:p-6 shadow-xl border border-[#E5E7EB]">
            <h3 class="text-base font-semibold text-[#252A34]">Impor Data Warga via CSV</h3>
            <p class="text-xs text-[#6B7280] mt-1">Unggah file CSV dengan kolom: NIK, Nama, Tanggal_Lahir, Jenis_Kelamin (L/P), Alamat, No_RW, No_RT, No_Telp.</p>

            <form action="{{ route('admin.residents.import') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <div class="border-2 border-dashed border-[#E5E7EB] rounded-lg p-5 text-center hover:border-[#1877F2] transition bg-slate-50">
                    <input type="file" name="file" accept=".csv,.txt" required class="w-full text-xs text-[#6B7280] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#1877F2] hover:file:bg-blue-100">
                </div>
                <div class="flex justify-end gap-2.5 pt-2">
                    <button type="button" @click="openImportModal = false" class="px-3.5 py-2 text-xs font-medium text-[#252A34] rounded-lg border border-[#E5E7EB] hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-[#1877F2] hover:bg-[#166fe5] rounded-lg shadow-xs transition">Mulai Impor</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
