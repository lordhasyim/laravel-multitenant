<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Add a test route to confirm it's working
Route::get('/central', function () {
    return 'This is the CENTRAL (landlord) application!';
});
