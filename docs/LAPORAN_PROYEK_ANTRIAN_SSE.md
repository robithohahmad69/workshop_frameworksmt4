# LAPORAN PROYEK
## Sistem Antrian Real-Time dengan Server-Sent Events (SSE)

---

**Disusun oleh:** Tim Pengembang
**Tanggal:** 23 Mei 2026
**Versi:** 1.0

---

## 1. PENDAHULUAN

### 1.1 Latar Belakang

Sistem antrian konvensional menggunakan kertas tiket yang memiliki beberapa kelemahan:
- Tiket fisik mudah hilang
- Tidak ada notifikasi suara otomatis
- Sulit memonitor status antrian secara real-time
- Tidak efisien untuk manajemen antrian yang kompleks

Dengan perkembangan teknologi web modern, khususnya HTML5 Server-Sent Events (SSE), kita dapat membangun sistem antrian digital yang real-time tanpa perlu refresh halaman secara manual.

### 1.2 Rumusan Masalah

1. Bagaimana membangun sistem antrian yang sinkron secara real-time antar multiple user?
2. Bagaimana mengimplementasikan SSE di Laravel untuk streaming data?
3. Bagaimana mengintegrasikan Web Speech API untuk notifikasi suara?

### 1.3 Tujuan

Membangun **Sistem Antrian Digital Real-Time** dengan fitur:
- Pendaftaran antrian oleh guest (hanya dengan nama)
- Dashboard admin untuk kelola antrian
- Papan antrian publik dengan notifikasi suara
- Sinkronisasi real-time menggunakan SSE

---

## 2. DASAR TEORI

### 2.1 Server-Sent Events (SSE)

Server-Sent Events adalah teknologi web standar (HTML5) yang memungkinkan server mengirim data secara **unidirectional** (satu arah) — dari server ke browser — melalui koneksi HTTP yang tetap terbuka.

#### Kelebihan SSE:
| SSE | WebSocket | Long Polling |
|-----|-----------|--------------|
| Server → Client saja | Full-duplex | Request-Response |
| HTTP biasa | ws:// atau wss:// | HTTP biasa |
| Auto-reconnect | Manual reconnect | Buka-tutup berulang |
| Simple implementasi | Lebih kompleks | Boros resource |

#### Format Data SSE:
```
event: antrian-update
data: {"nomor": 5, "nama": "Budi"}

```

Setiap pesan dipisahkan oleh baris kosong ganda (`\n\n`).

### 2.2 Web Speech API

API bawaan browser untuk text-to-speech yang memungkinkan aplikasi web berbicara kepada user.

```javascript
const pesan = new SpeechSynthesisUtterance('Nomor antrian 132. Budi, silakan masuk.');
pesan.lang = 'id-ID';  // Bahasa Indonesia
pesan.rate = 0.9;      // Kecepatan
window.speechSynthesis.speak(pesan);
```

---

## 3. DESAIN SISTEM

### 3.1 Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────────┐
│                         SISTEM ANTRIAN                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────────────┐ │
│  │    GUEST    │    │    ADMIN    │    │   PAPAN ANTRIAN      │ │
│  │   Browser   │    │   Browser   │    │     (Display)        │ │
│  └──────┬──────┘    └──────┬──────┘    └──────────┬──────────┘ │
│         │                  │                       │            │
│         │ SSE              │ SSE                   │ SSE        │
│         │                  │                       │            │
│         └──────────────────┼───────────────────────┘            │
│                            │                                    │
│                    ┌───────▼────────┐                          │
│                    │  SSE Endpoint  │                          │
│                    │  /sse/antrian  │                          │
│                    └───────┬────────┘                          │
│                            │                                    │
│                    ┌───────▼────────┐                          │
│                    │  Laravel Cache  │                          │
│                    │ (Shared State)  │                          │
│                    └───────┬────────┘                          │
│                            │                                    │
│         ┌──────────────────┼──────────────────┐                │
│         │                  │                  │                │
│    ┌────▼────┐       ┌────▼────┐       ┌────▼────┐           │
│    │ /daftar │       │ /panggil │       │ /selesai │           │
│    │  POST   │       │   POST   │       │   POST   │           │
│    └────┬────┘       └────┬────┘       └────┬────┘           │
│         │                  │                  │                │
│         └──────────────────┼──────────────────┘                │
│                            │                                    │
│                    ┌───────▼────────┐                          │
│                    │     MySQL      │                          │
│                    │   Database     │                          │
│                    └────────────────┘                          │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

### 3.2 Role Pengguna

