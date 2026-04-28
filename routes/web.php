<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Admin/dashboard-admin');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/penggawai', function () {
    return view('penggawai');
});