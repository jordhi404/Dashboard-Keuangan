<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="CSS\login-style.css">
    <link rel="icon" href="{{ asset('Logo_img/logo_rs.jpg') }}" type="image/x-icon">
</head>
<body>
    <div class="container login-container">
        <div class="login-card">
            <div class="login-logo">
                <img src="{{ asset('Logo_img/logo_rs.jpg') }}" alt="Logo rs">
            </div>
            <div class="login-form">
                <h3 class="text-center">Login</h3>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="nik">NIK</label>
                        <input type="text" class="form-control" id="nik" name="nik" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div> 
                    <button type="submit" class="btn btn-success">Login</button>
                </form>
                @if ($errors->has('error'))
                    <div class="alert alert-danger mt-3">
                        {{ $errors->first('error') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>