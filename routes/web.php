<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\VerifyEmailController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Verify email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::get('/email/verify', function () {
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/success', function () {
    return view('email.success', ['name' => 'aaa']);
})->name('verification.success');

// ✅ Database connection test
Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return response()->json([
            'status' => '✅ Database connection successful',
            'database' => DB::connection()->getDatabaseName(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => '❌ Database connection failed',
            'error' => $e->getMessage(),
        ]);
    }
});

use Illuminate\Support\Facades\Artisan;

Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return '✅ All caches cleared successfully.';
});

use App\Http\Controllers\HealthController;

Route::get('/health', [HealthController::class, 'health']);
Route::get('/db-test', [HealthController::class, 'dbTest']);

Route::get('/db-check', function () {
    try {
        DB::connection()->getPdo();
        return "✅ Database connection successful!";
    } catch (\Exception $e) {
        return "❌ Database connection failed: " . $e->getMessage();
    }
});

