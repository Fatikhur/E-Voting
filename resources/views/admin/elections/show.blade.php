@extends('layouts.app', ['title' => $election->title])

@section('content')
<div class="space-y-8">

    <!-- Header & Lifecycle Buttons -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase border {{ $election->status_badge['class'] }}">
                    {{ $election->status_badge['text'] }}
                </span>
                <span class="text-xs font-mono font-bold text-slate-400">{{ $election->code }}</span>
                <span class="text-xs text-slate-400">• {{ $election->scope_display }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $election->title }}</h1>
            <p class="text-sm text-slate-500 mt-1">Periode Jabatan: {{ $election->term_period }} • Jadwal: {{ $election->start_time->format('d M Y, H:i') }} - {{ $election->end_time->format('d M Y, H:i') }}</p>
        </div>

        <!-- Action Controls -->
        <div class="flex flex-wrap items-center gap-2">
            @if($election->isDraft())
                <a href="{{ route('admin.elections.edit', $election) }}" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700">
                    ✏️ Edit Detail
                </a>
                <form action="{{ route('admin.elections.schedule', $election) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-sm">
                        🗓️ Jadwalkan Pemilihan
                    </button>
                </form>
            @elseif($election->isScheduled())
                <a href="{{ route('admin.elections.edit', $election) }}" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700">
                    ✏️ Edit Jadwal
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
                            class="px-4 py-2 text-xs font-bold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 transition">
                        ▶️ Buka Voting Sekarang
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
                            class="px-4 py-2 text-xs font-bold rounded-xl bg-amber-600 hover:bg-amber-700 text-white shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 transition">
                        ⏹️ Tutup Pemungutan Suara
                    </button>
                </form>
            @endif

            <a href="{{ route('reports.results', $election) }}" class="px-3.5 py-2 text-xs font-bold rounded-xl border border-blue-300 bg-blue-50 hover:bg-blue-100 text-blue-900 transition">
                📊 Hasil & Grafik
            </a>

            @if($election->isPublished())
                <a href="{{ route('reports.pdf', $election) }}" class="px-3.5 py-2 text-xs font-bold rounded-xl bg-slate-900 hover:bg-slate-800 text-white transition">
                    📄 Unduh Berita Acara PDF
                </a>
            @endif
        </div>
    </div>

    <!-- Two-Person Rule Approval Card (When election is closed or published) -->
    @if(in_array($election->status, ['closed', 'published']))
        @php
            $approval = $election->approval;
        @endphp
        <div class="bg-white rounded-2xl border {{ $election->isPublished() ? 'border-emerald-300 bg-emerald-50/30' : 'border-amber-300 bg-amber-50/30' }} p-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider {{ $election->isPublished() ? 'text-emerald-700' : 'text-amber-700' }}">
                        Prosedur Verifikasi Dua Pihak (Two-Person Rule)
                    </span>
                    <h3 class="text-lg font-extrabold text-slate-900 mt-0.5">
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
                                class="px-5 py-2.5 text-sm font-bold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-md transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400">
                            📢 Publikasikan Hasil Resmi Sekarang
                        </button>
                    </form>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <!-- Pihak 1: Admin Panitia -->
                <div class="p-4 rounded-xl border {{ $approval?->admin_approved ? 'bg-emerald-50 border-emerald-200' : 'bg-white border-slate-200' }}">
                    <div class="flex items-center justify-between font-bold mb-2">
                        <span>1. Panitia Pemilihan (Admin)</span>
                        <span class="px-2 py-0.5 rounded text-[11px] {{ $approval?->admin_approved ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $approval?->admin_approved ? 'Disetujui' : 'Belum Ditandatangani' }}
                        </span>
                    </div>
                    @if($approval?->admin_approved)
                        <p class="text-slate-600 italic">"{{ $approval->admin_notes }}"</p>
                        <p class="text-[11px] text-slate-400 mt-2 font-mono">Ditandatangani oleh @ {{ $approval->admin?->username }} pada {{ $approval->admin_approved_at->format('d/m/Y H:i') }}</p>
                    @else
                        <form action="{{ route('admin.elections.approve', $election) }}" method="POST" class="mt-2 space-y-2">
                            @csrf
                            <input type="text" name="notes" placeholder="Catatan verifikasi panitia..." required class="w-full px-3 py-1.5 text-xs border rounded-lg">
                            <button type="submit" class="w-full px-3 py-1.5 font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg">
                                Tanda Tangani Persetujuan Panitia
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Pihak 2: Pengawas Independen -->
                <div class="p-4 rounded-xl border {{ $approval?->supervisor_approved ? 'bg-emerald-50 border-emerald-200' : 'bg-white border-slate-200' }}">
                    <div class="flex items-center justify-between font-bold mb-2">
                        <span>2. Pengawas Independen</span>
                        <span class="px-2 py-0.5 rounded text-[11px] {{ $approval?->supervisor_approved ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $approval?->supervisor_approved ? 'Disetujui' : 'Menunggu Pengawas' }}
                        </span>
                    </div>
                    @if($approval?->supervisor_approved)
                        <p class="text-slate-600 italic">"{{ $approval->supervisor_notes }}"</p>
                        <p class="text-[11px] text-slate-400 mt-2 font-mono">Ditandatangani oleh @ {{ $approval->supervisor?->username }} pada {{ $approval->supervisor_approved_at->format('d/m/Y H:i') }}</p>
                    @else
                        <p class="text-slate-500 italic mt-3">Menunggu proses audit dan tanda tangan persetujuan oleh akun Pengawas.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Turnout and Counting Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Daftar Pemilih (DPT)</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-slate-800">{{ number_format($counting['statistics']['total_eligible_voters']) }}</span>
                <a href="{{ route('admin.dpt.index', $election) }}" class="text-xs font-bold text-blue-600 hover:underline">Kelola ➔</a>
            </div>
            <p class="text-xs text-slate-500 mt-2">Warga terdaftar sah</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Suara Digunakan</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ number_format($counting['statistics']['total_voted']) }}</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">
                    {{ $counting['statistics']['turnout_percentage'] }}%
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Tingkat partisipasi warga</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Surat Suara di Kotak</span>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-blue-600">{{ number_format($counting['statistics']['total_valid_ballots']) }}</span>
                <span class="text-xs text-slate-400 font-mono">Secret Ballots</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Suara sah anonim</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Konsistensi Data</span>
            <div class="flex items-baseline justify-between">
                <span class="text-lg font-bold {{ $counting['statistics']['is_count_consistent'] ? 'text-emerald-700' : 'text-red-700' }}">
                    {{ $counting['statistics']['is_count_consistent'] ? '100% Sinkron' : 'Perbedaan Terdeteksi' }}
                </span>
                <span class="text-xl">{{ $counting['statistics']['is_count_consistent'] ? '⚖️' : '⚠️' }}</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Voted Voters vs Surat Suara</p>
        </div>
    </div>

    <!-- Candidates Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Kandidat Terdaftar</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar calon ketua yang bersaing dalam pemilihan ini.</p>
            </div>
            @if(in_array($election->status, ['draft', 'scheduled']))
                <a href="{{ route('admin.candidates.create', $election) }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-xl bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition">
                    <span>➕ Tambah Kandidat</span>
                </a>
            @else
                <span class="text-xs text-slate-400 italic">Data kandidat terkunci karena pemilihan telah berjalan/selesai.</span>
            @endif
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($election->candidates as $candidate)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 flex flex-col justify-between space-y-4 hover:border-blue-300 transition relative {{ $candidate->status === 'withdrawn' ? 'opacity-60 bg-slate-50' : '' }}">
                    <div>
                        <!-- Candidate Number Badge -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="w-10 h-10 rounded-xl bg-blue-600 text-white font-extrabold text-xl flex items-center justify-center shadow-md">
                                {{ $candidate->candidate_number }}
                            </span>
                            @if($candidate->status === 'withdrawn')
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-red-100 text-red-800 border border-red-200">
                                    Mundur
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Aktif
                                </span>
                            @endif
                        </div>

                        <!-- Photo & Name -->
                        <div class="flex items-center gap-3 mb-3">
                            <img src="{{ $candidate->photo_url }}" alt="{{ $candidate->name }}" class="w-14 h-14 rounded-xl object-cover border border-slate-200 shrink-0">
                            <div>
                                <h3 class="font-bold text-slate-900 text-base leading-tight">{{ $candidate->name }}</h3>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $candidate->address ?? 'Alamat warga' }}</p>
                            </div>
                        </div>

                        <!-- Vision & Mission -->
                        <div class="space-y-2 text-xs">
                            <div>
                                <span class="font-bold text-slate-700 block text-[11px] uppercase tracking-wider text-blue-600">Visi:</span>
                                <p class="text-slate-600 italic">"{{ $candidate->vision }}"</p>
                            </div>
                            <div>
                                <span class="font-bold text-slate-700 block text-[11px] uppercase tracking-wider text-blue-600">Misi:</span>
                                <p class="text-slate-600 whitespace-pre-line leading-relaxed">{{ Str::limit($candidate->mission, 150) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if(in_array($election->status, ['draft', 'scheduled']))
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <a href="{{ route('admin.candidates.edit', $candidate) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
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
                <div class="col-span-full py-8 text-center text-slate-400 text-sm">
                    Belum ada kandidat terdaftar pada pemilihan ini.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
