<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="180">
    <title>Dashboard Ranap</title>
    <link rel="icon" href="{{ asset('Logo_img/logo_rs.jpg') }}" type="image/x-icon">
    <link rel="stylesheet" href="CSS/bootstrap.min.css">
    <link rel="stylesheet" href="CSS/dashboards-style.css">
</head>
<body>
    <div class="container">
        <div class="navbar d-flex">
            <div class="d-flex align-items-center justify-content-start">
                <img src="{{ asset('Logo_img/hospital-bed.png') }}" alt="ranap" style="height: 80px; width: 75px; margin-right: 20px">
                <div class="d-flex flex-column">
                    <h1 class="mb-1">Pasien Rencana Pulang</h1>
                    <p class="mb-2"> Update terakhir: {{ now()->format('H:i:s') }}</p>
                </div>
            </div>
            <div class="nav ms-auto">
                @auth
                    <li class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-left" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z"/>
                                        <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z"/>
                                        </svg> 
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </div>
        </div>
    </div>
    <div class="content-container">
        <div class="row">

            <!-- Kolom Keperawatan -->
            <div class="col">
                <div class="header">
                    <h3>Keperawatan</h3>
                </div>
                <hr class="border-5"/>
                @foreach ($groupedPatients['Tunggu Keperawatan'] as $patient)
                    <div class="card">
                        <div class="card-header" style="background-color: {{ $customerTypeColors[$patient->CustomerType] }};">
                            <strong>{{ $patient->PatientName }} - {{ $patient->BedCode }}</strong>
                        </div>
                        <div class="card-body">
                            <p>Medical No: {{ $patient->MedicalNo }}</p>
                            <p>Rencana Pulang: {{ $patient->RencanaPulang }}</p>
                            <p>
                                <strong>Note:</strong> {{ $patient->short_note }}
                                @if ($patient->CatRencanaPulang !== null)
                                    <a class="more-link"
                                        data-bs-toggle="popover"
                                        title="{{ $patient->PatientName }}'s Note"
                                        data-bs-content="{{ $patient->CatRencanaPulang }}">
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

            <!-- Kolom Jangdik -->
            <div class="col">
                <div class="header">
                    <h3>Tunggu Jangdik</h3>
                </div>
                <hr class="border-5"/>
                @foreach ($groupedPatients['Tunggu Jangdik'] as $patient)
                    <div class="card">
                        <div class="card-header" style="background-color: {{ $customerTypeColors[$patient->CustomerType] }};">
                            <strong>{{ $patient->PatientName }} - {{ $patient->BedCode }}</strong>
                        </div>
                        <div class="card-body">
                            <p>Medical No: {{ $patient->MedicalNo }}</p>
                            <p>Rencana Pulang: {{ $patient->RencanaPulang }}</p>
                            <p>
                                <strong>Note:</strong> {{ $patient->short_note }}
                                @if ($patient->CatRencanaPulang !== null)
                                    <a class="more-link"
                                        data-bs-toggle="popover"
                                        title="{{ $patient->PatientName }}'s Note"
                                        data-bs-content="{{ $patient->CatRencanaPulang }}">
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
                <hr class="border-5"/>
                @foreach ($groupedPatients['Tunggu Farmasi'] as $patient)
                    <div class="card">
                        <div class="card-header" style="background-color: {{ $customerTypeColors[$patient->CustomerType] }};">
                            <strong>{{ $patient->PatientName }} - {{ $patient->BedCode }}</strong>
                        </div>
                        <div class="card-body">
                            <p>Medical No: {{ $patient->MedicalNo }}</p>
                            <p>Rencana Pulang: {{ $patient->RencanaPulang }}</p>
                            <p>
                                <strong>Note:</strong> {{ $patient->short_note }}
                                @if ($patient->CatRencanaPulang !== null)
                                    <a class="more-link"
                                        data-bs-toggle="popover"
                                        title="{{ $patient->PatientName }}'s Note"
                                        data-bs-content="{{ $patient->CatRencanaPulang }}">
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

            <!-- Kolom Tunggu Kasir -->
            <div class="col">
                <div class="header">
                    <h3>Tunggu Kasir</h3>
                </div>
                <hr class="border-5"/>
                @foreach ($groupedPatients['Tunggu Kasir'] as $patient)
                    <div class="card">
                        <div class="card-header" style="background-color: {{ $customerTypeColors[$patient->CustomerType] }};">
                            <strong>{{ $patient->PatientName }} - {{ $patient->BedCode }}</strong>
                        </div>
                        <div class="card-body">
                            <p>Medical No: {{ $patient->MedicalNo }}</p>
                            <p>Rencana Pulang: {{ $patient->RencanaPulang }}</p>
                            <p>
                                <strong>Note:</strong> {{ $patient->short_note }}
                                @if ($patient->CatRencanaPulang !== null)
                                    <a class="more-link"
                                        data-bs-toggle="popover"
                                        title="{{ $patient->PatientName }}'s Note"
                                        data-bs-content="{{ $patient->CatRencanaPulang }}">
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

            <!-- Kolom Selesai Kasir -->
            <div class="col">
                <div class="header">
                    <h3>Selesai Kasir</h3>
                </div>
                <hr class="border-5"/>
                @foreach ($groupedPatients['Selesai Kasir'] as $patient)
                    <div class="card">
                        <div class="card-header" style="background-color: {{ $customerTypeColors[$patient->CustomerType] }};">
                            <strong>{{ $patient->PatientName }} - {{ $patient->BedCode }}</strong>
                        </div>
                        <div class="card-body">
                            <p>Medical No: {{ $patient->MedicalNo }}</p>
                            <p>Rencana Pulang: {{ $patient->RencanaPulang }}</p>
                            <p>
                                <strong>Note:</strong> {{ $patient->short_note }}
                                @if ($patient->CatRencanaPulang !== null)
                                    <a class="more-link"
                                        data-bs-toggle="popover"
                                        title="{{ $patient->PatientName }}'s Note"
                                        data-bs-content="{{ $patient->CatRencanaPulang }}">
                                        selengkapnya
                                    </a>
                                @endif
                            </p>
                            <p><strong>Administrasi Selesai.</strong></p>
                        </div>
                    </div>    
                @endforeach
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="JS/bootstrap.bundle.min.js"></script>

    <script>
        window.patients = @json($allPatients); // Error tidak perlu dikhawatirkan.
        console.log(window.patients);
    </script>
    <script src="{{ asset('JS/script.js') }}"></script>
</body>
</html>