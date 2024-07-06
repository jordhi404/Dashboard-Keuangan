<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Keuangan</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Tabel Daftar Bed</h1>
        <p>Eksekusi query log: {{ $executionTime }} ms</p>

        <!-- Tabel -->
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Bed ID</th>
                    <th>Registration ID</th>
                    <th>Registration No</th>
                    <th>Bed Code</th>
                    <th>Room ID</th>
                    <th>Room Code</th>
                    <th>Room Name</th>
                    <th>GC Bed Status</th>
                    <th>Bed Status</th>
                    <th>Medical No</th>
                    <th>Patient Name</th>
                    <th>Business Partner Name</th>
                    <th>Paramedic Name</th>
                    <th>Class Name</th>
                    <th>Home Address</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($beds as $item)
                    <tr>
                        <td>{{ $item->BedID }}</td>
                        <td>{{ $item->RegistrationID }}</td>
                        <td>{{ $item->RegistrationNo }}</td>
                        <td>{{ $item->BedCode }}</td>
                        <td>{{ $item->RoomID }}</td>
                        <td>{{ $item->RoomCode }}</td>
                        <td>{{ $item->RoomName }}</td>
                        <td>{{ $item->GCBedStatus }}</td>
                        <td>{{ $item->BedStatus }}</td>
                        <td>{{ $item->MedicalNo }}</td>
                        <td>{{ $item->PatientName }}</td>
                        <td>{{ $item->BusinessPartnerName }}</td>
                        <td>{{ $item->ParamedicName }}</td>
                        <td>{{ $item->ClassName }}</td>
                        <td>{{ $item->patient->HomeAddress ?? '' }}</td>
                        <td>
                            @foreach ($item->patientNotes as $note)
                                <p>{{ $note->Notes }}</p>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>