| Role | Deskripsi | Akses |
|------|-----------|-------|
| **Guest** | Pengunjung yang mendaftar antrian | `/guest` - form pendaftaran |
| **Admin** | Petugas yang mengelola antrian | `/admin/antrian` - dashboard |
| **Papan** | Tampilan publik di ruang tunggu | `/papan` - display besar |

### 3.3 Alur Kerja

```
1. GUEST mendaftar di /guest
   ↓
2. Server simpan ke DB + update Cache
   ↓
3. Tab baru terbuka menampilkan tiket
   ↓
4. ADMIN melihat antrian baru via SSE (real-time)
   ↓
5. ADMIN klik "Panggil Berikutnya"
   ↓
6. Server update state DB + Cache
   ↓
7. SSE push update ke semua client
   ↓
8. PAPAN menerima event, update tampilan + bunyi suara
```

---

## 4. IMPLEMENTASI

### 4.1 Struktur Database

#### Tabel `antreans`

| Field | Type | Deskripsi |
|-------|------|-----------|
| id | BIGINT | Primary Key |
| nomor | INTEGER | Nomor urut antrian |
| nama | VARCHAR(255) | Nama tamu |
| status | ENUM | menunggu, dipanggil, selesai, terlambat |
| created_at | TIMESTAMP | Waktu pendaftaran |
| updated_at | TIMESTAMP | Waktu update |

#### Migration:
```php
Schema::create('antreans', function (Blueprint $table) {
    $table->id();
    $table->integer('nomor');
    $table->string('nama');
    $table->enum('status', ['menunggu', 'dipanggil', 'selesai', 'terlambat'])
          ->default('menunggu');
    $table->timestamps();
});
```

### 4.2 Routes

```php
// Public routes
Route::get('/guest', [AntrianController::class, 'guestIndex'])->name('antrian.guest');
Route::get('/tiket/{id}', [AntrianController::class, 'tiket'])->name('antrian.tiket');
Route::get('/papan', [AntrianController::class, 'papan'])->name('antrian.papan');
Route::post('/antrian/daftar', [AntrianController::class, 'daftar'])->name('antrian.daftar');

// Admin routes (auth middleware)
Route::get('/admin/antrian', [AntrianController::class, 'adminIndex'])->name('antrian.admin');
Route::post('/antrian/panggil', [AntrianController::class, 'panggil'])->name('antrian.panggil');
Route::post('/antrian/panggil-terlambat', [AntrianController::class, 'panggilTerlambat'])->name('antrian.panggilTerlambat');
Route::post('/antrian/selesai', [AntrianController::class, 'selesai'])->name('antrian.selesai');

// SSE endpoint (tanpa session middleware)
Route::get('/sse/antrian', [AntrianController::class, 'stream'])->name('antrian.stream');
    ->withoutMiddleware([\App\Http\Middleware\EncryptCookies::class, \Illuminate\Session\Middleware\StartSession::class]);
```

### 4.3 SSE Controller

```php
public function stream(Request $request)
{
    // Tutup session agar tidak blocking
    $request->session()->save();

    // Bersihkan output buffer
    if (ob_get_level() > 0) {
        ob_end_clean();
    }

    ignore_user_abort(true);
    set_time_limit(0);

    return response()->stream(function () {
        while (true) {
            // Ambil data terbaru dari cache
            $currentData = Cache::get('antrian_state', $this->buildState());

            // Kirim event SSE
            echo 'event: antrian-update' . PHP_EOL;
            echo 'data: ' . json_encode($currentData) . PHP_EOL;
            echo PHP_EOL;

            // Flush ke browser
            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();

            // Cek apakah client masih terhubung
            if (connection_aborted()) {
                break;
            }

            sleep(1);
        }
    }, 200, [
        'Content-Type'      => 'text/event-stream',
        'Cache-Control'     => 'no-cache',
        'X-Accel-Buffering' => 'no',  // Penting untuk Nginx
    ]);
}
```

### 4.4 Client Side SSE (JavaScript)

```javascript
// Membuka koneksi SSE
const eventSource = new EventSource('/sse/antrian');

// Mendengarkan event bernama 'antrian-update'
eventSource.addEventListener('antrian-update', function(e) {
    const data = JSON.parse(e.data);
    updateUI(data);
});

// Handle error dan auto-reconnect
eventSource.onerror = function(err) {
    console.error('SSE Error:', err);
    // Browser akan otomatis reconnect
};
```

### 4.5 Web Speech API

