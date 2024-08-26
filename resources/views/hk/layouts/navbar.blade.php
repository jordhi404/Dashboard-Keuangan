<nav class="navbar navbar-expand-lg navbar-light bg-success bg-gradient">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="{{ route('hk') }}"> <img
                src="{{ asset('Logo_img/house_keeping_white.png') }}" alt="HK"
                style="height: 50px; margin-right: 10px;">
            <h3 class="d-inline align-middle">House Keeping</h3>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ auth()->user()->name }}
                            <br>
                            <span class="text-white" id="current-datetime"></span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            {{-- <li><a class="dropdown-item" href="{{ route('change-password') }}">
                                    <i class="bi bi-shield-lock"></i>
                                    Ubah Password</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li> --}}
                            <li>
                                <form action="{{ route('logout') }}" method="post">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i
                                            class="bi bi-box-arrow-right"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        {{-- <a href="{{ route('login') }}"
                            class="nav-link text-white {{ request()->routeIs('login') ? 'active' : '' }}"><i
                                class="bi bi-box-arrow-in-right p-1"></i>Login <br>
                            <span class="text-white" id="current-datetime"></span>
                        </a> --}}
                        <span class="text-white" id="current-datetime"></span>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
