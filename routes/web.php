<?php

use Illuminate\Support\Facades\Auth;
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

Route::middleware(['auth', 'roles'])->group(function() {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    Route::resource('users', 'UserController');
});

Auth::routes(['verify' => true]);