```javascript
function suarakanNomor(nomor, nama) {
    if (!('speechSynthesis' in window)) return;

    window.speechSynthesis.cancel();

    const pesan = new SpeechSynthesisUtterance(
        `Nomor antrian ${nomor}. ${nama}, silakan masuk.`
    );
    pesan.lang = 'id-ID';  // Bahasa Indonesia
    pesan.rate = 0.9;      // Kecepatan
    pesan.pitch = 1;       // Nada
    pesan.volume = 1;      // Volume

    // Cari voice bahasa Indonesia
    const voices = window.speechSynthesis.getVoices();
    const indonesianVoice = voices.find(v => v.lang.startsWith('id'));
    if (indonesianVoice) pesan.voice = indonesianVoice;

    window.speechSynthesis.speak(pesan);
}

// Dipanggil setelah audio dingdong selesai
dingdong.onended = () => suarakanNomor(nomor, nama);
```

---

## 5. FITUR APLIKASI

### 5.1 Halaman Guest (`/guest`)

**Screenshot:**
```
┌─────────────────────────────────────┐
│         [ICON TIKET]                │
│                                     │
│      Ambil Antrian                  │
│   Silakan isi nama Anda             │
│                                     │
│  ┌─────────────────────────────┐   │
│  │ 👤 Nama Lengkap              │   │
│  │ ┌─────────────────────────┐ │   │
│  │ │ Masukkan nama Anda       │ │   │
│  │ └─────────────────────────┘ │   │
│  └─────────────────────────────┘   │
│                                     │
│  [Ambil Nomor Antrian]              │
│                                     │
└─────────────────────────────────────┘
```

**Fitur:**
- Form input nama sederhana
- AJAX submit tanpa refresh
- Tab baru otomatis terbuka dengan tiket

### 5.2 Halaman Tiket (`/tiket/{id}`)

**Screenshot:**
```
┌─────────────────────────────────────┐
│                                     │
│         TIKET ANTRIAN                │
│                                     │
│      ┌───────────────┐              │
│      │               │              │
│      │      005      │              │
│      │               │              │
│      └───────────────┘              │
│                                     │
│          Budi Santoso               │
│                                     │
│   Silakan menunggu di ruang         │
│   tunggu hingga nomor Anda         │
│   dipanggil                         │
│                                     │
└─────────────────────────────────────┘
```

### 5.3 Halaman Admin (`/admin/antrian`)

**Screenshot:**
```
┌──────────────────────────────────────────────────────────┐
│  Kelola Antrian                      ● Terhubung (SSE)   │
├──────────────────────────────────────────────────────────┤
│  ┌───────────────────┐  ┌────────────────────────────┐  │
│  │ Sedang Dipanggil  │  │ Menunggu           (5)     │  │
│  │                   │  │                            │  │
│  │       005         │  │ ┌───┐ Budi                │  │
│  │    Budi Santoso   │  │ │001│                      │  │
│  │                   │  │ └───┘                      │  │
│  │ [Panggil][Selesai]│  │ ┌───┐ Ani                 │  │
│  └───────────────────┘  │ │002│                      │  │
│                          │ └───┘                      │  │
│  ┌────────────────────────────────────────────────┐   │  │
│  │ Terlambat                              (2)     │   │  │
│  │ ┌───┐ Cici [Panggil Ulang]                   │   │  │
│  │ │003│                                       │   │  │
│  │ └───┘                                       │   │  │
│  └────────────────────────────────────────────────┘   │  │
└──────────────────────────────────────────────────────────┘
```

**Fitur:**
- Real-time update via SSE
- Status koneksi indicator
- Panggil antrian berikutnya
- Selesaikan antrian
- Panggil ulang antrian terlambat

### 5.4 Halaman Papan (`/papan`)

**Screenshot:**
```
┌────────────────────────────────────────────────────────────────┐
│                    PAPAN ANTRIAN                               │
│         Silakan menunggu, nomor akan dipanggil                 │
├────────────────────────────────────────────────────────────────┤
│                                                                │
│  ┌─────────────────────┐  ┌────────────────────────────────┐ │
│  │   Sedang Dipanggil  │  │    Antrian Menunggu     (5)    │ │
│  │                     │  │                                │ │
│  │                     │  │  ┌───┐ ┌───┐ ┌───┐ ┌───┐ ┌───┐│ │
│  │                     │  │  │001│ │002│ │006│ │007│ │008││ │
│  │        005          │  │  └───┘ └───┘ └───┘ └───┘ └───┘│ │
│  │                     │  │                                │ │
│  │    Budi Santoso     │  │  ┌───┐ ┌───┐ ┌───┐ ┌───┐ ┌───┐│ │
│  │                     │  │  │009│ │010│ │011│ │012│ │013││ │
│  └─────────────────────┘  │  └───┘ └───┘ └───┘ └───┘ └───┘│ │
│                           └────────────────────────────────┘ │
│                                                                │
│   Mohon menunggu di ruang tunggu hingga nomor Anda dipanggil  │
└────────────────────────────────────────────────────────────────┘
```

