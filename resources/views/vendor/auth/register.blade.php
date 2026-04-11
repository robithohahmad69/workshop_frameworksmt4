
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Vendor - Kantin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 420px; }
        .login-box h2 { text-align: center; margin-bottom: 8px; color: #333; }
        .login-box p { text-align: center; color: #888; margin-bottom: 28px; font-size: 14px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .form-group input:focus { outline: none; border-color: #f5576c; }
        .error { color: #dc3545; font-size: 12px; margin-top: 4px; }
        .error-box { background: #fff5f5; border: 1px solid #f5c6cb; border-radius: 5px; padding: 10px 14px; margin-bottom: 18px; color: #dc3545; font-size: 13px; }
        .btn { width: 100%; padding: 12px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; font-weight: bold; }
        .btn:hover { opacity: 0.9; }
        .links { text-align: center; margin-top: 20px; font-size: 14px; color: #888; }
        .links a { color: #f5576c; text-decoration: none; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🍽️ Daftar Vendor</h2>
        <p>Buat akun untuk mengelola kantin Anda</p>

        @if ($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('vendor.register.post') }}">
            @csrf

            <div class="form-group">
                <label>Nama Kantin</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="contoh: Kantin Bu Sari" required autofocus>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="email@kantin.com" required>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Minimal 6 karakter" required>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi password" required>
            </div>

            <button type="submit" class="btn">DAFTAR SEKARANG</button>
        </form>

        <div class="links">
            Sudah punya akun? <a href="{{ route('vendor.login') }}">Masuk disini</a>
        </div>
    </div>
</body>
</html>