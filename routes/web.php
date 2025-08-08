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

    Route::resource('marketings', 'MarketingController');
    Route::get('deleted-marketings', 'MarketingController@indexDeleted')->name('marketings.deleted');
    Route::put('deleted-marketings/{id}/restore', 'MarketingController@restore')->name('marketings.restore');
    Route::put('deleted-marketings/{id}/remove', 'MarketingController@remove')->name('marketings.remove');
    Route::get('export-marketings', 'MarketingController@export')->name('marketings.export');

    Route::resource('suppliers', 'SupplierController');
    Route::get('deleted-suppliers', 'SupplierController@indexDeleted')->name('suppliers.deleted');
    Route::put('deleted-suppliers/{id}/restore', 'SupplierController@restore')->name('suppliers.restore');
    Route::put('deleted-suppliers/{id}/remove', 'SupplierController@remove')->name('suppliers.remove');
    Route::get('export-suppliers', 'SupplierController@export')->name('suppliers.export');

    Route::resource('customers', 'CustomerController');
    Route::get('deleted-customers', 'CustomerController@indexDeleted')->name('customers.deleted');
    Route::put('deleted-customers/{id}/restore', 'CustomerController@restore')->name('customers.restore');
    Route::put('deleted-customers/{id}/remove', 'CustomerController@remove')->name('customers.remove');
    Route::get('export-customers', 'CustomerController@export')->name('customers.export');

    Route::resource('warehouses', 'WarehouseController');
    Route::get('deleted-warehouses', 'WarehouseController@indexDeleted')->name('warehouses.deleted');
    Route::put('deleted-warehouses/{id}/restore', 'WarehouseController@restore')->name('warehouses.restore');
    Route::put('deleted-warehouses/{id}/remove', 'WarehouseController@remove')->name('warehouses.remove');
    Route::get('export-warehouses', 'WarehouseController@export')->name('warehouses.export');

    Route::resource('prices', 'PriceController');
    Route::get('deleted-prices', 'PriceController@indexDeleted')->name('prices.deleted');
    Route::put('deleted-prices/{id}/restore', 'PriceController@restore')->name('prices.restore');
    Route::put('deleted-prices/{id}/remove', 'PriceController@remove')->name('prices.remove');

    Route::resource('categories', 'CategoryController');
    Route::get('deleted-categories', 'CategoryController@indexDeleted')->name('categories.deleted');
    Route::put('deleted-categories/{id}/restore', 'CategoryController@restore')->name('categories.restore');
    Route::put('deleted-categories/{id}/remove', 'CategoryController@remove')->name('categories.remove');
    Route::get('export-categories', 'CategoryController@export')->name('categories.export');

    Route::resource('subcategories', 'SubcategoryController');
    Route::get('subcategories-ajax', 'SubcategoryController@indexAjax')->name('subcategories.index-ajax');
    Route::get('deleted-subcategories', 'SubcategoryController@indexDeleted')->name('subcategories.deleted');
    Route::put('deleted-subcategories/{id}/restore', 'SubcategoryController@restore')->name('subcategories.restore');
    Route::put('deleted-subcategories/{id}/remove', 'SubcategoryController@remove')->name('subcategories.remove');
    Route::get('export-subcategories', 'SubcategoryController@export')->name('subcategories.export');

    Route::resource('units', 'UnitController');
    Route::get('deleted-units', 'UnitController@indexDeleted')->name('units.deleted');
    Route::put('deleted-units/{id}/restore', 'UnitController@restore')->name('units.restore');
    Route::put('deleted-units/{id}/remove', 'UnitController@remove')->name('units.remove');

    Route::resource('products', 'ProductController');
    Route::get('products/{id}/stock', 'ProductController@stock')->name('products.stock');
    Route::put('products/{id}/stock', 'ProductController@updateStock')->name('products.update-stock');
    Route::get('products-ajax', 'ProductController@indexAjax')->name('products.index-ajax');
    Route::post('products-stock-ajax', 'ProductController@checkStockAjax')->name('products.check-stock-ajax');
    Route::get('deleted-products', 'ProductController@indexDeleted')->name('products.deleted');
    Route::put('deleted-products/{id}/restore', 'ProductController@restore')->name('products.restore');
    Route::put('deleted-products/{id}/remove', 'ProductController@remove')->name('products.remove');
    Route::get('export-products', 'ProductController@export')->name('products.export');

    Route::resource('goods-receipts', 'GoodsReceiptController');
    Route::get('goods-receipts/{id}/detail', 'GoodsReceiptController@detail')->name('goods-receipts.detail');
    Route::get('goods-receipts/{id}/print', 'GoodsReceiptController@print')->name('goods-receipts.print');
    Route::get('goods-receipts/{id}/after-print', 'GoodsReceiptController@afterPrint')->name('goods-receipts.after-print');
    Route::get('print-goods-receipts', 'GoodsReceiptController@indexPrint')->name('goods-receipts.index-print');
    Route::get('edit-goods-receipts', 'GoodsReceiptController@indexEdit')->name('goods-receipts.index-edit');

    Route::resource('product-transfers', 'ProductTransferController')->except(['edit', 'update']);
    Route::get('product-transfers/{id}/detail', 'ProductTransferController@detail')->name('product-transfers.detail');
    Route::get('product-transfers/{id}/print', 'ProductTransferController@print')->name('product-transfers.print');
    Route::get('product-transfers/{id}/after-print', 'ProductTransferController@afterPrint')->name('product-transfers.after-print');
    Route::get('print-product-transfers', 'ProductTransferController@indexPrint')->name('product-transfers.index-print');

    Route::resource('sales-orders', 'SalesOrderController');
    Route::get('sales-orders/{id}/detail', 'SalesOrderController@detail')->name('sales-orders.detail');
    Route::get('edit-sales-orders', 'SalesOrderController@indexEdit')->name('sales-orders.index-edit');
});

Auth::routes(['verify' => true]);
