@extends('layouts.apps')

@section('title', 'Data Barang')

@push('styles')
<style>
    /* Responsive scanner container */
    #reader {
        position: relative;
    }

    /* Adjust scanner video for better fit */
    #reader video {
        object-fit: cover;
        border-radius: 8px;
    }

    /* Make sure scanner is responsive on mobile */
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

        .modal-xl .modal-content {
            max-height: 90vh;
            overflow-y: auto;
        }
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">📦 Data Barang</h2>
    <a href="{{ route('barang.create') }}" class="btn btn-success">➕ Tambah Barang</a>
</div>

{{-- Notifikasi sukses --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        ✅ {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Tombol aksi --}}
<div class="d-flex gap-2 mb-3">
    <button class="btn btn-primary" onclick="bukaModalCetak()">
        🖨️ Cetak Label Terpilih
    </button>
    <button class="btn btn-success" onclick="bukaModalScanner()">
        📷 Scan Barcode
    </button>
</div>

{{-- Tabel --}}
<div class="card shadow-sm">
    <div class="card-body">
        <table id="tabelBarang" class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th style="width:40px">
                        <input type="checkbox" id="checkAll" title="Pilih Semua">
                    </th>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th style="width:200px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barang as $b)
                <tr>
                    <td><input type="checkbox" class="check-item" value="{{ $b->id_barang }}"></td>
                    <td>{{ $b->id_barang }}</td>
                    <td>{{ $b->nama }}</td>
                    <td>Rp {{ number_format($b->harga, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('barang.show', $b->id_barang) }}"
                         class="btn btn-gradient-info btn-sm">
                        <i class="mdi mdi-eye"></i></a>

                        <a href="{{ route('barang.edit', $b->id_barang) }}"
                          class="btn btn-gradient-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i></a>

                        <form action="{{ route('barang.destroy', $b->id_barang) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-gradient-danger btn-sm">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ===================== MODAL CETAK ===================== --}}
<div class="modal fade" id="modalCetak" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🖨️ Pengaturan Cetak Label</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('barang.pdf') }}" id="formCetak" target="_blank">
                @csrf
                <div id="hiddenIds"></div>

                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Kertas TnJ No.108 memiliki <strong>5 kolom × 8 baris = 40 label</strong>.
                        Masukkan posisi label pertama yang akan diisi.
                    </p>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label fw-bold">Kolom X (1–5)</label>
                            <input type="number" name="start_x" id="start_x"
                                   class="form-control" min="1" max="5" value="1" required>
                            <div class="form-text">Posisi kolom dari kiri</div>
                        </div>
                        <div class="col">
                            <label class="form-label fw-bold">Baris Y (1–8)</label>
                            <input type="number" name="start_y" id="start_y"
                                   class="form-control" min="1" max="8" value="1" required>
                            <div class="form-text">Posisi baris dari atas</div>
                        </div>
                    </div>

                    <div id="infoLabel" class="alert alert-info py-2 mb-3"></div>

                    <label class="form-label fw-bold">Preview Kertas:</label>
                    <div id="previewGrid"
                         style="display:grid; grid-template-columns: repeat(5, 1fr); gap:4px;">
                    </div>

                    <div class="d-flex gap-3 mt-2" style="font-size:12px">
                        <span><span style="display:inline-block;width:14px;height:14px;background:#e9ecef;border:1px solid #ccc;border-radius:2px"></span> Terlewati</span>
                        <span><span style="display:inline-block;width:14px;height:14px;background:#d1e7dd;border:1px solid #ccc;border-radius:2px"></span> Akan diisi</span>
                        <span><span style="display:inline-block;width:14px;height:14px;background:#fff;border:1px solid #ccc;border-radius:2px"></span> Kosong</span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">📄 Generate PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== MODAL SCANNER BARCODE ===================== --}}