**Fitur:**
- Display besar untuk TV/Monitor
- Audio dingdong + speech
- Animasi saat nomor berubah
- Real-time sync via SSE

---

## 6. PENGUJIAN

### 6.1 Skenario Pengujian

| Step | Aksi | Hasil Expected |
|------|------|----------------|
| 1 | Buka `/guest` di browser | Form pendaftaran tampil |
| 2 | Input nama "Budi", submit | Tab baru terbuka dengan tiket |
| 3 | Buka `/admin/antrian` di tab lain | Antrian Budi muncul real-time |
| 4 | Klik "Panggil Berikutnya" | Status berubah jadi "dipanggil" |
| 5 | Buka `/papan` di tab lain | Nomor berubah + suara berbunyi |
| 6 | Klik "Selesai" | Antrian pindah ke selesai |
| 7 | Panggil berikutnya saat Budi tidak hadir | Status jadi "terlambat" |
| 8 | Klik "Panggil Ulang" terlambat | Terlambat dipanggil lagi |

### 6.2 Troubleshooting

| Masalah | Solusi |
|--------|--------|
| SSE tidak connect | Pastikan route tidak pakai session middleware |
| Data tidak update | Cek `ob_flush()` dan `flush()` terpanggil |
| Suara tidak bunyi | Browser butuh interaksi user dulu (user gesture) |
| Nginx buffer issue | Tambah header `X-Accel-Buffering: no` |
| Cache tidak persist | Gunakan `file` atau `database` driver, bukan `array` |

---

## 7. KELEBIHAN DAN KETERBATASAN

### 7.1 Kelebihan

1. ✅ **Real-time sync** - Semua client update simultaneously
2. ✅ **Tanpa refresh** - SSE push data otomatis
3. ✅ **Auto-reconnect** - Browser handle disconnect secara otomatis
4. ✅ **Notifikasi suara** - Dingdong + Web Speech API
5. ✅ **Multi-role** - Guest, Admin, Papan terpisah
6. ✅ **Tanpa kertas** - Tiket digital di browser

### 7.2 Keterbatasan

1. ⚠️ **One-way communication** - SSE hanya server → client
2. ⚠️ **Browser support** - IE tidak support (modern browsers OK)
3. ⚠️ **Audio policy** - Perlu interaksi user untuk play audio
4. ⚠️ **Connection limit** - HTTP/1.1 limited connection per domain

---

## 8. KESIMPULAN DAN SARAN

### 8.1 Kesimpulan

Sistem Antrian Real-Time dengan SSE berhasil dibangun dengan semua fitur utama berfungsi:
- Pendaftaran antrian guest
- Dashboard admin dengan kontrol lengkap
- Papan antrian publik dengan notifikasi suara
- Sinkronisasi real-time menggunakan SSE

Penggunaan SSE terbukti efektif untuk use case ini karena sifatnya yang one-way (server push ke client) dan auto-reconnect built-in dari browser.

### 8.2 Saran Pengembangan

1. **Multi-loket** - Tambah dukungan beberapa loket layanan
2. **Estimasi waktu** - Hitung estimasi tunggu berdasarkan rata-rata
3. **SMS/WhatsApp** - Kirim notifikasi ke nomor antrian
4. **Mobile App** - Build native app untuk guest
5. **Laporan** - Tambah halaman statistik dan laporan harian
6. **Laravel Reverb** - Untuk skala produksi yang lebih besar

---

## 9. LAMPIRAN

### Lampiran 1: Struktur Project

```
framework_smt4/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── AntrianController.php
│   │   └── Middleware/
│   │       └── WithoutSession.php
│   └── Models/
│       └── Antrian.php
├── resources/
│   └── views/
│       └── antrian/
│           ├── guest.blade.php
│           ├── tiket.blade.php
│           ├── admin.blade.php
│           └── papan.blade.php
├── database/
│   └── migrations/
│       └── 2026_05_19_081815_create_antreans_table.php
├── public/
│   └── dingdong.mp3
└── routes/
    └── web.php
```

### Lampiran 2: Referensi

1. MDN Web Docs — Using server-sent events
2. W3Schools — HTML5 Server-Sent Events
3. Laravel Docs — HTTP Responses: Streamed Responses
4. Web Speech API Specification

---

**Dokumen ini dibuat sebagai laporan resmi proyek Sistem Antrian Real-Time**

© 2026 - Tim Pengembang
