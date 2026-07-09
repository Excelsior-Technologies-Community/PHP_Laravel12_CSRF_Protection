<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel CSRF Protection Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .navbar {
            flex-shrink: 0;
        }

        .main-content {
            flex: 1;
        }

        footer {
            flex-shrink: 0;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="navbar-nav">
            <a class="nav-link" href="/">Home</a>

            <a class="nav-link" href="{{ route('dashboard') }}">
                Dashboard
            </a>

            <a class="nav-link" href="{{ route('form.show') }}">
                Protected Form
            </a>

            <a class="nav-link" href="{{ route('form.unsafe.show') }}">
                Unsafe Form
            </a>

            <a class="nav-link" href="{{ route('ajax.form.show') }}">
                AJAX Form
            </a>

            <a class="nav-link" href="{{ route('submissions.index') }}">
                Submissions
            </a>

        </div>
    </nav>

    <div class="container mt-4 main-content">
        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        </script>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-4">
        <div class="container">
            <p class="mb-0">
                © {{ date('Y') }} Laravel CSRF Protection Demo | Secure Application
            </p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $(document).ready(function() {

            $('#refreshCsrfBtn').click(function() {

                $.ajax({

                    url: "{{ route('csrf.refresh') }}",

                    type: "GET",

                    success: function(response) {

                        $('meta[name="csrf-token"]')
                            .attr('content', response.token);

                        $('input[name="_token"]')
                            .val(response.token);


                        $.ajaxSetup({

                            headers: {
                                'X-CSRF-TOKEN': response.token
                            }

                        });


                        Swal.fire({

                            icon: 'success',

                            title: 'CSRF Token Refreshed',

                            text: response.message,

                            timer: 1800,

                            showConfirmButton: false

                        });

                    },


                    error: function() {

                        Swal.fire({

                            icon: 'error',

                            title: 'Error',

                            text: 'Unable to refresh CSRF token.'

                        });

                    }

                });

            });

        });
    </script>


    @yield('scripts')
</body>

</html>