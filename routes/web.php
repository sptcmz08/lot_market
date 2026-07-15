<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PublicMapController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\SystemCommandController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminLotController;
use App\Http\Controllers\Admin\AdminDeliveryTaskController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminMapController;
use App\Http\Controllers\Admin\AdminLotPhotoReviewController;
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
    Route::post('/dashboard/bookings/{booking}/front-store-collection', [AdminDashboardController::class, 'collectFrontStore'])->name('dashboard.front_store_collection');

    Route::resource('/bookings', AdminBookingController::class)->except(['create', 'store']);
    Route::post('/bookings/{booking}/payment-slip', [AdminBookingController::class, 'uploadPaymentSlip'])->name('bookings.payment_slip');
    Route::post('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/assign', [AdminBookingController::class, 'assignStaff'])->name('bookings.assign');

    Route::resource('/lots', AdminLotController::class)->except(['show']);
    Route::get('/map', [AdminMapController::class, 'index'])->name('map.index');

    Route::get('/tasks', [AdminDeliveryTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [AdminDeliveryTaskController::class, 'show'])->name('tasks.show');
    Route::get('/lot-photo-reviews', [AdminLotPhotoReviewController::class, 'index'])->name('lot_photo_reviews.index');
    Route::get('/lot-photo-reviews/status', [AdminLotPhotoReviewController::class, 'status'])->name('lot_photo_reviews.status');
    Route::post('/lot-photo-reviews/{photo}/approve', [AdminLotPhotoReviewController::class, 'approve'])->name('lot_photo_reviews.approve');
    Route::post('/lot-photo-reviews/{photo}/reject', [AdminLotPhotoReviewController::class, 'reject'])->name('lot_photo_reviews.reject');

    Route::resource('/users', AdminUserController::class)->except(['show']);
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
});

// Staff Routes
Route::middleware(['auth', 'role:staff,admin'])->prefix('staff')->name('staff.')->group(function () {
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
