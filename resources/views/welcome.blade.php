@extends('layouts.app')

@section('content')
<div class="text-center py-5">
    <h1 class="display-4">Laravel 12 CSRF Protection Demo</h1>
    <p class="lead">Learn how CSRF protection works in Laravel</p>
    
    <div class="row mt-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Protected Form</h5>
                    <p class="card-text">Form with CSRF token that will work correctly.</p>
                    <a href="{{ route('form.show') }}" class="btn btn-primary">View Demo</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Unsafe Form</h5>
                    <p class="card-text">Form without CSRF token that will fail (419 error).</p>
                    <a href="{{ route('form.unsafe.show') }}" class="btn btn-danger">View Demo</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">AJAX Form</h5>
                    <p class="card-text">Form submitted via AJAX with CSRF protection.</p>
                    <a href="{{ route('ajax.form.show') }}" class="btn btn-info">View Demo</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-5">
        <h3>What is CSRF Protection?</h3>
        <div class="text-start mt-3">
            <p><strong>Cross-Site Request Forgery (CSRF)</strong> is an attack that tricks a user into performing actions they didn't intend to on a web application where they're authenticated.</p>
            
            <h5 class="mt-3">How Laravel Protects Against CSRF:</h5>
            <ul>
                <li>Generates a unique CSRF token for each user session</li>
                <li>Verifies this token on all POST, PUT, PATCH, and DELETE requests</li>
                <li>Tokens expire after 2 hours by default (configurable)</li>
                <li>Tokens are stored in the session</li>
            </ul>
            
            <h5 class="mt-3">Methods to Include CSRF Token:</h5>
            <ol>
                <li><code>@csrf</code> Blade directive in forms</li>
                <li><code>csrf_token()</code> helper function</li>
                <li>X-CSRF-TOKEN header for AJAX requests</li>
                <li>X-XSRF-TOKEN cookie (automatically handled by Axios)</li>
            </ol>
        </div>
    </div>
</div>
@endsection