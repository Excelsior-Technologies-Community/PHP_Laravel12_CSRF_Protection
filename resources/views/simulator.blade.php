@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Header -->
        <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #1e1b4b, #0f172a); color: white;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <span class="badge bg-danger mb-2 px-3 py-1 font-monospace">⚔️ MALICIOUS CROSS-SITE LAB</span>
                        <h2 class="fw-bold mb-1">Cross-Site Attack Simulator (CSRF Exploit Lab)</h2>
                        <p class="text-light opacity-75 mb-0 small">
                            Experience firsthand how an external malicious site triggers forged background POST payloads against vulnerable endpoints.
                        </p>
                    </div>
                    <span class="badge bg-dark border border-danger text-danger p-2 fs-6">
                        <i class="bi bi-shield-slash"></i> Sandboxed Environment
                    </span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Attack Vector 1: Exploiting Unsafe Form -->
            <div class="col-md-6">
                <div class="card h-100 border-danger border-top border-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-danger mb-0">
                                <i class="bi bi-unlock-fill"></i> Vector #1: Unsafe Endpoint
                            </h5>
                            <span class="badge bg-danger">Vulnerable</span>
                        </div>
                        <small class="text-muted">Target: <code>/form-unsafe</code> (No CSRF verification)</small>
                    </div>

                    <div class="card-body">
                        <p class="text-secondary small">
                            The attacker embeds a hidden malicious form or script on their phishing page that automatically fires a POST request to your app while the user is logged in.
                        </p>

                        <div class="bg-dark p-3 rounded mb-3 font-monospace text-light small" style="max-height: 140px; overflow-y: auto;">
                            <span class="text-secondary">// Forged Attacker Payload:</span><br>
                            POST /form-unsafe HTTP/1.1<br>
                            Host: your-app.com<br>
                            Origin: http://evil-hacker.org <span class="text-danger">[FORGED]</span><br>
                            name: "Injected Victim"<br>
                            email: "hacked@evil-domain.com"<br>
                            <span class="text-danger">// NO CSRF TOKEN SENT!</span>
                        </div>

                        <button id="btnAttackUnsafe" class="btn btn-danger w-100 fw-bold py-2">
                            <i class="bi bi-lightning-charge-fill"></i> Launch Exploit on Unsafe Route
                        </button>
                    </div>

                    <div class="card-footer bg-light" id="resultUnsafe" style="display: none;">
                        <!-- Result populated via JS -->
                    </div>
                </div>
            </div>

            <!-- Attack Vector 2: Attack on Protected Form (Blocked by CSRF) -->
            <div class="col-md-6">
                <div class="card h-100 border-success border-top border-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-success mb-0">
                                <i class="bi bi-shield-lock-fill"></i> Vector #2: Protected Endpoint
                            </h5>
                            <span class="badge bg-success">Defended</span>
                        </div>
                        <small class="text-muted">Target: <code>/form</code> (CSRF Token Required)</small>
                    </div>

                    <div class="card-body">
                        <p class="text-secondary small">
                            The attacker attempts the exact same cross-site forgery against a protected route. Notice how Laravel's CSRF token verification halts the request immediately.
                        </p>

                        <div class="bg-dark p-3 rounded mb-3 font-monospace text-light small" style="max-height: 140px; overflow-y: auto;">
                            <span class="text-secondary">// Forged Attacker Payload:</span><br>
                            POST /form HTTP/1.1<br>
                            Host: your-app.com<br>
                            Origin: http://evil-hacker.org <span class="text-danger">[FORGED]</span><br>
                            name: "Injected Victim"<br>
                            email: "hacked@evil-domain.com"<br>
                            <span class="text-success">// CSRF Missing ➔ 419 TokenMismatch!</span>
                        </div>

                        <button id="btnAttackProtected" class="btn btn-success w-100 fw-bold py-2">
                            <i class="bi bi-shield-slash-fill"></i> Launch Exploit on Protected Route
                        </button>
                    </div>

                    <div class="card-footer bg-light" id="resultProtected" style="display: none;">
                        <!-- Result populated via JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Terminal Console Logger -->
        <div class="card mt-4 bg-dark text-light border-0 shadow">
            <div class="card-header bg-black py-2 d-flex justify-content-between align-items-center">
                <span class="small font-monospace text-success"><i class="bi bi-terminal"></i> Exploit Radar Live Stream</span>
                <button class="btn btn-sm btn-outline-secondary py-0 text-white" onclick="$('#terminalLogs').empty();">Clear</button>
            </div>
            <div class="card-body p-3 font-monospace small" id="terminalLogs" style="height: 160px; overflow-y: auto; background-color: #0b0f19;">
                <div class="text-secondary">// Ready to simulate cross-site forgery payloads. Click an attack button above.</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function logTerminal(message, type = 'info') {
        const time = new Date().toLocaleTimeString();
        let color = '#38bdf8';
        if (type === 'error') color = '#ef4444';
        if (type === 'success') color = '#22c55e';
        if (type === 'warning') color = '#f59e0b';

        $('#terminalLogs').append(`<div><span style="color:#64748b;">[${time}]</span> <span style="color:${color};">${message}</span></div>`);
        $('#terminalLogs').scrollTop($('#terminalLogs')[0].scrollHeight);
    }

    // Launch Attack on Unsafe Route
    $('#btnAttackUnsafe').click(function() {
        logTerminal('🚀 Sending Cross-Origin Forged POST to /simulate-attack-unsafe...', 'warning');

        $.ajax({
            url: "{{ route('simulator.attack.unsafe') }}",
            type: "POST",
            data: {
                name: 'Hacked User (Cross-Site Forged)',
                email: 'victim@attacker-domain.org'
            },
            headers: {
                // Intentionally strip CSRF token to simulate 3rd party attack!
                'X-CSRF-TOKEN': ''
            },
            success: function(res) {
                logTerminal(`🔴 EXPLOIT SUCCESSFUL (200 OK): Record injected into database without consent!`, 'error');
                
                $('#resultUnsafe').show().html(`
                    <div class="alert alert-danger mb-0 small">
                        <b>🔴 VULNERABILITY CONFIRMED:</b><br>
                        ${res.message}<br>
                        <span class="badge bg-danger mt-1">Status: EXPLOITED (HTTP 200)</span>
                    </div>
                `);

                Swal.fire({
                    icon: 'error',
                    title: 'Vulnerability Exploited!',
                    text: 'The unsafe endpoint accepted the cross-site request because CSRF token check is bypassed.',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    });

    // Launch Attack on Protected Route
    $('#btnAttackProtected').click(function() {
        logTerminal('🛡️ Sending Cross-Origin Forged POST to /simulate-attack-protected...', 'info');

        $.ajax({
            url: "{{ route('simulator.attack.protected') }}",
            type: "POST",
            data: {
                name: 'Blocked Malicious Payload',
                email: 'attacker@evil-domain.com'
            },
            headers: {
                'X-CSRF-TOKEN': '' // No CSRF Token
            },
            error: function(xhr) {
                logTerminal(`🛡️ ATTACK HALTED (HTTP 419): Laravel rejected request & logged incident!`, 'success');

                $('#resultProtected').show().html(`
                    <div class="alert alert-success mb-0 small">
                        <b>🛡️ ATTACK BLOCKED BY CSRF:</b><br>
                        Laravel evaluated the request, detected a missing/unverified token, and rejected execution with HTTP 419.<br>
                        <span class="badge bg-success mt-1">Status: BLOCKED & LOGGED (HTTP 419)</span>
                    </div>
                `);

                Swal.fire({
                    icon: 'success',
                    title: 'Attack Blocked by CSRF!',
                    text: 'Laravel verified that the request did not originate from your legitimate session and blocked the attack.',
                    confirmButtonColor: '#198754'
                });
            }
        });
    });
</script>
@endsection
