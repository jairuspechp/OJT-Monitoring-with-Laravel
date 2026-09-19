<?php

use Illuminate\Support\Facades\Route;

// Serve your existing front end (public/index.html + public/app.js) at "/"
Route::get('/', fn () => response()->file(public_path('index.html')));
