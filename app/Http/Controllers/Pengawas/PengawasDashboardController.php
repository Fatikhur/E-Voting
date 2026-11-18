<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Election;
use App\Models\ElectionApproval;
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

        $recentAudits = AuditLog::orderBy('id', 'desc')->take(10)->get();
        $integrity = $this->auditLogService->verifyChainIntegrity();

        return view('pengawas.dashboard', compact(
            'elections',
            'activeElections',
            'pendingApprovals',
            'myReports',
            'recentAudits',
            'integrity'
        ));
    }
}
