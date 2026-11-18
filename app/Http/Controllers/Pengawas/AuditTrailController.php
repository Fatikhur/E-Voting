<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\AuditCheckpoint;
use App\Models\AuditLog;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditTrailController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function index(Request $request): View
    {
        $query = AuditLog::with('user');

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $checkpoints = AuditCheckpoint::with('signer')->orderBy('checkpoint_sequence', 'desc')->take(5)->get();

        $integrityStatus = session('integrity_result') ?? $this->auditLogService->verifyChainIntegrity();

        return view('pengawas.audit.index', compact('logs', 'checkpoints', 'integrityStatus'));
    }

    public function verify(): RedirectResponse
    {
        $result = $this->auditLogService->verifyChainIntegrity();

        return redirect()->route('pengawas.audit.index')
            ->with('integrity_result', $result)
            ->with($result['is_valid'] ? 'success' : 'error', $result['message']);
    }

    public function createCheckpoint(Request $request): RedirectResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $checkpoint = $this->auditLogService->createCheckpoint(
                note: $request->notes,
                signedBy: auth()->user()
            );

            return back()->with('success', "Checkpoint Integritas Pengawasan #{$checkpoint->checkpoint_sequence} berhasil ditandatangani.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
