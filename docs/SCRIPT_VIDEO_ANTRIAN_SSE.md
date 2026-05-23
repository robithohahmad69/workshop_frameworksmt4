# SCRIPT VIDEO PRESENTASI
## Sistem Antrian Real-Time dengan Server-Sent Events (SSE)

**Durasi:** 7-10 menit
**Gaya:** Edukatif, Teknis, Modern

---

## VIDEO SCRIPT

---

### [0:00 - 0:45] INTRO + TEORI SSE

**(Visual: Intro dengan animasi node/graph yang streaming)**

**NARRATOR:**
"Pernahkah Anda berpikir, bagaimana sebuah aplikasi web bisa mengirim update real-time ke browser tanpa user perlu refresh halaman?"

**(Visual: Ilustrasi polling vs SSE side by side)**

**NARRATOR:**
"Jawabannya adalah Server-Sent Events, atau SSE. Ini adalah teknologi HTML5 yang memungkinkan server mengirim data ke browser secara real-time melalui satu koneksi HTTP yang tetap terbuka."

**(Visual: Flow diagram SSE)**

**NARRATOR:**
"Bayangkan seperti siaran radio - server terus memancarkan data, dan browser hanya perlu 'mendengarkan'. Tidak perlu polling berulang, browser otomatis menerima update saat ada data baru."

**(Visual: Logo muncul - "Sistem Antrian Real-Time dengan SSE")**

**NARRATOR:**
"Hari ini kita akan membangun Sistem Antrian Real-Time menggunakan SSE di Laravel."

---

### [0:45 - 1:30] MASALAH DAN SOLUSI

**(Visual: Ilustrasi antrian konvensional dengan kertas tiket)**

**NARRATOR:**
"Sistem antrian konvensional menggunakan kertas tiket. Ada banyak masalah: tiket mudah hilang, tidak ada notifikasi suara otomatis, dan sulit monitor status secara real-time."

**(Visual: Diagram arsitektur sistem baru)**

**NARRATOR:**
"Solusinya adalah Sistem Antrian Digital dengan 3 role: Guest untuk mendaftar, Admin untuk mengelola antrian, dan Papan Antrian untuk tampilan publik di ruang tunggu."

**(Visual: Flow alur lengkap)**

**NARRATOR:**
"Semua disinkronkan secara real-time menggunakan SSE. Ketika admin memanggil nomor, semua client langsung menerima update tanpa refresh."

---

### [1:30 - 3:30] DEMO WEB APLIKASI

**(Visual: Screen recording browser - membuka localhost:8000/guest)**

**NARRATOR:**
"Mari kita lihat aplikasinya. Ini adalah halaman pendaftaran untuk guest. Desainnya simple dan modern."

**(Visual: Input nama "Budi Santoso", klik tombol)**

**NARRATOR:**
"Guest hanya perlu memasukkan nama, lalu klik 'Ambil Nomor Antrian'. Sistem memproses dengan AJAX, tanpa refresh halaman."

**(Visual: Tab baru terbuka menampilkan tiket)**

**NARRATOR:**
"Tab baru otomatis terbuka menampilkan tiket antrian. Ini pengganti kertas tiket konvensional - nomor antrian 005 untuk Budi Santoso."

**(Visual: Buka tab baru - localhost:8000/admin/antrian)**

**NARRATOR:**
"Sekarang buka dashboard admin di tab lain. Di sini petugas bisa mengelola seluruh antrian. Perhatikan indicator status di pojok kanan atas - menunjukkan koneksi SSE aktif."

**(Visual: Scroll ke daftar menunggu, hover ke nomor 005)**

**NARRATOR:**
"Antrian Budi sudah muncul di daftar menunggu. Ini terjadi secara real-time via SSE - admin tidak perlu refresh halaman."

**(Visual: Klik tombol 'Panggil Berikutnya')**

**NARRATOR:**
"Admin menekan tombol 'Panggil Berikutnya'. Status langsung berubah. Nomor 005 sekarang berada di posisi 'Sedang Dipanggil'."

**(Visual: Buka tab baru - localhost:8000/papan)**

**NARRATOR:**
"Ini adalah halaman Papan Antrian - tampilan untuk ruang tunggu. Perhatikan, nomor yang dipanggil langsung muncul di sini secara real-time!"

**(Visual: Zoom ke nomor 005 yang berubah dengan animasi)**

**NARRATOR:**
"Dan yang menarik... suara notifikasi otomatis berbunyi!"

**(Visual: Replay audio dingdong + suara "Nomor antrian 005. Budi Santoso, silakan masuk.")**

**NARRATOR:**
"Pertama bunyi dingdong, kemudian Web Speech API membacakan nomor dan nama. Ini menggunakan text-to-speech bawaan browser."

**(Visual: Kembali ke admin, klik 'Selesai')**

