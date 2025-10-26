<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostImageController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Všetky API routy pre Dreamhubb BE.
| Načítava ich RouteServiceProvider a sú priradené k "api" middleware.
|
*/

// ========== AUTH ROUTES ==========
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

// ========== POSTS ROUTES ==========
Route::controller(PostController::class)->group(function () {
    Route::get('posts', 'getPosts');
    Route::get('posts/{id}', 'getPost');
    Route::get('my-posts', 'getMyPosts');
    Route::post('post-create', 'createPost');
    Route::put('post-update/{id}', 'updatePost');
    Route::delete('post-delete/{id}', 'deletePost');
});

// ========== POST IMAGES UPLOAD ==========
Route::post('/image-upload', [PostImageController::class, 'store'])
    ->middleware('auth:api'); // obrázky len pre prihlásených

// ========== USER ROUTE ==========
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ========== HEALTH CHECK ==========
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'environment' => app()->environment(),
        'time' => now()->toDateTimeString(),
    ], 200);
});
