<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Services\ElectionApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ElectionApprovalSupervisorController extends Controller
{
    public function __construct(
        protected ElectionApprovalService $approvalService
    ) {}

    public function approve(Request $request, Election $election): RedirectResponse
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $approval = $this->approvalService->approveBySupervisor(
                $election,
                auth()->user(),
                $request->notes
            );

            $msg = 'Persetujuan Pengawas Independen berhasil ditandatangani.';
            if ($approval->isFullyApproved()) {
                $msg .= ' Persetujuan dua pihak kini lengkap. Hasil siap untuk dipublikasikan oleh Panitia.';
            } else {
                $msg .= ' Menunggu tanda tangan persetujuan dari Panitia Pemilihan (Admin).';
            }

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
