@extends('layouts.app', ['title' => 'Pengawasan Audit Trail'])

@section('content')
<div class="space-y-6" x-data="{ openCheckpointModal: false }">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Audit Trail Pengawas Independen</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Verifikasi integritas rantai hash kriptografis untuk mendeteksi manipulasi database.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <button @click="openCheckpointModal = true" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-white border border-[#E5E7EB] text-[#252A34] hover:bg-slate-50 shadow-xs transition">
                <svg class="w-4 h-4 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
                <span>Buat Checkpoint Pengawas</span>
            </button>
            <form action="{{ route('pengawas.audit.verify') }}" method="POST" class="inline">
                @csrf
                <button type="button"
                        data-confirm
                        data-confirm-type="info"
                        data-confirm-title="Pindai Integritas Rantai Audit"
                        data-confirm-message="Jalankan pemindaian integritas rantai audit kriptografis sekarang?"
                        data-confirm-detail="Pengawas independen dapat memverifikasi bahwa tidak ada catatan log yang diubah, dihapus, atau disisipkan secara ilegal."
                        data-confirm-button="Mulai Pemindaian"
                        data-confirm-cancel="Batal"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-[#1877F2] hover:bg-[#166fe5] text-white shadow-xs transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#1877F2]">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    <span>Pindai Integritas Rantai</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Integrity Status Banner -->
    <div class="p-4 sm:p-5 rounded-xl border {{ $integrityStatus['is_valid'] ? 'bg-emerald-50/70 border-emerald-200 text-emerald-950' : 'bg-red-50/70 border-red-200 text-red-950' }} shadow-xs">
        <div class="flex items-start gap-3.5">
            <div class="w-9 h-9 rounded-lg {{ $integrityStatus['is_valid'] ? 'bg-[#269B68] text-white' : 'bg-[#DC2626] text-white' }} flex items-center justify-center shrink-0">
                @if($integrityStatus['is_valid'])
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                @else
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                @endif
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-sm sm:text-base {{ $integrityStatus['is_valid'] ? 'text-emerald-950' : 'text-red-950' }}">
                    Status Integritas Audit: {{ $integrityStatus['is_valid'] ? 'TERVERIFIKASI UTUH & AMAN' : 'PERINGATAN: RANTAI RUSAK' }}
                </h3>
                <p class="text-xs sm:text-sm mt-0.5 {{ $integrityStatus['is_valid'] ? 'text-emerald-800' : 'text-red-800' }}">
                    {{ $integrityStatus['message'] }} (Total: {{ $integrityStatus['total_verified'] ?? 0 }} baris log terverifikasi).
                </p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden">
        <div class="p-4 border-b border-[#E5E7EB] flex flex-wrap items-center justify-between gap-3">
            <form method="GET" action="{{ route('pengawas.audit.index') }}" class="flex flex-wrap items-center gap-2.5 w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari log..." class="px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] placeholder-[#6B7280] flex-1 min-w-[200px] focus:outline-none focus:border-[#1877F2]">
                <select name="event_type" class="px-3 py-2 text-xs sm:text-sm rounded-lg border border-[#E5E7EB] bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                    <option value="">Semua Event</option>
                    <option value="auth" {{ request('event_type') === 'auth' ? 'selected' : '' }}>Auth</option>
                    <option value="voting" {{ request('event_type') === 'voting' ? 'selected' : '' }}>Voting</option>
                    <option value="election" {{ request('event_type') === 'election' ? 'selected' : '' }}>Election</option>
                    <option value="candidate" {{ request('event_type') === 'candidate' ? 'selected' : '' }}>Candidate</option>
                    <option value="supervision" {{ request('event_type') === 'supervision' ? 'selected' : '' }}>Supervision</option>
                    <option value="approval" {{ request('event_type') === 'approval' ? 'selected' : '' }}>Approval</option>
                    <option value="security" {{ request('event_type') === 'security' ? 'selected' : '' }}>Security</option>
                </select>
                <button type="submit" class="px-3.5 py-2 text-xs sm:text-sm font-semibold bg-[#252A34] text-white rounded-lg hover:bg-slate-800 transition">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#252A34]">
                <thead class="bg-slate-50 text-[#6B7280] uppercase font-semibold text-[11px] sm:text-xs border-b border-[#E5E7EB]">
                    <tr>
                        <th class="px-4 py-3">ID & Waktu</th>
                        <th class="px-4 py-3">Aktor</th>
                        <th class="px-4 py-3">Aksi & Event</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3 font-mono">Hash Rantai (SHA-256)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E7EB]">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-4 py-3 font-mono whitespace-nowrap">
                                <span class="font-bold text-[#252A34]">#{{ $log->id }}</span><br>
                                <span class="text-[11px] text-[#6B7280]">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-semibold text-[#252A34]">@ {{ $log->username }}</span><br>
                                <span class="text-[10px] text-[#6B7280]">({{ ucfirst($log->role) }})</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-slate-100 text-[#252A34] border border-[#E5E7EB]">
                                    {{ $log->event_type }}
                                </span>
                                <div class="text-[11px] text-[#6B7280] font-mono mt-0.5">{{ $log->action }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-[#252A34] font-medium leading-relaxed">{{ $log->description }}</p>
                            </td>
                            <td class="px-4 py-3 font-mono text-[10px] text-[#6B7280] whitespace-nowrap">
                                <div>Prev: {{ substr($log->prev_hash, 0, 12) }}...</div>
                                <div class="font-bold text-[#252A34]">Curr: {{ substr($log->current_hash, 0, 12) }}...</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-[#6B7280] text-xs sm:text-sm">Tidak ada catatan audit.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[#E5E7EB]">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- Modal Checkpoint -->
    <div x-show="openCheckpointModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" role="dialog" aria-modal="true">
        <div @click.away="openCheckpointModal = false" class="bg-white rounded-xl max-w-md w-full p-5 sm:p-6 shadow-xl border border-[#E5E7EB]">
            <h3 class="text-base font-semibold text-[#252A34]">Tanda Tangani Checkpoint Pengawas</h3>
            <form action="{{ route('pengawas.audit.checkpoint') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[#252A34] mb-1">Catatan Pengawasan</label>
                    <textarea name="notes" rows="3" required placeholder="Catatan integritas pengawasan..." class="w-full px-3 py-2 text-xs sm:text-sm border border-[#E5E7EB] rounded-lg bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]"></textarea>
                </div>
                <div class="flex justify-end gap-2.5 pt-2 border-t border-[#E5E7EB]">
                    <button type="button" @click="openCheckpointModal = false" class="px-3.5 py-1.5 text-xs font-medium text-[#252A34] rounded-lg border border-[#E5E7EB] hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold text-white bg-[#1877F2] hover:bg-[#166fe5] rounded-lg shadow-xs transition">Tanda Tangani</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
