<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="180">
    <title>Dashboard Keuangan</title>
    <link rel="icon" href="{{ asset('Logo_img/logo_rs.jpg') }}" type="image/x-icon">
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="CSS/trial-style.css">
    <link rel="stylesheet" href="CSS/snow.css">
</head>
<body>
    <div class="container">
        <div class="navbar d-flex">
            <div class="d-flex flex-column">
                <!-- snowflakes -->
                <div class="snowflake" style="left: 10%;"></div>
                <div class="snowflake" style="left: 20%;"></div>
                <div class="snowflake" style="left: 30%;"></div>
                <div class="snowflake" style="left: 40%;"></div>
                <div class="snowflake" style="left: 50%;"></div>
                <div class="snowflake" style="left: 60%;"></div>
                <div class="snowflake" style="left: 70%;"></div>
                <div class="snowflake" style="left: 80%;"></div>
                <div class="snowflake" style="left: 90%;"></div>

                <h1 class="mb-4">Pasien Rencana Pulang</h1>
                <p class="mb-2"> Update terakhir: {{ now()->format('H:i:s') }}</p>
                <p>Total Pasien Rencana Pulang: {{ $totalPatient }}</p>
            </div>
            <ul class="nav ms-auto">
                @auth
                    <li class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Selamat datang, {{ Auth::user()->name }}!
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
            </ul>
        </div>
    </div>
    <div class="content-container">
        <div class="row">

            <!-- Kolom Keperawatan -->
            <div class="col-md-2">
                <div class="header">
                    <h3>Keperawatan</h3>
                    <h5>Jumlah: {{ $patientCounts['Ruangan'] }}</h5>
                </div>
                <hr class="border-5 border-primary"/>
                @foreach ($groupedPatients['Ruangan'] as $patient)
                    @php 
                        $color = $customerTypeColors[$patient->CustomerType] ?? 'defaultColor';
                    @endphp

                    <div class="card" id="patient-card-{{ $patient->MedicalNo }}">
                        <div class="card-header" style="background-color: {{ $color }}">
                            {{ $patient->MedicalNo }}<br>
                            <span class="float-start">Rencana Pulang: {{ $patient->RencanaPulang }}</span>
                        </div>
                        <div class="card-body text-small">
                            <h5 class="card-title"><strong>{{ $patient->PatientName }} </strong></h5>
                            <p><strong>Penjamin Bayar:</strong> {{ $patient->CustomerType }}</p>
                            <p><strong>Bed Code:</strong> {{ $patient->BedCode }}</p>
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
                            <div class="full-note-bubble"> {{ $patient->NoteText }}</div>
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
            <div class="col-md-2">
                <div class="header">
                    <h3>Penunjang Medik</h3>
                    <h5>Jumlah: {{ $patientCounts['Jangdik'] }}</h5>
                </div>
                <hr class="border-5 border-info"/>
                @foreach ($groupedPatients['Jangdik'] as $patient)
                    @php 
                        $color = $customerTypeColors[$patient->CustomerType] ?? 'defaultColor';
                    @endphp

                    <div class="card" id="patient-card-{{ $patient->MedicalNo }}">
                        <div class="card-header" style="background-color: {{ $color }}">
                            {{ $patient->MedicalNo }}<br>
                            <span class="float-start">Rencana Pulang: {{ $patient->RencanaPulang }}</span>
                        </div>
                        <div class="card-body text-small">
                            <h5 class="card-title"><strong>{{ $patient->PatientName }}</strong></h5>
                            <p><strong>Penjamin Bayar:</strong> {{ $patient->CustomerType }}</p>
                            <p><strong>Bed Code:</strong> {{ $patient->BedCode }}<br></p>
                            <p><strong>Wait Time:</strong><span id="wait-time-{{ $patient->MedicalNo }}"> {{ $patient->wait_time }}</span><br></p>
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
                            <div class="full-note-bubble"> {{ $patient->NoteText }}</div>
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
            <div class="col-md-2">
                <div class="header">
                    <h3>Farmasi</h3>
                    <h5>Jumlah: {{ $patientCounts['Farmasi'] }}</h5>
                </div>
                <hr class="border-5 border-success"/>
                @foreach ($groupedPatients['Farmasi'] as $patient)
                    @php 
                        $color = $customerTypeColors[$patient->CustomerType] ?? 'defaultColor';
                    @endphp

                    <div class="card" id="patient-card-{{ $patient->MedicalNo }}">
                        <div class="card-header" style="background-color: {{ $color }}">
                            {{ $patient->MedicalNo }}<br>
                            <span class="float-start">Rencana Pulang: {{ $patient->RencanaPulang }}</span>
                        </div>
                        <div class="card-body text-small">
                            <h5 class="card-title"><strong>{{ $patient->PatientName }}</strong></h5>
                            <p><strong>Penjamin Bayar:</strong> {{ $patient->CustomerType }}</p>
                            <p><strong>Bed Code:</strong> {{ $patient->BedCode }}<br></p>
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
            <div class="col-md-2">
                <div class="header">
                    <h3>Kasir</h3>
                    <h5>Jumlah: {{ $patientCounts['Kasir'] }}</h5>
                </div>
                <hr class="border-5 border-danger"/>
                @foreach ($groupedPatients['Kasir'] as $patient)
                    @php 
                        $color = $customerTypeColors[$patient->CustomerType] ?? 'defaultColor';
                    @endphp

                    <div class="card" id="patient-card-{{ $patient->MedicalNo }}">
                        <div class="card-header" style="background-color: {{ $color }}">
                            {{ $patient->MedicalNo }}<br>
                            <span class="float-start">Rencana Pulang: {{ $patient->RencanaPulang }}</span>
                        </div>
                        <div class="card-body text-small">
                            <h5 class="card-title"><strong>{{ $patient->PatientName }}</strong></h5>
                            <p><strong>Penjamin Bayar:</strong> {{ $patient->CustomerType }}</p>
                            <p><strong>Bed Code:</strong> {{ $patient->BedCode }}<br></p>
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

            <!-- Kolom Administrasi selesai -->
            <div class="col-md-2">
                <div class="header">
                    <h3>Selesai</h3>
                    <h5>Jumlah:</h5>
                </div>
                <hr class="border-5 border-secondary"/>
                
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