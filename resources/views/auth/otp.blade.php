<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 { margin-bottom: 8px; color: #1a1a1a; }
        p { color: #666; margin-bottom: 24px; font-size: 14px; }

        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .otp-inputs input {
            width: 48px;
            height: 56px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #ddd;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.2s;
        }

        .otp-inputs input:focus { border-color: #4285f4; }

        button {
            width: 100%;
            padding: 12px;
            background: #4285f4;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover { background: #3367d6; }

        .error {
            color: red;
            font-size: 13px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Verifikasi OTP</h2>
        <p>Masukkan 6 digit kode OTP yang dikirim ke email kamu</p>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('otp.verify') }}" method="POST">
            @csrf
            <div class="otp-inputs">
                <input type="text" maxlength="1" name="otp_1" autofocus>
                <input type="text" maxlength="1" name="otp_2">
                <input type="text" maxlength="1" name="otp_3">
                <input type="text" maxlength="1" name="otp_4">
                <input type="text" maxlength="1" name="otp_5">
                <input type="text" maxlength="1" name="otp_6">
            </div>
            <input type="hidden" name="otp" id="otp_hidden">
            <button type="submit">Verifikasi</button>
        </form>
    </div>

    <script>
        // Auto pindah ke input berikutnya
        const inputs = document.querySelectorAll('.otp-inputs input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                // Gabungkan semua input ke hidden field
                document.getElementById('otp_hidden').value = 
                    Array.from(inputs).map(i => i.value).join('');
            });

            // Backspace kembali ke input sebelumnya
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>
</body>
</html>