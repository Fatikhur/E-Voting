<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Village;
use App\Services\AuditLogService;
use App\Services\ResidentImportExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResidentController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService,
        protected ResidentImportExportService $importExportService
    ) {}

    public function index(Request $request): View
    {
        $query = Resident::with(['village', 'rw', 'rt']);

        if ($request->filled('rw_id')) {
            $query->where('rw_id', $request->rw_id);
        }

        if ($request->filled('rt_id')) {
            $query->where('rt_id', $request->rt_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $cleanDigits = preg_replace('/\D/', '', $search);

            $query->where(function ($q) use ($search, $cleanDigits) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");

                if (!empty($cleanDigits)) {
                    $q->orWhere('nik_masked', 'like', "%{$cleanDigits}%");
                    // Jika memasukkan 16 digit lengkap, cari via hash
                    if (strlen($cleanDigits) === 16) {
                        $q->orWhere('nik_hash', Resident::hashNik($cleanDigits));
                    }
                }
            });
        }

        $residents = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();
        $rws = Rw::all();
        $rts = $request->filled('rw_id') ? Rt::where('rw_id', $request->rw_id)->get() : Rt::all();

        return view('admin.residents.index', compact('residents', 'rws', 'rts'));
    }

    public function create(): View
    {
        $villages = Village::all();
        $rws = Rw::all();
        $rts = Rt::all();

        return view('admin.residents.create', compact('villages', 'rws', 'rts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nik' => 'required|string|size:16',
            'name' => 'required|string|max:255',
            'no_kk' => 'nullable|string|size:16',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'required|in:L,P',
            'address' => 'required|string',
            'village_id' => 'required|exists:villages,id',
            'rw_id' => 'required|exists:rws,id',
            'rt_id' => 'required|exists:rts,id',
            'phone' => 'nullable|string|max:25',
            'status' => 'required|in:active,moved,deceased',
            'is_eligible_voter' => 'required|boolean',
            'notes' => 'nullable|string',
        ], [
            'nik.size' => 'NIK harus tepat berjumlah 16 digit angka.',
        ]);

        $cleanNik = preg_replace('/\D/', '', $request->nik);
        $nikHash = Resident::hashNik($cleanNik);

        if (Resident::where('nik_hash', $nikHash)->exists()) {
            return back()->withInput()->with('error', 'Warga dengan NIK ini sudah terdaftar sebelumnya.');
        }

        $cleanKk = $request->no_kk ? preg_replace('/\D/', '', $request->no_kk) : null;

        $resident = Resident::create([
            'nik_hash' => $nikHash,
            'nik_encrypted' => Crypt::encryptString($cleanNik),
            'nik_masked' => Resident::maskNik($cleanNik),
            'name' => trim($request->name),
            'no_kk_encrypted' => $cleanKk ? Crypt::encryptString($cleanKk) : null,
            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'address' => $request->address,
            'village_id' => $request->village_id,
            'rw_id' => $request->rw_id,
            'rt_id' => $request->rt_id,
            'phone' => $request->phone,
            'status' => $request->status,
            'is_eligible_voter' => $request->boolean('is_eligible_voter'),
            'notes' => $request->notes,
        ]);

        $this->auditLogService->log(
            action: 'create_resident',
            eventType: 'resident',
            description: "Admin menambahkan data warga baru: {$resident->name} ({$resident->nik_masked})",
            metadata: ['resident_id' => $resident->id]
        );

        return redirect()->route('admin.residents.index')
            ->with('success', "Data warga '{$resident->name}' berhasil ditambahkan.");
    }

    public function edit(Resident $resident): View
    {
        $villages = Village::all();
        $rws = Rw::all();
        $rts = Rt::all();

        return view('admin.residents.edit', compact('resident', 'villages', 'rws', 'rts'));
    }

    public function update(Request $request, Resident $resident): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'required|in:L,P',
            'address' => 'required|string',
            'village_id' => 'required|exists:villages,id',
            'rw_id' => 'required|exists:rws,id',
            'rt_id' => 'required|exists:rts,id',
            'phone' => 'nullable|string|max:25',
            'status' => 'required|in:active,moved,deceased',
            'is_eligible_voter' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);

        $resident->update([
            'name' => trim($request->name),
            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'address' => $request->address,
            'village_id' => $request->village_id,
            'rw_id' => $request->rw_id,
            'rt_id' => $request->rt_id,
            'phone' => $request->phone,
            'status' => $request->status,
            'is_eligible_voter' => $request->boolean('is_eligible_voter'),
            'notes' => $request->notes,
        ]);

        $this->auditLogService->log(
            action: 'update_resident',
            eventType: 'resident',
            description: "Admin memperbarui data warga: {$resident->name} ({$resident->nik_masked})",
            metadata: ['resident_id' => $resident->id]
        );

        return redirect()->route('admin.residents.index')
            ->with('success', "Data warga '{$resident->name}' berhasil diperbarui.");
    }

    public function destroy(Resident $resident): RedirectResponse
    {
        $name = $resident->name;
        $masked = $resident->nik_masked;
        $resident->delete();

        $this->auditLogService->log(
            action: 'delete_resident',
            eventType: 'resident',
            description: "Admin menghapus (soft delete) data warga: {$name} ({$masked})",
            metadata: ['resident_id' => $resident->id]
        );

        return back()->with('success', "Data warga '{$name}' telah dihapus.");
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'file.required' => 'File CSV wajib diunggah.',
            'file.mimes' => 'Format file harus berupa CSV (.csv atau .txt).',
        ]);

        try {
            $result = $this->importExportService->importCsv($request->file('file'), auth()->user());

            $msg = "Import selesai! Berhasil: {$result['inserted']} baru, {$result['updated']} diperbarui.";
            if ($result['skipped'] > 0) {
                $msg .= " Dilewati: {$result['skipped']} baris.";
            }

            return back()->with('success', $msg)->with('import_errors', $result['errors']);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file CSV: ' . $e->getMessage());
        }
    }

    public function export(Request $request): StreamedResponse
    {
        $csvContent = $this->importExportService->exportCsv($request->rw_id, $request->rt_id);
        $filename = 'data_warga_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
