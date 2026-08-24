<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System - Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card login-card p-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-primary">POS System</h3>
                        <p class="text-muted">Sign in to start your session</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label text-secondary">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label text-secondary">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Enter password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label text-secondary" for="remember">Remember me</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Sign In</button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0 text-muted">Don't have an account? <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-semibold">Register here</a></p>
                    </div>

                    <div class="mt-4 pt-3 border-top text-center text-muted">
                        <small>
                            <strong>Demo Credentials:</strong><br>
                            Admin: <code>admin@pos.com</code> / <code>admin123</code><br>
                            Cashier: <code>cashier@pos.com</code> / <code>cashier123</code>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
