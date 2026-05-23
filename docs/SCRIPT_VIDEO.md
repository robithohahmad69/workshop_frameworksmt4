# SCRIPT VIDEO PRESENTASI
## Sistem Kunjungan Toko Berbasis Geolocation

**Durasi:** 5-7 menit
**Gaya:** Edukatif, Profesional, Modern

---

## VIDEO SCRIPT

---

### [0:00 - 0:30] INTRO

**(Visual: Intro dengan musik upbeat modern)**

**NARRATOR:**
"Pernahkah Anda sebagai pemilik bisnis distributor merasa waspada... apakah sales Anda benar-benar mengunjungi toko-toko dalam area kerjanya?"

**(Visual: Animasi sales dengan tanda tanya, peta dengan lokasi-lokasi toko)**

**NARRATOR:**
"Kehilangan produktivitas karena sales tidak benar-benar melakukan kunjungan bisa merugikan bisnis. Tapi bagaimana cara memverifikasinya secara akurat?"

**(Visual: Logo muncul - "Sistem Kunjungan Toko")**

**NARRATOR:**
"Perkenalkan... Sistem Kunjungan Toko Berbasis Geolocation."

---

### [0:30 - 1:00] MASALAH DAN SOLUSI

**(Visual: Ilustrasi masalah - sales check-in dari kafe, bukan di toko)**

**NARRATOR:**
"Masalah utama adalah AKURASI. Bagaimana memastikan sales benar-benar berada di lokasi toko yang dituju?"

**(Visual: Split screen - masalah di kiri, solusi di kanan)**

**NARRATOR:**
"Solusinya ada 3:"
1. "Pertama, tentukan titik koordinat toko dengan akurat"
2. "Kedua, sales scan barcode untuk identifikasi toko"
3. "Ketiga, verifikasi lokasi dengan geolocation accuracy"

---

### [1:00 - 2:30] DEMO WEB APLIKASI

**(Visual: Screen recording browser - membuka halaman utama)**

**NARRATOR:**
"Mari kita lihat langsung aplikasinya. Ini adalah halaman utama sistem Kunjungan Toko."

**(Visual: Scroll ke menu sidebar, klik menu "Toko")**

**NARRATOR:**
"Di menu Toko, admin dapat melihat semua daftar toko yang terdaftar. Tersedia informasi barcode, nama toko, koordinat, dan tombol untuk cetak barcode."

**(Visual: Klik tombol "Tambah Toko", form input muncul)**

**NARRATOR:**
"Untuk menambah toko baru, admin cukup isi form ini. Barcode dapat digenerate otomatis. Lalu masukkan nama toko, latitude, longitude, dan accuracy GPS."

**(Visual: Isi form dan submit, muncul notifikasi sukses)**

**NARRATOR:**
"Setelah disimpan, toko baru langsung muncul di daftar. Sangat mudah dan cepat."

**(Visual: Pindah ke halaman "Kunjungan")**

**NARRATOR:**
"Sekarang kita lihat dari sisi sales. Di halaman Kunjungan, sales akan mengunjungi toko dengan cara scan barcode."

**(Visual: Klik tombol kamera untuk scan barcode, scan berhasil)**

**NARRATOR:**
"Sales scan barcode toko yang akan dikunjungi. Sistem langsung menampilkan informasi lengkap toko tersebut."

**(Visual: Info toko muncul - nama, lat, long, accuracy)**

**NARRATOR:**
"Terlihat di sini informasi toko: nama, koordinat lengkap, dan akurasi GPS saat pencatatan."

**(Visual: Klik tombol "Ambil Lokasi Saya")**

**NARRATOR:**
"Sales kemudian menekan tombol 'Ambil Lokasi Saya'. Sistem akan mencari posisi dengan akurasi terbaik."

**(Visual: Loading spinner, lalu muncul hasil lokasi)**

**NARRATOR:**
"Dalam beberapa detik, posisi sales didapatkan. Dengan akurasi 25 meter."

**(Visual: Klik "Check-In Kunjungan", muncul notifikasi sukses)**

**NARRATOR:**
"Sales menekan tombol Check-In. Sistem menghitung jarak dan memberikan hasil... KUNJUNGAN BERHASIL! Jarak hanya 45 meter dari toko, well within threshold."

**(Visual: Pindah ke halaman laporan/history)**

**NARRATOR:**
"Semua kunjungan tercatat di laporan. Admin dapat melihat riwayat lengkap: waktu kunjungan, nama toko, jarak, dan status validasi."

---

### [2:30 - 4:00] BEHIND THE CODE

**(Visual: VS Code terbuka, menampilkan struktur project)**

**NARRATOR:**
"Sekarang mari kita lihat di balik layar. Sistem ini dibangun dengan Laravel 11, framework PHP yang powerful dan modern."

**(Visual: Zoom ke folder app/Http/Controllers, buka TokoController.php)**

