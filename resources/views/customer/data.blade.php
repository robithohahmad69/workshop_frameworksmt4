<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Customer</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; min-height: 100vh; }
        .header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 24px 20px; }
        .header-top { display: flex; justify-content: space-between; align-items: center; max-width: 1000px; margin: 0 auto; }
        .header h1 { font-size: 22px; margin-bottom: 4px; }
        .header p { opacity: 0.85; font-size: 13px; }
        .btn-group { display: flex; gap: 8px; }
        .btn { display: inline-block; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: bold; text-decoration: none; border: none; cursor: pointer; }
        .btn-pink { background: white; color: #f5576c; }
        .btn-pink:hover { background: #fff0f0; }
        .container { max-width: 1000px; margin: 28px auto; padding: 0 20px 40px; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; background: #d4edda; color: #155724; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        thead { background: #333; color: white; }
        th, td { padding: 12px 14px; text-align: left; font-size: 13px; border-bottom: 1px solid #f0f0f0; }
        td img { width: 52px; height: 52px; object-fit: cover; border-radius: 6px; border: 1px solid #eee; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge-blob { background: #cfe2ff; color: #084298; }
        .badge-file { background: #d1e7dd; color: #0f5132; }
        .empty { text-align: center; color: #aaa; padding: 40px; }
        .no-foto { color: #ccc; font-size: 12px; }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.65);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal {
            background: white;
            border-radius: 12px;
            padding: 24px;
            width: 92%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }
        .modal-header h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .modal-close {
            background: #f5576c;
            color: white;
            border: none;
            border-radius: 6px;
            width: 32px;
            height: 32px;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-close:hover {
            background: #e6496e;
        }
        .modal-body {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 16px;
        }
        .modal-foto {
            width: 120px;
            height: 120px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #eee;
        }
        .modal-foto-placeholder {
            width: 120px;
            height: 120px;
            background: #f5f5f5;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
            font-size: 12px;
            border: 2px dashed #ddd;
        }
        .modal-info {
            display: grid;
            gap: 8px;
        }
        .modal-info-item {
            display: flex;
            flex-direction: column;
        }
        .modal-label {
            font-size: 11px;
            color: #888;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .modal-value {
            font-size: 14px;
            color: #333;
        }
        .modal-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .modal-badge-blob {
            background: #cfe2ff;
            color: #084298;
        }
        .modal-badge-file {
            background: #d1e7dd;
            color: #0f5132;
        }

        /* Table row hover effect */
        tbody tr {
            cursor: pointer;
            transition: background 0.2s;
        }
        tbody tr:hover {
            background: #fff5f7;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <div>
                <h1>👤 Data Customer</h1>
                <p>Daftar lengkap data customer yang pernah memesan makanan</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('customer.index') }}" class="btn btn-pink">📋 pesan makanan</a>
                <a href="{{ route('customer-data.create-blob') }}" class="btn btn-pink">🖼️ Tambah BLOB</a>
                <a href="{{ route('customer-data.create-file') }}" class="btn btn-pink">📁 Tambah File</a>
            </div>
        </div>
    </div>

    <div class="container">
        @if(session('success'))
            <div class="alert">✅ {{ session('success') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Kota</th>
                    <th>Provinsi</th>
                    <th>Kodepos</th>
                    <th>Tipe Foto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $c)
                <tr onclick="showCustomerModal({{ $c->id }})"
                    data-id="{{ $c->id }}"
                    data-nama="{{ $c->nama }}"
                    data-alamat="{{ $c->alamat ?? '' }}"
                    data-kota="{{ $c->kota ?? '' }}"
                    data-provinsi="{{ $c->provinsi ?? '' }}"
                    data-kecamatan="{{ $c->kecamatan ?? '' }}"
                    data-kelurahan="{{ $c->kodepos_kelurahan ?? '' }}"
                    data-foto-blob="{{ $c->foto_blob ?? '' }}"
                    data-foto-path="{{ $c->foto_path ?? '' }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($c->foto_blob)
                            <img src="data:image/png;base64,{{ $c->foto_blob }}" alt="foto" width="52" height="52">
                        @elseif($c->foto_path)
                            <img src="{{ asset('storage/' . $c->foto_path) }}" alt="foto" width="52" height="52">
                        @else
                            <span class="no-foto">—</span>
                        @endif
                    </td>
                    <td>{{ $c->nama }}</td>
                    <td>{{ $c->alamat ?? '—' }}</td>
                    <td>{{ $c->kota ?? '—' }}</td>
                    <td>{{ $c->provinsi ?? '—' }}</td>
                    <td>{{ $c->kodepos_kelurahan ?? '—' }}</td>
                    <td>
                        @if($c->foto_blob)
                            <span class="badge badge-blob">BLOB</span>
                        @elseif($c->foto_path)
                            <span class="badge badge-file">File</span>
                        @else
                            —
                        @endif
                    </td>
                </tr>
                @empty
                    <tr><td colspan="8" class="empty">😴 Belum ada data customer</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Detail Customer --}}
    <div class="modal-overlay" id="customerModal">
        <div class="modal">
            <div class="modal-header">
                <h3>👤 Detail Customer</h3>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="modal-foto-placeholder" id="modalFoto">
                        <span>No Foto</span>
                    </div>
                </div>
                <div class="modal-info">
                    <div class="modal-info-item">
                        <span class="modal-label">Nama Lengkap</span>
                        <span class="modal-value" id="modalNama">—</span>
                    </div>
                    <div class="modal-info-item">
                        <span class="modal-label">Alamat Lengkap</span>
                        <span class="modal-value" id="modalAlamat">—</span>
                    </div>
                    <div class="modal-info-item">
                        <span class="modal-label">Provinsi</span>
                        <span class="modal-value" id="modalProvinsi">—</span>
                    </div>
                    <div class="modal-info-item">
                        <span class="modal-label">Kota/Kabupaten</span>
                        <span class="modal-value" id="modalKota">—</span>
                    </div>
                    <div class="modal-info-item">
                        <span class="modal-label">Kecamatan</span>
                        <span class="modal-value" id="modalKecamatan">—</span>
                    </div>
                    <div class="modal-info-item">
                        <span class="modal-label">Kelurahan</span>
                        <span class="modal-value" id="modalKelurahan">—</span>
                    </div>
                    <div class="modal-info-item">
                        <span class="modal-label">Tipe Foto</span>
                        <span id="modalTipeFoto">—</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showCustomerModal(customerId) {
            const row = document.querySelector(`tr[data-id="${customerId}"]`);
            if (!row) return;

            // Ambil data dari atribut data
            const nama = row.dataset.nama;
            const alamat = row.dataset.alamat;
            const kota = row.dataset.kota;
            const provinsi = row.dataset.provinsi;
            const kecamatan = row.dataset.kecamatan;
            const kelurahan = row.dataset.kelurahan;
            const fotoBlob = row.dataset.fotoBlob;
            const fotoPath = row.dataset.fotoPath;

            // Isi modal dengan data
            document.getElementById('modalNama').textContent = nama || '—';
            document.getElementById('modalAlamat').textContent = alamat || '—';
            document.getElementById('modalProvinsi').textContent = provinsi || '—';
            document.getElementById('modalKota').textContent = kota || '—';
            document.getElementById('modalKecamatan').textContent = kecamatan || '—';
            document.getElementById('modalKelurahan').textContent = kelurahan || '—';

            // Handle foto
            const fotoContainer = document.getElementById('modalFoto');
            const tipeFotoContainer = document.getElementById('modalTipeFoto');

            if (fotoBlob) {
                fotoContainer.innerHTML = `<img src="data:image/png;base64,${fotoBlob}" class="modal-foto" alt="foto">`;
                tipeFotoContainer.innerHTML = '<span class="modal-badge modal-badge-blob">BLOB</span>';
            } else if (fotoPath) {
                fotoContainer.innerHTML = `<img src="${window.location.origin}/storage/${fotoPath}" class="modal-foto" alt="foto">`;
                tipeFotoContainer.innerHTML = '<span class="modal-badge modal-badge-file">File</span>';
            } else {
                fotoContainer.innerHTML = '<div class="modal-foto-placeholder"><span>No Foto</span></div>';
                tipeFotoContainer.textContent = '—';
            }

            // Tampilkan modal
            document.getElementById('customerModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('customerModal').classList.remove('active');
        }

        // Tutup modal saat klik di luar modal
        document.getElementById('customerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Tutup modal dengan tombol ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>
