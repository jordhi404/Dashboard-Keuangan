<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Keuangan</title>
    <link rel="icon" href="{{ asset('Logo_img/logo_rs.jpg') }}" type="image/x-icon">
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="CSS/trial-style.css">
</head>
<body>
    <div class="container mb-4">
        <div class="navbar d-flex">
            <div class="d-flex flex-column">
                <h1 class="mb-4">Pasien Rencana Pulang</h1>
                <p class="mb-3"> Update terakhir: {{ now()->format('H:i:s') }}</p>
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
    <div class="content-container mb-4">
        <div class="row">

            <!-- Kolom Ruangan -->
            <div class="col-md-3">
                <h3>Ruangan</h3>
                <h5>Jumlah: {{ $patientCounts['Ruangan'] }}</h5>
                <hr class="border-5 border-primary"/>
                @foreach ($groupedPatients['Ruangan'] as $patient)
                    <div class="card" id="patient-card-{{ $patient->MedicalNo }}">
                        <div class="card-header text-light bg-primary">
                            {{ $patient->MedicalNo }}<br>
                            <span class="float-start">Rencana Pulang: {{ $patient->RencanaPulang }}</span>
                        </div>
                        <div class="card-body text-small">
                            <h5 class="card-title"><strong>{{ $patient->PatientName }} </strong></h5>
                            <p><strong>Bed Code:</strong> {{ $patient->BedCode }} <br></p>
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
                            <a href="{{ route('patient.detail', ['id' => $patient->RegistrationID]) }}" class="btn btn-primary">Detail</a>
                        </div>
                    </div>    
                @endforeach
            </div>

            <!-- Kolom Jarsdik -->
            <div class="col-md-3">
                <h3>Jarsdik</h3>
                <h5>Jumlah: {{ $patientCounts['Jarsdik'] }}</h5>
                <hr class="border-5 border-info"/>
                @foreach ($groupedPatients['Jarsdik'] as $patient)
                    <div class="card" id="patient-card-{{ $patient->MedicalNo }}">
                        <div class="card-header text-light bg-info">
                            {{ $patient->MedicalNo }}<br>
                            <span class="float-start">Rencana Pulang: {{ $patient->RencanaPulang }}</span>
                        </div>
                        <div class="card-body text-small">
                            <h5 class="card-title"><strong>{{ $patient->PatientName }}</strong></h5>
                            <p><strong>Bed Code:</strong> {{ $patient->BedCode }}<br></p>
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
                            <a href="{{ route('patient.detail', ['id' => $patient->RegistrationID]) }}" class="btn btn-primary">Detail</a>
                        </div>
                    </div>    
                @endforeach
            </div>

            <!-- Kolom Farmasi -->
            <div class="col-md-3">
                <h3>Farmasi</h3>
                <h5>Jumlah: {{ $patientCounts['Farmasi'] }}</h5>
                <hr class="border-5 border-success"/>
                @foreach ($groupedPatients['Farmasi'] as $patient)
                    <div class="card" id="patient-card-{{ $patient->MedicalNo }}">
                        <div class="card-header text-light bg-success">
                            {{ $patient->MedicalNo }}<br>
                            <span class="float-start">Rencana Pulang: {{ $patient->RencanaPulang }}</span>
                        </div>
                        <div class="card-body text-small">
                            <h5 class="card-title"><strong>{{ $patient->PatientName }}</strong></h5>
                            <p><strong>Bed Code:</strong> {{ $patient->BedCode }}<br></p>
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
                            <a href="{{ route('patient.detail', ['id' => $patient->RegistrationID]) }}" class="btn btn-primary">Detail</a>
                        </div>
                    </div>    
                @endforeach
            </div>

            <!-- Kolom Kasir -->
            <div class="col-md-3">
                <h3>Kasir</h3>
                <h5>Jumlah: {{ $patientCounts['Kasir'] }}</h5>
                <hr class="border-5 border-danger"/>
                @foreach ($groupedPatients['Kasir'] as $patient)
                    <div class="card" id="patient-card-{{ $patient->MedicalNo }}">
                        <div class="card-header text-light bg-danger">
                            {{ $patient->MedicalNo }}<br>
                            <span class="float-start">Rencana Pulang: {{ $patient->RencanaPulang }}</span>
                        </div>
                        <div class="card-body text-small">
                            <h5 class="card-title"><strong>{{ $patient->PatientName }}</strong></h5>
                            <p><strong>Bed Code:</strong> {{ $patient->BedCode }}<br></p>
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
                            <a href="{{ route('patient.detail', ['id' => $patient->RegistrationID]) }}" class="btn btn-primary">Detail</a>
                        </div>
                    </div>    
                @endforeach
            </div>
        </div>
    </div>
    <script src="{{ asset('JS/script.js') }}"></script>
    <script>
        window.patients = @json($patients); // Tidak perlu dikhawatirkan.
        console.log("Patients data: ", window.patients);
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>