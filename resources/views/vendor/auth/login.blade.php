<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Kantin Online</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 100%; max-width: 400px; }
        .login-box h2 { text-align: center; margin-bottom: 8px; color: #333; }
        .login-box p { text-align: center; color: #888; margin-bottom: 28px; font-size: 14px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .form-group input:focus { outline: none; border-color: #f5576c; }
        .error-box { background: #fff5f5; border: 1px solid #f5c6cb; border-radius: 5px; padding: 10px 14px; margin-bottom: 18px; color: #dc3545; font-size: 13px; }
        .btn { width: 100%; padding: 12px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none; border-radius: 5px; font-size: 15px; cursor: pointer; font-weight: bold; }
        .btn:hover { opacity: 0.9; }
        .btn-guest { width: 100%; padding: 12px; background: white; color: #f5576c; border: 2px solid #f5576c; border-radius: 5px; font-size: 15px; cursor: pointer; font-weight: bold; text-align: center; text-decoration: none; display: block; margin-top: 10px; }
        .btn-guest:hover { background: #fff5f5; }
        .divider { text-align: center; margin: 18px 0 10px; color: #aaa; font-size: 13px; position: relative; }
        .divider::before, .divider::after { content: ''; position: absolute; top: 50%; width: 38%; height: 1px; background: #eee; }
        .divider::before { left: 0; }
        .divider::after { right: 0; }
        .links { text-align: center; margin-top: 18px; font-size: 14px; color: #888; }
        .links a { color: #f5576c; text-decoration: none; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🍽️ Kantin Online</h2>
        <p>Masuk sebagai vendor untuk mengelola kantin</p>

        @if ($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('vendor.login.post') }}">
            @csrf
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">🏪 MASUK SEBAGAI VENDOR</button>
        </form>

        <div class="divider">atau</div>
        <a href="{{ route('login') }}" class="btn-guest">
            🛠️ Login sebagai admin
        </a>

        {{-- Tombol langsung ke halaman order customer --}}
        <a href="{{ route('customer.index') }}" class="btn-guest">
            🛒 Pesan Makanan 
        </a>
         <a href="{{ route('customer-data.index') }}" class="btn-guest">
            👤 Data Customer
        </a>


        <div class="links">
            Belum punya akun vendor? <a href="{{ route('vendor.register') }}">Daftar disini</a>
        </div>
    </div>
</body>
</html>