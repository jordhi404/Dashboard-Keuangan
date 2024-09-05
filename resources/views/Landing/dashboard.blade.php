<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="CSS/landing-styles.css">
    <link rel="icon" href="{{ asset('Logo_img/logo_rs.jpg') }}" type="image/x-icon">
</head>

<body>
    <script>
        window.onload = function() {
            if (!window.location.href.includes('login')) {
                window.history.pushState(null, null, window.location.href);
                window.onpopstate = function() {
                    window.history.pushState(null, null, window.location.href);
                };
            }
        };
    </script>

    <div class="container">
        <div class="navbar">
            <h1>Menu Dashboard</h1>
            <ul class="nav ms-auto">
                @auth
                    <li class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><svg xmlns="http://www.w3.org/2000/svg"
                                            width="16" height="16" fill="currentColor" class="bi bi-box-arrow-left"
                                            viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z" />
                                            <path fill-rule="evenodd"
                                                d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z" />
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
        <div class="content-container text-center">
            <div class="logo-container mt-5">
                @if(Auth::user()->kode_bagian === 'k45' || Auth::user()->kode_bagian === 'os26')
                    <a href="{{ route('keuangan') }}" class="logo-link">
                        <img src="{{ asset('Logo_img/money.png') }}" alt="Keuangan">
                        <p>Dashboard Keuangan</p>
                    </a>
                    <a href="{{ route('hk') }}" class="logo-link">
                        <img src="{{ asset('Logo_img/house-keeping.png') }}" alt="HK">
                        <p>Dashboard House Keeping</p>
                    </a>
                    <a href="{{ route('ranap') }}" class="logo-link">
                        <img src="{{ asset('Logo_img/hospital-bed.png') }}" alt="Ranap">
                        <p>Dashboard Ranap</p>
                    </a>
                @elseif(Auth::user()->kode_bagian === 'k2' || Auth::user()->kode_bagian === 'k67')
                    <a href="{{ route('keuangan') }}" class="logo-link">
                        <img src="{{ asset('Logo_img/money.png') }}" alt="Keuangan">
                        <p>Dashboard Keuangan</p>
                    </a>
                @elseif(Auth::user()->kode_bagian === 'k13' || Auth::user()->kode_bagian === 'k14' || Auth::user()->kode_bagian === 'k15' || Auth::user()->kode_bagian === 'k16' || Auth::user()->kode_bagian === 'k41' || Auth::user()->kode_bagian === 'k58' || Auth::user()->kode_bagian === 'k59')
                    <a href="{{ route('ranap') }}" class="logo-link">
                        <img src="{{ asset('Logo_img/hospital-bed.png') }}" alt="Ranap">
                        <p>Dashboard Ranap</p>
                    </a>
                @elseif(Auth::user()->kode_bagian === 'k32')
                    <a href="{{ route('hk') }}" class="logo-link">
                        <img src="{{ asset('Logo_img/house-keeping.png') }}" alt="HK">
                        <p>Dashboard House Keeping</p>
                    </a>
                @endif
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
