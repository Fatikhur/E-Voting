<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\ElectionEligibleVoter;
use App\Models\Resident;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $admin = auth()->user();

        $stats = [
            'total_residents' => Resident::count(),
            'total_dpt' => ElectionEligibleVoter::count(),
            'total_voted' => ElectionEligibleVoter::where('has_voted', true)->count(),
            'total_unvoted' => ElectionEligibleVoter::where('has_voted', false)->count(),
            'total_elections' => Election::count(),
            'active_elections' => Election::where('status', 'open')->count(),
            'total_candidates' => Candidate::count(),
        ];

        $elections = Election::with(['candidates', 'rw', 'rt'])->orderBy('created_at', 'desc')->take(6)->get();
        $recentActivities = AuditLog::whereIn('event_type', ['resident', 'election', 'candidate', 'voter', 'approval'])
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'elections', 'recentActivities'));
    }
}