**NARRATOR:**
"Di TokoController, terdapat fungsi untuk menampilkan daftar toko, generate barcode otomatis, menyimpan toko baru, dan scan barcode."

**(Visual: Scroll ke method generateBarcode)**

**NARRATOR:**
"Fungsi generateBarcode ini membuat kode unik 'TOKO' diikuti 3 digit angka berurutan. Jadi setiap toko memiliki identifier yang unik."

**(Visual: Buka KunjunganController.php, scroll ke method simpan)**

**NARRATOR:**
"Di KunjunganController, method simpan menangani validasi kunjungan sales. Pertama, divalidasi input dari sales."

**(Visual: Scroll ke fungsi haversine)**

**NARRATOR:**
"Nah ini bagian penting - fungsi haversine. Ini formula matematika untuk menghitung jarak antara dua koordinat geografis."

**(Visual: Highlight baris perhitungan haversine)**

**NARRATOR:**
"R = 6371000 adalah radius bumi dalam meter. Fungsi ini mengembalikan jarak dalam meter antara posisi toko dan posisi sales."

**(Visual: Scroll ke perhitungan threshold efektif)**

**NARRATOR:**
"Threshold efektif dihitung sebagai 300 meter ditambah akurasi toko dan akurasi GPS sales. Kunjungan diterima jika jarak kurang dari atau sama dengan threshold."

**(Visual: Buka folder app/Models, buka Toko.php)**

**NARRATOR:**
"Model Toko merepresentasikan tabel tokos di database. Terdapat fillable fields dan casts untuk memastikan tipe data yang benar."

**(Visual: Buka Kunjungan.php)**

**NARRATOR:**
"Model Kunjungan merepresentasikan tabel kunjungans. Menyimpan data lengkap setiap kunjungan termasuk status diterima atau ditolak."

**(Visual: Buka folder database/migrations)**

**NARRATOR:**
"Migrations mendefinisikan struktur database. create_tokos_table dan create_kunjungans_table membuat tabel-tabel yang diperlukan."

**(Visual: Buka resources/views/kunjungan/index.blade.php)**

**NARRATOR:**
"Di frontend, kita menggunakan Blade template dari Laravel. View ini menampilkan halaman kunjungan dengan barcode scanner."

**(Visual: Scroll ke bagian JavaScript getAccuratePosition)**

**NARRATOR:**
"Fungsi getAccuratePosition menggunakan HTML5 Geolocation API. Fungsi ini terus memantau posisi sampai mendapatkan akurasi terbaik."

**(Visual: Highlight baris watchPosition dan clearWatch)**

**NARRATOR:**
"Menggunakan watchPosition, sistem mendapatkan posisi berulang kali dan menyimpan yang paling akurat. Setelah target tercapai atau timeout, watch dihentikan."

**(Visual: Buka routes/web.php)**

**NARRATOR:**
"Routes mendefinisikan URL endpoint. Setiap fungsi controller diakses melalui route yang didefinisikan di sini."

---

### [4:00 - 4:45] TEKNOLOGI & ARSITEKTUR

**(Visual: Diagram arsitektur sistem)**

**NARRATOR:**
"Secara arsitektur, sistem ini mengikuti pattern MVC dari Laravel."

**(Visual: Animation flow dari User -> Route -> Controller -> Model -> View)**

**NARRATOR:**
"User mengakses aplikasi melalui browser, request diteruskan ke route, kemudian ke controller. Controller memproses dengan bantuan model dan mengembalikan view."

**(Visual: Icon teknologi dengan animasi)**

**NARRATOR:**
"Teknologi yang digunakan:"
- "Laravel 11 untuk backend"
- "Blade Template untuk frontend"
- "MySQL sebagai database"
- "HTML5 Geolocation API untuk location services"
- "Barcode scanner library"

**(Visual: Diagram perhitungan jarak dengan animasi)**

**NARRATOR:**
"Formula Haversine menghitung jarak Great Circle antara dua titik di bumi. Memperhitungkan kelengkungan bumi untuk hasil yang akurat."

---

### [4:45 - 5:30] KELEBIHAN & FITUR

**(Visual: List fitur dengan icon checklist, alternating dengan screenshot web)**

**NARRATOR:**
"Kelebihan sistem ini:"

**(Visual: Screenshot halaman daftar toko)**
- ✓ "Manajemen toko lengkap dengan barcode"
- ✓ "Generate barcode otomatis"

**(Visual: Screenshot halaman kunjungan dengan scanner)**
- ✓ "Barcode scanner untuk identifikasi cepat"
- ✓ "Geolocation dengan akurasi tinggi"

**(Visual: Screenshot notifikasi sukses)**
- ✓ "Validasi real-time dengan Haversine"
- ✓ "Threshold efektif yang adil"

**(Visual: Screenshot halaman laporan)**
- ✓ "Riwayat lengkap setiap kunjungan"
- ✓ "Admin dashboard untuk monitoring"

---

### [5:30 - 6:00] KESIMPULAN & CTA

