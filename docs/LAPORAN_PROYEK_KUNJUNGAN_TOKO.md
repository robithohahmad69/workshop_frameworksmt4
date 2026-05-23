# LAPORAN PROYEK
## Sistem Kunjungan Toko Berbasis Geolocation

---

**Disusun oleh:** Tim Pengembang
**Tanggal:** 23 Mei 2026
**Versi:** 1.0

---

## 1. PENDAHULUAN

### 1.1 Latar Belakang

Seorang pemilik usaha distributor memerlukan sistem untuk memverifikasi bahwa sales-salesnya benar-benar telah mengunjungi toko-toko yang berada dalam area kerja masing-masing sales. Masalah utama adalah **akurasi** lokasi - bagaimana memastikan bahwa benar-benar berada di lokasi toko yang dituju.

### 1.2 Rumusan Masalah

1. Bagaimana menentukan titik koordinat toko yang akurat?
2. Bagaimana memverifikasi keberadaan sales di lokasi toko?
3. Bagaimana menghitung jarak antara posisi sales dan toko dengan memperhitungkan akurasi GPS?

### 1.3 Tujuan

Membangun sistem "Kunjungan Toko" yang memungkinkan:
- Manajemen data toko beserta koordinat lokasinya
- Scan barcode/QR code untuk identifikasi toko
- Verifikasi lokasi sales berdasarkan geolocation
- Pencatatan riwayat kunjungan dengan status validasi

---

## 2. SOLUSI YANG DITAWARKAN

### 2.1 Alur Kerja Sistem

```
┌─────────────────┐      ┌─────────────────┐      ┌─────────────────┐
│   ADMIN/CLIENT  │      │     SYSTEM      │      │     SALES       │
└────────┬────────┘      └────────┬────────┘      └────────┬────────┘
         │                        │                        │
    (1) Input Data Toko           │                        │
    - Barcode                      │                        │
    - Nama Toko                    │                        │
    - Lat, Long, Accuracy          │                        │
         │                        │                        │
         ├───────────────────────>│                        │
         │   Simpan ke Database    │                        │
         │                        │                        │
         │                        │                 (2) Scan Barcode
         │                        │                        │
         │                        │<───────────────────────┤
         │                        │   Kirim Barcode         │
         │                        │                        │
         │                        │   Info Toko             │
         │                        │<───────────────────────┤
         │                        │                        │
         │                        │                 (3) Ambil Lokasi
         │                        │                        │
         │                        │   Kirim Data Lokasi      │
         │                        │<───────────────────────┤
         │                        │                        │
         │                        │   (4) Validasi & Simpan │
         │                        │                        │
         │                        │   Status Kunjungan      │
         │                        │───────────────────────>│
         │                        │                        │
```

### 2.2 Metode Penentuan Lokasi

#### a) Penentuan Titik Toko
Dua cara yang didukung:
1. **Manual via Google Maps** - Mengambil latitude/longitude dari Google Maps
2. **On-Site** - Client datang ke lokasi dan mengambil koordinat langsung

#### b) Penentuan Titik Kunjungan Sales
1. Sales scan barcode/QR code toko
2. Sistem mengembalikan info toko (nama, alamat, koordinat, accuracy)
3. Sales mengambil posisi lokasi saat ini dengan fungsi akurasi tinggi
4. Sistem memvalidasi dan menyimpan kunjungan

---

## 3. IMPLEMENTASI TEKNIS

### 3.1 Struktur Database

#### Tabel `tokos`
| Field | Type | Deskripsi |
|-------|------|-----------|
| id | BIGINT | Primary Key |
| barcode | VARCHAR(20) | Unique ID untuk toko |
| nama_toko | VARCHAR(255) | Nama toko |
| latitude | DECIMAL(10,8) | Latitude toko |
| longitude | DECIMAL(11,8) | Longitude toko |
| accuracy | FLOAT | Akurasi GPS saat pencatatan (meter) |
| created_at | TIMESTAMP | Waktu pembuatan |
| updated_at | TIMESTAMP | Waktu update |

#### Tabel `kunjungans`
| Field | Type | Deskripsi |
|-------|------|-----------|
| id | BIGINT | Primary Key |
| toko_id | BIGINT | Foreign key ke tokos |
| lat_sales | DECIMAL(10,8) | Latitude sales saat kunjungan |
| lng_sales | DECIMAL(11,8) | Longitude sales saat kunjungan |
| accuracy_sales | FLOAT | Akurasi GPS sales (meter) |
| jarak_meter | FLOAT | Jarak aktual pusat ke pusat |
| threshold_efektif | FLOAT | Batas maksimum + akurasi |
| status | ENUM | 'diterima' atau 'ditolak' |
| created_at | TIMESTAMP | Waktu kunjungan |

