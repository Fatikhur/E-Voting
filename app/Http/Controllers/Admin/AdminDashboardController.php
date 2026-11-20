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

        $stats['turnout_percentage'] = $stats['total_dpt'] > 0
            ? round(($stats['total_voted'] / $stats['total_dpt']) * 100, 1)
            : 0;

        // Distribusi Status Pemilihan
        $electionStatusCounts = [
            'draft' => Election::where('status', 'draft')->count(),
            'scheduled' => Election::where('status', 'scheduled')->count(),
            'open' => Election::where('status', 'open')->count(),
            'closed' => Election::whereIn('status', ['closed', 'archived'])->count(),
        ];

        $elections = Election::with(['candidates', 'rw', 'rt'])->orderBy('created_at', 'desc')->take(6)->get();

        // Partisipasi per agenda pemilihan untuk visualisasi grafik
        $electionTurnouts = $elections->map(function ($el) {
            $total = $el->eligibleVoters()->count();
            $voted = $el->eligibleVoters()->where('has_voted', true)->count();
            return [
                'title' => $el->title,
                'code' => $el->code,
                'total' => $total,
                'voted' => $voted,
                'unvoted' => $total - $voted,
                'percentage' => $total > 0 ? round(($voted / $total) * 100, 1) : 0,
            ];
        });

        $recentActivities = AuditLog::whereIn('event_type', ['resident', 'election', 'candidate', 'voter', 'approval'])
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'elections',
            'electionStatusCounts',
            'electionTurnouts',
            'recentActivities'
        ));
    }
}
