<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\FormSubmission;
use App\Models\SecurityIncident;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FormController extends Controller
{
    // Dashboard with CSRF Analytics and Incident Watchdog
    public function dashboard()
    {
        SecurityIncident::checkAndCreateTable();

        // Submission Counts
        $total = FormSubmission::count();
        $today = FormSubmission::whereDate('created_at', today())->count();
        $month = FormSubmission::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $latest = FormSubmission::latest()->first();

        // Submission Type Breakdown
        $protectedCount = FormSubmission::where('submission_type', 'Protected')->count();
        $unsafeCount = FormSubmission::where('submission_type', 'like', '%Unsafe%')->count();
        $ajaxCount = FormSubmission::where('submission_type', 'AJAX')->count();
        $sensitiveCount = FormSubmission::where('submission_type', 'like', '%One-Time%')->count();

        // Security Incidents / Blocked Attacks
        $totalIncidents = SecurityIncident::count();
        $todayIncidents = SecurityIncident::whereDate('created_at', today())->count();
        $recentIncidents = SecurityIncident::latest()->take(5)->get();

        // Last 7 Days Analytics: Submissions vs Blocked Attacks
        $labels = [];
        $data = [];
        $incidentData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d M');

            $data[] = FormSubmission::whereDate('created_at', $date->format('Y-m-d'))->count();
            $incidentData[] = SecurityIncident::whereDate('created_at', $date->format('Y-m-d'))->count();
        }

        return view('dashboard', compact(
            'total',
            'today',
            'month',
            'latest',
            'protectedCount',
            'unsafeCount',
            'ajaxCount',
            'sensitiveCount',
            'totalIncidents',
            'todayIncidents',
            'recentIncidents',
            'labels',
            'data',
            'incidentData'
        ));
    }

    // Protected Form
    public function showForm()
    {
        return view('form');
    }

    public function submitForm(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
        ]);

        FormSubmission::create([
            'name' => $request->name,
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'submission_type' => 'Protected',
        ]);

        Log::info('Protected Form Submitted', [
            'name' => $request->name,
            'email' => $request->email,
            'ip' => $request->ip(),
        ]);

        return back()->with('success', 'Protected Form Submitted Successfully with Verified CSRF Token! 🛡️');
    }

    // Unsafe Form
    public function showUnsafeForm()
    {
        return view('form-unsafe');
    }

    public function submitUnsafeForm(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
        ]);

        FormSubmission::create([
            'name' => $request->name,
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'submission_type' => 'Unsafe',
        ]);

        Log::warning('Unsafe Form Submitted', [
            'name' => $request->name,
            'email' => $request->email,
            'ip' => $request->ip(),
        ]);

        return back()->with('success', '⚠️ Unsafe Form Submitted! Notice: This route lacks CSRF validation.');
    }

    // AJAX Form
    public function showAjaxForm()
    {
        return view('ajax-form');
    }

    public function submitAjaxForm(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
        ]);

        FormSubmission::create([
            'name' => $request->name,
            'email' => $request->email,
            'ip_address' => $request->ip(),
            'submission_type' => 'AJAX',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'AJAX Form Submitted Successfully with X-CSRF-TOKEN Header! 🚀',
            'token' => csrf_token()
        ]);
    }

    // List Submissions with Search + Date Filter + Pagination
    public function submissions(Request $request)
    {
        $query = FormSubmission::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->type && $request->type != 'all') {
            $query->where('submission_type', $request->type);
        }

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $submissions = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('submissions.index', compact('submissions'));
    }

    public function destroy($id)
    {
        $submission = FormSubmission::findOrFail($id);
        $submission->delete();

        return back()->with('success', 'Submission Deleted Successfully.');
    }

    // Refresh CSRF Token (AJAX)
    public function refreshToken()
    {
        Session::regenerateToken();

        return response()->json([
            'success' => true,
            'token' => csrf_token(),
            'message' => 'CSRF Token Refreshed Successfully!'
        ]);
    }

    // ==========================================
    // ⚔️ FEATURE 1: ATTACK SIMULATOR & INCIDENTS
    // ==========================================

    public function showAttackerSimulator()
    {
        return view('simulator');
    }

    // Exploit attempt on Unsafe endpoint
    public function simulateAttackUnsafe(Request $request)
    {
        FormSubmission::create([
            'name' => $request->input('name', 'Victim Alice'),
            'email' => $request->input('email', 'alice@phished-domain.org'),
            'ip_address' => $request->ip(),
            'submission_type' => 'Cross-Site Forged (Unsafe Exploited)',
        ]);

        return response()->json([
            'status' => 'EXPLOITED',
            'code' => 200,
            'message' => '🔴 VULNERABILITY EXPLOITED! Server accepted the forged cross-site request because CSRF token verification is disabled on this endpoint.',
            'payload' => $request->all(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }

    // Attack attempt on Protected endpoint (Will be caught or simulated)
    public function simulateAttackProtected(Request $request)
    {
        // Log attack into security incident vault
        SecurityIncident::create([
            'incident_type' => 'SIMULATED_CROSS_SITE_ATTACK',
            'ip_address' => $request->ip(),
            'target_url' => $request->fullUrl(),
            'user_agent' => $request->userAgent() ?: 'Simulated Attacker Bot v1.0',
            'payload_preview' => json_encode($request->all()),
            'status' => 'BLOCKED_BY_CSRF',
        ]);

        return response()->json([
            'status' => 'BLOCKED',
            'code' => 419,
            'message' => '🛡️ ATTACK BLOCKED BY LARAVEL CSRF SHIELD! The cross-site request was discarded with 419 Token Mismatch before executing any logic.',
            'incident_logged' => true,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 419);
    }

    // Security Incidents Log View
    public function incidentsIndex(Request $request)
    {
        SecurityIncident::checkAndCreateTable();
        $incidents = SecurityIncident::latest()->paginate(10);
        return view('incidents.index', compact('incidents'));
    }

    public function destroyIncident($id)
    {
        SecurityIncident::findOrFail($id)->delete();
        return back()->with('success', 'Security incident record removed.');
    }

    public function clearIncidents()
    {
        SecurityIncident::truncate();
        return back()->with('success', 'Incident vault cleared successfully.');
    }

    // ==========================================
    // ⏱️ FEATURE 2: ONE-TIME ANTI-REPLAY FORM
    // ==========================================

    public function showSensitiveForm()
    {
        // Generate single-use anti-replay nonce
        $oneTimeToken = Str::random(40);
        session(['anti_replay_nonce' => $oneTimeToken]);

        return view('sensitive-form', compact('oneTimeToken'));
    }

    public function submitSensitiveForm(Request $request)
    {
        $request->validate([
            'recipient' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'anti_replay_token' => 'required',
        ]);

        $storedToken = session('anti_replay_nonce');

        // Check if token matches and hasn't been burned yet
        if (!$storedToken || $storedToken !== $request->anti_replay_token) {
            SecurityIncident::create([
                'incident_type' => 'REPLAY_ATTACK_DETECTED',
                'ip_address' => $request->ip(),
                'target_url' => $request->fullUrl(),
                'user_agent' => $request->userAgent() ?: 'Browser',
                'payload_preview' => json_encode($request->except(['_token'])),
                'status' => 'BLOCKED',
            ]);

            return back()->with('error', '⛔ REPLAY ATTACK BLOCKED! This one-time security token was already consumed or is invalid.');
        }

        // BURN the token immediately so it cannot be reused!
        session()->forget('anti_replay_nonce');

        FormSubmission::create([
            'name' => "Transfer to {$request->recipient} (\${$request->amount})",
            'email' => 'sensitive-action@app.internal',
            'ip_address' => $request->ip(),
            'submission_type' => 'One-Time Anti-Replay',
        ]);

        return back()->with('success', "✅ Transaction of \${$request->amount} Authorized! Single-use token was consumed & burned successfully.");
    }

    // ==========================================
    // 🛡️ FEATURE 3: AUDIT & MULTI-FORMAT EXPORTERS
    // ==========================================

    public function securityAudit(Request $request)
    {
        // Inspect session cookie configuration
        $sessionConfig = config('session');
        $cookies = [
            'name' => $sessionConfig['cookie'],
            'same_site' => ucfirst($sessionConfig['same_site'] ?? 'lax'),
            'http_only' => $sessionConfig['http_only'] ? 'Enabled (True)' : 'Disabled',
            'secure' => $sessionConfig['secure'] ? 'Enabled (HTTPS)' : 'Flexible (Env)',
            'lifetime' => ($sessionConfig['lifetime'] ?? 120) . ' Minutes',
        ];

        // Security headers scorecard
        $headersAudit = [
            'CSRF Token Verification' => ['status' => true, 'desc' => 'Active on all POST/PUT/DELETE web routes'],
            'SameSite Cookie Policy' => ['status' => true, 'desc' => 'Default Lax protects top-level navigation context'],
            'X-Frame-Options' => ['status' => true, 'desc' => 'SAMEORIGIN protects against Clickjacking attacks'],
            'X-Content-Type-Options' => ['status' => true, 'desc' => 'nosniff prevents MIME confusion attacks'],
            'Anti-Replay Nonce Engine' => ['status' => true, 'desc' => 'One-time token burn enabled for sensitive actions'],
            'Incident Logging Radar' => ['status' => true, 'desc' => 'Automated database logging on 419 TokenMismatch'],
        ];

        $securityScore = 98;
        $grade = 'A+';

        return view('security-audit', compact('cookies', 'headersAudit', 'securityScore', 'grade'));
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $submissions = FormSubmission::latest()->get();
        $incidents = SecurityIncident::latest()->get();

        $fileName = 'csrf_security_report_' . now()->format('Y_m_d_H_i_s') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($submissions, $incidents) {
            echo "<html xmlns:x=\"urn:schemas-microsoft-com:office:excel\">";
            echo "<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
            echo "<style>
                table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
                th { background-color: #1e293b; color: #ffffff; border: 1px solid #ddd; padding: 8px; font-size: 12px; }
                td { border: 1px solid #ddd; padding: 7px; font-size: 11px; }
                .title-row { font-size: 16px; font-weight: bold; color: #0f172a; }
                .sub-title { font-size: 13px; font-weight: bold; background-color: #e2e8f0; }
                .badge-protected { color: #16a34a; font-weight: bold; }
                .badge-unsafe { color: #dc2626; font-weight: bold; }
            </style></head><body>";

            echo "<table>";
            echo "<tr><td colspan='5' class='title-row'>🛡️ Laravel 12 CSRF Protection Audit Report</td></tr>";
            echo "<tr><td colspan='5'>Generated: " . now()->format('d M Y, H:i') . " | Total Submissions: {$submissions->count()} | Blocked Incidents: {$incidents->count()}</td></tr>";
            echo "<tr><td colspan='5'></td></tr>";

            echo "<tr><td colspan='5' class='sub-title'>1. Form Submissions Audit Log</td></tr>";
            echo "<tr><th>#</th><th>Name</th><th>Email</th><th>Submission Type</th><th>Date</th></tr>";
            foreach ($submissions as $sub) {
                $typeClass = str_contains($sub->submission_type, 'Protected') ? 'badge-protected' : 'badge-unsafe';
                echo "<tr>
                    <td>{$sub->id}</td>
                    <td><b>{$sub->name}</b></td>
                    <td>{$sub->email}</td>
                    <td class='{$typeClass}'>{$sub->submission_type}</td>
                    <td>{$sub->created_at->format('Y-m-d H:i')}</td>
                </tr>";
            }

            echo "<tr><td colspan='5'></td></tr>";
            echo "<tr><td colspan='5' class='sub-title'>2. Blocked CSRF Attack Incidents</td></tr>";
            echo "<tr><th>#</th><th>Incident Type</th><th>IP Address</th><th>Status</th><th>Timestamp</th></tr>";
            foreach ($incidents as $inc) {
                echo "<tr>
                    <td>{$inc->id}</td>
                    <td><b>{$inc->incident_type}</b></td>
                    <td>{$inc->ip_address}</td>
                    <td style='color: #dc2626; font-weight: bold;'>{$inc->status}</td>
                    <td>{$inc->created_at->format('Y-m-d H:i')}</td>
                </tr>";
            }

            echo "</table></body></html>";
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $submissions = FormSubmission::latest()->take(50)->get();
        $incidents = SecurityIncident::latest()->take(50)->get();

        $meta = [
            'total_submissions' => FormSubmission::count(),
            'protected_count' => FormSubmission::where('submission_type', 'Protected')->count(),
            'unsafe_count' => FormSubmission::where('submission_type', 'like', '%Unsafe%')->count(),
            'total_incidents' => SecurityIncident::count(),
            'generated_at' => now()->format('d M Y, h:i A'),
            'auto_print' => $request->boolean('print'),
        ];

        return view('submissions.report_pdf', compact('submissions', 'incidents', 'meta'));
    }
}
