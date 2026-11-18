<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\ElectionEligibleVoter;
use App\Services\VoteCountingService;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function __construct(
        protected VoteCountingService $voteCountingService
    ) {}

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