**(Visual: Summary keypoints dengan icon)**

**NARRATOR:**
"Sistem Kunjungan Toko memberikan solusi lengkap untuk memverifikasi kunjungan sales. Dengan teknologi geolocation dan formula Haversine, akurasi verifikasi sangat tinggi."

**(Visual: Split screen - admin dashboard di kiri, sales app di kanan)**

**NARRATOR:**
"Mudah digunakan untuk admin dan sales. Anti-curang dengan validasi lokasi yang ketapi adil."

**(Visual: Logo dengan tagline)**

**NARRATOR:**
"Implementasikan sistem ini dan pastikan setiap kunjungan sales Anda berharga."

**(Musik fade out, end screen dengan contact info)**

---

## CATATAN PRODUKSI

### Visual Elements Needed:
1. Intro animation (5-10 detik)
2. **Screen recording web aplikasi (FULL)** - demo semua fitur
3. **Screen recording VS Code** - show code implementation
4. Icon/illustration untuk setiap section
5. Animasi untuk penjelasan teknis (Haversine, MVC)
6. End screen dengan CTA

### Screen Recording Checklist - Web:
- [ ] Halaman daftar toko dengan tabel
- [ ] Form tambah toko
- [ ] Generate barcode
- [ ] Scan barcode di halaman kunjungan
- [ ] Ambil lokasi geolocation
- [ ] Check-in dan notifikasi hasil
- [ ] Halaman laporan/history

### Screen Recording Checklist - Code:
- [ ] VS Code - struktur project
- [ ] TokoController.php - generateBarcode method
- [ ] KunjunganController.php - simpan method
- [ ] KunjunganController.php - haversine function
- [ ] Models - Toko.php dan Kunjungan.php
- [ ] Migrations - structure database
- [ ] Views - kunjungan/index.blade.php
- [ ] JavaScript - getAccuratePosition function
- [ ] Routes - web.php

### Audio:
- Background music: Upbeat, modern, tidak mengganggu
- Voiceover: Clear, profesional, moderate pace
- Sound effects: Subtle untuk transitions

### Style Guide:
- Warna primary: Biru profesional
- Warna accent: Hijau (success), Merah (rejected)
- Font: Modern sans-serif untuk overlay
- Code font: Monospace untuk highlight code
- Animation: Smooth, tidak berlebihan

### Video Editing Tips:
1. **Web demo**: Use smooth zoom in/out untuk highlight penting
2. **Code sections**: Use slow scroll dan highlight baris yang dijelaskan
3. **Transitions**: Gunakan fade atau slide antar section
4. **Overlay**: Tambahkan text overlay untuk keyword penting
5. **Cursor**: Jaga cursor movement smooth dan intentional

---

## STORYBOARD DETAIL

| Scene | Durasi | Visual | Audio | Notes |
|-------|--------|--------|-------|-------|
| Intro | 30s | Logo, peta, tanda tanya | Voice + Music | Animated |
| Problem | 30s | Ilustrasi masalah | Voice | Simple animation |
| Web Demo - Toko | 45s | Screen recording daftar toko | Voice | Show scrolling |
| Web Demo - Add Toko | 20s | Form input, submit | Voice | Show typing |
| Web Demo - Kunjungan | 45s | Scan barcode, info toko | Voice | Real scan |
| Web Demo - Location | 30s | Ambil lokasi, hasil | Voice | Show loading |
| Web Demo - Check-in | 20s | Check-in, notifikasi | Voice | Highlight success |
| Code - Structure | 30s | VS Code folder structure | Voice | Quick scroll |
| Code - TokoController | 40s | generateBarcode method | Voice | Highlight code |
| Code - KunjunganController | 60s | simpan & haversine | Voice | Explain logic |
| Code - Models | 30s | Toko & Kunjungan models | Voice | Quick overview |
| Code - Migrations | 20s | Database structure | Voice | Show schema |
| Code - Views | 30s | Blade template, JS | Voice | Show geolocation |
| Architecture | 45s | MVC diagram animation | Voice | Animated flow |
| Features | 45s | Checklist + screenshots | Voice | Alternating |
| Outro | 30s | Summary + CTA | Voice + Music | End card |

**Total:** ~6-7 menit

---

## SCREEN RECORDING GUIDE

### Web Demo Recording:
```bash
# Run development server
php artisan serve

# Recommended browser: Chrome/Edge
# Resolution: 1920x1080
# Use smooth mouse movements
# Practice the flow before recording
```

### Code Demo Recording:
```bash
# Open VS Code
# Use Zen Mode for clean view
# Font size: 18-20 for readability
# Use slow scroll (5-10 lines/second)
# Highlight important lines with cursor
```

### Post-Production:
- Add zoom in/out for focus
- Add arrow/circle highlights
- Add text overlays for key terms
- Color code: Green for success, Red for error
- Add transitions between scenes

---

© 2026 - Script Video Presentasi Kunjungan Toko
Updated with Web & Code Demonstration
