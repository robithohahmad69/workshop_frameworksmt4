@extends('layouts.apps')

@section('title', 'Kunjungan Toko')

@section('page-title', 'Kunjungan Toko')
@section('icon', 'mdi mdi-map-marker-multiple')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">Dashboard</a></li>
<li class="breadcrumb-item active">Kunjungan Toko</li>
@endsection

@push('styles')
<style>
    #reader {
        position: relative;
    }

    #reader video {
        object-fit: cover;
        border-radius: 8px;
    }

    @media (max-width: 768px) {
        #reader {
            height: 350px !important;
            max-width: 100% !important;
        }
    }

    @media (max-width: 576px) {
        #reader {
            height: 300px !important;
        }
    }
</style>
@endpush

@section('content')

<div class="row">
    {{-- KOLOM KIRI: SCANNER & LOKASI --}}
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📷 1. Scan Barcode Toko</h5>
            </div>
            <div class="card-body text-center">
                <div id="reader" style="border-radius: 8px; overflow: hidden; background: #000; width: 100%; max-width: 500px; height: 400px; margin: 0 auto;"></div>

                <div class="mt-3">
                    <button class="btn btn-success" onclick="mulaiScanUlang()">
                        🔄 Scan Ulang
                    </button>
                </div>

                <div id="scannerLoading" class="text-center d-none mt-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Mencari toko di database...</p>
                </div>

                <div id="scannerError" class="alert alert-danger d-none mt-3"></div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">📍 2. Ambil Lokasi Saya</h5>
            </div>
            <div class="card-body">
                <button class="btn btn-primary w-100 mb-3" onclick="ambilLokasiSales()" id="btnAmbilLokasi">
                    <i class="mdi mdi-crosshairs-gps"></i> Ambil Lokasi Saya
                </button>

                <div id="lokasiSalesInfo" class="alert alert-info d-none">
                    <strong>✅ Lokasi Terkirim!</strong>
                    <table class="table table-sm mb-0 mt-2">
                        <tr>
                            <td>Latitude:</td>
                            <td><span id="displayLatSales">-</span></td>
                        </tr>
                        <tr>
                            <td>Longitude:</td>
                            <td><span id="displayLngSales">-</span></td>
                        </tr>
                        <tr>
                            <td>Accuracy:</td>
                            <td><span id="displayAccSales">-</span> meter</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: INFO TOKO & HASIL --}}
    <div class="col-lg-6">
        {{-- Info Toko --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">🏪 Informasi Toko</h5>
            </div>
            <div class="card-body">
                <div id="infoTokoPlaceholder" class="text-center text-muted py-4">
                    <i class="mdi mdi-qrcode-scan" style="font-size: 48px;"></i>
                    <p class="mt-2">Scan barcode toko terlebih dahulu</p>
                </div>

                <div id="infoToko" class="d-none">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Nama Toko:</th>
                            <td><span id="displayNamaToko">-</span></td>
                        </tr>
                        <tr>
                            <th>Barcode:</th>
                            <td><code><span id="displayBarcodeToko">-</span></code></td>
                        </tr>
                        <tr>
                            <th>Latitude Toko:</th>
                            <td><span id="displayLatToko">-</span></td>
                        </tr>
                        <tr>
                            <th>Longitude Toko:</th>
                            <td><span id="displayLngToko">-</span></td>
                        </tr>
                        <tr>
                            <th>Accuracy Toko:</th>
                            <td><span id="displayAccToko">-</span> meter</td>
                        </tr>
                        <tr>
                            <th>Estimasi Jarak:</th>
                            <td>
                                <strong><span id="displayJarakPreview">-</span></strong> meter
                                <small class="text-muted d-block">(Preview realtime)</small>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tombol Proses & Hasil --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">📊 3. Proses Kunjungan</h5>
            </div>
            <div class="card-body">
                <button class="btn btn-primary w-100 mb-3" onclick="prosesKunjungan()" id="btnProses" disabled>
                    <i class="mdi mdi-check-circle"></i> Proses Kunjungan
                </button>

                <div class="alert alert-warning mb-3" id="peringatanProses">
                    <small>
                        <i class="mdi mdi-information"></i>
                        Pastikan Anda sudah meng scan barcode dan mengambil lokasi
                    </small>
                </div>

                <div id="hasilKunjungan" class="d-none">
                    <div id="statusDiterima" class="alert alert-success d-none">
                        <h4><i class="mdi mdi-check-circle"></i> KUNJUNGAN BERHASIL ✅</h4>
                        <p class="mb-2"><strong>SELAMAT!</strong> Kunjungan Anda <strong>SAH</strong> dan telah berhasil dicatat.</p>
                        <hr>
                        <div class="small">
                            <p class="mb-1"><i class="mdi mdi-information"></i> <strong>Detail:</strong></p>
                            <ul class="mb-0">
                                <li>Posisi Anda berada dalam radius yang diizinkan</li>
                                <li>Jarak ke toko: <strong><span id="infoJarakDiterima">-</span></strong> meter</li>
                                <li>Batas maksimal: <strong><span id="infoThresholdDiterima">-</span></strong> meter</li>
                            </ul>
                        </div>
                    </div>

                    <div id="statusDitolak" class="alert alert-danger d-none">
                        <h4><i class="mdi mdi-close-circle"></i> KUNJUNGAN GAGAL ❌</h4>
                        <p class="mb-2"><strong>MAAF!</strong> Kunjungan Anda <strong>TIDAK SAH</strong> karena jarak terlalu jauh dari lokasi toko.</p>
                        <hr>
                        <div class="small">
                            <p class="mb-1"><i class="mdi mdi-information"></i> <strong>Detail:</strong></p>
                            <ul class="mb-0">
                                <li>Posisi Anda berada <strong>DI LUAR</strong> radius yang diizinkan</li>
                                <li>Jarak ke toko: <strong><span id="infoJarakDitolak">-</span></strong> meter</li>
                                <li>Batas maksimal: <strong><span id="infoThresholdDitolak">-</span></strong> meter</li>
                                <li>Silakan mendekat ke lokasi toko dan coba lagi</li>
                            </ul>
                        </div>
                    </div>

                    <table class="table table-bordered mt-3">
                        <thead class="table-light">
                            <tr>
                                <th colspan="2" class="text-center">
                                    <strong>📊 Detail Kunjungan</strong>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th width="50%">🏪 Nama Toko:</th>
                                <td><span id="hasilNamaToko">-</span></td>
                            </tr>
                            <tr>
                                <th>📍 Jarak Aktual:</th>
                                <td><strong><span id="hasilJarak">-</span></strong> meter</td>
                            </tr>
                            <tr>
                                <th>🎯 Threshold Efektif:</th>
                                <td><strong><span id="hasilThreshold">-</span></strong> meter</td>
                            </tr>
                            <tr>
                                <th>✓ Status:</th>
                                <td>
                                    <span id="hasilStatus" class="badge fs-6">-</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <button class="btn btn-secondary w-100 mt-3" onclick="resetForm()">
                        🔄 Kunjungan Baru
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- html5-qrcode Library --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    // ============================================
    // KONFIGURASI
    // ============================================
    const API_SCAN = '{{ route('api.toko.scan') }}';
    const API_SIMPAN = '{{ route('kunjungan.simpan') }}';
    const THRESHOLD_MAX = 300;

    let html5QrCode = null;
    let isScanning = false;
    let tokoData = null;
    let salesLocation = null;

    // ============================================
    // FUNGSI HAVERSINE (JAVASCRIPT)
    // ============================================
    function haversine(lat1, lng1, lat2, lng2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) ** 2 +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLng / 2) ** 2;
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

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
    // INIT SCANNER
    // ============================================
    $(document).ready(function() {
        setTimeout(function() {
            mulaiScanner();
        }, 500);
    });

    async function mulaiScanner() {
        try {
            if (html5QrCode && isScanning) {
                return;
            }

            html5QrCode = new Html5Qrcode("reader");

            const config = {
                fps: 10,
                qrbox: { width: 350, height: 300 },
                aspectRatio: 1.333
            };

            await html5QrCode.start(
                { facingMode: "environment" },
                config,
                onScanSuccess,
                onScanFailure
            );

            isScanning = true;
            console.log('✅ Scanner started');

        } catch (error) {
            console.error('❌ Error starting scanner:', error);
            tampilkanError('Gagal mengakses kamera. Pastikan Anda telah memberikan izin kamera.');
        }
    }

    async function stopScanner() {
        if (html5QrCode && isScanning) {
            try {
                await html5QrCode.stop();
                isScanning = false;
                console.log('🛑 Scanner stopped');
            } catch (error) {
                console.error('❌ Error stopping scanner:', error);
            }
        }
    }

    function mulaiScanUlang() {
        stopScanner();
        document.getElementById('scannerError').classList.add('d-none');
        setTimeout(function() {
            mulaiScanner();
        }, 300);
    }

    // ============================================
    // SCAN CALLBACK
    // ============================================
    async function onScanSuccess(decodedText, decodedResult) {
        console.log('✅ Barcode detected:', decodedText);

        await stopScanner();

        document.getElementById('scannerLoading').classList.remove('d-none');

        await fetchTokoData(decodedText);
    }

    function onScanFailure(error) {
        // Normal, tidak perlu handling
    }

    // ============================================
    // FETCH TOKO DATA
    // ============================================
    async function fetchTokoData(barcode) {
        try {
            const response = await fetch(API_SCAN, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ barcode: barcode })
            });

            const result = await response.json();

            document.getElementById('scannerLoading').classList.add('d-none');

            if (result.success) {
                tokoData = result.data;
                tampilkanInfoToko(tokoData);
            } else {
                tampilkanError(result.message || 'Toko tidak ditemukan');
                setTimeout(function() {
                    mulaiScanUlang();
                }, 3000);
            }

        } catch (error) {
            console.error('❌ Error fetching toko:', error);
            document.getElementById('scannerLoading').classList.add('d-none');
            tampilkanError('Gagal mengambil data toko. Silakan coba lagi.');
        }
    }

    // ============================================
    // TAMPILKAN INFO TOKO
    // ============================================
    function tampilkanInfoToko(toko) {
        document.getElementById('infoTokoPlaceholder').classList.add('d-none');
        document.getElementById('infoToko').classList.remove('d-none');

        document.getElementById('displayNamaToko').textContent = toko.nama_toko;
        document.getElementById('displayBarcodeToko').textContent = toko.barcode;
        document.getElementById('displayLatToko').textContent = parseFloat(toko.latitude).toFixed(8);
        document.getElementById('displayLngToko').textContent = parseFloat(toko.longitude).toFixed(8);
        document.getElementById('displayAccToko').textContent = parseFloat(toko.accuracy).toFixed(2);

        // Update preview jarak jika lokasi sales sudah ada
        if (salesLocation) {
            updateJarakPreview();
        }

        cekSiapProses();
    }

    // ============================================
    // AMBIL LOKASI SALES
    // ============================================
    async function ambilLokasiSales() {
        const btn = document.getElementById('btnAmbilLokasi');
        const infoDiv = document.getElementById('lokasiSalesInfo');

        try {
            btn.disabled = true;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Mengambil lokasi...';

            const position = await getAccuratePosition(50, 20000);

            salesLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
                accuracy: position.coords.accuracy
            };

            document.getElementById('displayLatSales').textContent = salesLocation.lat.toFixed(8);
            document.getElementById('displayLngSales').textContent = salesLocation.lng.toFixed(8);
            document.getElementById('displayAccSales').textContent = salesLocation.accuracy.toFixed(2);

            infoDiv.classList.remove('d-none');

            // Update preview jarak jika toko sudah ada
            if (tokoData) {
                updateJarakPreview();
            }

            cekSiapProses();

        } catch (error) {
            console.error('Error:', error);

            let pesan = 'Gagal mengambil lokasi GPS';
            if (error.message === 'Timeout, tidak dapat posisi') {
                pesan = 'Timeout - pastikan GPS aktif dan berada di lokasi terbuka';
            } else if (error.code === 1) {
                pesan = 'Izin lokasi ditolak. Mohon izinkan akses lokasi.';
            } else if (error.code === 2) {
                pesan = 'Lokasi tidak tersedia. Pastikan GPS aktif.';
            }

            alert('⚠️ ' + pesan);

        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="mdi mdi-crosshairs-gps"></i> Ambil Lokasi Saya';
        }
    }

    // ============================================
    // UPDATE JARAK PREVIEW
    // ============================================
    function updateJarakPreview() {
        if (!tokoData || !salesLocation) return;

        const jarak = haversine(
            tokoData.latitude,
            tokoData.longitude,
            salesLocation.lat,
            salesLocation.lng
        );

        document.getElementById('displayJarakPreview').textContent = jarak.toFixed(2);
    }

    // ============================================
    // CEK SIAP PROSES
    // ============================================
    function cekSiapProses() {
        const btn = document.getElementById('btnProses');
        const peringatan = document.getElementById('peringatanProses');

        if (tokoData && salesLocation) {
            btn.disabled = false;
            peringatan.classList.add('d-none');
        } else {
            btn.disabled = true;
            peringatan.classList.remove('d-none');
        }
    }

    // ============================================
    // PROSES KUNJUNGAN
    // ============================================
    async function prosesKunjungan() {
        if (!tokoData || !salesLocation) {
            alert('⚠️ Pastikan Anda sudah scan barcode dan mengambil lokasi!');
            return;
        }

        try {
            const response = await fetch(API_SIMPAN, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    toko_id: tokoData.id,
                    lat_sales: salesLocation.lat,
                    lng_sales: salesLocation.lng,
                    accuracy_sales: salesLocation.accuracy
                })
            });

            const result = await response.json();

            if (result.success) {
                // Tampilkan alert message
                const status = result.data.status;
                const alertTitle = status === 'diterima' ? '✅ KUNJUNGAN BERHASIL' : '❌ KUNJUNGAN GAGAL';
                const alertClass = status === 'diterima' ? 'alert-success' : 'alert-danger';

                // Tampilkan hasil
                tampilkanHasil(result.data);

                // Optional: Show additional alert
                // alert(alertTitle + '\n\n' + result.message);

            } else {
                alert('❌ ' + result.message);
            }

        } catch (error) {
            console.error('Error:', error);
            alert('❌ Gagal memproses kunjungan. Silakan coba lagi.');
        }
    }

    // ============================================
    // TAMPILKAN HASIL
    // ============================================
    function tampilkanHasil(data) {
        document.getElementById('hasilKunjungan').classList.remove('d-none');

        const jarak = data.jarak_meter;
        const threshold = data.threshold_efektif;
        const status = data.status;

        // Isi data utama
        document.getElementById('hasilJarak').textContent = jarak.toFixed(2);
        document.getElementById('hasilThreshold').textContent = threshold.toFixed(2);
        document.getElementById('hasilNamaToko').textContent = data.toko.nama_toko;

        const statusEl = document.getElementById('hasilStatus');
        const diterimaEl = document.getElementById('statusDiterima');
        const ditolakEl = document.getElementById('statusDitolak');

        if (status === 'diterima') {
            diterimaEl.classList.remove('d-none');
            ditolakEl.classList.add('d-none');
            statusEl.textContent = 'BERHASIL ✅';
            statusEl.classList.remove('bg-danger');
            statusEl.classList.add('bg-success');

            // Isi detail info di alert
            document.getElementById('infoJarakDiterima').textContent = jarak.toFixed(2);
            document.getElementById('infoThresholdDiterima').textContent = threshold.toFixed(2);

        } else {
            diterimaEl.classList.add('d-none');
            ditolakEl.classList.remove('d-none');
            statusEl.textContent = 'GAGAL ❌';
            statusEl.classList.remove('bg-success');
            statusEl.classList.add('bg-danger');

            // Isi detail info di alert
            document.getElementById('infoJarakDitolak').textContent = jarak.toFixed(2);
            document.getElementById('infoThresholdDitolak').textContent = threshold.toFixed(2);
        }

        // Disable tombol proses
        document.getElementById('btnProses').disabled = true;
    }

    // ============================================
    // RESET FORM
    // ============================================
    function resetForm() {
        // Reset data
        tokoData = null;
        salesLocation = null;

        // Hide hasil
        document.getElementById('hasilKunjungan').classList.add('d-none');

        // Reset info toko
        document.getElementById('infoTokoPlaceholder').classList.remove('d-none');
        document.getElementById('infoToko').classList.add('d-none');

        // Reset lokasi sales
        document.getElementById('lokasiSalesInfo').classList.add('d-none');

        // Reset tombol proses
        document.getElementById('btnProses').disabled = true;
        document.getElementById('peringatanProses').classList.remove('d-none');

        // Mulai scanner ulang
        mulaiScanUlang();
    }

    // ============================================
    // TAMPILKAN ERROR
    // ============================================
    function tampilkanError(message) {
        const errorEl = document.getElementById('scannerError');
        errorEl.innerHTML = `
            <div class="d-flex align-items-center">
                <div style="font-size: 24px; margin-right: 10px;">❌</div>
                <div>${message}</div>
            </div>
        `;
        errorEl.classList.remove('d-none');
    }
</script>
@endpush
