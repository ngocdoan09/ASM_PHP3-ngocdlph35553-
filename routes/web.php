<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;

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

//User
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/tim-kiem', [HomeController::class, 'search'])->name('search');
Route::get('/tin-tuc-moi-nhat', [HomeController::class, 'newPost'])->name('newPost');
Route::get('/tin-nong', [HomeController::class, 'hotPost'])->name('hotPost');
Route::get('/xem-nhieu-nhat', [HomeController::class, 'viewPost'])->name('viewPost');

Route::get('/bai-viet/{post:slug}', [PostController::class, 'show'])->name('posts.show');

Route::get('/chuyen-muc/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/tat-ca-chuyen-muc', [CategoryController::class, 'index'])->name('categories.index');
