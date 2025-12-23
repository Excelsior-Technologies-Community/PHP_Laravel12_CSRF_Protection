@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Form WITHOUT CSRF Protection</h4>
                </div>
                <div class="card-body">
                    <p class="text-danger">
                        <strong>⚠️ This form is NOT protected by CSRF token</strong>
                    </p>
                    <p>Try submitting this form - it will fail with a 419 error.</p>

                    <form method="POST" action="{{ route('form.unsafe.submit') }}">
                        <!-- Notice: No @csrf directive here! -->

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <button type="submit" class="btn btn-danger">Submit Unsafe Form</button>
                    </form>

                    <div class="mt-4">
                        <h5>CSRF Attack Simulation:</h5>
                        <p>Here's what a malicious site might try:</p>
                        <pre class="bg-light p-3">
    &lt;form action="http://your-site/form-unsafe" method="POST"&gt;
        &lt;input type="hidden" name="name" value="Hacker"&gt;
        &lt;input type="hidden" name="email" value="hacker@example.com"&gt;
        &lt;input type="submit" value="Click to win a prize!"&gt;
    &lt;/form&gt;
                        </pre>
                        <p>This would fail if CSRF protection was enabled.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection