<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Admin/dashboard-admin');
});

Route::get('/dashboard-admin', function () {
    return view('Admin/dashboard-admin');
});

Route::get('/penggawai', function () {
    return view('penggawai');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/riwayat-admin', function (){
    return view('Admin/riwayat-admin');
});