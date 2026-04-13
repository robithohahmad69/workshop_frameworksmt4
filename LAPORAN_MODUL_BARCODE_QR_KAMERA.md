# LAPORAN PRAKTIKUM
## Barcode, QR Code, dan Akses Kamera

Disusun oleh:
**Nama:** [Nama Mahasiswa]
**NIM:** [NIM]
**Kelas:** [Kelas]

**Institut:** [Nama Institut]
**Tahun:** 2026

---

<style>
/* ==========================================
   VS CODE DARK+ THEME FOR CODE BLOCKS ONLY
   ========================================== */

/* Code Block Container */
pre {
    background-color: #1e1e1e !important;
    border: 1px solid #3c3c3c;
    border-radius: 6px;
    padding: 16px;
    overflow-x: auto;
    font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.5;
    margin: 16px 0;
}

/* Inline code dalam teks */
code {
    font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
    background-color: #f4f4f4;
    color: #d63384;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.9em;
}

/* Code dalam pre block (dark theme) */
pre code {
    background-color: transparent;
    color: #d4d4d4;
    padding: 0;
}

/* Syntax Highlighting - VS Code Dark+ */
.hljs-keyword { color: #569cd6; font-weight: bold; }
.hljs-string { color: #ce9178; }
.hljs-comment { color: #6a9955; font-style: italic; }
.hljs-function { color: #dcdcaa; }
.hljs-number { color: #b5cea8; }
.hljs-class { color: #4ec9b0; }
.hljs-variable { color: #9cdcfe; }
.hljs-tag { color: #569cd6; }
.hljs-attr { color: #9cdcfe; }
.hljs-value { color: #ce9178; }
.hljs-operator { color: #d4d4d4; }
.hljs-punctuation { color: #d4d4d4; }
.hljs-property { color: #9cdcfe; }
.hljs-built_in { color: #4ec9b0; }
.hljs-title { color: #dcdcaa; }
.hljs-params { color: #d4d4d4; }
.hljs-meta { color: #6a9955; }

/* Code Block Title */
.code-title {
    background-color: #2d2d2d;
    color: #cccccc;
    padding: 8px 16px;
    border-radius: 6px 6px 0 0;
    font-size: 12px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-weight: 600;
    margin-bottom: -8px;
    border: 1px solid #3c3c3c;
    border-bottom: none;
    display: inline-block;
}
</style>

## DAFTAR ISI

1. [Latar Belakang](#latar-belakang)
2. [Studi Kasus 1: Generate Barcode](#studi-kasus-1-generate-barcode)
3. [Studi Kasus 2: Generate QR Code](#studi-kasus-2-generate-qr-code)
4. [Studi Kasus 3: Akses Kamera](#studi-kasus-3-akses-kamera)
5. [Kesimpulan](#kesimpulan)
6. [Referensi](#referensi)

---

## LATAR BELAKANG

HTML5 tidak memiliki fitur bawaan untuk membuat Barcode ataupun QR Code, tetapi HTML5 menyediakan API untuk mengakses kamera pada perangkat. Perbedaan utama antara Barcode dan QR Code adalah jumlah dimensi untuk menyimpan data:

- **Barcode (1D):** Hanya mampu menyimpan 20-25 karakter
- **QR Code (2D):** Mampu menyimpan data lebih besar dalam format kotak-kotak piksel

### Tujuan Praktikum

1. Mengimplementasikan generate Barcode untuk label harga
2. Mengimplementasikan generate QR Code untuk payment gateway
3. Mengimplementasikan akses kamera untuk data customer

---

## STUDI KASUS 1: GENERATE BARCODE

### 1.1 Pengenalan Library

Library yang digunakan: **Picqer/php-barcode-generator** versi 3.2

**Instalasi:**

<div class="code-title">bash</div>

```bash
composer require picqer/php-barcode-generator
```

**Konfigurasi di `composer.json`:**

<div class="code-title">json</div>

```json
{
    "require": {
        "picqer/php-barcode-generator": "^3.2"
    }
}
```

### 1.2 Implementasi Generate Barcode

**File:** `app/Http/Controllers/BarangController.php`

<div class="code-title">php</div>

```php
<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;  // ← Import library

class BarangController extends Controller
{
    public function cetakPdf(Request $request)
    {
        $request->validate([
            'selected_ids' => 'required|array|min:1',
            'start_x'      => 'required|integer|min:1|max:5',
            'start_y'      => 'required|integer|min:1|max:8',
        ]);

        // Ambil barang yang dipilih
        $barangs = Barang::whereIn('id_barang', $request->selected_ids)->get();

        // Hitung posisi awal (grid 5x8 = 40 slot)
        $startIndex = (($request->start_y - 1) * 5) + ($request->start_x - 1);

        // ==========================================
        // GENERATE BARCODE
        // ==========================================
        $generator = new BarcodeGeneratorPNG();
        $barcodes  = [];

        foreach ($barangs as $b) {
            // Generate barcode PNG
            $png = $generator->getBarcode(
                (string) $b->id_barang,        // Data: ID barang
                $generator::TYPE_CODE_128      // Tipe: CODE 128 (alphanumeric)
            );

            // Convert ke base64 untuk embed di PDF
            $barcodes[$b->id_barang] = 'data:image/png;base64,' . base64_encode($png);
        }

        // Load view PDF dengan data barcode
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('barang.pdf', [
            'barangs'    => $barangs,
            'startIndex' => $startIndex,
            'barcodes'   => $barcodes,  // ← Pass barcode ke view
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('label-harga.pdf');
    }
}
```

**Penjelasan Kode:**

1. **Line 98:** Membuat instance `BarcodeGeneratorPNG()`
2. **Line 103-105:** Generate barcode dengan tipe `TYPE_CODE_128`
   - Mendukung alphanumeric (angka + huruf)
   - Standard industri untuk label harga
3. **Line 106:** Encode PNG ke base64 untuk embed di HTML/PDF

### 1.3 Tampilan di PDF

**File:** `resources/views/barang/pdf.blade.php`

<div class="code-title">html</div>

```html
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    .label-barcode img {
        width: 34mm;
        height: 8mm;
        display: block;
        margin: 0 auto;
    }

    .label-id {
        font-size: 5pt;
        color: #888;
    }
</style>
</head>
<body>

<table class="label-table">
    @foreach($baris as $row)
    <tr>
        @foreach($row as $slot)
        <td>
            @if($slot !== null)
                <div class="label-isi">
                    <div class="label-nama">{{ $slot->nama }}</div>
                    <div class="label-harga">
                        Rp {{ number_format($slot->harga, 0, ',', '.') }}
                    </div>

                    <!-- ========================================== -->
                    <!-- BARCODE DISPLAY -->
                    <!-- ========================================== -->
                    <div class="label-barcode">
                        <img src="{{ $barcodes[$slot->id_barang] }}"
                             alt="Barcode {{ $slot->id_barang }}">
                    </div>

                    <div class="label-id">{{ $slot->id_barang }}</div>
                </div>
            @endif
        </td>
        @endforeach
    </tr>
    @endforeach
</table>

</body>
</html>
```

**Penjelasan Kode:**

- **Line 96-99:** Menampilkan barcode image dari base64 data
- Barcode ditampilkan di atas ID barang
- Ukuran: 34mm x 8mm (standar label harga)

### 1.4 Hasil Output

![Contoh PDF Label Harga dengan Barcode](placeholder:barcode-pdf)

**Format Barcode:** TYPE_CODE_128

- **Kapasitas:** 20-25 karakter
- **Support:** Alphanumeric (A-Z, 0-9)
- **Penggunaan:** ID barang di label harga
- **Kelebihan:** Simple, cepat di-scan, universal

---

## STUDI KASUS 2: GENERATE QR CODE

### 2.1 Pengenalan Library

Library yang digunakan: **BaconQRCode**

**Instalasi:**

<div class="code-title">bash</div>

```bash
composer require bacon/bacon-qr-code
```

### 2.2 Implementasi Generate QR Code

**File:** `app/Http/Controllers/Customer/OrderController.php`

<div class="code-title">php</div>

```php
<?php

namespace App\Http\Controllers\Customer;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Halaman sukses setelah payment
    public function success($orderId)
    {
        $order = Order::with(['orderItems.menu', 'payment'])
                      ->findOrFail($orderId);

        // Cek status pembayaran via Midtrans API
        if ($order->status_bayar === 'pending' && $order->payment) {
            \Midtrans\Config::$serverKey    = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');

            try {
                $status = \Midtrans\Transaction::status(
                    $order->payment->midtrans_order_id
                );

                if ($status->transaction_status === 'settlement') {
                    $order->payment->update(['status' => 'settlement']);
                    $order->update(['status_bayar' => 'lunas']);
                }
            } catch (\Exception $e) {
                // Handle error
            }
        }

        // ==========================================
        // GENERATE QR CODE
        // ==========================================
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),  // Ukuran: 200px
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()           // Format: SVG
        );

        $writer = new \BaconQrCode\Writer($renderer);

        // Generate QR code berisi ID pesanan
        $qrSvg = $writer->writeString((string) $order->id);

        // Encode ke base64 untuk embed di HTML
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        return view('customer.success', compact('order', 'qrBase64'));
    }
}
```

**Penjelasan Kode:**

1. **Line 228-230:** Renderer configuration
   - `RendererStyle(200)`: Ukuran QR 200x200 px
   - `SvgImageBackEnd()`: Format SVG (vector, scalable)
2. **Line 232-233:** Generate QR dari ID pesanan
3. **Line 234:** Encode SVG ke base64

### 2.3 Tampilan QR Code di Halaman Success

**File:** `resources/views/customer/success.blade.php`

<div class="code-title">html</div>

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Pesanan Berhasil</title>
    <style>
        .qr-wrapper {
            margin: 0 0 24px 0;
            padding: 20px 16px;
            background: #f9f9f9;
            border-radius: 8px;
            text-align: center;
        }

        .qr-wrapper img {
            width: 180px;
            height: 180px;
        }

        .qr-wrapper .qr-label {
            font-size: 12px;
            color: #888;
            margin-top: 10px;
        }

        .qr-wrapper .qr-id {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="icon">✅</div>
        <h2>Pesanan Berhasil!</h2>
        <p>Terima kasih, pesananmu sedang diproses.</p>

        <!-- Info pesanan... -->

        <!-- ========================================== -->
        <!-- QR CODE DISPLAY -->
        <!-- ========================================== -->
        @if($order->status_bayar === 'lunas')
        <div class="qr-wrapper">
            <img src="{{ $qrBase64 }}"
                 alt="QR Code Pesanan #{{ $order->id }}">
            <div class="qr-label">
                Tunjukkan QR Code ini ke vendor
            </div>
            <div class="qr-id">
                ID Pesanan: #{{ $order->id }}
            </div>
        </div>
        @endif

        <div class="buttons-wrapper">
            <button onclick="window.print()">🖨️ Cetak Invoice</button>
            <a href="{{ route('customer.index') }}">Pesan Lagi</a>
        </div>
    </div>
</body>
</html>
```

**Penjelasan Kode:**

- **Line 81-86:** Menampilkan QR code hanya jika status = 'lunas'
- QR code berisi ID pesanan untuk validasi
- Ukuran: 180px x 180px

### 2.4 Hasil Output

![Contoh QR Code di Halaman Success](placeholder:qr-success)

**Format QR Code:** SVG (Scalable Vector Graphics)

- **Kapasitas:** Hingga 4,296 karakter (numeric)
- **Encoding:** Vector (tidak pecah saat di-zoom)
- **Penggunaan:** ID pesanan untuk validasi payment
- **Kelebihan:** Robust, bisa dibaca dari berbagai sudut

---

## STUDI KASUS 3: AKSES KAMERA

### 3.1 HTML5 MediaDevices API

HTML5 menyediakan API untuk mengakses kamera perangkat:

<div class="code-title">javascript</div>

```javascript
// Akses kamera
navigator.mediaDevices.getUserMedia({ video: true })

// List semua kamera
navigator.mediaDevices.enumerateDevices()

// Preview video
videoElement.srcObject = stream

// Capture snapshot
canvas.getContext('2d').drawImage(video, 0, 0)

// Convert ke base64
canvas.toDataURL('image/png')
```

**Browser Support:**

- ✅ Chrome/Edge: Full support
- ✅ Firefox: Full support
- ✅ Safari: Full support (iOS 11+)
- ❌ IE: Not supported

### 3.2 Routes Configuration

**File:** `routes/web.php`

<div class="code-title">php</div>

```php
<?php

use App\Http\Controllers\Customer\CustomerController;

// ==========================================
// CUSTOMER DATA ROUTES
// ==========================================
Route::prefix('customer-data')->name('customer-data.')->group(function () {
    // List data customer
    Route::get('/', [CustomerController::class, 'index'])->name('index');

    // Create dengan BLOB storage
    Route::get('/create-blob', [CustomerController::class, 'createBlob'])
         ->name('create-blob');
    Route::post('/store-blob', [CustomerController::class, 'storeBlob'])
         ->name('store-blob');

    // Create dengan File storage
    Route::get('/create-file', [CustomerController::class, 'createFile'])
         ->name('create-file');
    Route::post('/store-file', [CustomerController::class, 'storeFile'])
         ->name('store-file');
});

// ==========================================
// API WILAYAH INDONESIA (PUBLIC)
// ==========================================
Route::get('/api/provinsi', function () {
    $response = \Illuminate\Support\Facades\Http::get(
        'https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json'
    );
    return $response->json();
});

Route::get('/api/kota/{id}', function ($id) {
    $response = \Illuminate\Support\Facades\Http::get(
        "https://emsifa.github.io/api-wilayah-indonesia/api/regencies/$id.json"
    );
    return $response->json();
});

Route::get('/api/kecamatan/{id}', function ($id) {
    $response = \Illuminate\Support\Facades\Http::get(
        "https://emsifa.github.io/api-wilayah-indonesia/api/districts/$id.json"
    );
    return $response->json();
});

Route::get('/api/kelurahan/{id}', function ($id) {
    $response = \Illuminate\Support\Facades\Http::get(
        "https://emsifa.github.io/api-wilayah-indonesia/api/villages/$id.json"
    );
    return $response->json();
});
```

### 3.3 Database Migration

**File:** `database/migrations/2026_04_12_201515_create_customers_table.php`

<div class="code-title">php</div>

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('alamat')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kota')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kodepos_kelurahan')->nullable();

            // Dual storage strategy
            $table->text('foto_blob')->nullable();      // BLOB (base64)
            $table->string('foto_path')->nullable();    // File path

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
```

**Catatan:** Untuk PostgreSQL, tipe `binary` diganti menjadi `text` untuk menyimpan base64 string.

### 3.4 Controller: Store BLOB

**File:** `app/Http/Controllers/Customer/CustomerController.php`

<div class="code-title">php</div>

```php
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Simpan foto sebagai BLOB (base64 di database)
    public function storeBlob(Request $request)
    {
        $request->validate([
            'nama'              => 'required|string|max:255',
            'alamat'            => 'nullable|string',
            'provinsi'          => 'nullable|string',
            'kota'              => 'nullable|string',
            'kecamatan'         => 'nullable|string',
            'kodepos_kelurahan' => 'nullable|string',
            'foto'              => 'required|string',  // Base64 dari kamera
        ]);

        // ==========================================
        // PROSES FOTO BLOB
        // ==========================================
        // Hapus header "data:image/png;base64,"
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->foto);

        // Simpan ke database
        Customer::create([
            'nama'              => $request->nama,
            'alamat'            => $request->alamat,
            'provinsi'          => $request->provinsi,
            'kota'              => $request->kota,
            'kecamatan'         => $request->kecamatan,
            'kodepos_kelurahan' => $request->kodepos_kelurahan,
            'foto_blob'         => $base64,  // ← Simpan base64 string
        ]);

        return redirect()->route('customer-data.index')
                         ->with('success', 'Customer berhasil ditambahkan (BLOB)!');
    }
}
```

### 3.5 Controller: Store File

**File:** `app/Http/Controllers/Customer/CustomerController.php` (lanjutan)

<div class="code-title">php</div>

```php
    // Simpan foto sebagai File di storage
    public function storeFile(Request $request)
    {
        $request->validate([
            'nama'              => 'required|string|max:255',
            'alamat'            => 'nullable|string',
            'provinsi'          => 'nullable|string',
            'kota'              => 'nullable|string',
            'kecamatan'         => 'nullable|string',
            'kodepos_kelurahan' => 'nullable|string',
            'foto'              => 'required|string',  // Base64 dari kamera
        ]);

        // ==========================================
        // PROSES FOTO FILE
        // ==========================================
        // Hapus header
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $request->foto);

        // Decode base64 ke binary
        $binary = base64_decode($base64);

        // Generate nama file unik
        $filename = 'customer_' . time() . '.png';

        // Simpan ke storage/public/customers/
        \Illuminate\Support\Facades\Storage::disk('public')
            ->put('customers/' . $filename, $binary);

        // Simpan path ke database
        Customer::create([
            'nama'              => $request->nama,
            'alamat'            => $request->alamat,
            'provinsi'          => $request->provinsi,
            'kota'              => $request->kota,
            'kecamatan'         => $request->kecamatan,
            'kodepos_kelurahan' => $request->kodepos_kelurahan,
            'foto_path'         => 'customers/' . $filename,  // ← Simpan path
        ]);

        return redirect()->route('customer-data.index')
                         ->with('success', 'Customer berhasil ditambahkan (File)!');
    }
}
```

### 3.6 Form dengan Kamera (BLOB)

**File:** `resources/views/customer/create1.blade.php`

<div class="code-title">html</div>

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Customer 1 - BLOB</title>
    <style>
        .foto-preview-box {
            width: 130px;
            height: 130px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: #bbb;
            font-size: 12px;
            margin-bottom: 12px;
            background: #fafafa;
        }

        .foto-preview-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .btn-row {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .btn-camera { background: #0d6efd; color: white; }
        .btn-save {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }

        .loading { display: none; color: #999; font-size: 12px; margin-left: 10px; }
        select:disabled { background-color: #f5f5f5; cursor: not-allowed; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📷 Tambah Customer 1 — Simpan Foto sebagai BLOB</h1>
    </div>

    <div class="container">
        <a href="{{ route('customer-data.index') }}" class="btn-back">
            ← Kembali ke Data Customer
        </a>

        <div class="card">
            <!-- ========================================== -->
            <!-- FORM CUSTOMER -->
            <!-- ========================================== -->
            <form method="POST" action="{{ route('customer-data.store-blob') }}"
                  id="formCustomerBlob">
                @csrf
                <input type="hidden" name="foto" id="fotoBase64">

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" placeholder="Nama lengkap" required>
                </div>

                <!-- Dropdown Provinsi -->
                <div class="form-group">
                    <label>Provinsi</label>
                    <select name="provinsi" id="provinsi" required>
                        <option value="">-- Pilih Provinsi --</option>
                    </select>
                    <span class="loading" id="loadingProvinsi">Memuat...</span>
                </div>

                <!-- Dropdown Kota -->
                <div class="form-group">
                    <label>Kota/Kabupaten</label>
                    <select name="kota" id="kota" disabled required>
                        <option value="">-- Pilih Kota --</option>
                    </select>
                    <span class="loading" id="loadingKota">Memuat...</span>
                </div>

                <!-- Dropdown Kecamatan -->
                <div class="form-group">
                    <label>Kecamatan</label>
                    <select name="kecamatan" id="kecamatan" disabled required>
                        <option value="">-- Pilih Kecamatan --</option>
                    </select>
                    <span class="loading" id="loadingKecamatan">Memuat...</span>
                </div>

                <!-- Dropdown Kelurahan -->
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

                    <!-- Preview Foto -->
                    <div class="foto-preview-box" id="previewBox">
                        <span>Belum ada foto</span>
                    </div>

                    <!-- Tombol Kamera & Submit -->
                    <div class="btn-row">
                        <button type="button" class="btn btn-camera" onclick="bukaModal()">
                            📷 Ambil Foto
                        </button>
                        <button type="button" class="btn btn-save btn-submit"
                                data-form="#formCustomerBlob">
                            <span class="btn-text">💾 Simpan Data</span>
                            <span class="btn-loader d-none">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL KAMERA -->
    <!-- ========================================== -->
    <div class="modal-overlay" id="modalKamera"
         style="display: none; position: fixed; inset: 0;
                background: rgba(0,0,0,0.65); z-index: 999;
                align-items: center; justify-content: center;">
        <div class="modal"
             style="background: white; border-radius: 12px; padding: 20px;
                    width: 92%; max-width: 660px;">
            <h3>📷 Modal Ambil Foto</h3>

            <!-- Grid: Video + Canvas -->
            <div class="camera-grid"
                 style="display: grid; grid-template-columns: 1fr 1fr;
                        gap: 12px; margin-bottom: 16px;">
                <div>
                    <div style="font-size: 11px; color: #aaa; margin-bottom: 4px;">
                        Video (Live Preview)
                    </div>
                    <div style="border: 2px solid #ddd; border-radius: 8px;
                                overflow: hidden; aspect-ratio: 4/3; background: #111;">
                        <video id="video" autoplay playsinline
                               style="width: 100%; height: 100%; object-fit: cover;">
                        </video>
                    </div>
                </div>
                <div>
                    <div style="font-size: 11px; color: #aaa; margin-bottom: 4px;">
                        Snapshot (Hasil Capture)
                    </div>
                    <div style="border: 2px solid #ddd; border-radius: 8px;
                                overflow: hidden; aspect-ratio: 4/3; background: #111;">
                        <canvas id="canvas"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </canvas>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div style="display: flex; justify-content: space-between;
                          align-items: center; padding-top: 12px;
                          border-top: 1px solid #eee;">
                <div style="display: flex; gap: 8px;">
                    <select id="selectKamera"
                            style="padding: 8px 12px; border-radius: 8px;
                                   border: 1px solid #ddd; font-size: 13px;">
                    </select>
                    <button onclick="ambilFoto()"
                            style="padding: 8px 14px; background: #198754; color: white;
                                   border: none; border-radius: 8px; cursor: pointer;
                                   font-weight: bold;">
                        📸 Ambil Foto
                    </button>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button onclick="tutupModal()"
                            style="padding: 8px 14px; background: #6c757d; color: white;
                                   border: none; border-radius: 8px; cursor: pointer;
                                   font-weight: bold;">
                        Batal
                    </button>
                    <button onclick="gunakanFoto()"
                            style="padding: 8px 14px;
                                   background: linear-gradient(135deg, #f093fb, #f5576c);
                                   color: white; border: none; border-radius: 8px;
                                   cursor: pointer; font-weight: bold;">
                        ✅ Simpan Foto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/form-handler.js') }}"></script>
    <script>
    let stream = null;
    let devices = [];

    // ==========================================
    // API WILAYAH INDONESIA
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
            const provOption = Array.from(provSelect.options)
                                    .find(opt => opt.value === provinsiName);
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
            const kotaOption = Array.from(kotaSelect.options)
                                   .find(opt => opt.value === kotaName);
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
            const kecOption = Array.from(kecSelect.options)
                                  .find(opt => opt.value === kecamatanName);
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

    // Buka modal kamera
    async function bukaModal() {
        document.getElementById('modalKamera').style.display = 'flex';
        await muatKamera();
    }

    // Tutup modal kamera
    function tutupModal() {
        document.getElementById('modalKamera').style.display = 'none';
        if (stream) {
            stream.getTracks().forEach(t => t.stop());
            stream = null;
        }
    }

    // Muat kamera
    async function muatKamera(deviceId = null) {
        if (stream) {
            stream.getTracks().forEach(t => t.stop());
        }

        try {
            // ==========================================
            // AKSES KAMERA (HTML5 MediaDevices API)
            // ==========================================
            stream = await navigator.mediaDevices.getUserMedia({
                video: deviceId ? { deviceId: { exact: deviceId } } : true
            });

            // Tampilkan video preview
            document.getElementById('video').srcObject = stream;

            // List semua kamera (hanya sekali)
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

                // Switch kamera saat dropdown berubah
                select.addEventListener('change', () => muatKamera(select.value));
            }
        } catch (err) {
            alert('Tidak bisa mengakses kamera: ' + err.message);
        }
    }

    // Ambil foto dari video ke canvas
    function ambilFoto() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');

        // Set ukuran canvas sama dengan video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        // ==========================================
        // CAPTURE SNAPSHOT
        // ==========================================
        canvas.getContext('2d').drawImage(video, 0, 0);
    }

    // Gunakan foto yang sudah di-capture
    function gunakanFoto() {
        const canvas = document.getElementById('canvas');

        // ==========================================
        // CONVERT CANVAS KE BASE64
        // ==========================================
        const base64 = canvas.toDataURL('image/png');

        if (base64 === 'data:,') {
            alert('Klik "Ambil Foto" dulu sebelum menyimpan!');
            return;
        }

        // Simpan base64 ke hidden input
        document.getElementById('fotoBase64').value = base64;

        // Tampilkan preview
        document.getElementById('previewBox').innerHTML =
            `<img src="${base64}" alt="preview">`;

        tutupModal();
    }
    </script>
</body>
</html>
```

### 3.7 Tampilan Data Customer dengan Modal Detail

**File:** `resources/views/customer/data.blade.php`

<div class="code-title">html</div>

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Data Customer</title>
    <style>
        /* Table row hover effect */
        tbody tr {
            cursor: pointer;
            transition: background 0.2s;
        }
        tbody tr:hover {
            background: #fff5f7;
        }

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
        .modal-foto {
            width: 120px;
            height: 120px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #eee;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>👤 Data Customer</h1>
    </div>

    <div class="container">
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
                <!-- ========================================== -->
                <!-- ROW DENGAN DATA ATTRIBUTES -->
                <!-- ========================================== -->
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
                            <img src="data:image/png;base64,{{ $c->foto_blob }}"
                                 alt="foto" width="52" height="52">
                        @elseif($c->foto_path)
                            <img src="{{ asset('storage/' . $c->foto_path) }}"
                                 alt="foto" width="52" height="52">
                        @else
                            <span>—</span>
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

    <!-- ========================================== -->
    <!-- MODAL DETAIL CUSTOMER -->
    <!-- ========================================== -->
    <div class="modal-overlay" id="customerModal">
        <div class="modal">
            <div class="modal-header">
                <h3>👤 Detail Customer</h3>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <div>
                    <div id="modalFoto">
                        <!-- Foto akan di-render di sini -->
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
    // ==========================================
    // MODAL DETAIL CUSTOMER
    // ==========================================
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
            fotoContainer.innerHTML =
                `<img src="data:image/png;base64,${fotoBlob}" class="modal-foto" alt="foto">`;
            tipeFotoContainer.innerHTML =
                '<span class="modal-badge modal-badge-blob">BLOB</span>';
        } else if (fotoPath) {
            fotoContainer.innerHTML =
                `<img src="${window.location.origin}/storage/${fotoPath}" class="modal-foto" alt="foto">`;
            tipeFotoContainer.innerHTML =
                '<span class="modal-badge modal-badge-file">File</span>';
        } else {
            fotoContainer.innerHTML =
                '<div class="modal-foto-placeholder"><span>No Foto</span></div>';
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
```

### 3.8 Anti Double-Submit Protection

**File:** `public/assets/js/form-handler.js`

<div class="code-title">javascript</div>

```javascript
/**
 * form-handler.js
 * ─────────────────────────────────────────────────────────────────────────
 * Reusable script untuk semua form CRUD.
 *
 * CARA KERJA:
 *   Script ini mencari semua tombol dengan class "btn-submit" dan
 *   mencegah double-submit dengan mendisable tombol setelah klik.
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        // Cari semua tombol btn-submit
        var buttons = document.querySelectorAll('.btn-submit');

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                // Temukan form yang dituju
                var formId = button.getAttribute('data-form');
                var form   = document.querySelector(formId);

                if (!form) {
                    console.error('[form-handler.js] Form tidak ditemukan:', formId);
                    return;
                }

                // Cek validasi form
                var valid = form.checkValidity();

                if (!valid) {
                    form.reportValidity();
                    return;
                }

                // ==========================================
                // ANTI DOUBLE SUBMIT
                // ==========================================
                showSpinner(button);

                // Submit form
                form.submit();
            });
        });
    });

    /**
     * Mengubah tombol menjadi spinner dan DISABLE
     * Inilah yang mencegah double submit
     */
    function showSpinner(button) {
        var textEl   = button.querySelector('.btn-text');
        var loaderEl = button.querySelector('.btn-loader');

        button.disabled = true;  // ← KUNCI ANTI DOUBLE SUBMIT
        if (textEl)   textEl.classList.add('d-none');
        if (loaderEl) loaderEl.classList.remove('d-none');
    }

    /**
     * Reset tombol ke kondisi semula
     */
    window.FormHandler = {
        reset: function (button) {
            var textEl   = button.querySelector('.btn-text');
            var loaderEl = button.querySelector('.btn-loader');

            button.disabled = false;
            if (textEl)   textEl.classList.remove('d-none');
            if (loaderEl) loaderEl.classList.add('d-none');
        }
    };

})();
```

### 3.9 Hasil Output

#### Screenshot Tambah Customer (BLOB)
![Form Tambah Customer dengan Kamera](placeholder:customer-form)

#### Screenshot Modal Kamera
![Modal Kamera dengan Video Preview](placeholder:camera-modal)

#### Screenshot Data Customer
![Tabel Data Customer](placeholder:customer-table)

#### Screenshot Modal Detail
![Modal Detail Customer](placeholder:customer-modal)

---

## KESIMPULAN

### 4.1 Hasil Yang Dicapai

1. **Barcode Generation**
   - ✅ Berhasil generate barcode TYPE_CODE_128
   - ✅ Barcode ditampilkan di PDF label harga
   - ✅ Barcode dapat di-scan dengan scanner biasa
   - **Library:** Picqer/php-barcode-generator v3.2

2. **QR Code Generation**
   - ✅ Berhasil generate QR code SVG
   - ✅ QR code ditampilkan di halaman success payment
   - ✅ QR code berisi ID pesanan untuk validasi
   - **Library:** BaconQRCode

3. **Akses Kamera**
   - ✅ Berhasil mengakses kamera dengan HTML5 MediaDevices API
   - ✅ Implementasi dual storage (BLOB + File)
   - ✅ Dropdown wilayah Indonesia (cascade)
   - ✅ Anti double-submit protection
   - ✅ Modal detail customer
   - **API:** navigator.mediaDevices.getUserMedia()

### 4.2 Perbandingan Barcode vs QR Code

| Aspek | Barcode (1D) | QR Code (2D) |
|-------|-------------|--------------|
| **Kapasitas** | 20-25 karakter | Hingga 4,296 karakter |
| **Format** | TYPE_CODE_128 | SVG (vector) |
| **Penggunaan** | Label harga | Payment validation |
| **Kelebihan** | Simple, cepat di-scan | Robust, banyak data |
| **Kekurangan** | Data terbatas | Perlu QR scanner |

### 4.3 Teknologi Yang Digunakan

1. **Backend:**
   - Laravel 12 (PHP Framework)
   - PostgreSQL (Database)
   - Composer (Package Manager)

2. **Frontend:**
   - HTML5 MediaDevices API
   - JavaScript (ES6+)
   - Blade Template Engine

3. **Library:**
   - Picqer/php-barcode-generator (Barcode)
   - BaconQRCode (QR Code)
   - DomPDF (PDF Generation)

4. **External API:**
   - API Wilayah Indonesia (emsifa.github.io)

### 4.4 Saran Pengembangan

1. **QR Code Scanner**
   - Implementasi scanner QR code di halaman vendor
   - Validasi pesanan langsung dari QR

2. **Mobile Optimization**
   - PWA untuk akses kamera lebih baik
   - Responsive design optimization

3. **Enhanced Features**
   - Flash/torch control
   - Zoom control
   - Photo filters

---

## REFERENSI

1. **Picqer/php-barcode-generator**
   - URL: https://github.com/picqer/php-barcode-generator
   - Dokumentasi: Barcode generation library untuk PHP

2. **BaconQRCode**
   - URL: https://github.com/Bacon/BaconQrCode
   - Dokumentasi: QR code generation library

3. **HTML5 MediaDevices API**
   - URL: https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices
   - Dokumentasi: API untuk mengakses kamera

4. **API Wilayah Indonesia**
   - URL: https://github.com/emsifa/api-wilayah-indonesia
   - Dokumentasi: Data wilayah Indonesia lengkap

5. **Laravel Documentation**
   - URL: https://laravel.com/docs
   - Versi: 12.x

---

**Tanggal:** 13 April 2026
**Status:** ✅ SELESAI

---

## LAMPIRAN

### A. Struktur Project

```
framework_smt4/
├── app/
│   └── Http/
│       └── Controllers/
│           ├── BarangController.php          (Barcode)
│           ├── Customer/
│           │   ├── OrderController.php       (QR Code)
│           │   └── CustomerController.php    (Kamera)
│           └── ...
├── resources/
│   └── views/
│       ├── barang/
│       │   └── pdf.blade.php                (Label dengan Barcode)
│       └── customer/
│           ├── success.blade.php            (Payment dengan QR)
│           ├── data.blade.php               (Tabel + Modal)
│           ├── create1.blade.php            (Form BLOB + Kamera)
│           └── create2.blade.php            (Form File + Kamera)
├── public/
│   └── assets/
│       └── js/
│           └── form-handler.js              (Anti double-submit)
├── database/
│   └── migrations/
│       ├── 2026_04_12_201515_create_customers_table.php
│       └── 2026_04_12_233105_fix_foto_blob_column_for_postgresql.php
└── routes/
    └── web.php                              (Routing)
```

### B. Daftar Tabel

**Tabel 1. Perbandingan Storage Foto**

| Metode | Lokasi | Kelebihan | Kekurangan |
|--------|--------|-----------|------------|
| BLOB | Database | Mudah backup | Ukuran DB besar |
| File | Storage/public | DB tetap kecil | Perlu manage file |

**Tabel 2. Browser Support HTML5 MediaDevices**

| Browser | Versi | Support |
|---------|-------|---------|
| Chrome | 53+ | ✅ Full |
| Firefox | 36+ | ✅ Full |
| Safari | 11+ | ✅ Full |
| Edge | 79+ | ✅ Full |
| IE | - | ❌ No Support |

---

**Laporan ini dibuat untuk memenuhi tugas praktikum pemrograman web.**
