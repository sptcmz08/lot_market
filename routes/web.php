<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PublicMapController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\SystemCommandController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminLotController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminMapController;
use App\Http\Controllers\Admin\AdminInstallationReviewController;
use App\Http\Controllers\Staff\StaffBookingController;
use App\Http\Controllers\Staff\StaffTaskController;
use Illuminate\Support\Facades\Route;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// Public Routes
Route::get('/', [PublicBookingController::class, 'create'])->name('public.booking.create');
Route::get('/map', [PublicMapController::class, 'index'])->name('public.map');
Route::get('/lots/status', [PublicMapController::class, 'lotStatus'])->name('public.lots.status');
Route::redirect('/booking/create', '/');
Route::post('/booking', [PublicBookingController::class, 'store'])->name('public.booking.store');
Route::get('/booking/check', [PublicBookingController::class, 'checkForm'])->name('public.booking.check');
Route::post('/booking/check', [PublicBookingController::class, 'check'])->name('public.booking.check.submit');
Route::get('/media/{path}', [MediaController::class, 'show'])->where('path', '.*')->name('media.show');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/front-store-export', [AdminDashboardController::class, 'exportFrontStore'])->name('dashboard.front_store_export');
    Route::post('/dashboard/bookings/{booking}/front-store-collection', [AdminDashboardController::class, 'collectFrontStore'])->name('dashboard.front_store_collection');

    Route::resource('/bookings', AdminBookingController::class)->except(['create', 'store']);
    Route::post('/bookings/{booking}/payment-slip', [AdminBookingController::class, 'uploadPaymentSlip'])->name('bookings.payment_slip');
    Route::post('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');

    Route::resource('/lots', AdminLotController::class)->except(['show']);
    Route::get('/map', [AdminMapController::class, 'index'])->name('map.index');

    Route::post('/bookings/{booking}/lot-review/approve', [AdminInstallationReviewController::class, 'approveLot'])->name('bookings.lot_review.approve');
    Route::post('/bookings/{booking}/lot-review/reject', [AdminInstallationReviewController::class, 'rejectLot'])->name('bookings.lot_review.reject');
    Route::post('/bookings/{booking}/work-review/approve', [AdminInstallationReviewController::class, 'approveWork'])->name('bookings.work_review.approve');
    Route::post('/bookings/{booking}/work-review/reject', [AdminInstallationReviewController::class, 'rejectWork'])->name('bookings.work_review.reject');

    Route::resource('/users', AdminUserController::class)->except(['show']);
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
});

// Staff Routes
Route::middleware(['auth', 'role:staff,admin'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/bookings', [StaffBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}/camera', [StaffBookingController::class, 'camera'])->name('bookings.camera');
    Route::post('/bookings/{booking}/photos', [StaffBookingController::class, 'uploadPhotos'])->name('bookings.photos');
    Route::post('/bookings/{booking}/submit-lot', [StaffBookingController::class, 'submitLot'])->name('bookings.submit_lot');
    Route::post('/bookings/{booking}/submit-work', [StaffBookingController::class, 'submitWork'])->name('bookings.submit_work');
    Route::get('/tasks', [StaffTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [StaffTaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/start', [StaffTaskController::class, 'start'])->name('tasks.start');
    Route::post('/tasks/{task}/upload-photo', [StaffTaskController::class, 'uploadPhoto'])->name('tasks.upload_photo');
    Route::get('/tasks/{task}/review-status', [StaffTaskController::class, 'reviewStatus'])->name('tasks.review_status');
    Route::post('/tasks/{task}/complete', [StaffTaskController::class, 'complete'])->name('tasks.complete');
    Route::post('/tasks/{task}/problem', [StaffTaskController::class, 'reportProblem'])->name('tasks.problem');
});

// System Utility Routes for shared hosting (market.after-spa.com)
Route::get('/system/{command}/{secret}', SystemCommandController::class)->withoutMiddleware([
    StartSession::class,
    ShareErrorsFromSession::class,
]);
