<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Keuangan</title>
    <link rel="icon" href="{{ asset('Logo_img/logo_rs.jpg') }}" type="image/x-icon">
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .detail-container {
            margin: 20px;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
        }
        .table th, .table td {
            padding: .75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
            font-size: 20px;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }
        .table tbody + tbody {
            border-top: 2px solid #dee2e6;
            font-family: Arial;
        }
    </style>
</head>
<body>
    <div class="container detail-container">
        <h2>Detail Pasien</h2>
        <table class="table table-striped">
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
                    <th scope="row">Home Address</th>
                    <td>{{ $patient->HomeAddress }}</td>
                </tr>
                <tr>
                    <th scope="row">Business Partner Name</th>
                    <td>{{ $patient->BusinessPartnerName }}</td>
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
            </tbody>
        </table>
    </div>
</body>
</html>