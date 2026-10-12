<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->validateCsrfTokens(except: [
            'form-unsafe',
            'simulate-attack-unsafe',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            try {
                \App\Models\SecurityIncident::create([
                    'incident_type' => 'CSRF_TOKEN_MISMATCH',
                    'ip_address' => $request->ip(),
                    'target_url' => $request->fullUrl(),
                    'user_agent' => $request->userAgent() ?: 'Unknown Browser',
                    'payload_preview' => json_encode($request->except(['_token', 'password'])),
                    'status' => 'BLOCKED',
                ]);
            } catch (\Throwable $th) {
                // Safe fallback if table not yet initialized
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'code' => 419,
                    'error' => 'CSRF_TOKEN_MISMATCH',
                    'message' => 'Security Alert: CSRF Token Expired or Invalid! A fresh token has been generated.',
                    'new_token' => csrf_token()
                ], 419);
            }

            return redirect()->back()
                ->withInput()
                ->with('csrf_expired', true)
                ->with('error', '⚠️ Security Alert: Session / CSRF Token Expired! A fresh token has been generated. Please try submitting again.');
        });
    })->create();