### 3.2 Formula Haversine

Untuk menghitung jarak antara dua koordinat:

```php
FUNCTION haversine($lat1, $lng1, $lat2, $lng2) {
    $R = 6371000; // Radius bumi dalam meter
    $dLat = ($lat2 - $lat1) * M_PI / 180;
    $dLng = ($lng2 - $lng1) * M_PI / 180;
    $a = sin($dLat / 2) ** 2 +
         cos($lat1 * M_PI / 180) * cos($lat2 * M_PI / 180) *
         sin($dLng / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $R * $c; // Jarak dalam meter
}
```

### 3.3 Validasi Kunjungan

#### Perhitungan Threshold Efektif

```
threshold_efektif = threshold_max + accuracy_toko + accuracy_sales

Dimana:
- threshold_max = 300 meter (jarak maksimum tanpa akurasi)
- accuracy_toko = akurasi GPS saat pencatatan lokasi toko
- accuracy_sales = akurasi GPS sales saat kunjungan
```

#### Ilustrasi

```
Contoh PENERIMA:
[TOKO]──────────────────[SALES]
  ↑                        ↑
acc: 30m                 acc: 20m

jarak_aktual = 290m
threshold_efektif = 300 + 30 + 20 = 350m
290m ≤ 350m → DITERIMA ✓

Contoh PENOLAKAN:
jarak_aktual = 450m
threshold_efektif = 350m
450m > 350m → DITOLAK ✗
```

### 3.4 Fungsi Geolocation JavaScript

Fungsi untuk mengambil lokasi dengan akurasi terbaik:

```javascript
function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
  return new Promise((resolve, reject) => {
    let bestResult = null;
    const startTime = Date.now();

    const watchId = navigator.geolocation.watchPosition(
      (position) => {
        const acc = position.coords.accuracy;

        // Simpan hasil terbaik sejauh ini
        if (!bestResult || acc < bestResult.coords.accuracy) {
          bestResult = position;
        }

        // Kalau sudah cukup akurat, berhenti
        if (acc <= targetAccuracy) {
          navigator.geolocation.clearWatch(watchId);
          resolve(bestResult);
        }

        // Kalau timeout, pakai hasil terbaik yang ada
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
```

---

## 4. FITUR APLIKASI

### 4.1 Menu Utama

| Menu | Deskripsi |
|------|-----------|
| Daftar Toko | Kelola data toko, input lokasi, cetak barcode |
| Kunjungan Toko | Scan barcode, verifikasi lokasi, catat kunjungan |
| Laporan | Lihat riwayat kunjungan dengan statistik |

### 4.2 Halaman Daftar Toko

```
┌─────────────────────────────────────────────────────────────┐
│                      DAFTAR TOKO                             │
├─────────────────────────────────────────────────────────────┤
│  [Tambah Toko]                            [Cetak Semua]     │
├──────┬─────────────┬─────────┬──────────┬─────────┬────────┤
│  No  │   Barcode   │ Nama    │ Latitude │Longitude│ Action │
├──────┼─────────────┼─────────┼──────────┼─────────┼────────┤
│   1  │  TOKO001    │ Toko A  │ -6.2088  │ 106.8456│ 🖨️     │
│   2  │  TOKO002    │ Toko B  │ -6.2234  │ 106.8521│ 🖨️     │
│   3  │  TOKO003    │ Toko C  │ -6.2156  │ 106.8389│ 🖨️     │
└──────┴─────────────┴─────────┴──────────┴─────────┴────────┘
```

### 4.3 Halaman Kunjungan

