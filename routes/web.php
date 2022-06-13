<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhoneLoginController;

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
Route::middleware('throttle:180,1')->get('verify', [PhoneLoginController::class, 'index'])->name('verification-phone');

Route::middleware('throttle:30,1')->post('verification-form-phone', [PhoneLoginController::class, 'store']);
Route::middleware('throttle:30,1')->post('verify-phone-sendcode', [PhoneLoginController::class, 'sendcode']);

Route::get('sendemail', [\App\Http\Controllers\PhoneLoginController::class,'sendEmail'])->name('sendemail');
