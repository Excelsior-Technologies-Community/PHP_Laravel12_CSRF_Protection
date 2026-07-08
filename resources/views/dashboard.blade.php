@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">
        📊 CSRF Protection Dashboard
    </h2>

    <div class="row">

        <!-- Total -->

        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body text-center">
                    <h5>Total Submissions</h5>

                    <h2>{{ $total }}</h2>
                </div>
            </div>
        </div>

        <!-- Today -->

        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body text-center">
                    <h5>Today's</h5>

                    <h2>{{ $today }}</h2>
                </div>
            </div>
        </div>

        <!-- Month -->

        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark shadow">
                <div class="card-body text-center">
                    <h5>This Month</h5>

                    <h2>{{ $month }}</h2>
                </div>
            </div>
        </div>

        <!-- Latest -->

        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white shadow">
                <div class="card-body text-center">

                    <h5>Latest User</h5>

                    @if($latest)

                        <strong>{{ $latest->name }}</strong>

                        <br>

                        <small>{{ $latest->email }}</small>

                    @else

                        <strong>No Data</strong>

                    @endif

                </div>
            </div>
        </div>

    </div>

    <div class="card shadow">

        <div class="card-header bg-dark text-white">

            Dashboard Overview

        </div>

        <div class="card-body">

            <p>
                This dashboard displays all submitted forms stored in the database.
            </p>

            <div class="d-flex gap-2">

                <a href="{{ route('form.show') }}"
                    class="btn btn-primary">

                    Protected Form

                </a>

                <a href="{{ route('ajax.form.show') }}"
                    class="btn btn-info">

                    AJAX Form

                </a>

                <a href="{{ route('submissions.index') }}"
                    class="btn btn-success">

                    View Submissions

                </a>

            </div>

        </div>

    </div>

</div>

@endsection