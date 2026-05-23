<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pendaftaran Antrian</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }

        .container { max-width: 420px; width: 100%; }

        .card { background: white; border-radius: 20px; padding: 40px 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); text-align: center; }

        .icon-wrapper { width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
        .icon-wrapper i { font-size: 40px; color: white; }

        h1 { color: #333; font-size: 26px; margin-bottom: 8px; font-weight: 700; }
        p.subtitle { color: #888; font-size: 14px; margin-bottom: 32px; }

        .form-group { text-align: left; margin-bottom: 24px; }
        label { display: block; color: #555; font-size: 14px; font-weight: 600; margin-bottom: 8px; }
        .input-wrapper { position: relative; }
        .input-wrapper i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #999; font-size: 20px; }
        input[type="text"] { width: 100%; padding: 14px 16px 14px 48px; border: 2px solid #e0e0e0; border-radius: 12px; font-size: 15px; transition: border-color 0.3s; outline: none; }
        input[type="text"]:focus { border-color: #667eea; }

        .btn-submit { width: 100%; padding: 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
        .btn-submit:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4); }
        .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; }

        .btn-submit .spinner { display: none; width: 20px; height: 20px; border: 2px solid #fff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto; }
        .btn-submit.loading .btn-text { display: none; }
        .btn-submit.loading .spinner { display: block; }
        .btn-submit.loading span { display: none; }

        @keyframes spin { to { transform: rotate(360deg); } }

        .alert { padding: 14px 18px; border-radius: 10px; font-size: 14px; margin-top: 20px; display: none; }
        .alert.show { display: block; }
        .alert-error { background: #fee; color: #c33; border-left: 4px solid #c33; }
        .alert i { margin-right: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon-wrapper">
                <i class="mdi mdi-ticket-account"></i>
            </div>
            <h1>Ambil Antrian</h1>
            <p class="subtitle">Silakan isi nama Anda untuk mendapatkan nomor antrian</p>

            {{-- FIXED: hapus method, action, dan @csrf dari form --}}
            <form id="daftarForm">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <div class="input-wrapper">
                        <i class="mdi mdi-account"></i>
                        <input type="text" id="nama" name="nama" placeholder="Masukkan nama Anda" required autofocus>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span class="btn-text">Ambil Nomor Antrian</span>
                    <i class="mdi mdi-ticket mdi-24px" style="vertical-align: middle; margin-left: 8px;"></i>
                    <div class="spinner"></div>
                </button>

                <div class="alert alert-error" id="alertError">
                    <i class="mdi mdi-alert-circle"></i>
                    <span id="errorMessage"></span>
                </div>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('daftarForm');
        const submitBtn = document.getElementById('submitBtn');
        const alertError = document.getElementById('alertError');
        const errorMessage = document.getElementById('errorMessage');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const nama = document.getElementById('nama').value.trim();

            if (!nama) {
                showError('Nama wajib diisi');
                return;
            }

            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            hideError();

            try {
                const response = await fetch('{{ route('antrian.daftar') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ nama: nama })
                });

                const data = await response.json();

                if (data.success && data.tiket_url) {
                    window.open(data.tiket_url, '_blank');
                    form.reset();
                } else {
                    showError(data.message || 'Gagal mendaftar. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            }
        });

        function showError(message) {
            errorMessage.textContent = message;
            alertError.classList.add('show');
        }

        function hideError() {
            alertError.classList.remove('show');
        }
    </script>
</body>
</html>