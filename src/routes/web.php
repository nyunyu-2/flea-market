<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\ItemDetailController;
use App\Http\Controllers\PurchaseController;


use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [ItemController::class, 'index'])->name('items.index');

Route::post('/', [ItemController::class, 'store'])->name('items.store');
Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');

Route::get('/sell',[ItemController::class,'create'])->name('sell')
    ->middleware('auth')
    ->name('sell');

Route::get('/mypage', [MyPageController::class, 'index'])
    ->middleware('auth')
    ->name('mypage');
Route::get('/mypage/profile', [MyPageController::class, 'editProfile'])->name('mypage.profile');
Route::post('/mypage/profile', [MyPageController::class, 'updateProfile'])->name('mypage.profile.update');


Route::post('/items/{item}/like', [ItemDetailController::class, 'like'])->name('items.like');
Route::delete('/items/{item}/like', [ItemDetailController::class, 'unlike'])->name('items.unlike');

Route::get('/items/{item}', [ItemDetailController::class, 'show'])->name('items.show');
Route::post('/comments', [ItemDetailController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');


Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');
Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'success'])->name('purchase.success');
Route::get('/purchase/cancel/{item_id}', [PurchaseController::class, 'cancel'])->name('purchase.cancel');


Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])
    ->middleware('auth')
    ->name('purchase.address.update');






