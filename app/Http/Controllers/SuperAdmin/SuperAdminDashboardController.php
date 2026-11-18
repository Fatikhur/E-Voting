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

        $recentLogs = AuditLog::with('user')->orderBy('id', 'desc')->take(10)->get();
        $recentElections = Election::with(['village', 'rw', 'rt'])->orderBy('created_at', 'desc')->take(5)->get();

        $chainIntegrity = $this->auditLogService->verifyChainIntegrity();

        return view('superadmin.dashboard', compact('stats', 'recentLogs', 'recentElections', 'chainIntegrity'));
    }
}
