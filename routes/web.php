<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;

// Home page
Route::get('/', function () {
    return view('welcome');
});

// CSRF Demo Routes
Route::get('/form', [FormController::class, 'showForm'])->name('form.show');
Route::post('/form', [FormController::class, 'submitForm'])->name('form.submit');

// Routes without CSRF protection (for demonstration)
Route::get('/form-unsafe', [FormController::class, 'showUnsafeForm'])->name('form.unsafe.show');
Route::post('/form-unsafe', [FormController::class, 'submitUnsafeForm'])->name('form.unsafe.submit');

// AJAX form demo
Route::get('/ajax-form', [FormController::class, 'showAjaxForm'])->name('ajax.form.show');
Route::post('/ajax-form', [FormController::class, 'submitAjaxForm'])->name('ajax.form.submit');