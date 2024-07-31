<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pasien</title>
    <link rel="icon" href="{{ asset('Logo_img/logo_rs.jpg') }}" type="image/x-icon">
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('CSS/detail-styles.css') }}">
</head>
<body>
    <div class="container detail-container">
        <div class="navbar mb-4">
            <h1>Detail Pasien</h1>
        </div>
        <table class="table table-striped table-bordered">
            <tbody>
                <tr>
                    <th scope="row">Medical No</th>
                    <td>{{ $patient->MedicalNo }}</td>
                </tr>
                <tr>
                    <th scope="row">Registration ID</th>
                    <td>{{ $patient->RegistrationID }}</td>
                </tr>
                <tr>
                    <th scope="row">Registration No</th>
                    <td>{{ $patient->RegistrationNo }}</td>
                </tr>
                <tr>
                    <th scope="row">Patient Name</th>
                    <td>{{ $patient->PatientName }}</td>
                </tr>
                <tr>
                    <th scope="row">Patient Type</th>
                    <td>{{ $patient->CustomerType }}</td>
                </tr>
                <tr>
                    <th scope="row">Charge Class Name</th>
                    <td>{{ $patient->ChargeClassName }}</td>
                </tr>
                <tr>
                    <th scope="row">Bed Code</th>
                    <td>{{ $patient->BedCode }}</td>
                </tr>
                <tr>
                    <th scope="row">Bed Status</th>
                    <td>{{ $patient->BedStatus }}</td>
                </tr>
                <tr>
                    <th scope="row">Paramedic Name</th>
                    <td>{{ $patient->ParamedicName }}</td>
                </tr>
                <tr>
                    <th scope="row">Rencana Pulang</th>
                    <td>{{ $patient->RencanaPulang }}</td>
                </tr>
                <tr>
                    <th scope="row">Keterangan</th>
                    <td>{{ $patient->Keterangan }}</td>
                </tr>
                <tr>
                    <th scope="row">Note</th>
                    <td class="note-text">{{ $patient->NoteText }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>