@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">

    <!-- Dashboard Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-0 text-dark">
                <i class="bi bi-shield-shaded text-primary"></i> CSRF Defense & Exploit Monitoring Radar
            </h2>
            <small class="text-secondary">Real-time Cross-Site Request Forgery Protection, Blocked Attack Logs & Session Audits</small>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('simulator.show') }}" class="btn btn-outline-danger">
                <i class="bi bi-bug-fill"></i> Attack Simulator
            </a>
            <a href="{{ route('submissions.export.excel') }}" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('submissions.export.pdf') }}" target="_blank" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> PDF Report
            </a>
        </div>
    </div>

    <!-- 1. KPI Statistic Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-primary border-4 p-3">
                <small class="text-muted fw-semibold">Total Submissions</small>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <h2 class="fw-bold text-primary mb-0">{{ $total }}</h2>
                    <span class="badge bg-primary bg-opacity-10 text-primary p-2 fs-5"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-success border-4 p-3">
                <small class="text-muted fw-semibold">Protected By @csrf</small>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <h2 class="fw-bold text-success mb-0">{{ $protectedCount }}</h2>
                    <span class="badge bg-success bg-opacity-10 text-success p-2 fs-5"><i class="bi bi-shield-check"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-warning border-4 p-3">
                <small class="text-muted fw-semibold">Unsafe Submissions</small>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <h2 class="fw-bold text-warning mb-0">{{ $unsafeCount }}</h2>
                    <span class="badge bg-warning bg-opacity-10 text-warning p-2 fs-5"><i class="bi bi-exclamation-triangle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-danger border-4 p-3">
                <small class="text-muted fw-semibold">Blocked Attack Incidents</small>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <h2 class="fw-bold text-danger mb-0">{{ $totalIncidents }}</h2>
                    <span class="badge bg-danger bg-opacity-10 text-danger p-2 fs-5"><i class="bi bi-shield-x"></i></span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Visual Analytics Radar (Chart.js) -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-primary mb-0">
                        <i class="bi bi-graph-up"></i> 7-Day Activity: Valid Submissions vs Blocked Attacks
                    </h6>
                    <span class="badge bg-danger">Auto-Updating</span>
                </div>
                <div class="card-body pt-0">
                    <div style="height: 250px;">
                        <canvas id="timelineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold text-secondary mb-0">
                        <i class="bi bi-pie-chart"></i> Security Status Breakdown
                    </h6>
                </div>
                <div class="card-body pt-0 d-flex align-items-center justify-content-center">
                    <div style="height: 220px; width: 100%;">
                        <canvas id="donutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Blocked CSRF Attack Incidents Live Radar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-danger mb-0">
                    <i class="bi bi-radar"></i> Blocked CSRF Attack Attempts (Recent Incidents)
                </h5>
                <small class="text-muted">Real-time log of cross-site forgery payloads intercepted and blocked by Laravel CSRF filter</small>
            </div>
            <a href="{{ route('incidents.index') }}" class="btn btn-outline-danger btn-sm">
                View All Incidents ({{ $totalIncidents }})
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Incident Type</th>
                            <th>Origin IP</th>
                            <th>Target URL</th>
                            <th>User Agent</th>
                            <th>Status</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentIncidents as $inc)
                        <tr>
                            <td class="fw-bold text-muted">#{{ $inc->id }}</td>
                            <td><span class="badge bg-danger">{{ $inc->incident_type }}</span></td>
                            <td><code>{{ $inc->ip_address ?? '127.0.0.1' }}</code></td>
                            <td><small class="font-monospace text-muted">{{ $inc->target_url }}</small></td>
                            <td><small class="text-muted">{{ \Illuminate\Support\Str::limit($inc->user_agent, 25) }}</small></td>
                            <td><span class="badge bg-success">BLOCKED</span></td>
                            <td><small class="text-muted">{{ $inc->created_at->diffForHumans() }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-shield-check text-success fs-3 d-block mb-1"></i>
                                No active attacks logged recently. Use the <a href="{{ route('simulator.show') }}">Attack Simulator</a> to test live attack logging.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 4. Quick Action Modules -->
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card h-100 p-3 text-center border-0 shadow-sm">
                <div class="fs-2 text-danger mb-2">☠️</div>
                <h6 class="fw-bold">Attack Simulator</h6>
                <p class="text-muted small">Simulate cross-site forged POST requests from external malicious sites.</p>
                <a href="{{ route('simulator.show') }}" class="btn btn-danger btn-sm mt-auto">Launch Sandbox</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 p-3 text-center border-0 shadow-sm">
                <div class="fs-2 text-warning mb-2">🔐</div>
                <h6 class="fw-bold">One-Time Nonce</h6>
                <p class="text-muted small">Single-use anti-replay token for sensitive financial authorizations.</p>
                <a href="{{ route('sensitive.show') }}" class="btn btn-warning btn-sm mt-auto text-dark fw-bold">Test Nonce</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 p-3 text-center border-0 shadow-sm">
                <div class="fs-2 text-info mb-2">⚡</div>
                <h6 class="fw-bold">AJAX CSRF Token</h6>
                <p class="text-muted small">Submit forms dynamically with X-CSRF-TOKEN header and auto-refresh.</p>
                <a href="{{ route('ajax.form.show') }}" class="btn btn-info btn-sm mt-auto text-white">AJAX Form</a>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 p-3 text-center border-0 shadow-sm">
                <div class="fs-2 text-success mb-2">🛡️</div>
                <h6 class="fw-bold">Security Audit</h6>
                <p class="text-muted small">Inspect SameSite cookies, security headers compliance and A+ grade.</p>
                <a href="{{ route('security.audit') }}" class="btn btn-success btn-sm mt-auto">Audit Scorecard</a>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // 7-Day Timeline Chart
    const ctxTimeline = document.getElementById('timelineChart').getContext('2d');
    new Chart(ctxTimeline, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [
                {
                    label: 'Valid Submissions',
                    data: @json($data),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.12)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5
                },
                {
                    label: 'Blocked CSRF Attacks',
                    data: @json($incidentData),
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.12)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    // Donut Chart: Submission Status & Security Breakdown
    const ctxDonut = document.getElementById('donutChart').getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: ['Protected Form', 'Unsafe Form', 'AJAX Form', 'One-Time Nonce', 'Blocked Attacks'],
            datasets: [{
                data: [
                    {{ $protectedCount }},
                    {{ $unsafeCount }},
                    {{ $ajaxCount }},
                    {{ $sensitiveCount }},
                    {{ $totalIncidents }}
                ],
                backgroundColor: ['#198754', '#ffc107', '#0dcaf0', '#6f42c1', '#dc3545'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
            },
            cutout: '65%'
        }
    });
</script>
@endsection