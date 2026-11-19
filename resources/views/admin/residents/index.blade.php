@extends('layouts.app', ['title' => 'Manajemen Data Warga'])

@section('content')
<div class="space-y-6" x-data="{ openImportModal: false }">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Data Warga & Calon Pemilih</h1>
            <p class="text-sm text-slate-500 mt-1">Pengelolaan data kependudukan terenkripsi dan penentuan kelayakan hak suara.</p>
        </div>
        <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
            <a href="{{ route('admin.residents.export', request()->only(['rw_id', 'rt_id'])) }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 px-3.5 py-2 rounded-xl text-sm font-semibold border border-slate-300 shadow-sm transition">
                <span>📥 Ekspor CSV</span>
            </a>
            <button @click="openImportModal = true" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 px-3.5 py-2 rounded-xl text-sm font-semibold border border-slate-300 shadow-sm transition">
                <span>📤 Impor CSV</span>
            </button>
            <a href="{{ route('admin.residents.create') }}" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm transition">
                <span>➕ Tambah Warga</span>
            </a>
        </div>
    </div>

    <!-- Import Errors Banner if any -->
    @if(session('import_errors') && count(session('import_errors')) > 0)
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg text-xs space-y-1">
            <p class="font-bold text-amber-900">Catatan Baris Impor yang Dilewati:</p>
            <ul class="list-disc list-inside text-amber-800 space-y-0.5">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.residents.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[220px]">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama, NIK (16 digit), atau alamat..."
                    class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500"
                >
            </div>
            <div>
                <select name="rw_id" class="px-3 py-2 text-sm rounded-lg border border-slate-300">
                    <option value="">Semua RW</option>
                    @foreach($rws as $rw)
                        <option value="{{ $rw->id }}" {{ request('rw_id') == $rw->id ? 'selected' : '' }}>
                            RW {{ $rw->rw_number }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="px-3 py-2 text-sm rounded-lg border border-slate-300">
                    <option value="">Semua Status Warga</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="moved" {{ request('status') === 'moved' ? 'selected' : '' }}>Pindah</option>
                    <option value="deceased" {{ request('status') === 'deceased' ? 'selected' : '' }}>Meninggal</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-lg bg-slate-800 text-white hover:bg-slate-900">
                Filter
            </button>
            @if(request()->hasAny(['search', 'rw_id', 'status']))
                <a href="{{ route('admin.residents.index') }}" class="px-3 py-2 text-sm text-slate-500 hover:text-slate-800">
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
                        <th class="px-5 py-3.5">Nama & NIK (Masked)</th>
                        <th class="px-5 py-3.5">L/P & Usia</th>
                        <th class="px-5 py-3.5">Alamat & Wilayah</th>
                        <th class="px-5 py-3.5">Status Warga</th>
                        <th class="px-5 py-3.5">Hak Pilih</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($residents as $res)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $res->name }}</div>
                                <div class="text-xs font-mono text-slate-500 tracking-wider">{{ $res->nik_masked }}</div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $res->gender === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                                    {{ $res->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $res->age ? $res->age . ' Tahun' : '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="text-xs text-slate-800 font-medium">{{ $res->address }}</div>
                                <div class="text-[11px] text-slate-400 font-semibold">
                                    RT {{ $res->rt?->rt_number ?? '-' }} / RW {{ $res->rw?->rw_number ?? '-' }}
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $res->status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($res->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if($res->is_eligible_voter && $res->status === 'active' && (!$res->age || $res->age >= 17))
                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-700 font-semibold">
                                        <span class="text-emerald-500 font-bold">✓</span> Memenuhi Syarat
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs text-red-600 font-semibold">
                                        <span class="text-red-500 font-bold">✗</span> Tidak Memenuhi
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.residents.edit', $res) }}" class="p-1.5 rounded-lg border text-slate-600 hover:bg-slate-100 text-xs font-semibold">
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
                                                class="p-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-400 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">Tidak ada data warga yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $residents->links() }}
        </div>
    </div>

    <!-- Modal Import CSV -->
    <div x-show="openImportModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div @click.away="openImportModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900">Impor Data Warga via CSV</h3>
            <p class="text-xs text-slate-500 mt-1">Unggah file CSV dengan kolom: NIK, Nama, Tanggal_Lahir, Jenis_Kelamin (L/P), Alamat, No_RW, No_RT, No_Telp.</p>

            <form action="{{ route('admin.residents.import') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-blue-400 transition bg-slate-50">
                    <input type="file" name="file" accept=".csv,.txt" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openImportModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 rounded-lg shadow-sm">Mulai Impor</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
