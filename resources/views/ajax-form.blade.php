@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">AJAX Form with CSRF Protection</h4>
                </div>
                <div class="card-body">
                    <div id="response-message" class="alert d-none"></div>

                    <form id="ajaxForm">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <button type="submit" class="btn btn-info">Submit via AJAX</button>
                    </form>

                    <div class="mt-4">
                        <h5>AJAX CSRF Protection Methods:</h5>
                        <ol>
                            <li><strong>Meta Tag:</strong> Token is in meta tag:
                                <code>&lt;meta name="csrf-token" content="{{ csrf_token() }}"&gt;</code></li>
                            <li><strong>JavaScript Header:</strong> We set X-CSRF-TOKEN header in AJAX requests</li>
                            <li><strong>Form Data:</strong> Include token in form data (already done by @csrf)</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Set up CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle AJAX form submission
            $('#ajaxForm').on('submit', function (e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: '{{ route("ajax.form.submit") }}',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    beforeSend: function () {
                        $('#response-message').removeClass('alert-success alert-danger')
                            .addClass('alert-info')
                            .text('Submitting...')
                            .removeClass('d-none');
                    },
                    success: function (response) {
                        $('#response-message').removeClass('alert-info alert-danger')
                            .addClass('alert-success')
                            .html('<strong>Success!</strong> ' + response.message +
                                '<br>Name: ' + response.data.name +
                                '<br>Email: ' + response.data.email);
                    },
                    error: function (xhr) {
                        var errorMessage = 'An error occurred.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 419) {
                            errorMessage = 'CSRF token mismatch. Please refresh the page.';
                        }

                        $('#response-message').removeClass('alert-info alert-success')
                            .addClass('alert-danger')
                            .html('<strong>Error!</strong> ' + errorMessage);
                    }
                });
            });
        });
    </script>
@endsection