<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Services\VoteCountingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ElectionReportController extends Controller
{
    public function __construct(
        protected VoteCountingService $voteCountingService
    ) {}

    /**
     * Tampilan publik / terotentikasi untuk melihat hasil resmi pemilihan.
     */
    public function show(Election $election): View
    {
        $election->load(['village', 'rw', 'rt', 'candidates', 'approval.admin', 'approval.supervisor']);
        $results = $this->voteCountingService->getElectionResults($election);

        return view('reports.results', compact('election', 'results'));
    }

    /**
     * Cetak Berita Acara (Print-friendly format).
     */
    public function print(Election $election): View
    {
        $election->load(['village', 'rw', 'rt', 'candidates', 'approval.admin', 'approval.supervisor']);
        $results = $this->voteCountingService->getElectionResults($election);

        return view('reports.print', compact('election', 'results'));
    }

    /**
     * Unduh Laporan Resmi Berita Acara dalam format Dokumen PDF.
     */
    public function downloadPdf(Election $election): Response
    {
        $election->load(['village', 'rw', 'rt', 'candidates', 'approval.admin', 'approval.supervisor']);
        $results = $this->voteCountingService->getElectionResults($election);

        $pdf = Pdf::loadView('reports.pdf', compact('election', 'results'))
            ->setPaper('a4', 'portrait');

        $filename = 'Berita_Acara_' . str_replace(['/', ' '], '_', $election->code) . '.pdf';

        return $pdf->download($filename);
    }
}
