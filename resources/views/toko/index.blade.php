@extends('layouts.apps')

@section('title', 'Data Toko')

@section('page-title', 'Data Toko')
@section('icon', 'mdi mdi-store')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">Dashboard</a></li>
<li class="breadcrumb-item active">Data Toko</li>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">🏪 Data Toko</h2>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahToko">
        ➕ Tambah Toko
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    ✅ {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Tabel List Toko --}}
<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Barcode</th>
                    <th>Nama Toko</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Accuracy (m)</th>
                    <th style="width:200px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if($tokos->count() > 0)
                    @foreach($tokos as $toko)
                    <tr>
                        <td>{{ $toko->id }}</td>
                        <td>
                            <code>{{ $toko->barcode }}</code>
                        </td>
                        <td>{{ $toko->nama_toko }}</td>
                        <td>{{ $toko->latitude }}</td>
                        <td>{{ $toko->longitude }}</td>
                        <td>{{ number_format($toko->accuracy, 2) }}</td>
                        <td>
                            <button class="btn btn-gradient-info btn-sm"
                                    onclick="tampilkanBarcode('{{ $toko->barcode }}', '{{ $toko->nama_toko }}')">
                                <i class="mdi mdi-qrcode"></i> Lihat Barcode
                            </button>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="mdi mdi-information-outline"></i> Belum ada data toko
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

