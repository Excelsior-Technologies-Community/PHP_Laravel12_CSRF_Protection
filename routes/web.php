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

// ⚔️ FEATURE 1: Attacker Simulator & Security Incidents
Route::get('/attacker-simulator', [FormController::class, 'showAttackerSimulator'])
    ->name('simulator.show');

Route::post('/simulate-attack-unsafe', [FormController::class, 'simulateAttackUnsafe'])
    ->name('simulator.attack.unsafe');

Route::post('/simulate-attack-protected', [FormController::class, 'simulateAttackProtected'])
    ->name('simulator.attack.protected');

Route::get('/incidents', [FormController::class, 'incidentsIndex'])
    ->name('incidents.index');

Route::delete('/incidents/{id}', [FormController::class, 'destroyIncident'])
    ->name('incidents.destroy');

Route::post('/incidents/clear', [FormController::class, 'clearIncidents'])
    ->name('incidents.clear');

// ⏱️ FEATURE 2: Sensitive Form with One-Time Anti-Replay Token
Route::get('/sensitive-action', [FormController::class, 'showSensitiveForm'])
    ->name('sensitive.show');

Route::post('/sensitive-action', [FormController::class, 'submitSensitiveForm'])
    ->name('sensitive.submit');

// 🛡️ FEATURE 3: Cookie SameSite Inspector, Security Headers Audit & Exporter
Route::get('/security-audit', [FormController::class, 'securityAudit'])
    ->name('security.audit');

Route::get('/submissions/export/excel', [FormController::class, 'exportExcel'])
    ->name('submissions.export.excel');

Route::get('/submissions/export/pdf', [FormController::class, 'exportPdf'])
    ->name('submissions.export.pdf');    