<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Election;
use App\Models\ElectionEligibleVoter;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use App\Models\Village;
use App\Services\AuditLogService;
use Illuminate\View\View;

class SuperAdminDashboardController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function index(): View
    {
        $stats = [
            'total_residents' => Resident::count(),
            'total_voters_registered' => ElectionEligibleVoter::count(),
            'total_users' => User::count(),
            'total_villages' => Village::count(),
            'total_rws' => Rw::count(),
            'total_rts' => Rt::count(),
            'total_elections' => Election::count(),
            'active_elections' => Election::where('status', 'open')->count(),
            'total_audit_logs' => AuditLog::count(),
        ];

        // 1. Distribusi Status Pemilihan
        $electionStatusDistribution = [
            'draft' => Election::where('status', 'draft')->count(),
            'scheduled' => Election::where('status', 'scheduled')->count(),
            'open' => Election::where('status', 'open')->count(),
            'closed' => Election::whereIn('status', ['closed', 'archived'])->count(),
        ];

        // 2. Partisipasi Pemilih Keseluruhan (Agregat)
        $voterParticipation = [
            'voted' => ElectionEligibleVoter::where('has_voted', true)->count(),
            'unvoted' => ElectionEligibleVoter::where('has_voted', false)->count(),
            'total' => ElectionEligibleVoter::count(),
        ];
        $voterParticipation['percentage'] = $voterParticipation['total'] > 0
            ? round(($voterParticipation['voted'] / $voterParticipation['total']) * 100, 1)
            : 0;

        // 3. Distribusi Aktivitas Sistem Berdasarkan Tipe Event
        $auditActivityBreakdown = AuditLog::selectRaw('event_type, COUNT(*) as count')
            ->groupBy('event_type')
            ->pluck('count', 'event_type')
            ->toArray();

        // 4. Distribusi Tingkat Wilayah Pemilihan (RW vs RT)
        $electionScopeDistribution = [
            'rw' => Election::where('type', 'ketua_rw')->count(),
            'rt' => Election::where('type', 'ketua_rt')->count(),
        ];

        $recentLogs = AuditLog::with('user')->orderBy('id', 'desc')->take(10)->get();
        $recentElections = Election::with(['village', 'rw', 'rt'])->orderBy('created_at', 'desc')->take(5)->get();

        $chainIntegrity = $this->auditLogService->verifyChainIntegrity();

        return view('superadmin.dashboard', compact(
            'stats',
            'electionStatusDistribution',
            'voterParticipation',
            'auditActivityBreakdown',
            'electionScopeDistribution',
            'recentLogs',
            'recentElections',
            'chainIntegrity'
        ));
    }
}
