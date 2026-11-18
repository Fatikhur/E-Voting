<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    public function download(): StreamedResponse
    {
        $this->auditLogService->log(
            action: 'download_database_backup',
            eventType: 'security',
            description: 'Superadmin mengunduh cadangan (backup) data sistem.'
        );

        $tables = [
            'villages', 'rws', 'rts', 'roles', 'permissions', 'permission_role',
            'users', 'residents', 'elections', 'candidates', 'election_eligible_voters',
            'ballots', 'audit_logs', 'audit_checkpoints', 'election_approvals',
            'observer_reports', 'system_settings'
        ];

        $filename = 'backup_evoting_' . Carbon::now()->format('Y-m-d_H-i-s') . '.json';

        return response()->streamDownload(function () use ($tables) {
            $backupData = [
                'metadata' => [
                    'app' => 'E-Voting RT/RW',
                    'timestamp' => Carbon::now()->toIso8601String(),
                    'generated_by' => auth()->user()->username,
                ],
                'tables' => [],
            ];

            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $backupData['tables'][$table] = DB::table($table)->get()->toArray();
                }
            }

            echo json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }
}
