<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Password</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { margin-bottom: 8px; color: #1a1a1a; }
        p { color: #666; margin-bottom: 24px; font-size: 14px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; color: #555; font-size: 14px; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; }
        input:focus { border-color: #4285f4; }
        .error { color: red; font-size: 12px; margin-top: 4px; }
        button { width: 100%; padding: 12px; background: #4285f4; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; margin-top: 8px; }
        button:hover { background: #3367d6; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Set Password</h2>
        <p>Halo <strong>{{ auth()->user()->name }}</strong>! Karena kamu login via Google, silakan set password untuk bisa login biasa.</p>

        <form method="POST" action="{{ route('set.password.store') }}">
            @csrf
            <div class="form-group">
                <label>Password Baru</label>
                <input type="password" name="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <button type="submit">Simpan Password</button>
        </form>
    </div>
</body>
</html>