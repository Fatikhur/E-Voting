<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Election;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CandidateController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function create(Election $election): View|RedirectResponse
    {
        if (in_array($election->status, ['open', 'closed', 'published'], true)) {
            return redirect()->route('admin.elections.show', $election)
                ->with('error', 'Kandidat tidak dapat ditambahkan pada pemilihan yang sedang berjalan atau selesai.');
        }

        $nextNumber = ($election->candidates()->max('candidate_number') ?? 0) + 1;

        return view('admin.candidates.create', compact('election', 'nextNumber'));
    }

    public function store(Request $request, Election $election): RedirectResponse
    {
        if (in_array($election->status, ['open', 'closed', 'published'], true)) {
            return back()->with('error', 'Kandidat tidak dapat ditambahkan pada pemilihan yang sedang berjalan.');
        }

        $request->validate([
            'candidate_number' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'vision' => 'required|string',
            'mission' => 'required|string',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if (Candidate::where('election_id', $election->id)->where('candidate_number', $request->candidate_number)->exists()) {
            return back()->withInput()->with('error', "Nomor urut {$request->candidate_number} sudah digunakan pada pemilihan ini.");
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('candidates', 'public');
        }

        $candidate = Candidate::create([
            'election_id' => $election->id,
            'candidate_number' => $request->candidate_number,
            'name' => trim($request->name),
            'address' => $request->address,
            'vision' => $request->vision,
            'mission' => $request->mission,
            'bio' => $request->bio,
            'photo_path' => $photoPath,
            'status' => 'active',
        ]);

        $this->auditLogService->log(
            action: 'create_candidate',
            eventType: 'candidate',
            description: "Menambahkan kandidat nomor urut {$candidate->candidate_number}: '{$candidate->name}' pada pemilihan '{$election->title}'",
            metadata: ['election_id' => $election->id, 'candidate_id' => $candidate->id]
        );

        return redirect()->route('admin.elections.show', $election)
            ->with('success', "Kandidat No. Urut {$candidate->candidate_number} ({$candidate->name}) berhasil ditambahkan.");
    }

    public function edit(Candidate $candidate): View|RedirectResponse
    {
        if (in_array($candidate->election->status, ['open', 'closed', 'published'], true)) {
            return redirect()->route('admin.elections.show', $candidate->election)
                ->with('error', 'Kandidat tidak dapat diubah setelah pemilihan dibuka.');
        }

        return view('admin.candidates.edit', compact('candidate'));
    }

    public function update(Request $request, Candidate $candidate): RedirectResponse
    {
        if (in_array($candidate->election->status, ['open', 'closed', 'published'], true)) {
            return back()->with('error', 'Kandidat tidak dapat diubah pada pemilihan yang sedang berjalan.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'vision' => 'required|string',
            'mission' => 'required|string',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $photoPath = $candidate->photo_path;
        if ($request->hasFile('photo')) {
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('candidates', 'public');
        }

        $candidate->update([
            'name' => trim($request->name),
            'address' => $request->address,
            'vision' => $request->vision,
            'mission' => $request->mission,
            'bio' => $request->bio,
            'photo_path' => $photoPath,
        ]);

        $this->auditLogService->log(
            action: 'update_candidate',
            eventType: 'candidate',
            description: "Memperbarui profil kandidat '{$candidate->name}' (No. Urut {$candidate->candidate_number})",
            metadata: ['candidate_id' => $candidate->id]
        );

        return redirect()->route('admin.elections.show', $candidate->election)
            ->with('success', "Profil kandidat No. Urut {$candidate->candidate_number} berhasil diperbarui.");
    }

    public function withdraw(Request $request, Candidate $candidate): RedirectResponse
    {
        $request->validate([
            'withdrawal_reason' => 'required|string|max:500',
        ]);

        $candidate->update([
            'status' => 'withdrawn',
            'withdrawal_reason' => $request->withdrawal_reason,
        ]);

        $this->auditLogService->log(
            action: 'withdraw_candidate',
            eventType: 'candidate',
            description: "Kandidat No. Urut {$candidate->candidate_number} ({$candidate->name}) mengundurkan diri. Alasan: {$request->withdrawal_reason}",
            metadata: ['candidate_id' => $candidate->id, 'reason' => $request->withdrawal_reason]
        );

        return back()->with('success', "Status kandidat '{$candidate->name}' telah diubah menjadi mengundurkan diri.");
    }
}
