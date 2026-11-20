<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\ElectionEligibleVoter;
use App\Services\VoteCountingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function __construct(
        protected VoteCountingService $voteCountingService
    ) {}

    public function index(Request $request): View
    {
        $query = Election::with(['village', 'rw', 'rt', 'candidates'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $elections = $query->paginate(10)->withQueryString();

        return view('pengawas.monitoring.index', compact('elections'));
    }

    public function show(Election $election): View
    {
        $election->load(['village', 'rw', 'rt', 'candidates', 'approval']);
        $counting = $this->voteCountingService->getElectionResults($election);

        $voterRecords = ElectionEligibleVoter::where('election_id', $election->id)
            ->with(['resident.rt', 'resident.rw'])
            ->orderBy('voted_at', 'desc')
            ->paginate(25);

        return view('pengawas.monitoring.show', compact('election', 'counting', 'voterRecords'));
    }
}