```
┌─────────────────────────────────────────────────────────────┐
│                    KUNJUNGAN TOKO                            │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│   📷 Scan Barcode Toko                                       │
│   ┌─────────────────────────────────────────┐               │
│   │                                         │               │
│   │       [Camera Scanner Here]             │               │
│   │                                         │               │
│   └─────────────────────────────────────────┘               │
│   atau input manual: [_________]                             │
│                                                               │
│   Setelah scan:                                              │
│   ┌─────────────────────────────────────────┐               │
│   │ Toko: TOKO001 - Toko ABC                │               │
│   │ 📍 Lat: -6.2088, Lng: 106.8456          │               │
│   │ 📏 Accuracy: 30m                         │               │
│   └─────────────────────────────────────────┘               │
│                                                               │
│   [📍 Ambil Lokasi Saya]                                     │
│                                                               │
│   ┌─────────────────────────────────────────┐               │
│   │ Posisi Anda:                             │               │
│   │ Lat: -6.2091, Lng: 106.8462             │               │
│   │ Accuracy: 25m                            │               │
│   │ Jarak: 45m dari toko                     │               │
│   └─────────────────────────────────────────┘               │
│                                                               │
│                   [✓ Check-In Kunjungan]                    │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

---

## 5. TEKNOLOGI YANG DIGUNAKAN

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 11 (PHP) |
| Frontend | Blade Template, JavaScript |
| Database | MySQL |
| Geolocation | HTML5 Geolocation API |
| Barcode Scanner | HTML5-QRCode |
| Formula | Haversine Formula |

---

## 6. HASIL DAN PEMBAHASAN

### 6.1 Skenario Pengujian

#### Skenario 1: Kunjungan Valid (Diterima)
- Toko: TOKO001 (Lat: -6.2088, Lng: 106.8456, Acc: 30m)
- Posisi Sales: (Lat: -6.2091, Lng: 106.8462, Acc: 25m)
- Jarak Aktual: 45m
- Threshold Efektif: 300 + 30 + 25 = 355m
- **Hasil: DITERIMA** ✓

#### Skenario 2: Kunjungan Invalid (Ditolak)
- Toko: TOKO002 (Lat: -6.2234, Lng: 106.8521, Acc: 25m)
- Posisi Sales: (Lat: -6.2300, Lng: 106.8600, Acc: 30m)
- Jarak Aktual: 850m
- Threshold Efektif: 300 + 25 + 30 = 355m
- **Hasil: DITOLAK** ✗

### 6.2 Kelebihan Sistem

1. ✅ **Akurasi Tinggi** - Menggunakan threshold efektif yang memperhitungkan akurasi GPS
2. ✅ **Anti-Curang** - Sales tidak bisa check-in dari jarak jauh
3. ✅ **Real-time** - Validasi dilakukan langsung saat kunjungan
4. ✅ **Riwayat Lengkap** - Semua kunjungan tercatat dengan detail koordinat

### 6.3 Keterbatasan

1. ⚠️ **Ketergantungan GPS** - Akurasi bergantung pada kondisi GPS perangkat
2. ⚠️ **Indoor Location** - GPS kurang akurat di dalam gedung
3. ⚠️ **Internet Connection** - Membutuhkan koneksi internet untuk scan dan kirim data

---

## 7. KESIMPULAN DAN SARAN

### 7.1 Kesimpulan

Sistem Kunjungan Toko Berbasis Geolocation berhasil dibangun dengan fitur:
1. Manajemen data toko dengan barcode/QR code
2. Verifikasi lokasi sales menggunakan geolocation
3. Validasi jarak dengan formula Haversine
4. Pencatatan riwayat kunjungan dengan status

Sistem mampu memverifikasi keberadaan sales di lokasi toko dengan memperhitungkan akurasi GPS, sehingga mengurangi kemungkinan kecurangan.

### 7.2 Saran Pengembangan

1. Menambahkan fitur foto bukti kunjungan
2. Integrasi dengan sistem absensi sales
3. Dashboard analitik performa sales
4. Notifikasi kunjungan real-time ke admin
5. Mode offline untuk area dengan sinyal lemah

---

## 8. LAMPIRAN

### Lampiran 1: Struktur Folder Project

```
framework_smt4/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── TokoController.php
│   │       └── KunjunganController.php
│   └── Models/
│       ├── Toko.php
│       └── Kunjungan.php
├── resources/
│   └── views/
│       ├── toko/
│       │   └── index.blade.php
│       └── kunjungan/
│           └── index.blade.php
├── database/
│   └── migrations/
│       ├── create_tokos_table.php
│       └── create_kunjungans_table.php
└── routes/
    └── web.php
```

### Lampiran 2: Route Configuration

```php
// Toko Management
Route::get('/toko', [TokoController::class, 'index'])->name('toko.index');
Route::post('/toko/generate-barcode', [TokoController::class, 'generateBarcode']);
Route::post('/toko/store', [TokoController::class, 'store']);
Route::post('/toko/scan', [TokoController::class, 'scanBarcode']);

// Kunjungan
Route::get('/kunjungan', [KunjunganController::class, 'index'])->name('kunjungan.index');
Route::post('/kunjungan/simpan', [KunjunganController::class, 'simpan']);
```

---

**Dokumen ini dibuat sebagai laporan resmi proyek Sistem Kunjungan Toko**

© 2026 - Tim Pengembang