<div class="modal fade" id="modalScanner" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">📷 Barcode Scanner - Praktikum 1</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                {{-- Instruksi --}}
                <div class="alert alert-info mb-3">
                    <strong>📋 Cara Penggunaan:</strong>
                    <ol class="mb-0 mt-2">
                        <li>Arahkan kamera ke barcode label barang</li>
                        <li>Tunggu hingga barcode terdeteksi dan bunyi "beep" terdengar</li>
                        <li>Scanner akan berhenti otomatis dan menampilkan data barang</li>
                    </ol>
                </div>

                {{-- Area Scanner --}}
                <div class="text-center">
                    <div id="reader" style="border-radius: 8px; overflow: hidden; background: #000; width: 100%; max-width: 600px; height: 450px; margin: 0 auto;"></div>
                </div>

                {{-- Loading Spinner --}}
                <div class="text-center d-none mt-3" id="scannerLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Mencari barang di database...</p>
                </div>

                {{-- Error Message --}}
                <div class="alert alert-danger d-none mt-3" id="scannerError"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" onclick="mulaiScanUlang()">
                    🔄 Scan Ulang
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===================== MODAL HASIL SCAN ===================== --}}
<div class="modal fade" id="modalHasilScan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">✅ Barang Ditemukan!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="card border-success">
                    <div class="card-body bg-light">
                        <div class="mb-3">
                            <label class="fw-bold text-muted">🏷️ ID Barang:</label>
                            <div class="fs-4" id="hasilIdBarang">-</div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold text-muted">📦 Nama Barang:</label>
                            <div class="fs-4" id="hasilNamaBarang">-</div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold text-muted">💰 Harga:</label>
                            <div class="fs-4 text-success fw-bold" id="hasilHargaBarang">-</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success mt-3 mb-0">
                    <strong>🎉 Scan Berhasil!</strong>
                    <br>Data barang telah ditampilkan di atas.
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" onclick="tutupHasilDanScanUlang()">
                    🔄 Scan Lagi
                </button>
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
    // KONFIGURASI BARCODE SCANNER
    // ============================================

    const BEEP_SOUND_PATH = '{{ asset('jokowi-oamatiketu-version.mp3') }}';
    const API_ENDPOINT = '{{ route('api.barang.scan') }}';

    let html5QrCode = null;
    let isScanning = false;
    let beepSound = null;
    let modalScanner = null;
    let modalHasilScan = null;

    // ============================================
    // INISIALISASI
    // ============================================

    $(document).ready(function() {
        // Load beep sound
        beepSound = new Audio(BEEP_SOUND_PATH);

        // Inisialisasi modal Bootstrap
        modalScanner = new bootstrap.Modal(document.getElementById('modalScanner'));
        modalHasilScan = new bootstrap.Modal(document.getElementById('modalHasilScan'));

        // Stop scanner saat modal ditutup
        document.getElementById('modalScanner').addEventListener('hidden.bs.modal', function () {
            stopScanner();
        });
    });

    // ============================================
    // FUNGSI MODAL SCANNER
    // ============================================

    function bukaModalScanner() {
        // Reset UI
        document.getElementById('scannerLoading').classList.add('d-none');
        document.getElementById('scannerError').classList.add('d-none');

        // Tampilkan modal
        modalScanner.show();

        // Mulai scanner setelah modal terbuka
        setTimeout(function() {
            mulaiScanner();
        }, 500);
    }

    function mulaiScanUlang() {
        stopScanner();
        document.getElementById('scannerLoading').classList.add('d-none');
        document.getElementById('scannerError').classList.add('d-none');
        setTimeout(function() {
            mulaiScanner();
        }, 300);
    }

    function tutupHasilDanScanUlang() {
        modalHasilScan.hide();
        setTimeout(function() {
            bukaModalScanner();
        }, 300);
    }

    // ============================================
    // FUNGSI SCANNER
    // ============================================

    async function mulaiScanner() {
        try {
            // Inisialisasi html5-qrcode
            html5QrCode = new Html5Qrcode("reader");

            // Konfigurasi kamera (prioritas: kamera belakang)
            // Ukuran disesuaikan dengan container: 600px width x 450px height
            const config = {
                fps: 10,                               // Frame per second
                qrbox: { width: 400, height: 300 },    // Area scan (4:3 ratio)
                aspectRatio: 1.333                      // Rasio aspek 4:3
            };

            // Mulai scanning dengan kamera belakang (environment)
            await html5QrCode.start(
                { facingMode: "environment" },         // Kamera belakang
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

    // ============================================
    // CALLBACK SCANNER
    // ============================================

    async function onScanSuccess(decodedText, decodedResult) {
        console.log('✅ Barcode detected:', decodedText);

        // 1. Mainkan bunyi beep
        playBeep();

        // 2. Stop scanner
        await stopScanner();

        // 3. Tampilkan loading
        document.getElementById('scannerLoading').classList.remove('d-none');

        // 4. Fetch data barang
        await fetchBarangData(decodedText);
    }

    function onScanFailure(error) {
        // Normal, tidak perlu handling
    }

    // ============================================
    // FUNGSI AUDIO
    // ============================================

    function playBeep() {
        if (beepSound) {
            beepSound.currentTime = 0;
            beepSound.play().catch(error => {
                console.warn('⚠️ Gagal memainkan beep:', error);
            });
        }
    }

    // ============================================
    // FUNGSI API & DATA
    // ============================================

    async function fetchBarangData(barcode) {
        try {
            const response = await fetch(API_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    barcode: barcode
                })
            });

            const result = await response.json();

            // Sembunyikan loading
            document.getElementById('scannerLoading').classList.add('d-none');

            if (result.success) {
                // Tampilkan hasil scan
                tampilkanHasilScan(result.data);
            } else {
                tampilkanError(result.message || 'Barang tidak ditemukan');

                // Auto restart setelah 3 detik
                setTimeout(function() {
                    mulaiScanUlang();
                }, 3000);
            }

        } catch (error) {
            console.error('❌ Error fetching barang:', error);
            document.getElementById('scannerLoading').classList.add('d-none');
            tampilkanError('Gagal mengambil data barang. Silakan coba lagi.');
        }
    }

    function tampilkanHasilScan(barangData) {
        // Isi data ke modal hasil
        document.getElementById('hasilIdBarang').textContent = barangData.id_barang;
        document.getElementById('hasilNamaBarang').textContent = barangData.nama;
        document.getElementById('hasilHargaBarang').textContent = formatRupiah(barangData.harga);

        // Tutup modal scanner
        modalScanner.hide();

        // Tampilkan modal hasil
        modalHasilScan.show();
    }

    // ============================================
    // FUNGSI UTILITY
    // ============================================

    function tampilkanError(message) {
        const errorEl = document.getElementById('scannerError');
        errorEl.innerHTML = `
            <div class="d-flex align-items-center">
                <div style="font-size: 24px; margin-right: 10px;">❌</div>
                <div>${message}</div>
            </div>
            <div style="margin-top: 10px; font-size: 14px;">
                Pastikan barcode yang discan adalah barcode label barang yang valid.
            </div>
        `;
        errorEl.classList.remove('d-none');
    }

    function formatRupiah(amount) {
        return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
    }

    // ============================================
    // DATA TABLES & CETAK (EXISTING CODE)
    // ============================================

    // Aktifkan DataTables
    $('#tabelBarang').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        },
        columnDefs: [
            { orderable: false, targets: [0, 4] }
        ]
    });

    // Checkbox pilih semua
    $('#checkAll').on('change', function () {
        $('.check-item').prop('checked', this.checked);
    });

    // Buka modal cetak
    function bukaModalCetak() {
        let dipilih = $('.check-item:checked');
        if (dipilih.length === 0) {
            alert('⚠️ Pilih minimal 1 barang terlebih dahulu!');
            return;
        }

        // Isi hidden inputs
        let hiddenDiv = $('#hiddenIds');
        hiddenDiv.empty();
        dipilih.each(function () {
            hiddenDiv.append(
                `<input type="hidden" name="selected_ids[]" value="${$(this).val()}">`
            );
        });

        updatePreview();
        new bootstrap.Modal('#modalCetak').show();
    }

    // Update preview saat X/Y berubah
    $('#start_x, #start_y').on('input', updatePreview);

    function updatePreview() {
        let x = Math.min(Math.max(parseInt($('#start_x').val()) || 1, 1), 5);
        let y = Math.min(Math.max(parseInt($('#start_y').val()) || 1, 1), 8);

        let startIndex = (y - 1) * 5 + (x - 1);
        let jumlah     = $('.check-item:checked').length;
        let sisa       = 40 - startIndex;

        // Render grid 40 kotak
        let grid = $('#previewGrid');
        grid.empty();
        for (let i = 0; i < 40; i++) {
            let tipe, teks;
            if (i < startIndex)              { tipe = 'terlewat'; teks = '–'; }
            else if (i < startIndex + jumlah){ tipe = 'terisi';   teks = (i - startIndex + 1); }
            else                             { tipe = 'kosong';   teks = ''; }

            grid.append(`
                <div style="height:28px;border:1px solid #dee2e6;border-radius:4px;
                            display:flex;align-items:center;justify-content:center;
                            font-size:10px;
                            background:${tipe==='terlewat'?'#e9ecef':tipe==='terisi'?'#d1e7dd':'#fff'};
                            color:${tipe==='terisi'?'#0f5132':tipe==='terlewat'?'#adb5bd':'#000'};
                            font-weight:${tipe==='terisi'?'bold':'normal'}">
                    ${teks}
                </div>
            `);
        }

        let warna = jumlah > sisa ? 'danger' : 'info';
        let pesan = jumlah > sisa
            ? `⚠️ ${jumlah} label dipilih tapi hanya ada ${sisa} ruang tersisa!`
            : `✅ ${jumlah} label akan dicetak mulai kolom ${x}, baris ${y}. Sisa: ${sisa - jumlah} label.`;

        $('#infoLabel').removeClass('alert-info alert-danger').addClass(`alert-${warna}`).html(pesan);
    }
</script>
@endpush