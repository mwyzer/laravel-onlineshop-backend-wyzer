<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('pages.dashboard');
// });

Route::get('/', function () {
    return view('pages.auth.login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('home', function() {
        return view('pages.dashboard');
    })->name('home');
    // User Route
    Route::resource('user', UserController::class);
    // Category Route
    Route::resource('category', CategoryController::class);
    // Product Route
    Route::resource('product', ProductController::class);
});
