<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\FacebookController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DashboardController;

Route::group(['middleware' => 'NotLogin'], function(){
  //Login routes--------------------------------------------------------------------------------
  Route::get('/', [LoginController::class, 'index'])->name('/');
  Route::post('/login.logIn', [LoginController::class, 'logIn'])->name('logIn');
  //Register routes------------------------------------------------------------------------------
  Route::get('/register', [RegisterController::class, 'index'])->name('register');
  Route::post('/register.store', [RegisterController::class, 'store'])->name('register.store');
  //Facebook routes------------------------------------------------------------------------------
  Route::controller(FacebookController::class)->group(function(){
    Route::get('auth/facebook', 'redirectToFacebook')->name('auth.facebook');
    Route::get('auth/facebook/callback', 'handleFacebookCallback');
  });
  Route::controller(GoogleController::class)->group(function(){
    Route::get('authorized/google', 'redirectToGoogle')->name('auth.google');
    Route::get('authorized/google/callback', 'handleGoogleCallback');
  });
});

Route::group(['middleware' => 'IsLogin'], function(){
  //Profile routes---------------------------------------------------------------
  Route::get('/profile',[ProfileController::class, 'index'])->name('profile');
  Route::get('/profile.edit',[ProfileController::class, 'edit'])->name('profile.edit');
  Route::post('/profile.update',[ProfileController::class, 'update'])->name('profile.update');
  //Login out--------------------------------------------------------------
  Route::get('/logOut',[LoginController::class, 'logOut'])->name('logOut');
  //Brand routes---------------------------------------------------------------------------------------
  Route::get('/brands', [BrandController::class, 'index'])->name('brands');
  Route::post('/brands.store', [BrandController::class, 'store'])->name('brands.store');
  Route::get('/brands.fetchAll', [BrandController::class, 'fetchAll'])->name('brands.fetchAll');
  Route::delete('/brands.delete', [BrandController::class, 'delete'])->name('brands.delete');
  Route::get('/brands.edit', [BrandController::class, 'edit'])->name('brands.edit');
  Route::post('/brands.update', [BrandController::class, 'update'])->name('brands.update');
  //Product routes----------------------------------------------------------------------------------------
  Route::get('/products', [ProductController::class, 'index']);
  Route::post('/products.store', [ProductController::class, 'store'])->name('products.store');
  Route::get('/products.fetchAll', [ProductController::class, 'fetchAll'])->name('products.fetchAll');
  Route::delete('/products.delete', [ProductController::class, 'delete'])->name('products.delete');
  Route::get('/products.edit', [ProductController::class, 'edit'])->name('products.edit');
  Route::post('/products.update', [ProductController::class, 'update'])->name('products.update');
  //Client routes-----------------------------------------------------------------------------------------------
  Route::get('/clients', [ClientController::class, 'index']);
  Route::post('/clients.store', [ClientController::class, 'store'])->name('clients.store');
  Route::get('/clients.fetchAll', [ClientController::class, 'fetchAll'])->name('clients.fetchAll');
  Route::delete('/clients.delete', [ClientController::class, 'delete'])->name('clients.delete');
  Route::get('/clients.edit', [ClientController::class, 'edit'])->name('clients.edit');
  Route::post('/clients.update', [ClientController::class, 'update'])->name('clients.update');
  //Order routes------------------------------------------------------------------------------------------------
  Route::get('/orders', [OrderController::class, 'index']);
  Route::post('/orders.store', [OrderController::class, 'store'])->name('orders.store');
  Route::get('/orders.fetchAll', [OrderController::class, 'fetchAll'])->name('orders.fetchAll');
  Route::delete('/orders.delete', [OrderController::class, 'delete'])->name('orders.delete');
  Route::get('/orders.edit', [OrderController::class, 'edit'])->name('orders.edit');
  Route::post('/orders.update', [OrderController::class, 'update'])->name('orders.update');
  Route::get('/orders.confirmOrder', [OrderController::class, 'confirmOrder'])->name('orders.confirmOrder');
  Route::get('/orders.cancelOrder', [OrderController::class, 'cancelOrder'])->name('orders.cancelOrder');
  //Task routes-----------------------------------------------------------------------------------
  Route::get('/tasks', [TaskController::class, 'index']);
  Route::post('/task.store', [TaskController::class, 'store'])->name('task.store');
  Route::get('/task.fetchAll', [TaskController::class, 'fetchAll'])->name('task.fetchAll');
  Route::delete('/task.delete', [TaskController::class, 'delete'])->name('task.delete');
  Route::get('/task/edit', [TaskController::class, 'edit'])->name('task.edit');
  Route::post('/task.update', [TaskController::class, 'update'])->name('task.update');
});
