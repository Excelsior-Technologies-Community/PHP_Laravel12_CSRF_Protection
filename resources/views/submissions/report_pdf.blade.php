<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSRF Defense & Security Audit Executive Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8fafc;
            color: #0f172a;
            font-family: Arial, sans-serif;
            padding: 30px 0;
        }
        .report-box {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            padding: 35px;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-protected { background-color: #dcfce7; color: #15803d; }
        .badge-unsafe { background-color: #fee2e2; color: #b91c1c; }
        @media print {
            body { background: white !important; padding: 0 !important; }
            .report-box { box-shadow: none !important; padding: 0 !important; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Print / Save as PDF
            </button>
        </div>

        <div class="report-box">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-shield-lock-fill"></i> Laravel 12 CSRF Defense Suite
                    </h2>
                    <h5 class="text-secondary fw-semibold">Executive Security Audit & Incident Report</h5>
                    <p class="text-muted small mb-0">Generated On: {{ $meta['generated_at'] }}</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-success px-3 py-2 fs-6">Security Grade: A+ (OWASP Compliant)</span>
                </div>
            </div>

            <!-- KPI Metric Summary -->
            <div class="row g-3 mb-4 text-center">
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded border">
                        <small class="text-muted">Total Submissions</small>
                        <h4 class="fw-bold text-dark mb-0">{{ $meta['total_submissions'] }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded border">
                        <small class="text-muted">Protected Submissions</small>
                        <h4 class="fw-bold text-success mb-0">{{ $meta['protected_count'] }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded border">
                        <small class="text-muted">Unsafe Submissions</small>
                        <h4 class="fw-bold text-danger mb-0">{{ $meta['unsafe_count'] }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded border">
                        <small class="text-muted">Blocked Attack Incidents</small>
                        <h4 class="fw-bold text-danger mb-0">{{ $meta['total_incidents'] }}</h4>
                    </div>
                </div>
            </div>

            <!-- Section 1: Submissions -->
            <h5 class="fw-bold text-dark mb-3">1. Form Submissions Audit History</h5>
            <div class="table-responsive mb-5">
                <table class="table table-bordered table-striped align-middle small">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>IP Address</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $sub)
                        <tr>
                            <td>{{ $sub->id }}</td>
                            <td><strong>{{ $sub->name }}</strong></td>
                            <td>{{ $sub->email }}</td>
                            <td>
                                <span class="status-badge {{ str_contains($sub->submission_type, 'Protected') ? 'badge-protected' : 'badge-unsafe' }}">
                                    {{ $sub->submission_type }}
                                </span>
                            </td>
                            <td>{{ $sub->ip_address ?? '127.0.0.1' }}</td>
                            <td>{{ $sub->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted">No submissions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Section 2: Blocked CSRF Incidents -->
            <h5 class="fw-bold text-danger mb-3">2. Blocked CSRF & Replay Attack Incidents</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle small">
                    <thead class="table-danger">
                        <tr>
                            <th width="50">#</th>
                            <th>Incident Type</th>
                            <th>Target URL</th>
                            <th>Origin IP</th>
                            <th>Status</th>
                            <th>Logged At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incidents as $inc)
                        <tr>
                            <td>{{ $inc->id }}</td>
                            <td><strong>{{ $inc->incident_type }}</strong></td>
                            <td>{{ $inc->target_url }}</td>
                            <td>{{ $inc->ip_address }}</td>
                            <td><span class="badge bg-danger">BLOCKED</span></td>
                            <td>{{ $inc->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted">No security incidents recorded.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 pt-3 border-top text-center text-muted small">
                Laravel 12 CSRF Defense Suite • Automated Security Verification Report
            </div>
        </div>
    </div>

    @if($meta['auto_print'])
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.print();
        });
    </script>
    @endif
</body>
</html>
