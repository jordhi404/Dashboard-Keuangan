<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasien Selesai Billing</title>
</head>
<body>
<div class="container">
    @foreach ($completedBilling as $group => $patientGroup)
        <h3>{{ $group }}</h3>
        <div class="row">
            @foreach ($patientGroup as $patient)
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            {{ $patient->PatientName }} ({{ $patient->ChargeClassName }})
                        </div>
                        <div class="card-body">
                            <p><strong>Bed Code:</strong> {{ $patient->BedCode }}</p>
                            <p><strong>Selesai Billing:</strong> {{ $patient->SelesaiBilling }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    @foreach ($incompleteBilling as $group => $patientGroup)
        <h3>{{ $group }}</h3>
        <div class="row">
            @foreach ($patientGroup as $patient)
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-warning text-dark">
                            {{ $patient->PatientName }} ({{ $patient->ChargeClassName }})
                        </div>
                        <div class="card-body">
                            <p><strong>Bed Code:</strong> {{ $patient->BedCode }}</p>
                            <p><strong>Selesai Billing:</strong> Belum Selesai</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
</body>
</html>