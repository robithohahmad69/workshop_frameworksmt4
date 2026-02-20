<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Perpustakaan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .register-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 400px; }
        .register-box h2 { text-align: center; margin-bottom: 30px; color: #333; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .form-group input:focus { outline: none; border-color: #667eea; }
        .error { color: #dc3545; font-size: 12px; margin-top: 5px; }
        .btn { width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; font-weight: bold; }
        .btn:hover { opacity: 0.9; }
        .links { text-align: center; margin-top: 20px; }
        .links a { color: #667eea; text-decoration: none; }
        .links a:hover { text-decoration: underline; }
        .divider { text-align: center; margin: 16px 0; color: #aaa; font-size: 13px; position: relative; }
        .divider::before, .divider::after { content: ''; position: absolute; top: 50%; width: 42%; height: 1px; background: #ddd; }
        .divider::before { left: 0; }
        .divider::after { right: 0; }
        .btn-google { width: 100%; padding: 12px; background: white; color: #333; border: 1px solid #ddd; border-radius: 5px; font-size: 15px; cursor: pointer; font-weight: bold; display: flex; align-items: center; justify-content: center; gap: 10px; text-decoration: none; }
        .btn-google:hover { background: #f5f5f5; }
        .btn-google img { width: 20px; height: 20px; }
        .alert { padding: 12px 16px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; }
        .alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #dc3545; }
        .alert-warning { background: #fffbeb; border: 1px solid #fde68a; color: #d97706; }
    </style>
</head>
<body>
    <div class="register-box">
        <h2>📚 Daftar Perpustakaan</h2>

        {{-- Notif email sudah terdaftar --}}
        @if(session('email_exists'))
            <div class="alert alert-warning">
                ⚠️ Email ini sudah terdaftar. Silakan <a href="{{ route('login') }}">login disini</a>.
            </div>
        @endif

        {{-- Error umum --}}
        @if($errors->has('email') && str_contains($errors->first('email'), 'taken'))
            <div class="alert alert-danger">
                Email sudah digunakan. Silakan gunakan email lain atau login.
            </div>
        @endif
        
        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required>
            </div>
            
            <button type="submit" class="btn">DAFTAR</button>
        </form>

        <div class="divider">atau</div>
        <a href="{{ route('google.redirect') }}" class="btn-google">
            <img src="https://www.google.com/favicon.ico" alt="Google">
            Daftar dengan Google
        </a>

        <div class="links">
            Sudah punya akun? <a href="{{ route('login') }}">Login disini</a>
        </div>
    </div>
</body>
</html>