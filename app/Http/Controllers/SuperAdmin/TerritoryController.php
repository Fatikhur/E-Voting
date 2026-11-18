<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Village;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TerritoryController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function index(): View
    {
        $villages = Village::with(['rws.rts'])->get();

        return view('superadmin.territories.index', compact('villages'));
    }

    public function storeVillage(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:villages,code',
            'name' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'regency' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
        ]);

        $village = Village::create($request->all());

        $this->auditLogService->log(
            action: 'create_village',
            eventType: 'resident',
            description: "Menambahkan Kelurahan baru: {$village->name} ({$village->code})",
            metadata: ['village_id' => $village->id]
        );

        return back()->with('success', "Kelurahan '{$village->name}' berhasil ditambahkan.");
    }

    public function storeRw(Request $request): RedirectResponse
    {
        $request->validate([
            'village_id' => 'required|exists:villages,id',
            'rw_number' => 'required|string|max:10',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $rwNumber = str_pad(preg_replace('/\D/', '', $request->rw_number), 2, '0', STR_PAD_LEFT);

        $rw = Rw::firstOrCreate(
            ['village_id' => $request->village_id, 'rw_number' => $rwNumber],
            [
                'name' => $request->name ?? "RW {$rwNumber}",
                'description' => $request->description,
            ]
        );

        $this->auditLogService->log(
            action: 'create_rw',
            eventType: 'resident',
            description: "Menambahkan RW baru: RW {$rwNumber} di Kelurahan ID #{$request->village_id}",
            metadata: ['rw_id' => $rw->id]
        );

        return back()->with('success', "RW {$rwNumber} berhasil didaftarkan.");
    }

    public function storeRt(Request $request): RedirectResponse
    {
        $request->validate([
            'rw_id' => 'required|exists:rws,id',
            'rt_number' => 'required|string|max:10',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $rtNumber = str_pad(preg_replace('/\D/', '', $request->rt_number), 2, '0', STR_PAD_LEFT);

        $rt = Rt::firstOrCreate(
            ['rw_id' => $request->rw_id, 'rt_number' => $rtNumber],
            [
                'name' => $request->name ?? "RT {$rtNumber}",
                'description' => $request->description,
            ]
        );

        $this->auditLogService->log(
            action: 'create_rt',
            eventType: 'resident',
            description: "Menambahkan RT baru: RT {$rtNumber} di RW ID #{$request->rw_id}",
            metadata: ['rt_id' => $rt->id]
        );

        return back()->with('success', "RT {$rtNumber} berhasil didaftarkan.");
    }
}
