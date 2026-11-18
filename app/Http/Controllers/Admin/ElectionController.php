<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Village;
use App\Services\ElectionService;
use App\Services\VoteCountingService;
use App\Services\VoterEligibilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ElectionController extends Controller
{
    public function __construct(
        protected ElectionService $electionService,
        protected VoterEligibilityService $voterEligibilityService,
        protected VoteCountingService $voteCountingService
    ) {}

    public function index(Request $request): View
    {
        $query = Election::with(['village', 'rw', 'rt', 'candidates']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $elections = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.elections.index', compact('elections'));
    }

    public function create(): View
    {
        $villages = Village::all();
        $rws = Rw::all();
        $rts = Rt::all();

        return view('admin.elections.create', compact('villages', 'rws', 'rts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:ketua_rt,ketua_rw',
            'village_id' => 'required|exists:villages,id',
            'rw_id' => 'required|exists:rws,id',
            'rt_id' => 'nullable|required_if:type,ketua_rt|exists:rts,id',
            'term_period' => 'required|string|max:50',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'description' => 'nullable|string',
            'tie_breaker_rule' => 'nullable|string',
        ], [
            'end_time.after' => 'Waktu berakhir harus lebih lambat dari waktu mulai.',
            'rt_id.required_if' => 'Wilayah RT wajib dipilih jika jenis pemilihan adalah Ketua RT.',
        ]);

        try {
            $election = $this->electionService->createElection($request->all(), auth()->user());

            // Otomatis sinkronisasi DPT
            $this->voterEligibilityService->syncDptForElection($election, auth()->user());

            return redirect()->route('admin.elections.show', $election)
                ->with('success', "Pemilihan '{$election->title}' berhasil dibuat dan DPT telah disinkronkan.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Election $election): View
    {
        $election->load(['village', 'rw', 'rt', 'candidates', 'approval.admin', 'approval.supervisor', 'observerReports.supervisor']);
        $counting = $this->voteCountingService->getElectionResults($election);

        return view('admin.elections.show', compact('election', 'counting'));
    }

    public function edit(Election $election): View|RedirectResponse
    {
        if (in_array($election->status, ['open', 'closed', 'published'], true)) {
            return redirect()->route('admin.elections.show', $election)
                ->with('error', 'Pemilihan yang sedang berlangsung atau selesai tidak dapat diedit.');
        }

        $villages = Village::all();
        $rws = Rw::all();
        $rts = Rt::all();

        return view('admin.elections.edit', compact('election', 'villages', 'rws', 'rts'));
    }

    public function update(Request $request, Election $election): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'term_period' => 'required|string|max:50',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'description' => 'nullable|string',
            'tie_breaker_rule' => 'nullable|string',
        ]);

        try {
            $this->electionService->updateElection($election, $request->all(), auth()->user());

            return redirect()->route('admin.elections.show', $election)
                ->with('success', "Konfigurasi pemilihan '{$election->title}' berhasil diperbarui.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function schedule(Election $election): RedirectResponse
    {
        try {
            $this->electionService->scheduleElection($election, auth()->user());
            return back()->with('success', "Pemilihan '{$election->title}' berhasil dijadwalkan.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function open(Election $election): RedirectResponse
    {
        try {
            $this->electionService->openElection($election, auth()->user());
            return back()->with('success', "Pemungutan suara pemilihan '{$election->title}' telah DIBUKA.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function close(Election $election): RedirectResponse
    {
        try {
            $this->electionService->closeElection($election, auth()->user());
            return back()->with('success', "Pemungutan suara pemilihan '{$election->title}' telah DITUTUP.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
