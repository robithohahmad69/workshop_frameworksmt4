<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Customer 1</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; min-height: 100vh; }
        .header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; }
        .header h1 { font-size: 20px; max-width: 600px; margin: 0 auto; }
        .container { max-width: 600px; margin: 28px auto; padding: 0 20px 40px; }
        .btn-back { display: inline-block; margin-bottom: 16px; color: #f5576c; text-decoration: none; font-size: 13px; font-weight: bold; }
        .card { background: white; border-radius: 12px; padding: 28px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: 13px; font-weight: bold; color: #555; margin-bottom: 6px; }
        input[type=text], select { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; transition: border 0.2s; }
        input[type=text]:focus, select:focus { border-color: #f5576c; }
        .foto-preview-box { width: 130px; height: 130px; border: 2px dashed #ddd; border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden; color: #bbb; font-size: 12px; margin-bottom: 12px; background: #fafafa; }
        .foto-preview-box img { width: 100%; height: 100%; object-fit: cover; }
        .btn-row { display: flex; gap: 10px; align-items: center; }
        .btn { padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: bold; border: none; cursor: pointer; }
        .btn-camera { background: #0d6efd; color: white; }
        .btn-save { background: linear-gradient(135deg, #f093fb, #f5576c); color: white; }
        .loading { display: none; color: #999; font-size: 12px; margin-left: 10px; }
        select:disabled { background-color: #f5f5f5; cursor: not-allowed; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📷 Tambah Customer 1 — Simpan Foto sebagai BLOB</h1>
    </div>

    <div class="container">
        <a href="{{ route('customer-data.index') }}" class="btn-back">← Kembali ke Data Customer</a>

        <div class="card">
            <form method="POST" action="{{ route('customer-data.store-blob') }}">
                @csrf
                <input type="hidden" name="foto" id="fotoBase64">

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" placeholder="Nama lengkap" required>
                </div>

                {{-- Dropdown Wilayah --}}
                <div class="form-group">
                    <label>Provinsi</label>
                    <select name="provinsi" id="provinsi" required>
                        <option value="">-- Pilih Provinsi --</option>
                    </select>
                    <span class="loading" id="loadingProvinsi">Memuat...</span>
                </div>

                <div class="form-group">
                    <label>Kota/Kabupaten</label>
                    <select name="kota" id="kota" disabled required>
                        <option value="">-- Pilih Kota --</option>
                    </select>
                    <span class="loading" id="loadingKota">Memuat...</span>
                </div>

                <div class="form-group">
                    <label>Kecamatan</label>
                    <select name="kecamatan" id="kecamatan" disabled required>
                        <option value="">-- Pilih Kecamatan --</option>
                    </select>
                    <span class="loading" id="loadingKecamatan">Memuat...</span>
                </div>

                <div class="form-group">
                    <label>Kelurahan</label>
                    <select name="kodepos_kelurahan" id="kelurahan" disabled required>
                        <option value="">-- Pilih Kelurahan --</option>
                    </select>
                    <span class="loading" id="loadingKelurahan">Memuat...</span>
                </div>

                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <input type="text" name="alamat" placeholder="Jl. Contoh No. 1">
                </div>

                <div class="form-group">
                    <label>Foto Customer</label>
                    <div class="foto-preview-box" id="previewBox">
                        <span>Belum ada foto</span>
                    </div>
                    <div class="btn-row">
                        <button type="button" class="btn btn-camera" onclick="bukaModal()">📷 Ambil Foto</button>
                        <button type="submit" class="btn btn-save">💾 Simpan Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Kamera --}}
    <div class="modal-overlay" id="modalKamera" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65); z-index: 999; align-items: center; justify-content: center;">
        <div class="modal" style="background: white; border-radius: 12px; padding: 20px; width: 92%; max-width: 660px;">
            <h3>📷 Modal Ambil Foto</h3>
            <div class="camera-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                <div>
                    <div style="font-size: 11px; color: #aaa; margin-bottom: 4px;">Video</div>
                    <div style="border: 2px solid #ddd; border-radius: 8px; overflow: hidden; aspect-ratio: 4/3; background: #111;">
                        <video id="video" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                    </div>
                </div>
                <div>
                    <div style="font-size: 11px; color: #aaa; margin-bottom: 4px;">Snapshot</div>
                    <div style="border: 2px solid #ddd; border-radius: 8px; overflow: hidden; aspect-ratio: 4/3; background: #111;">
                        <canvas id="canvas" style="width: 100%; height: 100%; object-fit: cover;"></canvas>
                    </div>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid #eee;">
                <div style="display: flex; gap: 8px;">
                    <select id="selectKamera" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #ddd; font-size: 13px;"></select>
                    <button onclick="ambilFoto()" style="padding: 8px 14px; background: #198754; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">📸 Ambil Foto</button>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button onclick="tutupModal()" style="padding: 8px 14px; background: #6c757d; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Batal</button>
                    <button onclick="gunakanFoto()" style="padding: 8px 14px; background: linear-gradient(135deg, #f093fb, #f5576c); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">✅ Simpan Foto</button>
                </div>
            </div>
        </div>
    </div>

<script>
    let stream = null;
    let devices = [];

    // ==========================================
    // API WILAYAH
    // ==========================================
    async function loadProvinsi() {
        const loading = document.getElementById('loadingProvinsi');
        const select = document.getElementById('provinsi');

        loading.style.display = 'inline';
        try {
            const response = await fetch('/api/provinsi');
            const data = await response.json();

            select.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
            data.forEach(prov => {
                const option = document.createElement('option');
                option.value = prov.name;
                option.textContent = prov.name;
                option.dataset.id = prov.id;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading provinsi:', error);
            alert('Gagal memuat data provinsi');
        } finally {
            loading.style.display = 'none';
        }
    }

    async function loadKota(provinsiName) {
        const loading = document.getElementById('loadingKota');
        const select = document.getElementById('kota');
        const provSelect = document.getElementById('provinsi');

        if (!provinsiName) {
            select.disabled = true;
            select.innerHTML = '<option value="">-- Pilih Kota --</option>';
            return;
        }

        loading.style.display = 'inline';
        select.disabled = true;

        try {
            const provOption = Array.from(provSelect.options).find(opt => opt.value === provinsiName);
            const provId = provOption?.dataset.id;

            if (provId) {
                const response = await fetch(`/api/kota/${provId}`);
                const data = await response.json();

                select.innerHTML = '<option value="">-- Pilih Kota --</option>';
                data.forEach(kota => {
                    const option = document.createElement('option');
                    option.value = kota.name;
                    option.textContent = kota.name;
                    option.dataset.id = kota.id;
                    select.appendChild(option);
                });
                select.disabled = false;
            }
        } catch (error) {
            console.error('Error loading kota:', error);
            alert('Gagal memuat data kota');
        } finally {
            loading.style.display = 'none';
        }
    }

    async function loadKecamatan(kotaName) {
        const loading = document.getElementById('loadingKecamatan');
        const select = document.getElementById('kecamatan');
        const kotaSelect = document.getElementById('kota');

        if (!kotaName) {
            select.disabled = true;
            select.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
            return;
        }

        loading.style.display = 'inline';
        select.disabled = true;

        try {
            const kotaOption = Array.from(kotaSelect.options).find(opt => opt.value === kotaName);
            const kotaId = kotaOption?.dataset.id;

            if (kotaId) {
                const response = await fetch(`/api/kecamatan/${kotaId}`);
                const data = await response.json();

                select.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                data.forEach(kec => {
                    const option = document.createElement('option');
                    option.value = kec.name;
                    option.textContent = kec.name;
                    option.dataset.id = kec.id;
                    select.appendChild(option);
                });
                select.disabled = false;
            }
        } catch (error) {
            console.error('Error loading kecamatan:', error);
            alert('Gagal memuat data kecamatan');
        } finally {
            loading.style.display = 'none';
        }
    }

    async function loadKelurahan(kecamatanName) {
        const loading = document.getElementById('loadingKelurahan');
        const select = document.getElementById('kelurahan');
        const kecSelect = document.getElementById('kecamatan');

        if (!kecamatanName) {
            select.disabled = true;
            select.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
            return;
        }

        loading.style.display = 'inline';
        select.disabled = true;

        try {
            const kecOption = Array.from(kecSelect.options).find(opt => opt.value === kecamatanName);
            const kecId = kecOption?.dataset.id;

            if (kecId) {
                const response = await fetch(`/api/kelurahan/${kecId}`);
                const data = await response.json();

                select.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
                data.forEach(kel => {
                    const option = document.createElement('option');
                    option.value = kel.name;
                    option.textContent = `${kel.name} (${kel.zip})`;
                    option.dataset.zip = kel.zip;
                    select.appendChild(option);
                });
                select.disabled = false;
            }
        } catch (error) {
            console.error('Error loading kelurahan:', error);
            alert('Gagal memuat data kelurahan');
        } finally {
            loading.style.display = 'none';
        }
    }

    // Event listeners untuk dropdown cascade
    document.getElementById('provinsi').addEventListener('change', (e) => {
        loadKota(e.target.value);
        document.getElementById('kota').value = '';
        document.getElementById('kecamatan').value = '';
        document.getElementById('kelurahan').value = '';
    });

    document.getElementById('kota').addEventListener('change', (e) => {
        loadKecamatan(e.target.value);
        document.getElementById('kecamatan').value = '';
        document.getElementById('kelurahan').value = '';
    });

    document.getElementById('kecamatan').addEventListener('change', (e) => {
        loadKelurahan(e.target.value);
        document.getElementById('kelurahan').value = '';
    });

    // Load provinsi saat page load
    loadProvinsi();

    // ==========================================
    // FUNGSI KAMERA
    // ==========================================
    async function bukaModal() {
        document.getElementById('modalKamera').style.display = 'flex';
        await muatKamera();
    }

    function tutupModal() {
        document.getElementById('modalKamera').style.display = 'none';
        if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
    }

    async function muatKamera(deviceId = null) {
        if (stream) { stream.getTracks().forEach(t => t.stop()); }

        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: deviceId ? { deviceId: { exact: deviceId } } : true
            });
            document.getElementById('video').srcObject = stream;

            if (devices.length === 0) {
                devices = (await navigator.mediaDevices.enumerateDevices())
                            .filter(d => d.kind === 'videoinput');

                const select = document.getElementById('selectKamera');
                select.innerHTML = '';
                devices.forEach((d, i) => {
                    const opt = document.createElement('option');
                    opt.value = d.deviceId;
                    opt.text = d.label || 'Kamera ' + (i + 1);
                    select.appendChild(opt);
                });

                select.addEventListener('change', () => muatKamera(select.value));
            }
        } catch (err) {
            alert('Tidak bisa mengakses kamera: ' + err.message);
        }
    }

    function ambilFoto() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
    }

    function gunakanFoto() {
        const canvas = document.getElementById('canvas');
        const base64 = canvas.toDataURL('image/png');

        if (base64 === 'data:,') {
            alert('Klik "Ambil Foto" dulu sebelum menyimpan!');
            return;
        }

        document.getElementById('fotoBase64').value = base64;
        document.getElementById('previewBox').innerHTML = `<img src="${base64}" alt="preview">`;
        tutupModal();
    }
</script>

</body>
</html>