**NARRATOR:**
"Setelah selesai melayani, admin menekan tombol 'Selesai'. Antrian 005 hilang dari daftar aktif."

**(Visual: Panggil nomor berikutnya, lalu panggil lagi tanpa selesaikan)**

**NARRATOR:**
"Coba kita panggil nomor berikutnya, tapi selesaikan dulu yang sekarang... Jika tidak diselesaikan, status akan berubah jadi 'terlambat'."

**(Visual: Nomor pindah ke list terlambat, klik 'Panggil Ulang')**

**NARRATOR:**
"Antrian terlambat masuk ke daftar terpisah. Admin bisa memanggil ulang kapan saja dengan tombol 'Panggil Ulang'."

---

### [3:30 - 6:00] BEHIND THE CODE - SSE IMPLEMENTATION

**(Visual: VS Code terbuka, menampilkan struktur project)**

**NARRATOR:**
"Sekarang mari kita lihat bagaimana SSE diimplementasikan di Laravel."

**(Visual: Buka routes/web.php, scroll ke route antrian)**

**NARRATOR:**
"Pertama, di routes kita definisikan endpoint untuk setiap fitur. Perhatikan route SSE ini - `/sse/antrian`. Route ini tidak menggunakan session middleware agar tidak blocking."

**(Visual: Buka AntrianController.php, scroll ke method stream())**

**NARRATOR:**
"Ini adalah bagian terpenting - method `stream()` yang mengimplementasikan SSE."

**(Visual: Highlight bagian session()->save())**

**NARRATOR:**
"Pertama, kita save dan tutup session. Ini penting karena session locking di Laravel akan memblokir request lain."

**(Visual: Highlight response()->stream())**

**NARRATOR:**
"Kemudian kita gunakan `response()->stream()` dari Laravel. Di dalamnya ada infinite loop yang terus mengirim data ke browser."

**(Visual: Highlight echo event dan data)**

**NARRATOR:**
"Perhatikan format SSE - `event: antrian-update` adalah nama event, dan `data:` berisi JSON payload. Setiap pesan dipisahkan dengan baris kosong."

**(Visual: Highlight ob_flush() dan flush())**

**NARRATOR:**
"`ob_flush()` dan `flush()` sangat penting - ini mengirim output ke browser segera, bukan menunggu script selesai."

**(Visual: Highlight header Content-Type: text/event-stream)**

**NARRATOR:**
"Header `Content-Type: text/event-stream` memberi tahu browser bahwa ini adalah SSE stream. `Cache-Control: no-cache` mencegah buffering."

**(Visual: Scroll ke method buildState())**

**NARRATOR:**
"Data antrian diambil dari database dan dibentuk menjadi array state. State ini disimpan ke Cache agar SSE bisa membaca tanpa query database berulang."

**(Visual: Scroll ke method updateCache())**

**NARRATOR:**
"Setiap kali ada perubahan - panggil, selesai, atau daftar baru - method `updateCache()` dipanggil untuk update state di Cache."

---

### [6:00 - 7:00] CLIENT SIDE - JAVASCRIPT

**(Visual: Buka resources/views/antrian/admin.blade.php)**

**NARRATOR:**
"Sekarang ke client side. Ini adalah view admin dengan Blade template."

**(Visual: Scroll ke bagian JavaScript EventSource)**

**NARRATOR:**
"Di sini kita inisialisasi EventSource - API browser untuk menerima SSE."

**(Visual: Highlight new EventSource())**

**NARRATOR:**
"`new EventSource('{{ route('antrian.stream') }}')` membuka koneksi SSE ke endpoint yang tadi kita lihat."

**(Visual: Highlight addEventListener('antrian-update'))**

**NARRATOR:**
"Kita listen ke event `antrian-update`. Setiap kali server mengirim event ini, callback dijalankan."

**(Visual: Highlight updateUI(data))**

**NARRATOR:**
"Data di-parse dari JSON, lalu `updateUI()` memperbarui tampilan. Karena semua client menggunakan kode yang sama, semua akan update bersamaan."

**(Visual: Buka papan.blade.php, scroll ke fungsi suarakanNomor())**

**NARRATOR:**
"Di halaman papan, ada tambahan fitur suara. Fungsi `suarakanNomor()` menggunakan Web Speech API."

**(Visual: Highlight SpeechSynthesisUtterance)**

**NARRATOR:**
"`SpeechSynthesisUtterance` membuat objek pesan suara. Kita set bahasa Indonesia, kecepatan 0.9, dan volume maksimal."

**(Visual: Highlight speechSynthesis.speak())**

**NARRATOR:**
"`speechSynthesis.speak()` memutar suara. Dipanggil setelah audio dingdong selesai."

**(Visual: Scroll ke bagian audio dingdong)**

