<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="180">
    <title>House Keeping</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="CSS/HKDash-styles.css">
</head>
<body>
    <div class="navbar">
        <h1>Housekeeping Dashboard</h1>
        <p class="mb-2"> Update terakhir: {{ now()->format('H:i:s') }}</p>
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
    <div class="body-container">
        @foreach ($rooms as $room) 
        <div class="card" id="room">
            <h5 class="card-title"><strong>{{ $room->RoomName }} </strong></h5>
            <p><strong>Unit:</strong> {{ $room->ServiceUnitName }}</p>
            <p><strong>Kelas:</strong> {{ $room->ClassName }}</p>
            <p><strong>Status:</strong> {{ $room->BedStatus }}</p>
            <p><strong>Cleaning Time:</strong><span id="cleaning-time-{{ $room->RoomName }}"> {{ $room->clean_time }} </span><br></p>
        </div> 
        @endforeach
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>