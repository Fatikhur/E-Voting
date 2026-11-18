@extends('layouts.app', ['title' => 'Jejak Audit & Integritas Rantai Hash'])

@section('content')
<div class="space-y-6" x-data="{ openCheckpointModal: false }">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Audit Trail & Integritas Kriptografis</h1>
            <p class="text-sm text-slate-500 mt-1">Rantai log tak-terbantahkan (*tamper-evident hash chaining*) untuk transparansi pemilihan.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openCheckpointModal = true" class="px-4 py-2 text-sm font-semibold rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 shadow-sm transition">
                ✍️ Tanda Tangani Checkpoint
            </button>
            <form action="{{ route('superadmin.audit.verify') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm font-bold rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
                    🛡️ Pindai & Uji Integritas Rantai
                </button>
            </form>
        </div>
    </div>

    <!-- Status Box -->
    <div class="p-5 rounded-2xl border {{ $integrityStatus['is_valid'] ? 'bg-emerald-50 border-emerald-300 text-emerald-900' : 'bg-red-50 border-red-300 text-red-900' }} shadow-sm">
        <div class="flex items-start gap-4">
            <span class="text-3xl">{{ $integrityStatus['is_valid'] ? '✅' : '🚨' }}</span>
            <div class="flex-1">
                <h3 class="font-bold text-lg">
                    Hasil Verifikasi Rantai Hash: {{ $integrityStatus['is_valid'] ? 'SEMPURNA & TIDAK DIMANIPULASI' : 'INTEGRITAS TERPUTUS' }}
                </h3>
                <p class="text-sm mt-1 {{ $integrityStatus['is_valid'] ? 'text-emerald-700' : 'text-red-700' }}">
                    {{ $integrityStatus['message'] }}
                </p>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-mono bg-white/80 p-3 rounded-xl border border-slate-200">
                    <div>Total Log Terverifikasi: <span class="font-bold">{{ $integrityStatus['total_verified'] }} baris</span></div>
                    <div>Hash Rantai Terakhir: <span class="font-bold">{{ substr($integrityStatus['latest_hash'], 0, 24) }}...</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkpoints List -->
    @if($checkpoints->isNotEmpty())
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-800 text-sm mb-3">Checkpoint Tanda Tangan Integritas Berkala</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($checkpoints as $cp)
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 text-xs space-y-1">
                        <div class="flex items-center justify-between font-bold text-slate-800">
                            <span>Checkpoint #{{ $cp->checkpoint_sequence }}</span>
                            <span class="text-[11px] text-slate-400 font-normal">{{ $cp->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="text-slate-600 italic">"{{ $cp->signature_note }}"</p>
                        <div class="pt-1 text-[11px] text-slate-400 font-mono">
                            Oleh: <span class="font-semibold text-slate-700">@ {{ $cp->signer->username }}</span><br>
                            Hash: {{ substr($cp->checkpoint_hash, 0, 16) }}...
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Audit Log Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <form method="GET" action="{{ route('superadmin.audit.index') }}" class="flex flex-wrap items-center gap-3 w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari deskripsi / aksi..." class="px-3.5 py-2 text-xs rounded-lg border flex-1 min-w-[200px]">
                <select name="event_type" class="px-3 py-2 text-xs rounded-lg border">
                    <option value="">Semua Kategori Event</option>
                    <option value="auth" {{ request('event_type') === 'auth' ? 'selected' : '' }}>Auth</option>
                    <option value="voting" {{ request('event_type') === 'voting' ? 'selected' : '' }}>Voting</option>
                    <option value="resident" {{ request('event_type') === 'resident' ? 'selected' : '' }}>Resident</option>
                    <option value="election" {{ request('event_type') === 'election' ? 'selected' : '' }}>Election</option>
                    <option value="candidate" {{ request('event_type') === 'candidate' ? 'selected' : '' }}>Candidate</option>
                    <option value="approval" {{ request('event_type') === 'approval' ? 'selected' : '' }}>Approval</option>
                    <option value="supervision" {{ request('event_type') === 'supervision' ? 'selected' : '' }}>Supervision</option>
                    <option value="security" {{ request('event_type') === 'security' ? 'selected' : '' }}>Security</option>
                </select>
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-slate-800 text-white rounded-lg">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">ID & Waktu</th>
                        <th class="px-4 py-3">Aktor</th>
                        <th class="px-4 py-3">Event & Aksi</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3 font-mono">Hash Rantai (SHA-256)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-sans">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3 font-mono whitespace-nowrap">
                                <span class="font-bold text-slate-900">#{{ $log->id }}</span><br>
                                <span class="text-[11px] text-slate-400">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-bold text-slate-800">@ {{ $log->username }}</span><br>
                                <span class="text-[10px] text-slate-400">IP: {{ $log->ip_address }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $log->event_type }}
                                </span>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">{{ $log->action }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-slate-800 font-medium leading-relaxed">{{ $log->description }}</p>
                                @if($log->metadata)
                                    <details class="mt-1 text-[11px] text-slate-500 cursor-pointer">
                                        <summary class="font-mono text-blue-600 hover:underline">Lihat Metadata JSON</summary>
                                        <pre class="mt-1 p-2 bg-slate-900 text-emerald-400 rounded-lg text-[10px] overflow-x-auto">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </details>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-[10px] text-slate-400 whitespace-nowrap">
                                <div>Prev: {{ substr($log->prev_hash, 0, 14) }}...</div>
                                <div class="font-bold text-slate-700">Curr: {{ substr($log->current_hash, 0, 14) }}...</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">Tidak ada catatan audit yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- Modal Checkpoint -->
    <div x-show="openCheckpointModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div @click.away="openCheckpointModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-base font-bold text-slate-900">Tanda Tangani Checkpoint Audit</h3>
            <p class="text-xs text-slate-500 mt-1">Checkpoint akan mengunci hash audit log terkini untuk pembuktian verifikasi independen di luar database.</p>
            <form action="{{ route('superadmin.audit.checkpoint') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Penandatanganan</label>
                    <textarea name="notes" rows="3" required placeholder="Contoh: Verifikasi audit sebelum tahapan pencoblosan dimulai..." class="w-full px-3 py-2 text-sm border rounded-lg"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openCheckpointModal = false" class="px-3 py-1.5 text-xs text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 rounded-lg">Tanda Tangani</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
