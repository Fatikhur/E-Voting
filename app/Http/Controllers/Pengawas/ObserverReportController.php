<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\ObserverReport;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ObserverReportController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function index(): View
    {
        $reports = ObserverReport::with(['election', 'supervisor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pengawas.reports.index', compact('reports'));
    }

    public function create(Request $request): View
    {
        $elections = Election::all();
        $selectedElectionId = $request->query('election_id');

        return view('pengawas.reports.create', compact('elections', 'selectedElectionId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'election_id' => 'required|exists:elections,id',
            'report_type' => 'required|in:catatan,indikasi_pelanggaran,rekomendasi,klarifikasi',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'evidence' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:4096',
        ]);

        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('evidence', 'public');
        }

        $report = ObserverReport::create([
            'election_id' => $request->election_id,
            'supervisor_user_id' => auth()->id(),
            'report_type' => $request->report_type,
            'title' => $request->title,
            'content' => $request->content,
            'evidence_path' => $evidencePath,
            'status' => 'submitted',
        ]);

        $this->auditLogService->log(
            action: 'submit_observer_report',
            eventType: 'supervision',
            description: "Pengawas mengajukan laporan '{$report->title}' (Tipe: {$report->report_type}) pada pemilihan ID #{$request->election_id}",
            metadata: ['report_id' => $report->id, 'type' => $report->report_type]
        );

        return redirect()->route('pengawas.reports.index')
            ->with('success', 'Laporan pengawasan berhasil diajukan.');
    }

    public function show(ObserverReport $report): View
    {
        $report->load(['election', 'supervisor']);
        return view('pengawas.reports.show', compact('report'));
    }
}
