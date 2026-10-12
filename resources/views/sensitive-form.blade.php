@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold"><i class="bi bi-key-fill text-warning"></i> One-Time Anti-Replay Token Studio</h5>
                    <small class="text-secondary">Single-Use Nonce Authorization for Sensitive Financial Transactions</small>
                </div>
                <span class="badge bg-warning text-dark px-3 py-1 font-monospace">High Security</span>
            </div>

            <div class="card-body p-4">
                <div class="alert alert-info border-0 d-flex align-items-center gap-3">
                    <i class="bi bi-info-circle-fill fs-3 text-info"></i>
                    <div class="small">
                        <b>Anti-Replay Security Nonce:</b> In addition to standard CSRF protection, this form includes a <b>Single-Use Cryptographic Nonce</b>. Once submitted, the token is permanently burned from the session so identical replayed requests cannot be forged or duplicated!
                    </div>
                </div>

                <!-- Active One-Time Token Info Box -->
                <div class="p-3 bg-light rounded border mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-bold">Active One-Time Nonce:</span>
                        <span class="badge bg-success">Status: Fresh (Unconsumed)</span>
                    </div>
                    <code class="text-primary font-monospace fs-6 text-break">{{ $oneTimeToken }}</code>
                </div>

                <form method="POST" action="{{ route('sensitive.submit') }}" id="sensitiveForm">
                    @csrf
                    <!-- One-Time Nonce Field -->
                    <input type="hidden" name="anti_replay_token" id="antiReplayTokenInput" value="{{ $oneTimeToken }}">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Recipient Account</label>
                        <input type="text" name="recipient" class="form-control" placeholder="e.g. Finance Vault / Alex Morgan" required value="Enterprise Vendor Ops">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Authorization Amount ($ USD)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="amount" class="form-control" placeholder="100.00" step="0.01" required value="250.00">
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning btn-lg fw-bold text-dark py-2">
                            <i class="bi bi-shield-lock"></i> Authorize Transaction & Burn Token
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <!-- Replay Attack Exploit Simulator Button -->
                <div class="bg-light p-3 rounded text-center">
                    <h6 class="fw-bold text-danger mb-1"><i class="bi bi-repeat"></i> Test Replay Attack Vulnerability</h6>
                    <p class="text-secondary small mb-2">Simulate an attacker intercepting the request and attempting to execute it a second time with the already-burned nonce.</p>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="simulateReplayAttack()">
                        🚀 Test Replay Request (Should Be Blocked)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function simulateReplayAttack() {
        Swal.fire({
            title: 'Testing Replay Attack...',
            text: 'Sending forged repeat request with expired/burned token.',
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Submit with an intentionally burned or invalid token
        $.ajax({
            url: "{{ route('sensitive.submit') }}",
            type: "POST",
            data: {
                recipient: 'Hacker Account',
                amount: 999.00,
                anti_replay_token: 'already_burned_token_nonce_12345'
            },
            success: function() {
                Swal.fire('Unexpected', 'Request was allowed.', 'warning');
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Replay Attack Blocked!',
                    text: 'The server detected that the single-use token was consumed or invalid, and halted the transaction.',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    }
</script>
@endsection
