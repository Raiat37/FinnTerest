<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// User dashboard (role: user)
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'ensure.role:user'])->name('dashboard');

// Admin dashboard (role: admin)
Route::get('admin/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'ensure.role:admin'])->name('admin.dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