{{-- ===================== MODAL TAMBAH TOKO ===================== --}}
<div class="modal fade" id="modalTambahToko" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">➕ Tambah Toko Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="formTambahToko">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">🏷️ Barcode Toko</label>
                        <div class="input-group">
                            <input type="text" name="barcode" id="barcode" class="form-control"
                                   placeholder="Generating..." readonly required>
                            <button type="button" class="btn btn-outline-primary"
                                    onclick="generateNewBarcode()">
                                <i class="mdi mdi-refresh"></i> Generate Baru
                            </button>
                        </div>
                        <div class="form-text">
                            <i class="mdi mdi-information"></i>
                            Barcode auto-generate (TOKO001, TOKO002, dst)
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">🏪 Nama Toko</label>
                        <input type="text" name="nama_toko" id="nama_toko" class="form-control"
                               placeholder="Contoh: Toko Kelontong Madura" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">📍 Lokasi Toko</label>
                        <div class="input-group mb-2">
                            <button type="button" class="btn btn-primary" onclick="ambilLokasiToko()">
                                <i class="mdi mdi-crosshairs-gps"></i> Ambil Lokasi
                            </button>
                            <input type="text" class="form-control" id="statusLokasi" readonly
                                   placeholder="Klik tombol untuk mengambil lokasi">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Latitude</label>
                                <input type="text" name="latitude" id="latitude" class="form-control"
                                       step="any" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Longitude</label>
                                <input type="text" name="longitude" id="longitude" class="form-control"
                                       step="any" required>
                            </div>
                        </div>

                        <div class="mt-2">
                            <label class="form-label text-muted small">Accuracy (meter)</label>
                            <input type="text" name="accuracy" id="accuracy" class="form-control"
                                   step="any" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-content-save"></i> Simpan Toko
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== MODAL TAMPILKAN BARCODE ===================== --}}
<div class="modal fade" id="modalBarcode" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">🏷️ Barcode Toko</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <h4 id="displayNamaToko" class="mb-3">-</h4>

                <div class="card bg-light">
                    <div class="card-body">
                        <div id="qrcode" class="mb-3"></div>
                        <h3><code id="displayBarcode">-</code></h3>
                    </div>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <i class="mdi mdi-information"></i>
                    Scan barcode ini saat melakukan kunjungan toko
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- QRCode.js Library --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    // ============================================
    // KONFIGURASI
    // ============================================
    const API_STORE = '{{ route('toko.store') }}';
    const API_GENERATE_BARCODE = '{{ route('api.toko.generateBarcode') }}';

    // ============================================
    // GENERATE BARCODE
    // ============================================
    async function generateNewBarcode() {
        const barcodeEl = document.getElementById('barcode');

        try {
            barcodeEl.value = 'Generating...';

            const response = await fetch(API_GENERATE_BARCODE, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const result = await response.json();

            if (result.success) {
                barcodeEl.value = result.barcode;
            } else {
                barcodeEl.value = '';
                alert('❌ Gagal generate barcode');
            }

        } catch (error) {
            console.error('Error:', error);
            barcodeEl.value = '';
            alert('❌ Gagal generate barcode');
        }
    }

    // Auto-generate barcode saat modal dibuka
    document.getElementById('modalTambahToko').addEventListener('show.bs.modal', function() {
        generateNewBarcode();
    });

    // ============================================
    // FUNGSI GPS (GET ACCURATE POSITION)
    // ============================================
    function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
        return new Promise((resolve, reject) => {
            let bestResult = null;
            const startTime = Date.now();
            const watchId = navigator.geolocation.watchPosition(
                (position) => {
                    const acc = position.coords.accuracy;
                    if (!bestResult || acc < bestResult.coords.accuracy) {
                        bestResult = position;
                    }
                    if (acc <= targetAccuracy) {
                        navigator.geolocation.clearWatch(watchId);
                        resolve(bestResult);
                    }
                    if (Date.now() - startTime >= maxWait) {
                        navigator.geolocation.clearWatch(watchId);
                        if (bestResult) resolve(bestResult);
                        else reject(new Error("Timeout, tidak dapat posisi"));
                    }
                },
                (error) => reject(error),
                { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
            );
        });
    }

    // ============================================
    // AMBIL LOKASI TOKO
    // ============================================
    async function ambilLokasiToko() {
        const statusEl = document.getElementById('statusLokasi');
        const latEl = document.getElementById('latitude');
        const lngEl = document.getElementById('longitude');
        const accEl = document.getElementById('accuracy');

        try {
            statusEl.value = '🔄 Mengambil lokasi...';

            const position = await getAccuratePosition(50, 20000);

            latEl.value = position.coords.latitude;
            lngEl.value = position.coords.longitude;
            accEl.value = position.coords.accuracy;

            statusEl.value = `✅ Lokasi terkirim! Accuracy: ${position.coords.accuracy.toFixed(2)}m`;
            statusEl.classList.add('text-success');

        } catch (error) {
            console.error('Error:', error);
            statusEl.value = '❌ Gagal mengambil lokasi';
            statusEl.classList.add('text-danger');

            let pesan = 'Gagal mengambil lokasi GPS';
            if (error.message === 'Timeout, tidak dapat posisi') {
                pesan = 'Timeout - pastikan GPS aktif dan berada di lokasi terbuka';
            } else if (error.code === 1) {
                pesan = 'Izin lokasi ditolak. Mohon izinkan akses lokasi.';
            } else if (error.code === 2) {
                pesan = 'Lokasi tidak tersedia. Pastikan GPS aktif.';
            } else if (error.code === 3) {
                pesan = 'Timeout mengambil lokasi. Coba lagi.';
            }

            alert('⚠️ ' + pesan);
        }
    }

    // ============================================
    // FORM SUBMIT (AJAX)
    // ============================================
    document.getElementById('formTambahToko').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(API_STORE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                alert('✅ ' + result.message);
                window.location.href = '{{ route('toko.index') }}';
            } else {
                alert('❌ ' + result.message);
            }

        } catch (error) {
            console.error('Error:', error);
            alert('❌ Gagal menyimpan toko. Silakan coba lagi.');
        }
    });

    // ============================================
    // TAMPILKAN BARCODE (QR CODE)
    // ============================================
    function tampilkanBarcode(barcode, namaToko) {
        document.getElementById('displayBarcode').textContent = barcode;
        document.getElementById('displayNamaToko').textContent = namaToko;

        // Clear previous QR code
        const qrcodeContainer = document.getElementById('qrcode');
        qrcodeContainer.innerHTML = '';

        // Generate new QR code
        new QRCode(qrcodeContainer, {
            text: barcode,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('modalBarcode'));
        modal.show();
    }
</script>
@endpush