**NARRATOR:**
"Audio file dingdong disimpan di folder public. Browser memutar audio ini dulu, baru setelah onended, speech synthesis berjalan."

---

### [7:00 - 7:45] MIDDLEWARE TANPA SESSION

**(Visual: Buka app/Http/Middleware/WithoutSession.php)**

**NARRATOR:**
"Ada satu komponen penting lagi - Middleware WithoutSession."

**(Visual: Scroll isi middleware)**

**NARRATOR:**
"Middleware ini menonaktifkan session dan encryption cookies. Sangat penting untuk SSE karena session locking akan membuat stream terblokir."

**(Visual: Buka bootstrap/app.php, scroll ke alias middleware)**

**NARRATOR:**
"Di bootstrap/app.php, kita daftarkan middleware ini sebagai alias. Lalu di routes, kita apply ke route SSE."

**(Visual: Highlight withoutMiddleware())**

**NARRATOR:**
"Chain `withoutMiddleware()` memberitahu Laravel untuk skip session middleware pada route ini."

---

### [7:45 - 8:30] ARSITEKTUR & TEKNOLOGI

**(Visual: Diagram arsitektur lengkap dengan animasi)**

**NARRATOR:**
"Secara arsitektur, sistem ini mengikuti pattern berikut: Guest, Admin, dan Papan semuanya terhubung ke SSE endpoint."

**(Visual: Animation flow data)**

**NARRATOR:**
"Client membuka koneksi SSE dan terus menerima data. Admin mengirim action via POST request - panggil, selesai, panggil ulang."

**(Visual: Tampilkan database icon dan cache icon)**

**NARRATOR:**
"Setiap action update database dan cache. SSE loop membaca cache dan mengirim ke semua client yang terhubung."

**(Visual: Icon teknologi dengan label)**

**NARRATOR:**
"Teknologi yang digunakan: Laravel 11 untuk backend, Blade template untuk frontend, MySQL untuk database, dan Laravel Cache untuk shared state."

---

### [8:30 - 9:15] FITUR & KELEBIHAN

**(Visual: Screenshot halaman-halaman dengan label fitur)**

**NARRATOR:**
"Fitur-fitur utama sistem ini:"

**(Visual: Screenshot guest.blade.php)**
- "Pendaftaran antrian sederhana - hanya nama"
- "Tiket digital di tab baru"

**(Visual: Screenshot admin.blade.php)**
- "Dashboard admin dengan kontrol lengkap"
- "Real-time status indicator"
- "Panggil, selesai, panggil ulang terlambat"

**(Visual: Screenshot papan.blade.php)**
- "Display besar untuk ruang tunggu"
- "Audio notifikasi otomatis"
- "Grid antrian menunggu"

**(Visual: Checklist kelebihan)**

**NARRATOR:**
"Kelebihan menggunakan SSE:"
- "Real-time sync tanpa refresh"
- "Auto-reconnect built-in dari browser"
- "Implementasi sederhana"
- "HTTP biasa, tidak perlu WebSocket server"

---

### [9:15 - 10:00] KESIMPULAN & CTA

**(Visual: Summary keypoints dengan icon)**

**NARRATOR:**
"Sistem Antrian Real-Time dengan SSE memberikan solusi lengkap untuk manajemen antrian modern. Dari pendaftaran hingga pemanggilan, semua terintegrasi secara real-time."

**(Visual: Screenshot multi-tab syncing)**

**NARRATOR:**
"Dengan SSE, kita mengubah pengalaman antrian dari manual dan kertas menjadi digital dan real-time."

**(Visual: Logo dengan tagline)**

**NARRATOR:**
"Terima kasih telah menyimak. Implementasikan SSE di project Anda berikutnya untuk pengalaman real-time yang modern."

**(Music fade out, end screen dengan contact info)**

---

## CATATAN PRODUKSI

### Visual Elements Needed:
1. Intro animation (node/streaming graph)
2. **Screen recording web aplikasi (FULL)** - demo semua role
3. **Screen recording VS Code** - show SSE implementation
4. Animasi diagram (architectural flow, SSE concept)
5. End screen dengan CTA

### Screen Recording Checklist - Web:
- [ ] Halaman guest - input nama
- [ ] Submit form, tab baru tiket terbuka
- [ ] Halaman admin - list antrian, status indicator
- [ ] Panggil antrian
- [ ] Halaman papan - update real-time
- [ ] Audio dingdong + speech berbunyi
- [ ] Selesaikan antrian
- [ ] Panggil berikutnya (jadi terlambat)
- [ ] Panggil ulang terlambat

