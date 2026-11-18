<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Services\ElectionApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ElectionApprovalAdminController extends Controller
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
            $approval = $this->approvalService->approveByAdmin(
                $election,
                auth()->user(),
                $request->notes
            );

            $msg = 'Persetujuan Panitia Pemilihan berhasil ditandatangani.';
            if ($approval->isFullyApproved()) {
                $msg .= ' Persetujuan dua pihak kini lengkap. Hasil siap untuk dipublikasikan.';
            } else {
                $msg .= ' Menunggu tanda tangan persetujuan dari Pengawas Pemilihan.';
            }

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function publish(Election $election): RedirectResponse
    {
        try {
            $this->approvalService->publishResults($election, auth()->user());
            return back()->with('success', "Hasil resmi pemilihan '{$election->title}' telah BERHASIL DIPUBLIKASIKAN kepada publik.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
