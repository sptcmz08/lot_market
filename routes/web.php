<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicMapController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminLotController;
use App\Http\Controllers\Admin\AdminDeliveryTaskController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminMapController;
use App\Http\Controllers\Staff\StaffTaskController;
use Illuminate\Support\Facades\Route;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// Public Routes
Route::get('/', [PublicMapController::class, 'index'])->name('public.map');
Route::get('/lots/status', [PublicMapController::class, 'lotStatus'])->name('public.lots.status');
Route::get('/booking/create', [PublicBookingController::class, 'create'])->name('public.booking.create');
Route::post('/booking', [PublicBookingController::class, 'store'])->name('public.booking.store');
Route::get('/booking/check', [PublicBookingController::class, 'checkForm'])->name('public.booking.check');
Route::post('/booking/check', [PublicBookingController::class, 'check'])->name('public.booking.check.submit');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('/bookings', AdminBookingController::class);
    Route::post('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/assign', [AdminBookingController::class, 'assignStaff'])->name('bookings.assign');

    Route::resource('/lots', AdminLotController::class);
    Route::get('/map', [AdminMapController::class, 'index'])->name('map.index');

    Route::get('/tasks', [AdminDeliveryTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [AdminDeliveryTaskController::class, 'show'])->name('tasks.show');

    Route::resource('/users', AdminUserController::class);
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
});

// Staff Routes
Route::middleware(['auth', 'role:staff,admin'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/tasks', [StaffTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [StaffTaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/start', [StaffTaskController::class, 'start'])->name('tasks.start');
    Route::post('/tasks/{task}/upload-photo', [StaffTaskController::class, 'uploadPhoto'])->name('tasks.upload_photo');
    Route::post('/tasks/{task}/complete', [StaffTaskController::class, 'complete'])->name('tasks.complete');
});

// System Utility Routes for shared hosting (market.after-spa.com)
Route::get('/system/{command}/{secret}', function ($command, $secret) {
    $expectedSecret = env('SYSTEM_SECRET_KEY', 'market-secret-99');
    if ($secret !== $expectedSecret) {
        abort(403, 'Unauthorized.');
    }

    try {
        $allowedCommands = [
            'migrate' => 'migrate --force',
            'migrate-fresh' => 'migrate:fresh --force',
            'seed' => 'db:seed --force',
            'migrate-seed' => 'migrate:fresh --seed --force',
            'optimize' => 'optimize',
            'clear-cache' => 'cache:clear',
            'config-cache' => 'config:cache',
            'route-cache' => 'route:cache',
            'view-clear' => 'view:clear',
            'storage-link' => 'storage:link',
        ];

        if (!array_key_exists($command, $allowedCommands)) {
            return "Command [{$command}] is not allowed or supported.";
        }

        $artisanCommand = $allowedCommands[$command];
        
        echo "<h3>Executing: php artisan {$artisanCommand}</h3>";
        
        \Illuminate\Support\Facades\Artisan::call($artisanCommand);
        
        $output = \Illuminate\Support\Facades\Artisan::output();
        
        echo "<pre style='background: #272822; color: #f8f8f2; padding: 15px; border-radius: 8px; font-family: monospace;'>";
        echo e($output ?: 'Command executed successfully with empty output.');
        echo "</pre>";
        
        return "<p style='color: green; font-weight: bold;'>Execution complete!</p>";
    } catch (\Exception $e) {
        return "<p style='color: red; font-weight: bold;'>Error occurred:</p><pre style='background: #ffd6d6; color: #900; padding: 15px; border-radius: 8px;'>" . e($e->getMessage()) . "</pre>";
    }
})->withoutMiddleware([
    StartSession::class,
    ShareErrorsFromSession::class,
]);
