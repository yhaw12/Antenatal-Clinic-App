<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DailyQueueController;
use App\Http\Controllers\CallLogController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AppointmentWebController;
use App\Http\Controllers\DailyQueueWebController;
use App\Http\Controllers\DashboardWebController;
use App\Http\Controllers\ExportController;

Route::middleware(['auth:sanctum'])->group(function(){
//   Route::get('/daily-queue', [DailyQueueController::class,'index']);
//   Route::post('/appointments/{appointment}/present', [DailyQueueController::class,'markPresent']);
//   Route::post('/appointments/{appointment}/absent', [DailyQueueController::class,'markAbsent']);

  // Route::post('/call-logs', [CallLogController::class,'store']);
  // Route::post('/visits/{visit}/refer', [ReferralController::class,'referToChns']);
  // Route::post('/visits/{visit}/feedback', [ReferralController::class,'addFeedback']);

  Route::post('/admin/users', [AdminUserController::class,'store'])->middleware('role:admin');
  Route::post('/admin/exports/queue', [ExportController::class,'queueExport'])->middleware('role:admin');

  Route::get('/patients/daily-attendance', [DashboardWebController::class, 'index']);
Route::post('/patients/{patient}/attendance', [AppointmentWebController::class, 'store']);

Route::get('/daily-queue/api', [DailyQueueController::class, 'apiAppointments'])->name('daily-queue.api');
});
