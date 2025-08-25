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
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::post('validate-password-ajax', 'UserController@validatePasswordAjax')->name('validate-password-ajax');

    Route::group(['roles' => [
        \App\Utilities\Constant::USER_ROLE_SUPER_ADMIN,
    ]], function() {
        Route::resource('approvals', 'ApprovalController')->except(['create', 'edit']);
        Route::get('approvals-ajax', 'ApprovalController@indexAjax')->name('approvals.index-ajax');
        Route::get('approval-histories', 'ApprovalController@indexHistory')->name('approvals.index-history');
        Route::get('approval-histories/{id}', 'ApprovalController@detail')->name('approvals.detail');
        Route::get('approval-history-ajax', 'ApprovalController@indexHistoryAjax')->name('approvals.index-history-ajax');

        Route::resource('users', 'UserController');
        Route::get('deleted-users', 'UserController@indexDeleted')->name('users.deleted');
        Route::put('deleted-users/{id}/restore', 'UserController@restore')->name('users.restore');
        Route::put('deleted-users/{id}/remove', 'UserController@remove')->name('users.remove');
    });

    Route::group(['roles' => [
        \App\Utilities\Constant::USER_ROLE_ADMIN,
    ]], function() {
        Route::resource('notifications', 'NotificationController')->except(['create', 'edit']);
        Route::post('notifications-read-all', 'NotificationController@readAll')->name('notifications.read-all');
    });

    Route::group(['roles' => [
        \App\Utilities\Constant::USER_ROLE_SUPER_ADMIN,
        \App\Utilities\Constant::USER_ROLE_ADMIN,
    ]], function() {
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

        Route::resource('plan-orders', 'PlanOrderController')->only(['create', 'store']);
        Route::get('plan-orders/{id}/detail', 'PlanOrderController@detail')->name('plan-orders.detail');
        Route::get('plan-orders/{id}/print', 'PlanOrderController@print')->name('plan-orders.print');
        Route::get('plan-orders/{id}/after-print', 'PlanOrderController@afterPrint')->name('plan-orders.after-print');
        Route::get('print-plan-orders', 'PlanOrderController@indexPrint')->name('plan-orders.index-print');

        Route::resource('goods-receipts', 'GoodsReceiptController');
        Route::get('goods-receipts/{id}/detail', 'GoodsReceiptController@detail')->name('goods-receipts.detail');
        Route::get('goods-receipts/{id}/print', 'GoodsReceiptController@print')->name('goods-receipts.print');
        Route::get('goods-receipts/{id}/after-print', 'GoodsReceiptController@afterPrint')->name('goods-receipts.after-print');
        Route::get('goods-receipt-ajax', 'GoodsReceiptController@indexAjax')->name('goods-receipts.index-ajax');
        Route::get('print-goods-receipts', 'GoodsReceiptController@indexPrint')->name('goods-receipts.index-print');
        Route::get('edit-goods-receipts', 'GoodsReceiptController@indexEdit')->name('goods-receipts.index-edit');

        Route::resource('product-transfers', 'ProductTransferController')->except(['edit', 'update']);
        Route::get('product-transfers/{id}/detail', 'ProductTransferController@detail')->name('product-transfers.detail');
        Route::get('product-transfers/{id}/print', 'ProductTransferController@print')->name('product-transfers.print');
        Route::get('product-transfers/{id}/after-print', 'ProductTransferController@afterPrint')->name('product-transfers.after-print');
        Route::get('print-product-transfers', 'ProductTransferController@indexPrint')->name('product-transfers.index-print');

        Route::resource('sales-orders', 'SalesOrderController');
        Route::get('sales-orders/{id}/detail', 'SalesOrderController@detail')->name('sales-orders.detail');
        Route::get('sales-orders/{id}/print', 'SalesOrderController@print')->name('sales-orders.print');
        Route::get('sales-orders/{id}/after-print', 'SalesOrderController@afterPrint')->name('sales-orders.after-print');
        Route::get('sales-orders-ajax', 'SalesOrderController@indexAjax')->name('sales-orders.index-ajax');
        Route::get('print-sales-orders', 'SalesOrderController@indexPrint')->name('sales-orders.index-print');
        Route::get('edit-sales-orders', 'SalesOrderController@indexEdit')->name('sales-orders.index-edit');

        Route::resource('delivery-orders', 'DeliveryOrderController');
        Route::get('delivery-orders/{id}/detail', 'DeliveryOrderController@detail')->name('delivery-orders.detail');
        Route::get('delivery-orders/{id}/print', 'DeliveryOrderController@print')->name('delivery-orders.print');
        Route::get('delivery-orders/{id}/after-print', 'DeliveryOrderController@afterPrint')->name('delivery-orders.after-print');
        Route::get('print-delivery-orders', 'DeliveryOrderController@indexPrint')->name('delivery-orders.index-print');
        Route::get('edit-delivery-orders', 'DeliveryOrderController@indexEdit')->name('delivery-orders.index-edit');

        Route::resource('returns', 'ReturnController')->only(['index']);
    });

    Route::group(['roles' => [
        \App\Utilities\Constant::USER_ROLE_SUPER_ADMIN,
        \App\Utilities\Constant::USER_ROLE_FINANCE,
    ]], function() {
        Route::resource('account-payables', 'AccountPayableController')->only(['index', 'store']);
        Route::get('account-payables/{id}/detail', 'AccountPayableController@detail')->name('account-payables.detail');
        Route::get('account-payables/{id}/payment', 'AccountPayableController@payment')->name('account-payables.payment');

        Route::resource('account-receivables', 'AccountReceivableController')->only(['index', 'store']);
        Route::get('account-receivables/{id}/detail', 'AccountReceivableController@detail')->name('account-receivables.detail');
        Route::get('account-receivables/{id}/payment', 'AccountReceivableController@payment')->name('account-receivables.payment');
        Route::get('check-invoices', 'AccountReceivableController@checkInvoice')->name('account-receivables.check-invoice');
    });
});

Auth::routes(['verify' => true]);
