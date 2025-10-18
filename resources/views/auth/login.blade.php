<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - RSUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Inter', sans-serif;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="card p-4 p-md-5" style="width: 100%; max-width: 420px;">
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-0">Login Admin</h4>
            <p class="text-muted">Masuk ke sistem KIR RSUD</p>
        </div>

        <!-- Status / Error Message -->
        @if (session('status'))
            <div class="alert alert-success small">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger small">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input 
                    id="email" 
                    type="email" 
                    class="form-control @error('email') is-invalid @enderror" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                    placeholder="Masukkan email Anda">
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi</label>
                <input 
                    id="password" 
                    type="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    name="password" 
                    required
                    placeholder="Masukkan kata sandi">
            </div>

            <!-- Remember Me -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label" for="remember_me">
                    Ingat saya
                </label>
            </div>

            <!-- Tombol Login -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    Masuk
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <a href="/" class="text-decoration-none text-secondary small">← Kembali ke Beranda</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
