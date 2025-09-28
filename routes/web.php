<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\Api\ScheduleApiController;
use App\Http\Controllers\PatientWebController;
use App\Http\Controllers\AppointmentWebController;
use App\Http\Controllers\CallLogWebController;
use App\Http\Controllers\DashboardWebController;
use App\Http\Controllers\ReferralWebController;
use App\Http\Controllers\ReportWebController;
use App\Http\Controllers\ExportWebController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleWebController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserActivityLogController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('login', [UserController::class, 'showLoginForm'])->name('login');
    Route::post('login', [UserController::class, 'login']);
});

// Temporary registration (dev/demo only)
Route::middleware('guest')->group(function () {
    Route::get('temp-register', [UserController::class, 'showTempRegisterForm'])->name('temp.register');
    Route::post('temp-register', [UserController::class, 'tempRegister'])->name('temp.register.post');
});

// Logout (auth)
Route::post('logout', [UserController::class, 'logout'])->middleware('auth')->name('logout');

// Admin-only user management routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::resource('users', AdminUserController::class)->except('show');
    Route::post('users/{user}/toggle-permission', [AdminUserController::class, 'togglePermission'])->name('users.toggle-permission');
});

// Assign role to existing user (protected by gate)
Route::middleware('auth')->post('/users/{user}/assign-role', [UserController::class, 'assignRole'])->name('users.assign-role');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardWebController::class, 'dashboard'])->name('dashboard');

    // Patients
    Route::get('/patients', [PatientWebController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [PatientWebController::class, 'create'])->name('patients.create');
    Route::post('/patients', [PatientWebController::class, 'store'])->name('patients.store');
    Route::get('/patients/{id}', [PatientWebController::class, 'show'])->name('patients.show');
    Route::get('/patients/{id}/edit', [PatientWebController::class, 'edit'])->name('patients.edit');
    Route::put('/patients/{id}', [PatientWebController::class, 'update'])->name('patients.update');
    

    // Appointments
    Route::get('/appointments', [AppointmentWebController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentWebController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentWebController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{id}', [AppointmentWebController::class, 'show'])->name('appointments.show');

    // Daily queue
    Route::get('/daily-queue', [DashboardWebController::class, 'index'])->name('daily-queue');
    Route::post('/daily-queue/mark-present', [DashboardWebController::class, 'markPresent'])->name('daily-queue.mark-present');
    Route::post('/daily-queue/mark-absent', [DashboardWebController::class, 'markAbsent'])->name('daily-queue.mark-absent');

    // Call Logs
    Route::get('/call-logs', [CallLogWebController::class, 'index'])->name('call-logs');
    Route::get('/call-logs/new', [CallLogWebController::class, 'create'])->name('call-logs.new');
    Route::post('/call-logs', [CallLogWebController::class, 'store'])->name('call-logs.store');

    // CHNS referrals
    Route::get('/referrals', [ReferralWebController::class, 'index'])->name('referrals.index');
    Route::get('/referrals/new', [ReferralWebController::class, 'create'])->name('referrals.create');
    Route::post('/referrals', [ReferralWebController::class, 'store'])->name('referrals.store');

    // Schedule Manager
    Route::get('/schedule-manager', [ScheduleWebController::class, 'index'])->name('schedule.manager');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Alerts & Notifications
    Route::get('alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::get('notifications', [AlertController::class, 'view'])->name('notifications.index');
    Route::post('alerts/{alert}/read', [AlertController::class, 'read'])->name('alerts.read');
    Route::post('alerts/dismiss-all', [AlertController::class, 'dismissAll'])->name('alerts.dismiss-all');
    Route::post('alerts/custom/create', [AlertController::class, 'createCustom'])->name('alerts.custom.create')->middleware('role:admin');

    // Admin-only routes (reports, exports, activity logs)
    Route::middleware(['can:view-reports'])->group(function () {
        Route::get('/reports', [ReportWebController::class, 'index'])->name('reports.index');
        Route::get('/reports/generate', [ReportWebController::class, 'generate'])->name('reports.generate');
    });

    Route::middleware(['can:manage-exports'])->group(function () {
        Route::get('/exports', [ExportWebController::class, 'index'])->name('exports.index');
        Route::post('/exports/queue', [ExportWebController::class, 'queue'])->name('exports.queue');
        Route::get('/exports/history', [ExportWebController::class, 'history'])->name('exports.history');
    });

    Route::middleware(['can:view-activity-logs'])->prefix('admin')->group(function () {
        Route::get('activity-logs', [UserActivityLogController::class, 'index'])->name('admin.activity-logs.index');
        Route::get('activity-logs/{userActivityLog}', [UserActivityLogController::class, 'show'])->name('admin.activity-logs.show');
        Route::delete('activity-logs/{userActivityLog}', [UserActivityLogController::class, 'destroy'])->name('admin.activity-logs.destroy');
    });

    Route::get('/patients/search', [PatientWebController::class, 'search'])->middleware('auth');

    // API for schedule
    Route::prefix('api')->group(function () {
        Route::get('/schedule', [ScheduleApiController::class, 'list']);
        Route::post('/schedule/{appointment}/arrive', [ScheduleApiController::class, 'markArrived']);
        Route::post('/schedule/{appointment}/status', [ScheduleApiController::class, 'updateStatus']);
        Route::post('/schedule/{appointment}/call-log', [ScheduleApiController::class, 'logCall']);
    });

    // Search suggestions (AJAX)
Route::get('/search/patients', [SearchController::class, 'patients'])->name('search.patients');
});