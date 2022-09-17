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
