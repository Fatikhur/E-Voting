@extends('layouts.app', ['title' => 'Pengawasan Audit Trail'])

@section('content')
<div class="space-y-6" x-data="{ openCheckpointModal: false }">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Audit Trail Pengawas Independen</h1>
            <p class="text-sm text-slate-500 mt-1">Verifikasi integritas rantai hash kriptografis untuk mendeteksi manipulasi database.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="openCheckpointModal = true" class="px-4 py-2 text-sm font-semibold rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 shadow-sm transition">
                ✍️ Buat Checkpoint Pengawas
            </button>
            <form action="{{ route('pengawas.audit.verify') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm font-bold rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
                    🛡️ Pindai Integritas Rantai
                </button>
            </form>
        </div>
    </div>

    <!-- Integrity Status Banner -->
    <div class="p-5 rounded-2xl border {{ $integrityStatus['is_valid'] ? 'bg-emerald-50 border-emerald-300 text-emerald-950' : 'bg-red-50 border-red-300 text-red-950' }} shadow-sm">
        <div class="flex items-start gap-4">
            <span class="text-3xl">{{ $integrityStatus['is_valid'] ? '🛡️' : '🚨' }}</span>
            <div class="flex-1">
                <h3 class="font-bold text-base">
                    Status Integritas Audit: {{ $integrityStatus['is_valid'] ? 'TERVERIFIKASI UTUH & AMAN' : 'PERINGATAN: RANTAI RUSAK' }}
                </h3>
                <p class="text-sm mt-0.5 {{ $integrityStatus['is_valid'] ? 'text-emerald-700' : 'text-red-700' }}">
                    {{ $integrityStatus['message'] }} (Total: {{ $integrityStatus['total_verified'] ?? 0 }} baris log terverifikasi).
                </p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <form method="GET" action="{{ route('pengawas.audit.index') }}" class="flex flex-wrap items-center gap-3 w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari log..." class="px-3.5 py-2 text-xs rounded-lg border flex-1 min-w-[200px]">
                <select name="event_type" class="px-3 py-2 text-xs rounded-lg border">
                    <option value="">Semua Event</option>
                    <option value="auth" {{ request('event_type') === 'auth' ? 'selected' : '' }}>Auth</option>
                    <option value="voting" {{ request('event_type') === 'voting' ? 'selected' : '' }}>Voting</option>
                    <option value="election" {{ request('event_type') === 'election' ? 'selected' : '' }}>Election</option>
                    <option value="candidate" {{ request('event_type') === 'candidate' ? 'selected' : '' }}>Candidate</option>
                    <option value="supervision" {{ request('event_type') === 'supervision' ? 'selected' : '' }}>Supervision</option>
                    <option value="approval" {{ request('event_type') === 'approval' ? 'selected' : '' }}>Approval</option>
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
                        <th class="px-4 py-3">Aksi & Event</th>
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
                                <span class="text-[10px] text-slate-400">({{ ucfirst($log->role) }})</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $log->event_type }}
                                </span>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">{{ $log->action }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-slate-800 font-medium leading-relaxed">{{ $log->description }}</p>
                            </td>
                            <td class="px-4 py-3 font-mono text-[10px] text-slate-400 whitespace-nowrap">
                                <div>Prev: {{ substr($log->prev_hash, 0, 12) }}...</div>
                                <div class="font-bold text-slate-700">Curr: {{ substr($log->current_hash, 0, 12) }}...</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">Tidak ada catatan audit.</td>
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
            <h3 class="text-base font-bold text-slate-900">Tanda Tangani Checkpoint Pengawas</h3>
            <form action="{{ route('pengawas.audit.checkpoint') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Pengawasan</label>
                    <textarea name="notes" rows="3" required placeholder="Catatan integritas pengawasan..." class="w-full px-3 py-2 text-sm border rounded-lg"></textarea>
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
