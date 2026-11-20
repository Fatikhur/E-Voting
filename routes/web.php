<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\DptController;
use App\Http\Controllers\Admin\ElectionApprovalAdminController;
use App\Http\Controllers\Admin\ElectionController;
use App\Http\Controllers\Admin\ResidentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Pengawas\AuditTrailController;
use App\Http\Controllers\Pengawas\ElectionApprovalSupervisorController;
use App\Http\Controllers\Pengawas\MonitoringController;
use App\Http\Controllers\Pengawas\ObserverReportController;
use App\Http\Controllers\Pengawas\PengawasDashboardController;
use App\Http\Controllers\Report\ElectionReportController;
use App\Http\Controllers\SuperAdmin\AuditLogViewerController;
use App\Http\Controllers\SuperAdmin\BackupController;
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\SystemSettingController;
use App\Http\Controllers\SuperAdmin\TerritoryController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Http\Controllers\Voter\VoterDashboardController;
use App\Http\Controllers\Voter\VotingProcessController;
use Illuminate\Support\Facades\Route;

// Redirect Halaman Utama
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Autentikasi Tamu (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login');
});

// Area Terotentikasi & Akun Aktif
Route::middleware(['auth', 'active.user'])->group(function () {
    // Logout & Profil
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/password', [PasswordController::class, 'show'])->name('password.edit');
    Route::post('/password', [PasswordController::class, 'update'])->name('password.update');

    // Root Dashboard Router
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // ROLE: SUPER ADMIN
    // ==========================================
    Route::middleware('role:superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

        // Manajemen Pengguna
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}/status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');

        // Manajemen Wilayah
        Route::get('/territories', [TerritoryController::class, 'index'])->name('territories.index');
        Route::post('/territories/villages', [TerritoryController::class, 'storeVillage'])->name('territories.store-village');
        Route::post('/territories/rws', [TerritoryController::class, 'storeRw'])->name('territories.store-rw');
        Route::post('/territories/rts', [TerritoryController::class, 'storeRt'])->name('territories.store-rt');

        // Audit Trail & Pemeriksaan Integritas Hash
        Route::get('/audit', [AuditLogViewerController::class, 'index'])->name('audit.index');
        Route::post('/audit/verify', [AuditLogViewerController::class, 'verify'])->name('audit.verify');
        Route::post('/audit/checkpoint', [AuditLogViewerController::class, 'createCheckpoint'])->name('audit.checkpoint');

        // Pengaturan Sistem & Backup
        Route::get('/settings', [SystemSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SystemSettingController::class, 'update'])->name('settings.update');
        Route::get('/backup/download', [BackupController::class, 'download'])->name('backup.download');
    });

    // ==========================================
    // ROLE: ADMIN (PANITIA PEMILIHAN)
    // ==========================================
    Route::middleware('role:superadmin,admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Data Warga
        Route::get('/residents/export', [ResidentController::class, 'export'])->name('residents.export');
        Route::post('/residents/import', [ResidentController::class, 'import'])->name('residents.import');
        Route::resource('residents', ResidentController::class);

        // Pemilihan
        Route::resource('elections', ElectionController::class);
        Route::post('/elections/{election}/schedule', [ElectionController::class, 'schedule'])->name('elections.schedule');
        Route::post('/elections/{election}/open', [ElectionController::class, 'open'])->name('elections.open');
        Route::post('/elections/{election}/close', [ElectionController::class, 'close'])->name('elections.close');

        // Kandidat
        Route::get('/elections/{election}/candidates/create', [CandidateController::class, 'create'])->name('candidates.create');
        Route::post('/elections/{election}/candidates', [CandidateController::class, 'store'])->name('candidates.store');
        Route::get('/candidates/{candidate}/edit', [CandidateController::class, 'edit'])->name('candidates.edit');
        Route::put('/candidates/{candidate}', [CandidateController::class, 'update'])->name('candidates.update');
        Route::post('/candidates/{candidate}/withdraw', [CandidateController::class, 'withdraw'])->name('candidates.withdraw');

        // DPT
        Route::get('/elections/{election}/dpt', [DptController::class, 'index'])->name('dpt.index');
        Route::post('/elections/{election}/dpt/sync', [DptController::class, 'sync'])->name('dpt.sync');

        // Persetujuan & Publikasi Hasil Pihak Admin
        Route::post('/elections/{election}/approve', [ElectionApprovalAdminController::class, 'approve'])->name('elections.approve');
        Route::post('/elections/{election}/publish', [ElectionApprovalAdminController::class, 'publish'])->name('elections.publish');
    });

    // ==========================================
    // ROLE: PENGAWAS (INDEPENDENT SUPERVISOR)
    // ==========================================
    Route::middleware('role:superadmin,pengawas')->prefix('pengawas')->name('pengawas.')->group(function () {
        Route::get('/dashboard', [PengawasDashboardController::class, 'index'])->name('dashboard');
        Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('/monitoring/{election}', [MonitoringController::class, 'show'])->name('monitoring.show');

        // Audit Pengawasan & Integritas Hash
        Route::get('/audit', [AuditTrailController::class, 'index'])->name('audit.index');
        Route::post('/audit/verify', [AuditTrailController::class, 'verify'])->name('audit.verify');
        Route::post('/audit/checkpoint', [AuditTrailController::class, 'createCheckpoint'])->name('audit.checkpoint');

        // Laporan Temuan & Pengawasan
        Route::resource('reports', ObserverReportController::class)->only(['index', 'create', 'store', 'show']);

        // Persetujuan Hasil Pihak Pengawas
        Route::post('/elections/{election}/approve', [ElectionApprovalSupervisorController::class, 'approve'])->name('elections.approve');
    });

    // ==========================================
    // ROLE: VOTER / PEMILIH
    // ==========================================
    Route::middleware('role:voter')->group(function () {
        Route::get('/voter/dashboard', [VoterDashboardController::class, 'index'])->name('voter.dashboard');
        Route::get('/voting/{election}', [VotingProcessController::class, 'showCandidates'])->name('voting.candidates');
        Route::get('/voting/{election}/confirm', [VotingProcessController::class, 'confirmChoice'])->name('voting.confirm');
        Route::post('/voting/{election}/cast', [VotingProcessController::class, 'cast'])
            ->middleware('throttle:voting')
            ->name('voting.cast');
        Route::get('/voting/{election}/receipt', [VotingProcessController::class, 'receipt'])->name('voting.receipt');
    });

    // ==========================================
    // LAPORAN RESMI & HASIL PEMILIHAN
    // ==========================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/{election}', [ElectionReportController::class, 'show'])->name('results');
        Route::get('/{election}/print', [ElectionReportController::class, 'print'])->name('print');
        Route::get('/{election}/pdf', [ElectionReportController::class, 'downloadPdf'])->name('pdf');
    });
});
