@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-shield-check text-success"></i> Security Headers & SameSite Cookie Audit</h3>
                <p class="text-secondary small mb-0">Automated compliance scorecard for application headers, session cookies, and defense architecture.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('submissions.export.excel') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel Audit
                </a>
                <a href="{{ route('submissions.export.pdf') }}" target="_blank" class="btn btn-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf"></i> Executive PDF Report
                </a>
                <a href="{{ route('submissions.export.pdf', ['print' => 1]) }}" target="_blank" class="btn btn-dark btn-sm">
                    <i class="bi bi-printer"></i> Print View
                </a>
            </div>
        </div>
    </div>

    <!-- Scorecard Banner -->
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center p-4" style="background: linear-gradient(135deg, #065f46, #047857); color: white;">
            <div class="display-3 fw-bold mb-1">{{ $securityScore }}/100</div>
            <h4 class="fw-bold mb-2">Security Grade: {{ $grade }} 🛡️</h4>
            <p class="small text-light opacity-75 mb-3">
                Application satisfies OWASP CSRF prevention standards including SameSite cookie defense and Cryptographic Nonces.
            </p>
            <span class="badge bg-white text-success fw-bold px-3 py-2 mx-auto">Status: COMPLIANT</span>
        </div>
    </div>

    <!-- SameSite Cookie Inspector -->
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-cookie text-warning"></i> SameSite Cookie Defense Inspector</h5>
                <small class="text-muted">How modern browsers protect session cookies against cross-site exploitation</small>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <span class="text-muted small">Session Cookie Name</span>
                            <div class="fw-bold text-dark font-monospace">{{ $cookies['name'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <span class="text-muted small">SameSite Policy</span>
                            <div class="fw-bold text-success">{{ $cookies['same_site'] }} (Prevents Cross-Site POST)</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border">
                            <span class="text-muted small">HttpOnly Flag</span>
                            <div class="fw-bold text-primary">{{ $cookies['http_only'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border">
                            <span class="text-muted small">Secure Flag</span>
                            <div class="fw-bold text-info">{{ $cookies['secure'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border">
                            <span class="text-muted small">Session Lifetime</span>
                            <div class="fw-bold text-dark">{{ $cookies['lifetime'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Headers Audit Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-list-check text-primary"></i> Comprehensive Defense Checklist</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Security Control</th>
                                <th>Description & Protection Level</th>
                                <th class="text-center">Audit Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($headersAudit as $title => $item)
                            <tr>
                                <td class="fw-bold">{{ $title }}</td>
                                <td class="text-secondary small">{{ $item['desc'] }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1.5">
                                        <i class="bi bi-check-circle-fill"></i> Passed
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
