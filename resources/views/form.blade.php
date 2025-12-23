@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Form with CSRF Protection</h4>
            </div>
            <div class="card-body">
                <p class="text-success">
                    <strong>✓ This form is protected by CSRF token</strong>
                </p>
                <p>Try submitting this form - it will work because it includes the CSRF token.</p>
                
                <form method="POST" action="{{ route('form.submit') }}">
                    @csrf <!-- This adds the CSRF token -->
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Submit Protected Form</button>
                </form>
                
                <div class="mt-4">
                    <h5>How CSRF Protection Works:</h5>
                    <ul>
                        <li>The <code>@csrf</code> directive generates a hidden input field with CSRF token</li>
                        <li>Laravel automatically verifies this token on POST requests</li>
                        <li>Without this token, the form submission would be rejected</li>
                    </ul>
                    
                    <p>View the page source to see the generated token.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection