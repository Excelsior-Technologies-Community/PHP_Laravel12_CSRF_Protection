<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FormController extends Controller
{
    // Show form with CSRF protection
    public function showForm()
    {
        return view('form');
    }

    // Handle form submission with CSRF protection
    public function submitForm(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        // Log the submission
        Log::info('Form submitted successfully', [
            'name' => $request->name,
            'email' => $request->email,
            'ip' => $request->ip(),
        ]);

        return back()->with('success', 'Form submitted successfully!');
    }

    // Show form without CSRF protection (for demo)
    public function showUnsafeForm()
    {
        return view('form-unsafe');
    }

    // Handle form submission without CSRF protection
    public function submitUnsafeForm(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        Log::warning('Unsafe form submitted', [
            'name' => $request->name,
            'email' => $request->email,
            'ip' => $request->ip(),
        ]);

        return back()->with('success', 'Unsafe form submitted!');
    }

    // Show AJAX form
    public function showAjaxForm()
    {
        return view('ajax-form');
    }

    // Handle AJAX form submission
    public function submitAjaxForm(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Form submitted via AJAX successfully!',
            'data' => [
                'name' => $request->name,
                'email' => $request->email,
            ]
        ]);
    }
}