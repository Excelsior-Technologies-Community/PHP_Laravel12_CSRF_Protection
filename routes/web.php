<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;

// Home Page
Route::get('/', function () {
    return view('welcome');
});

// Dashboard
Route::get('/dashboard', [FormController::class, 'dashboard'])
    ->name('dashboard');

// Protected Form
Route::get('/form', [FormController::class, 'showForm'])
    ->name('form.show');

Route::post('/form', [FormController::class, 'submitForm'])
    ->name('form.submit');

// Unsafe Form
Route::get('/form-unsafe', [FormController::class, 'showUnsafeForm'])
    ->name('form.unsafe.show');

Route::post('/form-unsafe', [FormController::class, 'submitUnsafeForm'])
    ->name('form.unsafe.submit');

// AJAX Form
Route::get('/ajax-form', [FormController::class, 'showAjaxForm'])
    ->name('ajax.form.show');

Route::post('/ajax-form', [FormController::class, 'submitAjaxForm'])
    ->name('ajax.form.submit');

// All Submissions
Route::get('/submissions', [FormController::class, 'submissions'])
    ->name('submissions.index');

// Delete Submission
Route::delete('/submissions/{id}', [FormController::class, 'destroy'])
    ->name('submissions.destroy');

// Refresh CSRF Token (AJAX)
Route::get('/refresh-csrf-token', [FormController::class, 'refreshToken'])
    ->name('csrf.refresh');    