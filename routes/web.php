<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// heartbeat
Route::get('/heartbeat', [App\Http\Controllers\HomeController::class, 'heartbeat']);

// assets
    Route::post('/upload-assets', [App\Http\Controllers\Controller::class, 'uploadAsset']);
    
//user
// Route::get('/users/upload', [App\Http\Controllers\User\UserController::class, 'upload']);
// Route::get('/users/upload/show', [App\Http\Controllers\User\UserController::class, 'show_upload']);
// Route::post('/users/upload/finish', [App\Http\Controllers\User\UserController::class, 'finish_upload']);
// Route::post('/users/upload/confirm', [App\Http\Controllers\User\UserController::class, 'confirm_upload']);
Route::post('/users/{id}/restore', [App\Http\Controllers\User\UserController::class, 'restore']);
Route::post('/users/getUserList', [App\Http\Controllers\User\UserController::class, 'getUserList']);
Route::resource('/users', App\Http\Controllers\User\UserController::class);

//permission
Route::post('/users/acl/permissions/{id}/restore', [App\Http\Controllers\User\ACL\PermissionController::class, 'restore']);
Route::post('/users/acl/permissions/getPermissionList', [App\Http\Controllers\User\ACL\PermissionController::class, 'getPermissionList']);
Route::resource('/users/acl/permissions', App\Http\Controllers\User\ACL\PermissionController::class);
//roles
Route::post('/users/acl/roles/{id}/restore', [App\Http\Controllers\User\ACL\RoleController::class, 'restore']);
Route::post('/users/acl/roles/getRoleList', [App\Http\Controllers\User\ACL\RoleController::class, 'getRoleList']);
Route::resource('/users/acl/roles', App\Http\Controllers\User\ACL\RoleController::class);

//Setting/Company
Route::post('/companies/{id}/restore', [App\Http\Controllers\Setting\CompanyController::class, 'restore']);
Route::post('/companies/getCompanyList', [App\Http\Controllers\Setting\CompanyController::class, 'getCompanyList']);
Route::resource('/companies', App\Http\Controllers\Setting\CompanyController::class);

//Setting/Store
Route::post('/stores/{id}/restore', [App\Http\Controllers\Setting\StoreController::class, 'restore']);
Route::post('/stores/getStoreList', [App\Http\Controllers\Setting\StoreController::class, 'getStoreList']);
Route::resource('/stores', App\Http\Controllers\Setting\StoreController::class);

//category
Route::post('/categories/{id}/restore', [App\Http\Controllers\Product\CategoryController::class, 'restore']);
Route::post('/categories/getCategoryList', [App\Http\Controllers\Product\CategoryController::class, 'getCategoryList']);
Route::resource('/categories', App\Http\Controllers\Product\CategoryController::class);

//product
Route::get('/products/upload', [App\Http\Controllers\Product\ProductController::class, 'upload']);
Route::get('/products/upload/show', [App\Http\Controllers\Product\ProductController::class, 'show_upload']);
Route::post('/products/upload/finish', [App\Http\Controllers\Product\ProductController::class, 'finish_upload']);
Route::post('/products/upload/confirm', [App\Http\Controllers\Product\ProductController::class, 'confirm_upload']);
Route::post('/products/{id}/restore', [App\Http\Controllers\Product\ProductController::class, 'restore']);
Route::post('/products/getProductList', [App\Http\Controllers\Product\ProductController::class, 'getProductList']);
Route::resource('/products', App\Http\Controllers\Product\ProductController::class);

//order
Route::post('/orders/export', [App\Http\Controllers\Order\OrderController::class, 'export']);
Route::post('/orders/getOrderList', [App\Http\Controllers\Order\OrderController::class, 'getOrderList']);
Route::resource('/orders', App\Http\Controllers\Order\OrderController::class);

//Kasir
Route::get('/kasir/order-list', [App\Http\Controllers\Kasir\KasirController::class, 'orderList']);
Route::post('/kasir/getProductList', [App\Http\Controllers\Kasir\KasirController::class, 'getProductList']);
Route::get('/kasir', [App\Http\Controllers\Kasir\KasirController::class, 'index']);
Route::post('/kasir/store', [App\Http\Controllers\Kasir\KasirController::class, 'store']);
Route::get('/kasir/{id}', [App\Http\Controllers\Kasir\KasirController::class, 'show']);
Route::put('/kasir/{id}', [App\Http\Controllers\Kasir\KasirController::class, 'update']);