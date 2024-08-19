<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="180">
    <title>Dashboard Ranap</title>
    <link rel="icon" href="{{ asset('Logo_img/logo_rs.jpg') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="CSS/ranapDashboard-styles.css">
</head>
<body>
    <div class="container">
        <div id="mySidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            @auth
                <p>{{ Auth::user()->name }}</p>
                <hr class="border-5 border-dark"/>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z"/>
                        <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z"/>
                        </svg> 
                        Logout
                    </button>
                </form>
            @endauth
        </div>
        <div class="container">
            <div class="navbar d-flex">
                <div class="d-flex align-items-center">
                    <span style="cursor:pointer; margin-right: 20px" onclick="openNav()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="50" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M2.5 12.5a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-11zm0-3a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-11zm0-3a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-11z"/>
                        </svg>
                    </span>
                    <h1>Pasien Rencana Pulang</h1>
                </div>
                <div class="nav">
                    <p class="mb-2"> Update terakhir: {{ now()->format('H:i:s') }}</p>
                </div>
            </div>
        </div>
        <div class="content-container">
        <div class="row">

            <!-- Kolom Keperawatan -->
            <div class="col">
                <div class="header">
                    <h3>Tunggu Keperawatan</h3>
                </div>
                <hr class="border-5 border-primary"/>
                @foreach ($groupedPatients['Keperawatan'] as $patient)
                    @php 
                        $color = $customerTypeColors[$patient->CustomerType] ?? 'defaultColor';
                    @endphp

                    <div class="card" id="patient-card-{{ $patient->MedicalNo }}" data-customer-type="{{ $patient->CustomerType }}">
                        <div class="card-header" style="background-color: {{ $color }}">
                            {{ $patient->MedicalNo }}
                            @if ($patient->SelesaiBilling)
                                <p><strong>Sudah Bayar</strong></p>
                            @endif
                            <h5 class="card-title"><strong>{{ $patient->PatientName }}</strong></h5>                        
                        </div>
                        <div class="card-body text-small">
                            <p><strong>Penjamin:</strong> {{ $patient->CustomerType }}</p>
                            <p><strong>Bed:</strong> {{ $patient->BedCode }}</p>
                            <p>
                                <strong>Note:</strong> {{ $patient->short_note }}
                                @if ($patient->NoteText !== null)
                                    <a class="more-link"
                                        data-bs-toggle="popover"
                                        title="{{ $patient->PatientName }}'s Note"
                                        data-bs-content="{{ $patient->NoteText }}">
                                        selengkapnya
                                    </a>
                                @endif
                            </p>
                            <p><strong>Wait Time:</strong><span id="wait-time-{{ $patient->MedicalNo }}"> {{ $patient->wait_time }} </span><br></p>
                            <div class="progress mb-2">
                                <div id="progress-bar-{{ $patient->MedicalNo }}" 
                                    class="progress-bar {{ $patient->progress_percentage > 100 ? 'progress-bar-red' : 'progress-bar-blue' }}"
                                    role="progressbar"
                                    style="width: {{ $patient->progress_percentage }}%"
                                    aria-valuenow="{{ $patient->progress_percentage }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>    
                @endforeach
            </div>

            <!-- Kolom Jangdik -->
            <div class="col">
                <div class="header">
                    <h3>Tunggu Penunjang Medik</h3>
                </div>
                <hr class="border-5 border-info"/>
                @foreach ($groupedPatients['Jangdik'] as $patient)
                    @php 
                        $color = $customerTypeColors[$patient->CustomerType] ?? 'defaultColor';
                    @endphp

                    <div class="card" id="patient-card-{{ $patient->MedicalNo }}" data-customer-type="{{ $patient->CustomerType }}">
                        <div class="card-header" style="background-color: {{ $color }}">
                            {{ $patient->MedicalNo }}
                            @if ($patient->SelesaiBilling)
                            <p><strong>Sudah Bayar</strong></p>
                            @endif
                            <h5 class="card-title"><strong>{{ $patient->PatientName }}</strong></h5>                        </div>
                        <div class="card-body text-small">
                            <p><strong>Penjamin:</strong> {{ $patient->CustomerType }}</p>
                            <p><strong>Bed:</strong> {{ $patient->BedCode }}<br></p>
                            <p>
                                <strong>Note:</strong> {{ $patient->short_note }}
                                @if ($patient->NoteText !== null)
                                    <a class="more-link"
                                        data-bs-toggle="popover"
                                        title="{{ $patient->PatientName }}'s Note"
                                        data-bs-content="{{ $patient->NoteText }}">
                                        selengkapnya
                                    </a>
                                @endif
                            </p>
                            <p><strong>Wait Time:</strong><span id="wait-time-{{ $patient->MedicalNo }}"> {{ $patient->wait_time }}</span><br></p>
                            <div class="progress mb-2">
                                <div id="progress-bar-{{ $patient->MedicalNo }}" 
                                    class="progress-bar {{ $patient->progress_percentage > 100 ? 'progress-bar-red' : 'progress-bar-blue' }}"
                                    role="progressbar"
                                    style="width: {{ $patient->progress_percentage }}%"
                                    aria-valuenow="{{ $patient->progress_percentage }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>    
                @endforeach
            </div>

            <!-- Kolom Farmasi -->
            <div class="col">
                <div class="header">
                    <h3>Tunggu Farmasi</h3>
                </div>
                <hr class="border-5 border-success"/>
                @foreach ($groupedPatients['Farmasi'] as $patient)
                    @php 
                        $color = $customerTypeColors[$patient->CustomerType] ?? 'defaultColor';
                    @endphp

                    <div class="card" id="patient-card-{{ $patient->MedicalNo }}" data-customer-type="{{ $patient->CustomerType }}">
                        <div class="card-header" style="background-color: {{ $color }}">
                            {{ $patient->MedicalNo }}
                            @if ($patient->SelesaiBilling)
                                <p><strong>Sudah Bayar</strong></p>
                            @endif
                            <h5 class="card-title"><strong>{{ $patient->PatientName }}</strong></h5>                        </div>
                        <div class="card-body text-small">
                            <p><strong>Penjamin:</strong> {{ $patient->CustomerType }}</p>
                            <p><strong>Bed:</strong> {{ $patient->BedCode }}<br></p>
                            <p>
                                <strong>Note:</strong> {{ $patient->short_note }}
                                @if ($patient->NoteText !== null)
                                    <a class="more-link"
                                        data-bs-toggle="popover"
                                        title="{{ $patient->PatientName }}'s Note"
                                        data-bs-content="{{ $patient->NoteText }}">
                                        selengkapnya
                                    </a>
                                @endif
                            </p>
                            <p><strong>Wait Time:</strong><span id="wait-time-{{ $patient->MedicalNo }}"> {{ $patient->wait_time }}</span><br></p>
                            <div class="progress mb-2">
                                <div id="progress-bar-{{ $patient->MedicalNo }}" 
                                    class="progress-bar {{ $patient->progress_percentage > 100 ? 'progress-bar-red' : 'progress-bar-blue' }}"
                                    role="progressbar"
                                    style="width: {{ $patient->progress_percentage }}%"
                                    aria-valuenow="{{ $patient->progress_percentage }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>    
                @endforeach
            </div>

            <!-- Kolom Kasir -->
            <div class="col">
                <div class="header">
                    <h3>Tunggu Kasir</h3>
                </div>
                <hr class="border-5 border-danger"/>
                @foreach ($groupedPatients['Kasir'] as $patient)
                    @php 
                        $color = $customerTypeColors[$patient->CustomerType] ?? 'defaultColor';
                    @endphp

                    <div class="card" id="patient-card-{{ $patient->MedicalNo }}" data-customer-type="{{ $patient->CustomerType }}">
                        <div class="card-header" style="background-color: {{ $color }}">
                            {{ $patient->MedicalNo }}
                            @if ($patient->SelesaiBilling)
                                <p><strong>Sudah Bayar</strong></p>
                            @endif
                            <h5 class="card-title"><strong>{{ $patient->PatientName }}</strong></h5>
                        </div>
                        <div class="card-body text-small">
                            <p><strong>Penjamin:</strong> {{ $patient->CustomerType }}</p>
                            <p><strong>Bed:</strong> {{ $patient->BedCode }}<br></p>
                            <p>
                                <strong>Note:</strong> {{ $patient->short_note }}
                                @if ($patient->NoteText !== null)
                                    <a class="more-link"
                                        data-bs-toggle="popover"
                                        title="{{ $patient->PatientName }}'s Note"
                                        data-bs-content="{{ $patient->NoteText }}">
                                        selengkapnya
                                    </a>
                                @endif
                            </p>
                            <p><strong>Wait Time:</strong><span id="wait-time-{{ $patient->MedicalNo }}"> {{ $patient->wait_time }}</span><br></p>
                            <div class="progress mb-2">
                                <div id="progress-bar-{{ $patient->MedicalNo }}" 
                                    class="progress-bar {{ $patient->progress_percentage > 100 ? 'progress-bar-red' : 'progress-bar-blue' }}"
                                    role="progressbar"
                                    style="width: {{ $patient->progress_percentage }}%"
                                    aria-valuenow="{{ $patient->progress_percentage }}"
                                    aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>    
                @endforeach
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('JS/script.js') }}"></script>
    <script>
        window.patients = @json($patients); // Tidak perlu dikhawatirkan.
        console.log("Patients data: ", window.patients);
    </script>
</body>
</html>