### Screen Recording Checklist - Code:
- [ ] VS Code - struktur project
- [ ] routes/web.php - route definitions
- [ ] AntrianController.php - stream() method
- [ ] Highlight session()->save()
- [ ] Highlight response()->stream()
- [ ] Highlight echo SSE format
- [ ] Highlight ob_flush() and flush()
- [ ] Highlight headers
- [ ] buildState() and updateCache()
- [ ] admin.blade.php - EventSource
- [ ] papan.blade.php - Web Speech API
- [ ] WithoutSession.php middleware
- [ ] bootstrap/app.php - middleware registration

### Audio:
- Background music: Tech/Modern style, tidak mengganggu
- Voiceover: Clear, moderate pace
- Sound effects: Subtle untuk transitions
- **Sesuai highlight**: Audio dingdong + speech harus terdengar jelas

### Style Guide:
- Warna primary: Ungu gradient (#667eea → #764ba2)
- Warna accent: Biru untuk papan (#1e3c72 → #2a5298)
- Font: Modern sans-serif untuk overlay
- Code font: Monospace untuk highlight code
- Animation: Smooth, tidak berlebihan

### Video Editing Tips:
1. **SSE diagram**: Use animated arrows untuk flow data
2. **Web demo**: Use multi-view (split screen) untuk menunjukkan real-time sync
3. **Code sections**: Use slow scroll dan highlight baris penting
4. **Audio section**: Replay the dingdong+speech agar jelas terdengar
5. **Cursor**: Gunakan cursor highlight untuk fokus perhatian

---

## STORYBOARD DETAIL

| Scene | Durasi | Visual | Audio | Notes |
|-------|--------|--------|-------|-------|
| Intro | 45s | Intro animation, SSE diagram | Voice + Music | Animated |
| Problem | 45s | Ilustrasi antrian konvensional | Voice | Simple |
| Solution | 30s | Arsitektur diagram | Voice | Animated flow |
| Web Demo - Guest | 45s | Form input, submit, tiket | Voice | Real action |
| Web Demo - Admin | 45s | Dashboard, panggil antrian | Voice | Show list |
| Web Demo - Papan | 45s | Real-time update, suara | Voice | **Show audio** |
| Web Demo - Complete | 30s | Selesai, terlambat, recall | Voice | Full flow |
| Code - Routes | 30s | web.php routes | Voice | Show SSE route |
| Code - Stream | 90s | AntrianController stream() | Voice | **Detailed** |
| Code - Helpers | 30s | buildState, updateCache | Voice | Quick |
| Client - JS | 60s | admin.blade.php EventSource | Voice | Show JS |
| Client - Audio | 45s | papan.blade.php Speech API | Voice | **Detail** |
| Middleware | 45s | WithoutSession + bootstrap | Voice | Important |
| Architecture | 45s | Full diagram animation | Voice | Comprehensive |
| Features | 45s | Checklist + screenshots | Voice | Alternating |
| Outro | 45s | Summary + CTA | Voice + Music | End card |

**Total:** ~10 menit

---

## SCREEN RECORDING GUIDE

### Web Demo Recording:
```bash
# 1. Jalankan server
php artisan serve

# 2. Buka 3 tab browser:
#    - Tab 1: localhost:8000/guest
#    - Tab 2: localhost:8000/admin/antrian
#    - Tab 3: localhost:8000/papan

# 3. Recording tips:
#    - Use 1920x1080 resolution
#    - Gunakan split-screen untuk menunjukkan real-time sync
#    - Pastikan audio dingdong tercapture jelas
#    - Practice alur sebelum recording
```

### Code Demo Recording:
```bash
# 1. Buka VS Code
# 2. Use Zen Mode untuk clean view
# 3. Font size: 18-20 untuk readability
# 4. Use slow scroll (5-10 lines/second)
# 5. Highlight penting: gunakan cursor atau box highlight

# Key sections to capture:
# - SSE format (event:, data:)
# - flush() dan ob_flush()
# - EventSource initialization
# - Web Speech API setup
```

### Post-Production:
- Tambahkan zoom in/out untuk focus
- Tambahkan arrow/circle highlights
- Tambahkan text overlays untuk key terms
- Color code: Green untuk success, Red untuk error
- Tambahkan transitions antar scene
- **Audio enhancement**: Normalize volume untuk dingdong+speech

---

## KEY HIGHLIGHTS VIDEO

Pastikan menekankan poin-poin ini di video:

1. **SSE Concept**: Server push ke client tanpa polling
2. **Format SSE**: `event:` dan `data:` dengan baris kosong
3. **flush() penting**: Tanpa ini, data tidak terkirim real-time
4. **Session blocking**: Why we need WithoutSession middleware
5. **EventSource API**: Browser built-in untuk receive SSE
6. **Web Speech API**: Text-to-speech untuk notifikasi
7. **Real-time sync**: Semua client update bersamaan
8. **Auto-reconnect**: Browser handle disconnect otomatis

---

© 2026 - Script Video Presentasi Sistem Antrian SSE
