<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(145deg, #2c3e50 0%, #3498db 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            margin: 0;
        }
        .reset-password-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            max-width: 400px;
            width: 100%;
            padding: 1.5rem;
            transition: transform 0.3s ease;
        }
        .reset-password-card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            text-align: center;
            padding-bottom: 1rem;
        }
        .card-header h3 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
        }
        .form-control {
            border-radius: 10px;
            padding: 0.7rem;
            border: 1px solid #d1d9e6;
            background: #f8fafc;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.2);
            background: #ffffff;
        }
        .form-control.is-invalid {
            border-color: #dc3545;
            background: #fff5f5;
        }
        .btn-primary {
            background: #3498db;
            border: none;
            border-radius: 10px;
            padding: 0.7rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        .form-label {
            font-weight: 500;
            color: #2c3e50;
            font-size: 0.8rem;
        }
        .input-group {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #7f8c8d;
            z-index: 10;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        .password-toggle:hover {
            color: #3498db;
        }
        .form-control:focus + .password-toggle {
            color: #3498db;
        }
        .link {
            text-align: center;
            margin-top: 1rem;
        }
        .link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.8rem;
            transition: color 0.3s ease;
        }
        .link a:hover {
            color: #2980b9;
            text-decoration: underline;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .alert {
            font-size: 0.8rem;
            margin-bottom: 1rem;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="reset-password-card mx-auto">
            <div class="card-header">
                <h3>Reset Password</h3>
            </div>
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group">
                    <label for="resetEmail" class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="resetEmail" placeholder="Enter your email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="resetPassword" class="form-label">New Password</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="resetPassword" placeholder="Enter new password" required>
                        <i class="fas fa-eye password-toggle" onclick="togglePassword('resetPassword')"></i>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="resetPasswordConfirm" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" id="resetPasswordConfirm" placeholder="Confirm new password" required>
                        <i class="fas fa-eye password-toggle" onclick="togglePassword('resetPasswordConfirm')"></i>
                    </div>
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                <div class="link">
                    <p>Remembered your password? <a href="{{ route('login') }}">Sign in here</a></p>
                </div>
            </form>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>