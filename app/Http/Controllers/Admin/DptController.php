<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\ElectionEligibleVoter;
use App\Services\VoterEligibilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DptController extends Controller
{
    public function __construct(
        protected VoterEligibilityService $voterEligibilityService
    ) {}

    public function index(Request $request, Election $election): View
    {
        $query = ElectionEligibleVoter::where('election_id', $election->id)
            ->with(['resident.village', 'resident.rw', 'resident.rt', 'user']);

        if ($request->filled('status')) {
            $hasVoted = $request->status === 'voted';
            $query->where('has_voted', $hasVoted);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('resident', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik_masked', 'like', "%{$search}%");
            });
        }

        $voters = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => ElectionEligibleVoter::where('election_id', $election->id)->count(),
            'voted' => ElectionEligibleVoter::where('election_id', $election->id)->where('has_voted', true)->count(),
            'unvoted' => ElectionEligibleVoter::where('election_id', $election->id)->where('has_voted', false)->count(),
        ];

        return view('admin.dpt.index', compact('election', 'voters', 'stats'));
    }

    public function sync(Election $election): RedirectResponse
    {
        try {
            $count = $this->voterEligibilityService->syncDptForElection($election, auth()->user());
            return back()->with('success', "Sinkronisasi DPT berhasil. Sebanyak {$count} pemilih baru ditambahkan ke DPT.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal sinkronisasi DPT: ' . $e->getMessage());
        }
    }
}
