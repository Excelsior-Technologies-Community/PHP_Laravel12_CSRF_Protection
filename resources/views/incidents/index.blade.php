@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-radar text-danger"></i> CSRF Security Incident Vault</h3>
                <p class="text-secondary mb-0 small">Real-time log of blocked Cross-Site Request Forgery attempts, 419 token mismatches, and replay attacks.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('simulator.show') }}" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-bug"></i> Launch Attack Simulator
                </a>
                <form action="{{ route('incidents.clear') }}" method="POST" onsubmit="return confirm('Clear all security incident records?');">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i> Clear All Incidents
                    </button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Incident Type</th>
                                <th>Client IP</th>
                                <th>Target Route</th>
                                <th>User Agent</th>
                                <th>Status</th>
                                <th>Logged At</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($incidents as $inc)
                            <tr>
                                <td class="fw-bold text-muted">#{{ $inc->id }}</td>
                                <td>
                                    @if(str_contains($inc->incident_type, 'REPLAY'))
                                        <span class="badge bg-warning text-dark"><i class="bi bi-repeat"></i> Replay Blocked</span>
                                    @elseif(str_contains($inc->incident_type, 'SIMULATED'))
                                        <span class="badge bg-danger"><i class="bi bi-radioactive"></i> Attack Simulated</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-shield-x"></i> Token Mismatch</span>
                                    @endif
                                    <div class="small fw-semibold text-secondary">{{ $inc->incident_type }}</div>
                                </td>
                                <td><code class="text-primary">{{ $inc->ip_address ?? '127.0.0.1' }}</code></td>
                                <td><small class="text-muted font-monospace">{{ $inc->target_url }}</small></td>
                                <td><small class="text-muted" title="{{ $inc->user_agent }}">{{ \Illuminate\Support\Str::limit($inc->user_agent, 30) }}</small></td>
                                <td>
                                    <span class="badge bg-success bg-opacity-75 text-white">
                                        <i class="bi bi-check-circle"></i> {{ $inc->status }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $inc->created_at->format('d M Y, H:i:s') }}</small></td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('incidents.destroy', $inc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this incident log?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-shield-check text-success display-4 d-block mb-3"></i>
                                    <h5>No Security Violations Logged Yet</h5>
                                    <p class="small text-secondary">Launch the <a href="{{ route('simulator.show') }}">Attack Simulator</a> to test real-time CSRF blocking and incident logging.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($incidents->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $incidents->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
