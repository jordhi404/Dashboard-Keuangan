<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Keeping</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="CSS/detail-styles.css">
</head>
<body>
    <h1>Housekeeping Dashboard</h1>
    <table class="table tabled-bordered">
        <thead>
            <tr>
                <th>BedID</th>
                <th>BedCode</th>
                <th>RoomID</th>
                <th>RoomCode</th>
                <th>RoomName</th>
                <th>GCBedStatus</th>
                <th>BedStatus</th>
                <th>ServiceUnitCode</th>
                <th>ServiceUnitName</th>
                <th>ClassID</th>
                <th>ClassCode</th>
                <th>ClassName</th>
                <th>GCClassRL</th>
                <th>LastUnoccupiedDate</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($beds as $bed)
            <tr>
                <td>{{ $bed->BedID }}</td>
                <td>{{ $bed->BedCode }}</td>
                <td>{{ $bed->RoomID }}</td>
                <td>{{ $bed->RoomCode }}</td>
                <td>{{ $bed->RoomName }}</td>
                <td>{{ $bed->GCBedStatus }}</td>
                <td>{{ $bed->BedStatus }}</td>
                <td>{{ $bed->ServiceUnitCode }}</td>
                <td>{{ $bed->ServiceUnitName }}</td>
                <td>{{ $bed->ClassID }}</td>
                <td>{{ $bed->ClassCode }}</td>
                <td>{{ $bed->ClassName }}</td>
                <td>{{ $bed->GCClassRL }}</td>
                <td>{{ $bed->LastUnoccupiedDate }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>