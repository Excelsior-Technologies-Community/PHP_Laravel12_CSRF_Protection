<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel 12 CSRF Defense Suite & Attack Simulator</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        html, body {
            height: 100%;
            background-color: #f8fafc;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        .navbar {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .main-content {
            flex: 1;
        }

        .csrf-lifecycle-bar {
            background: linear-gradient(90deg, #1e293b, #0f172a);
            color: #e2e8f0;
            padding: 8px 16px;
            font-size: 0.82rem;
            border-bottom: 2px solid #3b82f6;
        }

        .token-badge {
            background: rgba(255, 255, 255, 0.1);
            padding: 3px 8px;
            border-radius: 6px;
            font-family: monospace;
            color: #38bdf8;
            letter-spacing: 0.5px;
        }

        .heartbeat-dot {
            height: 9px;
            width: 9px;
            background-color: #22c55e;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 rgba(34, 197, 94, 0.4);
            animation: pulse 1.6s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { box-shadow: 0 0 0 7px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        }

        footer {
            flex-shrink: 0;
        }
    </style>
</head>

<body>
    <!-- Top CSRF Token Lifecycle Watchdog Bar -->
    <div class="csrf-lifecycle-bar">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="heartbeat-dot" id="heartbeatIndicator" title="CSRF Guard Active"></span>
                <span><b>CSRF Token:</b></span>
                <span class="token-badge" id="displayCsrfToken">{{ substr(csrf_token(), 0, 16) }}...</span>
                <button type="button" class="btn btn-outline-info btn-sm py-0 px-2" style="font-size: 0.72rem;" onclick="copyCurrentCsrfToken()">
                    <i class="bi bi-clipboard"></i> Copy
                </button>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-1.5 text-warning small">
                    <i class="bi bi-clock-history"></i>
                    <span>Token Lifetime:</span>
                    <span class="fw-bold text-white font-monospace" id="tokenCountdown">120:00</span>
                </div>

                <div class="form-check form-switch mb-0 small">
                    <input class="form-check-input" type="checkbox" role="switch" id="heartbeatToggle" checked>
                    <label class="form-check-label text-light small" for="heartbeatToggle">Auto-Heartbeat</label>
                </div>

                <button type="button" class="btn btn-primary btn-sm py-0 px-2" style="font-size: 0.75rem;" id="refreshCsrfBtn">
                    <i class="bi bi-arrow-repeat"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-shield-lock-fill text-primary"></i> CSRF Defense Suite
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('form.show') }}"><i class="bi bi-shield-check text-success"></i> Protected Form</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('form.unsafe.show') }}"><i class="bi bi-exclamation-triangle text-danger"></i> Unsafe Form</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('ajax.form.show') }}"><i class="bi bi-lightning text-info"></i> AJAX Form</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-warning" href="{{ route('simulator.show') }}">
                            <i class="bi bi-bug-fill"></i> Attack Simulator
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('sensitive.show') }}"><i class="bi bi-key-fill text-warning"></i> One-Time Token</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('incidents.index') }}"><i class="bi bi-radar text-danger"></i> Incident Vault</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('security.audit') }}"><i class="bi bi-file-earmark-check text-success"></i> Security Audit</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('submissions.index') }}"><i class="bi bi-table"></i> Submissions</a>
                    </li>
                </ul>
            </div>
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
                    timer: 2500,
                    showConfirmButton: false
                });
            });
        </script>
        @endif

        @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Security Alert',
                    text: "{{ session('error') }}",
                });
            });
        </script>
        @endif

        @if(session('csrf_expired'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'warning',
                    title: '419 Session Token Expired',
                    text: "Your CSRF token expired. We refreshed it automatically and preserved your form inputs. Please submit again!",
                    confirmButtonColor: '#0d6efd'
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
            <p class="mb-0 small">
                © {{ date('Y') }} Laravel 12 CSRF Defense Suite | Enterprise Cross-Site Request Forgery Protection & Audit
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Setup CSRF header for all jQuery AJAX calls
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ⏱️ CSRF Token Countdown Watchdog Timer
        let tokenExpiresInSeconds = 120 * 60; // 120 minutes default
        const countdownEl = document.getElementById('tokenCountdown');

        const timerInterval = setInterval(function() {
            if (tokenExpiresInSeconds > 0) {
                tokenExpiresInSeconds--;
                const mins = Math.floor(tokenExpiresInSeconds / 60);
                const secs = tokenExpiresInSeconds % 60;
                if (countdownEl) {
                    countdownEl.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
                }
            } else {
                if (countdownEl) {
                    countdownEl.textContent = 'EXPIRED';
                    countdownEl.className = 'fw-bold text-danger font-monospace';
                }
            }
        }, 1000);

        // 💓 Silent Background Heartbeat (Keep-Alive)
        let heartbeatInterval = null;

        function startHeartbeat() {
            if (heartbeatInterval) clearInterval(heartbeatInterval);
            heartbeatInterval = setInterval(function() {
                if ($('#heartbeatToggle').is(':checked')) {
                    silentRefreshCsrfToken();
                }
            }, 60000); // Silent heartbeat every 60 seconds
        }

        $('#heartbeatToggle').change(function() {
            const indicator = document.getElementById('heartbeatIndicator');
            if ($(this).is(':checked')) {
                indicator.style.backgroundColor = '#22c55e';
                startHeartbeat();
            } else {
                indicator.style.backgroundColor = '#94a3b8';
                if (heartbeatInterval) clearInterval(heartbeatInterval);
            }
        });

        startHeartbeat();

        function silentRefreshCsrfToken(callback) {
            $.ajax({
                url: "{{ route('csrf.refresh') }}",
                type: "GET",
                success: function(response) {
                    updateTokenState(response.token);
                    tokenExpiresInSeconds = 120 * 60; // Reset timer
                    if (callback) callback(response.token);
                }
            });
        }

        function updateTokenState(token) {
            $('meta[name="csrf-token"]').attr('content', token);
            $('input[name="_token"]').val(token);
            $('#displayCsrfToken').text(token.substring(0, 16) + '...');
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': token }
            });
        }

        function copyCurrentCsrfToken() {
            const token = $('meta[name="csrf-token"]').attr('content');
            navigator.clipboard.writeText(token);
            Swal.fire({
                icon: 'success',
                title: 'Token Copied!',
                text: 'Active CSRF Token copied to clipboard.',
                timer: 1400,
                showConfirmButton: false
            });
        }

        // Manual Refresh Button
        $('#refreshCsrfBtn').click(function() {
            silentRefreshCsrfToken(function(token) {
                Swal.fire({
                    icon: 'success',
                    title: 'Token Refreshed',
                    text: 'A fresh CSRF session token was generated and updated.',
                    timer: 1600,
                    showConfirmButton: false
                });
            });
        });

        // 🛡️ Smart 419 Auto-Recovery Modal for AJAX calls
        $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
            if (jqxhr.status === 419) {
                Swal.fire({
                    title: '⚠️ 419 CSRF Token Expired',
                    text: 'Your security token expired. We refreshed it automatically in the background. Would you like to auto-retry the request?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '🔄 Auto-Retry Request',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#0d6efd'
                }).then((result) => {
                    if (result.isConfirmed) {
                        silentRefreshCsrfToken(function(freshToken) {
                            settings.headers = settings.headers || {};
                            settings.headers['X-CSRF-TOKEN'] = freshToken;
                            $.ajax(settings); // Auto-retry the original request!
                        });
                    }
                });
            }
        });
    </script>

    @yield('scripts')
</body>
</html>