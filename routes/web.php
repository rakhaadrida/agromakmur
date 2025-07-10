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
    Route::get('deleted-users', 'UserController@indexDeleted')->name('users.deleted');
    Route::put('deleted-users/{id}/restore', 'UserController@restore')->name('users.restore');
    Route::put('deleted-users/{id}/remove', 'UserController@remove')->name('users.remove');

    Route::resource('suppliers', 'SupplierController');
    Route::get('deleted-suppliers', 'SupplierController@indexDeleted')->name('suppliers.deleted');
    Route::put('deleted-suppliers/{id}/restore', 'SupplierController@restore')->name('suppliers.restore');
    Route::put('deleted-suppliers/{id}/remove', 'SupplierController@remove')->name('suppliers.remove');

    Route::resource('marketings', 'MarketingController');
    Route::get('deleted-marketings', 'MarketingController@indexDeleted')->name('marketings.deleted');
    Route::put('deleted-marketings/{id}/restore', 'MarketingController@restore')->name('marketings.restore');
    Route::put('deleted-marketings/{id}/remove', 'MarketingController@remove')->name('marketings.remove');
});

Auth::routes(['verify' => true]);
