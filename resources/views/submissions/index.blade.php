@extends('layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Form Submissions</h2>

        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            Dashboard
        </a>
    </div>

    <div class="card mb-4">

        <div class="card-header bg-dark text-white">
            Search & Date Filter
        </div>

        <div class="card-body">

            <form method="GET" action="{{ route('submissions.index') }}">

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <input type="text" class="form-control" name="search" placeholder="Search Name or Email"
                            value="{{ request('search') }}">

                    </div>

                    <div class="col-md-3 mb-3">

                        <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">

                    </div>

                    <div class="col-md-3 mb-3">

                        <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">

                    </div>

                    <div class="col-md-2 mb-3">

                        <button class="btn btn-success w-100">

                            Search

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <div class="card">

        <div class="card-header bg-primary text-white">

            Submission List

        </div>

        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="table-dark">

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Email</th>

                        <th>Type</th>

                        <th>IP Address</th>

                        <th>Date</th>

                        <th width="120">Action</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($submissions as $submission)

                        <tr>

                            <td>{{ $submission->id }}</td>

                            <td>{{ $submission->name }}</td>

                            <td>{{ $submission->email }}</td>

                            <td>{{ $submission->submission_type }}</td>

                            <td>{{ $submission->ip_address }}</td>

                            <td>
                                {{ $submission->created_at->format('d M Y h:i A') }}
                            </td>

                            <td>

                                <form action="{{ route('submissions.destroy', $submission->id) }}" method="POST"
                                    class="delete-form">

                                    @csrf

                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm">

                                        Delete

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="text-center">

                                No Records Found

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

            <div class="d-flex justify-content-center mt-4">
                <nav>
                    <ul class="pagination">

                        @for ($page = 1; $page <= $submissions->lastPage(); $page++)
                            <li class="page-item {{ $page == $submissions->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $submissions->url($page) }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endfor

                    </ul>
                </nav>
            </div>

        </div>

    </div>

</div>

@endsection


@section('scripts')

    <script>

        document.querySelectorAll('.delete-form').forEach(function (form) {

            form.addEventListener('submit', function (e) {

                e.preventDefault();

                Swal.fire({

                    title: 'Are you sure?',

                    text: "You won't be able to recover this record!",

                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonColor: '#d33',

                    cancelButtonColor: '#3085d6',

                    confirmButtonText: 'Yes, Delete'

                }).then((result) => {

                    if (result.isConfirmed) {

                        form.submit();

                    }

                });

            });

        });

    </script>

@endsection