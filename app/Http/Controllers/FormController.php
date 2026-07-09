<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\FormSubmission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class FormController extends Controller
{
    // Dashboard
    // Dashboard
    public function dashboard()
    {
        // Dashboard Cards
        $total = FormSubmission::count();

        $today = FormSubmission::whereDate('created_at', today())->count();

        $month = FormSubmission::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $latest = FormSubmission::latest()->first();

        // Submission Type Counts
        $protectedCount = FormSubmission::where('submission_type', 'Protected')->count();

        $unsafeCount = FormSubmission::where('submission_type', 'Unsafe')->count();

        $ajaxCount = FormSubmission::where('submission_type', 'AJAX')->count();

        // Last 7 Days Analytics
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {

            $date = Carbon::now()->subDays($i);

            $labels[] = $date->format('d M');

            $data[] = FormSubmission::whereDate(
                'created_at',
                $date->format('Y-m-d')
            )->count();
        }

        return view('dashboard', compact(
            'total',
            'today',
            'month',
            'latest',
            'protectedCount',
            'unsafeCount',
            'ajaxCount',
            'labels',
            'data'
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

        return back()->with('success', 'Protected Form Submitted Successfully!');
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

        return back()->with('success', 'Unsafe Form Submitted!');
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
            'message' => 'AJAX Form Submitted Successfully!',
        ]);
    }

    // List with Search + Date Filter + Pagination
    public function submissions(Request $request)
    {
        $query = FormSubmission::query();

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // From Date
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // To Date
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $submissions = $query
            ->oldest()
            ->paginate(7)
            ->withQueryString();

        return view('submissions.index', compact('submissions'));
    }

    // Delete
    public function destroy($id)
    {
        $submission = FormSubmission::findOrFail($id);

        $submission->delete();

        return back()->with('success', 'Submission Deleted Successfully.');
    }

    // Refresh CSRF Token
    public function refreshToken()
    {
        Session::regenerateToken();

        return response()->json([
            'success' => true,
            'token' => csrf_token(),
            'message' => 'CSRF Token Refreshed Successfully!'
        ]);
    }
}
