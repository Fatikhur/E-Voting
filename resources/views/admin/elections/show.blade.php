@extends('layouts.app', ['title' => $election->title])

@section('content')
<div class="space-y-6">

    <!-- Header & Lifecycle Buttons -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded text-[11px] font-semibold uppercase border {{ $election->status_badge['class'] }}">
                    {{ $election->status_badge['text'] }}
                </span>
                <span class="text-xs font-mono font-medium text-[#6B7280]">{{ $election->code }}</span>
                <span class="text-xs text-[#6B7280]">• {{ $election->scope_display }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">{{ $election->title }}</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Periode Jabatan: {{ $election->term_period }} • Jadwal: {{ $election->start_time->format('d M Y, H:i') }} - {{ $election->end_time->format('d M Y, H:i') }}</p>
        </div>

        <!-- Action Controls -->
        <div class="flex flex-wrap items-center gap-2">
            @if($election->isDraft())
                <a href="{{ route('admin.elections.edit', $election) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border border-[#E5E7EB] bg-white hover:bg-slate-50 text-[#252A34] transition">
                    <svg class="w-3.5 h-3.5 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    <span>Edit Detail</span>
                </a>
                <form action="{{ route('admin.elections.schedule', $election) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-[#1877F2] hover:bg-[#166fe5] text-white shadow-xs transition">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.253 3.75m3 0h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 21h15a2.25 2.25 0 002.25-2.25V9a2.25 2.25 0 00-2.25-2.25z" />
                        </svg>
                        <span>Jadwalkan Pemilihan</span>
                    </button>
                </form>
            @elseif($election->isScheduled())
                <a href="{{ route('admin.elections.edit', $election) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border border-[#E5E7EB] bg-white hover:bg-slate-50 text-[#252A34] transition">
                    <svg class="w-3.5 h-3.5 text-[#6B7280]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    <span>Edit Jadwal</span>
                </a>
                <form action="{{ route('admin.elections.open', $election) }}" method="POST" class="inline">
                    @csrf
                    <button type="button"
                            data-confirm
                            data-confirm-type="success"
                            data-confirm-title="Buka Pemungutan Suara"
                            data-confirm-message="Buka waktu pemungutan suara untuk '{{ $election->title }}' sekarang?"
                            data-confirm-detail="Status pemilihan akan menjadi aktif/buka. Warga yang terdaftar pada DPT dapat segera melakukan voting digital."
                            data-confirm-button="Ya, Buka Voting Sekarang"
                            data-confirm-cancel="Batal"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-[#269B68] hover:bg-emerald-700 text-white shadow-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 transition">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                        </svg>
                        <span>Buka Voting Sekarang</span>
                    </button>
                </form>
            @elseif($election->isOpen())
                <form action="{{ route('admin.elections.close', $election) }}" method="POST" class="inline">
                    @csrf
                    <button type="button"
                            data-confirm
                            data-confirm-type="warning"
                            data-confirm-title="Tutup Pemungutan Suara"
                            data-confirm-message="Apakah Anda yakin ingin menutup pemungutan suara sekarang?"
                            data-confirm-detail="Setelah ditutup, warga tidak dapat voting lagi dan pemilihan akan masuk ke tahap verifikasi hasil Two-Person Rule."
                            data-confirm-button="Ya, Tutup Pemungutan Suara"
                            data-confirm-cancel="Batal"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-amber-600 hover:bg-amber-700 text-white shadow-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 transition">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9z" />
                        </svg>
                        <span>Tutup Pemungutan Suara</span>
                    </button>
                </form>
            @endif

            <a href="{{ route('reports.results', $election) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border border-[#E5E7EB] bg-white hover:bg-slate-50 text-[#1877F2] transition">
                <svg class="w-3.5 h-3.5 text-[#1877F2]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
                <span>Hasil & Grafik</span>
            </a>

            @if($election->isPublished())
                <a href="{{ route('reports.pdf', $election) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-[#252A34] hover:bg-slate-800 text-white transition">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <span>Unduh Berita Acara PDF</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Two-Person Rule Approval Card (When election is closed or published) -->
    @if(in_array($election->status, ['closed', 'published']))
        @php
            $approval = $election->approval;
        @endphp
        <div class="bg-white rounded-xl border {{ $election->isPublished() ? 'border-emerald-200 bg-emerald-50/20' : 'border-amber-200 bg-amber-50/20' }} p-5 sm:p-6 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider {{ $election->isPublished() ? 'text-[#269B68]' : 'text-amber-700' }}">
                        Prosedur Verifikasi Dua Pihak (Two-Person Rule)
                    </span>
                    <h3 class="text-base sm:text-lg font-bold text-[#252A34] mt-0.5">
                        {{ $election->isPublished() ? 'Hasil Telah Disetujui Dua Pihak & Resmi Dipublikasikan' : 'Verifikasi & Tanda Tangan Hasil Pemilihan' }}
                    </h3>
                </div>

                @if(!$election->isPublished() && $approval?->isFullyApproved())
                    <form action="{{ route('admin.elections.publish', $election) }}" method="POST" class="inline">
                        @csrf
                        <button type="button"
                                data-confirm
                                data-confirm-type="success"
                                data-confirm-title="Publikasikan Hasil Resmi"
                                data-confirm-message="Terbitkan dan umumkan hasil resmi pemilihan ini ke publik sekarang?"
                                data-confirm-detail="Hasil telah disahkan oleh Panitia Pemilihan dan Pengawas Independen. Publikasi ini bersifat final."
                                data-confirm-button="Ya, Publikasikan Sekarang"
                                data-confirm-cancel="Batal"
                                class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-[#269B68] hover:bg-emerald-700 text-white shadow-xs transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.16-1.608-.372a19.467 19.467 0 01-2.18-4.302m3.923 0a19.53 19.53 0 010-9.18m0 9.18a19.53 19.53 0 000-9.18m0 0c.253-.962.584-1.892.985-2.783.247-.55.06-1.21-.463-1.511l-.657-.38c-.551-.318-1.26-.16-1.608.372a19.467 19.467 0 00-2.18 4.302m11.238 4.59c0 2.485-2.015 4.5-4.5 4.5h-.75" />
                            </svg>
                            <span>Publikasikan Hasil Resmi Sekarang</span>
                        </button>
                    </form>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <!-- Pihak 1: Admin Panitia -->
                <div class="p-4 rounded-lg border {{ $approval?->admin_approved ? 'bg-emerald-50/70 border-emerald-200' : 'bg-white border-[#E5E7EB]' }}">
                    <div class="flex items-center justify-between font-semibold mb-2">
                        <span class="text-[#252A34]">1. Panitia Pemilihan (Admin)</span>
                        <span class="px-2 py-0.5 rounded text-[11px] font-medium border {{ $approval?->admin_approved ? 'bg-emerald-50 text-[#269B68] border-emerald-200' : 'bg-slate-100 text-[#6B7280] border-slate-200' }}">
                            {{ $approval?->admin_approved ? 'Disetujui' : 'Belum Ditandatangani' }}
                        </span>
                    </div>
                    @if($approval?->admin_approved)
                        <p class="text-[#252A34] italic">"{{ $approval->admin_notes }}"</p>
                        <p class="text-[11px] text-[#6B7280] mt-2 font-mono">Ditandatangani oleh @ {{ $approval->admin?->username }} pada {{ $approval->admin_approved_at->format('d/m/Y H:i') }}</p>
                    @else
                        <form action="{{ route('admin.elections.approve', $election) }}" method="POST" class="mt-2 space-y-2">
                            @csrf
                            <input type="text" name="notes" placeholder="Catatan verifikasi panitia..." required class="w-full px-3 py-1.5 text-xs border border-[#E5E7EB] rounded-lg bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                            <button type="submit" class="w-full px-3 py-1.5 font-semibold text-white bg-[#1877F2] hover:bg-[#166fe5] rounded-lg transition">
                                Tanda Tangani Persetujuan Panitia
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Pihak 2: Pengawas Independen -->
                <div class="p-4 rounded-lg border {{ $approval?->supervisor_approved ? 'bg-emerald-50/70 border-emerald-200' : 'bg-white border-[#E5E7EB]' }}">
                    <div class="flex items-center justify-between font-semibold mb-2">
                        <span class="text-[#252A34]">2. Pengawas Independen</span>
                        <span class="px-2 py-0.5 rounded text-[11px] font-medium border {{ $approval?->supervisor_approved ? 'bg-emerald-50 text-[#269B68] border-emerald-200' : 'bg-slate-100 text-[#6B7280] border-slate-200' }}">
                            {{ $approval?->supervisor_approved ? 'Disetujui' : 'Menunggu Pengawas' }}
                        </span>
                    </div>
                    @if($approval?->supervisor_approved)
                        <p class="text-[#252A34] italic">"{{ $approval->supervisor_notes }}"</p>
                        <p class="text-[11px] text-[#6B7280] mt-2 font-mono">Ditandatangani oleh @ {{ $approval->supervisor?->username }} pada {{ $approval->supervisor_approved_at->format('d/m/Y H:i') }}</p>
                    @else
                        <p class="text-[#6B7280] italic mt-3">Menunggu proses audit dan tanda tangan persetujuan oleh akun Pengawas.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Turnout and Counting Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">Daftar Pemilih (DPT)</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-bold text-[#252A34]">{{ number_format($counting['statistics']['total_eligible_voters']) }}</span>
                <a href="{{ route('admin.dpt.index', $election) }}" class="text-xs font-semibold text-[#1877F2] hover:underline">Kelola</a>
            </div>
            <p class="text-xs text-[#6B7280] mt-2">Warga terdaftar sah</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">Suara Digunakan</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-bold text-[#269B68]">{{ number_format($counting['statistics']['total_voted']) }}</span>
                <span class="text-xs px-2 py-0.5 rounded-md bg-emerald-50 text-[#269B68] font-semibold border border-emerald-200">
                    {{ $counting['statistics']['turnout_percentage'] }}%
                </span>
            </div>
            <p class="text-xs text-[#6B7280] mt-2">Tingkat partisipasi warga</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">Surat Suara di Kotak</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-bold text-[#1877F2]">{{ number_format($counting['statistics']['total_valid_ballots']) }}</span>
                <span class="text-xs text-[#6B7280] font-mono">Secret Ballots</span>
            </div>
            <p class="text-xs text-[#6B7280] mt-2">Suara sah anonim</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-[#E5E7EB] shadow-xs">
            <span class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider block mb-1">Konsistensi Data</span>
            <div class="flex items-baseline justify-between">
                <span class="text-base sm:text-lg font-bold {{ $counting['statistics']['is_count_consistent'] ? 'text-[#269B68]' : 'text-red-600' }}">
                    {{ $counting['statistics']['is_count_consistent'] ? '100% Sinkron' : 'Perbedaan Terdeteksi' }}
                </span>
                <span class="w-5 h-5 flex items-center justify-center">
                    @if($counting['statistics']['is_count_consistent'])
                        <svg class="w-5 h-5 text-[#269B68]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 01-2.031.352 5.989 5.989 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971z" />
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                    @endif
                </span>
            </div>
            <p class="text-xs text-[#6B7280] mt-2">Voted Voters vs Surat Suara</p>
        </div>
    </div>

    <!-- Candidates Section -->
    <div class="bg-white rounded-xl border border-[#E5E7EB] shadow-xs overflow-hidden">
        <div class="p-4 sm:px-5 sm:py-4 border-b border-[#E5E7EB] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm sm:text-base font-semibold text-[#252A34]">Kandidat Terdaftar</h2>
                <p class="text-xs text-[#6B7280] mt-0.5">Daftar calon ketua yang bersaing dalam pemilihan ini.</p>
            </div>
            @if(in_array($election->status, ['draft', 'scheduled']))
                <a href="{{ route('admin.candidates.create', $election) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-[#1877F2] hover:bg-[#166fe5] text-white shadow-xs transition">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Kandidat</span>
                </a>
            @else
                <span class="text-xs text-[#6B7280] italic">Data kandidat terkunci karena pemilihan telah berjalan/selesai.</span>
            @endif
        </div>

        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($election->candidates as $candidate)
                <div class="rounded-xl border border-[#E5E7EB] bg-white p-5 flex flex-col justify-between space-y-4 hover:border-slate-300 transition-colors relative {{ $candidate->status === 'withdrawn' ? 'opacity-60 bg-slate-50' : '' }}">
                    <div>
                        <!-- Candidate Number Badge -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="w-8 h-8 rounded-lg bg-[#1877F2] text-white font-bold text-base flex items-center justify-center shadow-xs">
                                {{ $candidate->candidate_number }}
                            </span>
                            @if($candidate->status === 'withdrawn')
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-red-50 text-red-700 border border-red-200">
                                    Mundur
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-[#269B68] border border-emerald-200">
                                    Aktif
                                </span>
                            @endif
                        </div>

                        <!-- Photo & Name -->
                        <div class="flex items-center gap-3 mb-3">
                            <img src="{{ $candidate->photo_url }}" alt="{{ $candidate->name }}" class="w-14 h-14 rounded-lg object-cover border border-[#E5E7EB] shrink-0">
                            <div>
                                <h3 class="font-semibold text-[#252A34] text-sm sm:text-base leading-tight">{{ $candidate->name }}</h3>
                                <p class="text-xs text-[#6B7280] mt-0.5">{{ $candidate->address ?? 'Alamat warga' }}</p>
                            </div>
                        </div>

                        <!-- Vision & Mission -->
                        <div class="space-y-2 text-xs">
                            <div>
                                <span class="font-semibold block text-[11px] uppercase tracking-wider text-[#1877F2]">Visi:</span>
                                <p class="text-[#252A34] italic">"{{ $candidate->vision }}"</p>
                            </div>
                            <div>
                                <span class="font-semibold block text-[11px] uppercase tracking-wider text-[#1877F2]">Misi:</span>
                                <p class="text-[#6B7280] whitespace-pre-line leading-relaxed">{{ Str::limit($candidate->mission, 150) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if(in_array($election->status, ['draft', 'scheduled']))
                        <div class="pt-3 border-t border-[#E5E7EB] flex items-center justify-between">
                            <a href="{{ route('admin.candidates.edit', $candidate) }}" class="text-xs font-semibold text-[#1877F2] hover:underline">
                                Edit Profil
                            </a>
                            @if($candidate->status === 'active')
                                <form action="{{ route('admin.candidates.withdraw', $candidate) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="withdrawal_reason" value="Mengundurkan diri secara resmi atas permohonan pribadi">
                                    <button type="button"
                                            data-confirm
                                            data-confirm-type="danger"
                                            data-confirm-title="Tandai Pengunduran Diri Kandidat"
                                            data-confirm-message="Tandai kandidat No. {{ $candidate->candidate_number }} ({{ $candidate->name }}) sebagai mengundurkan diri?"
                                            data-confirm-detail="Kandidat yang telah mengundurkan diri tidak dapat dipilih kembali saat pemungutan suara berlangsung."
                                            data-confirm-button="Ya, Tandai Mengundurkan Diri"
                                            data-confirm-cancel="Batal"
                                            class="text-xs text-red-600 hover:underline font-semibold focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-400">
                                        Undurkan Diri
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-[#6B7280] text-xs sm:text-sm">
                    Belum ada kandidat terdaftar pada pemilihan ini.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
