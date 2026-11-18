<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemSettingController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function index(): View
    {
        $settings = SystemSetting::all()->groupBy('group');

        return view('superadmin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => is_array($value) ? json_encode($value) : $value]);
            }
        }

        $this->auditLogService->log(
            action: 'update_system_settings',
            eventType: 'security',
            description: 'Superadmin memperbarui konfigurasi sistem.',
            metadata: $data
        );

        return back()->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}
