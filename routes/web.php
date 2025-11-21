<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');


   
    Route::get('/product/list', function () {
        return view('products.listproductos');
    })->name('list.product');
    Route::get('/product/register', function () {
        return view('products.registerform');
    })->name('register.product');

    Route::get('/product/edit/{id}', function () {
        return view('products.updateform');
    })->name('edit.product');
 