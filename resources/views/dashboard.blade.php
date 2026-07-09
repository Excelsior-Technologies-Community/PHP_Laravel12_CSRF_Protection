@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">📊 CSRF Protection Dashboard</h2>

    <div class="row">

        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body text-center">
                    <h5>Total Submissions</h5>
                    <h2>{{ $total }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body text-center">
                    <h5>Today's</h5>
                    <h2>{{ $today }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark shadow">
                <div class="card-body text-center">
                    <h5>This Month</h5>
                    <h2>{{ $month }}</h2>
                </div>
            </div>
        </div>

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


    <div class="card shadow mb-4">

        <div class="card-header bg-dark text-white">
            Dashboard Overview
        </div>

        <div class="card-body">

            <p>
                This dashboard displays all submitted forms stored in the database.
            </p>

            <div class="d-flex gap-2">

                <a href="{{ route('form.show') }}" class="btn btn-primary">
                    Protected Form
                </a>

                <a href="{{ route('ajax.form.show') }}" class="btn btn-info">
                    AJAX Form
                </a>

                <a href="{{ route('submissions.index') }}" class="btn btn-success">
                    View Submissions
                </a>

            </div>

        </div>

    </div>


    <!-- CSRF Security Monitor -->

    <div class="card shadow mb-4">

        <div class="card-header bg-dark text-white">
            🔐 CSRF Security Monitor
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">

                    <h6>Current Token</h6>

                    <input
                        type="text"
                        id="csrfTokenDisplay"
                        class="form-control"
                        value="{{ substr(csrf_token(),0,20) }}********"
                        readonly>


                    <button
                        class="btn btn-outline-primary btn-sm mt-2"
                        data-bs-toggle="modal"
                        data-bs-target="#tokenModal">

                        👁 View Full Token

                    </button>

                </div>

                <div class="col-md-4">

                    <h6>Status</h6>

                    <span class="badge bg-success fs-6">
                        ✅ Token Valid
                    </span>

                </div>


                <div class="col-md-4">

                    <h6>Last Refresh</h6>

                    <span id="tokenTime">
                        {{ now()->format('d M Y h:i A') }}
                    </span>

                </div>

            </div>


            <button
                id="refreshCsrfBtnDashboard"
                class="btn btn-warning mt-3">

                🔄 Refresh Token

            </button>


        </div>

    </div>

    <!-- Full Token Modal -->

    <div class="modal fade" id="tokenModal">

        <div class="modal-dialog">

            <div class="modal-content">


                <div class="modal-header bg-dark text-white">

                    <h5 class="modal-title">
                        Full CSRF Token
                    </h5>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">

                    </button>

                </div>


                <div class="modal-body">

                    <textarea
                        id="fullToken"
                        class="form-control"
                        rows="5"
                        readonly>{{ csrf_token() }}</textarea>

                </div>


                <div class="modal-footer">

                    <button
                        class="btn btn-primary"
                        onclick="copyToken()">

                        📋 Copy Token

                    </button>

                </div>


            </div>

        </div>

    </div>


    <!-- Charts -->

    <div class="row">

        <div class="col-md-5 mb-4">

            <div class="card shadow">

                <div class="card-header bg-primary text-white">
                    Submission Types
                </div>

                <div class="card-body">

                    <canvas id="typeChart"></canvas>

                </div>

            </div>

        </div>


        <div class="col-md-7 mb-4">

            <div class="card shadow">

                <div class="card-header bg-success text-white">
                    Last 7 Days Submissions
                </div>

                <div class="card-body">

                    <canvas id="weeklyChart"></canvas>

                </div>

            </div>

        </div>

    </div>


</div>
@endsection


@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>
    new Chart(document.getElementById('typeChart'), {

        type: 'doughnut',

        data: {

            labels: [
                'Protected',
                'Unsafe',
                'AJAX'
            ],

            datasets: [{

                data: @json([
                    $protectedCount,
                    $unsafeCount,
                    $ajaxCount
                ])

            }]

        },

        options: {

            responsive: true,

            plugins: {

                legend: {
                    position: 'bottom'
                }

            }

        }

    });



    new Chart(document.getElementById('weeklyChart'), {

        type: 'line',

        data: {

            labels: @json($labels),

            datasets: [{

                label: 'Submissions',

                data: @json($data),

                fill: true,

                tension: 0.4

            }]

        },

        options: {

            responsive: true,

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {

                        precision: 0

                    }

                }

            }

        }

    });


    // CSRF Token Refresh

    $('#refreshCsrfBtnDashboard').click(function() {


        $.ajax({

            url: "{{ route('csrf.refresh') }}",

            type: "GET",


            success: function(response) {


                let maskedToken =
                    response.token.substring(0, 20) + "********";


                $('#csrfTokenDisplay')
                    .val(maskedToken);


                $('#fullToken')
                    .val(response.token);


                $('#tokenTime')
                    .text(new Date().toLocaleString());



                $.ajaxSetup({

                    headers: {

                        'X-CSRF-TOKEN': response.token

                    }

                });



                Swal.fire({

                    icon: 'success',

                    title: 'CSRF Token Updated',

                    text: 'New security token generated',

                    timer: 1500,

                    showConfirmButton: false

                });


            },


            error: function() {


                Swal.fire({

                    icon: 'error',

                    title: 'Error',

                    text: 'Unable to refresh CSRF token'

                });


            }


        });


    });

    function copyToken() {

        let token = document.getElementById('fullToken');

        token.select();

        document.execCommand('copy');


        Swal.fire({

            icon: 'success',

            title: 'Copied',

            text: 'CSRF Token copied successfully',

            timer: 1500,

            showConfirmButton: false

        });

    }
</script>

@endsection