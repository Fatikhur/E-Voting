<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Election;
use App\Models\ElectionApproval;
use App\Models\ElectionEligibleVoter;
use App\Models\ObserverReport;
use App\Services\AuditLogService;
use App\Services\VoteCountingService;
use Illuminate\View\View;

class PengawasDashboardController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService,
        protected VoteCountingService $voteCountingService
    ) {}

    public function index(): View
    {
        $supervisor = auth()->user();

        $elections = Election::with(['village', 'rw', 'rt', 'candidates'])
            ->orderBy('created_at', 'desc')
            ->get();

        $activeElections = $elections->where('status', 'open');

        $pendingApprovals = ElectionApproval::with('election')
            ->where('supervisor_approved', false)
            ->whereHas('election', fn($q) => $q->where('status', 'closed'))
            ->get();

        $myReports = ObserverReport::where('supervisor_user_id', $supervisor->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $totalReports = ObserverReport::count();

        // 6 Metrik Numerik Pengawas
        $stats = [
            'total_dpt' => ElectionEligibleVoter::count(),
            'total_voted' => ElectionEligibleVoter::where('has_voted', true)->count(),
            'total_unvoted' => ElectionEligibleVoter::where('has_voted', false)->count(),
            'total_audit_logs' => AuditLog::count(),
            'supervisor_audits' => AuditLog::where('role', 'pengawas')->count(),
            'total_reports' => $totalReports,
        ];

        $turnoutPercentage = $stats['total_dpt'] > 0
            ? round(($stats['total_voted'] / $stats['total_dpt']) * 100, 1)
            : 0;

        // Visualisasi: Suara Masuk Terpantau Per Rentang Waktu (Agregat)
        $votingTimeline = ElectionEligibleVoter::where('has_voted', true)
            ->whereNotNull('voted_at')
            ->orderBy('voted_at', 'asc')
            ->take(50)
            ->get(['voted_at'])
            ->groupBy(fn($item) => $item->voted_at ? $item->voted_at->format('d/m H:00') : '-')
            ->map->count()
            ->take(8)
            ->toArray();

        // Visualisasi: Distribusi Log Kriptografis Berdasarkan Tipe Event
        $auditByType = AuditLog::selectRaw('event_type, COUNT(*) as count')
            ->groupBy('event_type')
            ->pluck('count', 'event_type')
            ->toArray();

        $recentAudits = AuditLog::orderBy('id', 'desc')->take(10)->get();
        $integrity = $this->auditLogService->verifyChainIntegrity();

        return view('pengawas.dashboard', compact(
            'elections',
            'activeElections',
            'pendingApprovals',
            'myReports',
            'stats',
            'turnoutPercentage',
            'votingTimeline',
            'auditByType',
            'recentAudits',
            'integrity'
        ));
    }
}
