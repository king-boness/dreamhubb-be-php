<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostImageController;
use App\Http\Controllers\UploadController;

/*
|--------------------------------------------------------------------------
| API Routes – Dreamhubb Backend
|--------------------------------------------------------------------------
| Všetky API routy aplikácie – rozdelené podľa logických sekcií.
| Od tejto verzie sú debug routy odstránené.
*/

// ========== AUTH ROUTES ==========
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

// ========== EMAIL VERIFICATION ROUTES ==========
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['auth:api', 'signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
    ->middleware('auth:api');

// ========== POSTS ROUTES ==========

// Verejné (bez loginu)
Route::controller(PostController::class)->group(function () {
    Route::get('posts', 'getAllPosts');
    Route::get('posts/{id}', 'getPost');
});

// Chránené (len prihlásený používateľ)
Route::group(['middleware' => ['auth:api']], function () {
    Route::controller(PostController::class)->group(function () {
        Route::get('my-posts', 'getMyPosts');
        Route::post('post-create', 'createPost');
        Route::put('post-update/{id}', 'updatePost');
        Route::delete('post-delete/{id}', 'deletePost');
    });
});

// ========== POST IMAGES UPLOAD ==========
Route::post('/image-upload', [PostImageController::class, 'store'])
    ->middleware('auth:api');

// ========== GLOBAL UPLOAD (Cloudinary / Local) ==========
Route::post('/upload', [UploadController::class, 'upload'])
    ->middleware('auth:api');

// ========== USER PROFILE ROUTES ==========
Route::group(['middleware' => ['auth:api', 'jwt.refresh']], function () {
    Route::get('/user', [UserController::class, 'me']);
    Route::put('/user/update', [UserController::class, 'update']);
    Route::post('/user/profile-picture', [UserController::class, 'uploadProfilePicture']);
    Route::delete('/user/profile-picture', [UserController::class, 'deleteProfilePicture']);
    Route::post('/user/change-password', [UserController::class, 'changePassword']);
});

// ========== HEALTH CHECK ==========
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'environment' => app()->environment(),
        'time' => now()->toDateTimeString(),
    ], 200);
});
