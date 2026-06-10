# 📹 SCRIPT VIDEO PRESENTASI (FOCUS CODING)
## Sistem Absensi NFC - Laravel & Web NFC API

---

## 🎯 STRUKTUR VIDEO (15-20 Menit)

```
00:00 - 01:00    Pembukaan & Overview Proyek
01:00 - 02:30    Struktur Database & ERD
02:30 - 05:00    Coding Backend (Model, Controller, Routes)
05:00 - 08:00    Coding Frontend NFC (Web NFC API)
08:00 - 10:00    Halaman Views (Scanner, Warga, Riwayat)
10:00 - 12:00    Demo Aplikasi Live
12:00 - 13:30    Pengujian & Hasil
13:30 - 15:00    Kesimpulan & Penutup
```

---

# 🎬 SCRIPT DETAIL

## PART 1: PEMBUKAAN (00:00 - 01:00)

> **Visual:** Tampilan awal project di VS Code
>
> **Narrator:**
> "Assalamu'alaikum. Pada video ini, saya akan menjelaskan implementasi sistem absensi berbasis NFC menggunakan Laravel dan Web NFC API.
>
> Proyek ini memanfaatkan Web NFC API pada browser Chrome Android untuk membaca serial number dari kartu e-KTP, kemudian mengirimnya ke backend Laravel untuk verifikasi dan pencatatan kehadiran.
>
> Saya akan menjelaskan langsung dari kode yang sudah saya implementasikan, mulai dari database migration, model, controller, hingga frontend JavaScript."

---

## PART 2: DATABASE (01:00 - 02:30)

> **Visual:** VS Code - file `database/migrations/2026_05_26_174640_create_warga_table.php`
>
> **Narrator:**
> "Mari kita mulai dari struktur database. Sistem ini memiliki dua tabel utama.
>
> Pertama, tabel `warga` untuk menyimpan data warga yang terdaftar. Perhatikan di sini: field `nik` dibatasi 16 digit sesuai format NIK Indonesia, dan field `nfc_serial` dibuat unique untuk memastikan satu kartu hanya terdaftar untuk satu warga."
>
> **🎯 SHOW CODE:**
> ```php
> $table->string('nik', 16)->unique();
> $table->string('nfc_serial')->unique();
> ```

> **Visual:** VS Code - file `database/migrations/2026_05_26_174656_create_riwayat_scan_table.php`
>
> **Narrator:**
> "Kedua, tabel `riwayat_scan` untuk menyimpan semua aktivitas scan. Yang penting di sini: `warga_id` saya buat nullable dengan nullOnDelete.
>
> Kenapa nullable? Karena sistem mencatat SEMUA scan, termasuk kartu yang TIDAK terdaftar. Jadi jika kartu tidak dikenal, warga_id akan bernilai null, tapi riwayat tetap tersimpan untuk audit."
>
> **🎯 SHOW CODE:**
> ```php
> $table->foreignId('warga_id')
>       ->nullable()
>       ->constrained('warga')
>       ->nullOnDelete();
> $table->string('status'); // 'dikenal' atau 'tidak_dikenal'
> ```

---

## PART 3: BACKEND CODING (02:30 - 05:00)

> **Visual:** VS Code - file `app/Models/Warga.php`
>
> **Narrator:**
> "Sekarang ke Model. Di `Warga.php`, kita definisikan fillable fields dan relasi hasMany ke RiwayatScan. Artinya, satu warga bisa memiliki banyak riwayat scan selama masa aktifnya di sistem."
>
> **🎯 SHOW CODE:**
> ```php
> protected $fillable = ['nama', 'nik', 'nfc_serial', 'alamat'];
>
> public function riwayatScan()
> {
>     return $this->hasMany(RiwayatScan::class);
> }
> ```

> **Visual:** VS Code - file `app/Models/RiwayatScan.php`
>
> **Narrator:**
> "Sedangkan di `RiwayatScan.php`, ada relasi belongsTo ke Warga. Gunanya untuk eager loading saat mengambil riwayat - kita bisa dapat data warga dalam satu query dengan with('warga')."
>
> **🎯 SHOW CODE:**
> ```php
> public function warga()
> {
>     return $this->belongsTo(Warga::class);
> }
> ```

> **Visual:** VS Code - file `app/Http/Controllers/WargaController.php`
>
> **Narrator:**
> "Selanjutnya WargaController. Method `store` ini menarik - ada validasi bahwa NIK harus 16 digit dan nfc_serial harus unik. Jadi tidak bisa ada duplikasi NIK atau serial number di sistem."
>
> **🎯 SHOW CODE:**
> ```php
> public function store(Request $request)
> {
>     $request->validate([
>         'nama'       => 'required|string|max:255',
>         'nik'        => 'required|digits:16|unique:warga,nik',
>         'nfc_serial' => 'required|unique:warga,nfc_serial',
>         'alamat'     => 'nullable|string',
>     ]);
>
>     Warga::create($request->all());
>     // ...
> }
> ```

> **Visual:** VS Code - file `app/Http/Controllers/NfcController.php` - method `scan()`
>
> **Narrator:**
> "Ini adalah bagian terpenting: NfcController method `scan()`. Method ini adalah API endpoint yang dipanggil dari frontend.
>
> Pertama, kita terima serial_number dari request. Kedua, cari warga berdasarkan nfc_serial. Ketiga, tentukan status - dikenal atau tidak. Keempat, SELALU catat riwayat scan."
>
> **🎯 SHOW CODE:**
> ```php
> public function scan(Request $request)
> {
>     $request->validate(['serial_number' => 'required|string']);
>
>     $warga = Warga::where('nfc_serial', $request->serial_number)->first();
>     $status = $warga ? 'dikenal' : 'tidak_dikenal';
>
>     RiwayatScan::create([
>         'warga_id'      => $warga?->id,  // null jika tidak dikenal
>         'serial_number' => $request->serial_number,
>         'status'        => $status,
>         'waktu_scan'    => now(),
>     ]);
>
>     if ($warga) {
>         return response()->json([
>             'status' => 'dikenal',
>             'pesan'  => 'Selamat datang, ' . $warga->nama . '!',
>             'warga'  => ['nama' => $warga->nama, 'nik' => $warga->nik]
>         ]);
>     }
>
>     return response()->json(['status' => 'tidak_dikenal'], 404);
> }
> ```

> **Visual:** VS Code - file `routes/web.php`
>
> **Narrator:**
> "Untuk routing, saya buat 4 route utama. GET /nfc untuk halaman scanner, POST /api/nfc/scan untuk endpoint API, GET /warga untuk halaman daftar warga, dan POST /warga untuk menyimpan data warga baru."
>
> **🎯 SHOW CODE:**
> ```php
> Route::get('/warga', [WargaController::class, 'index'])->name('warga.index');
> Route::post('/warga', [WargaController::class, 'store'])->name('warga.store');
> Route::get('/nfc', [NfcController::class, 'index'])->name('nfc.index');
> Route::post('/api/nfc/scan', [NfcController::class, 'scan'])->name('nfc.scan');
> ```

---

## PART 4: FRONTEND NFC (05:00 - 08:00)

> **Visual:** VS Code - file `resources/views/nfc/nfc.blade.php` - bagian HTML
>
> **Narrator:**
> "Sekarang masuk ke frontend. Di halaman scanner, ada tombol untuk mengaktifkan NFC, div untuk status, dan div untuk hasil scan. Semua wrapped dalam card dari template admin panel."
>
> **🎯 SHOW CODE:**
> ```blade
> <button id="tombol-scan" onclick="startScan()"
>         class="btn btn-gradient-success btn-lg btn-block mb-3">
>     <i class="mdi mdi-nfc"></i> Aktifkan Scanner NFC
> </button>
>
> <div id="status" class="alert alert-secondary">Belum aktif.</div>
> <div id="hasil" style="display:none"></div>
> ```

> **Visual:** VS Code - file `resources/views/nfc/nfc.blade.php` - JavaScript part 1
>
> **Narrator:**
> "Bagian JavaScript ini yang paling menarik. Pertama, saya cek apakah browser mendukung Web NFC dengan memeriksa NDEFReader di window. Jika tidak support, user akan dapat pesan error."
>
> **🎯 SHOW CODE:**
> ```javascript
> if (!('NDEFReader' in window)) {
>     setStatus('danger', '❌ Browser tidak mendukung Web NFC');
>     return;
> }
> ```

> **Visual:** VS Code - file `resources/views/nfc/nfc.blade.php` - JavaScript part 2
>
> **Narrator:**
> "Kemudian saya buat instance NDEFReader dan panggil method scan(). Ini akan mengaktifkan NFC reader di browser dan meminta izin kepada user."
>
> **🎯 SHOW CODE:**
> ```javascript
> const ndef = new NDEFReader();
> await ndef.scan();
>
> setStatus('success', '✅ NFC aktif. Tempelkan e-KTP ke belakang HP...');
> ```

> **Visual:** VS Code - file `resources/views/nfc/nfc.blade.php` - JavaScript part 3
>
> **Narrator:**
> "Setelah NFC aktif, saya tambahkan event listener untuk event 'reading'. Event ini akan trigger setiap kali kartu NFC didekatkan ke HP. Di dalam callback, saya mendapatkan serialNumber dari kartu."
>
> **🎯 SHOW CODE:**
> ```javascript
> ndef.addEventListener('reading', async ({ serialNumber, message }) => {
>     setStatus('warning', '📖 Kartu terbaca! Memproses...');

>     console.log('Serial Number:', serialNumber);
>     // ...
> });
> ```

> **Visual:** VS Code - file `resources/views/nfc/nfc.blade.php` - JavaScript part 4
>
> **Narrator:**
> "Setelah dapat serialNumber, saya kirim ke backend Laravel menggunakan fetch API. Perhatikan saya sertakan header X-CSRF-TOKEN - ini wajib untuk Laravel. Body request adalah JSON dengan serial_number."
>
> **🎯 SHOW CODE:**
> ```javascript
> const response = await fetch('/api/nfc/scan', {
>     method: 'POST',
>     headers: {
>         'Content-Type': 'application/json',
>         'X-CSRF-TOKEN': csrfToken,
>     },
>     body: JSON.stringify({ serial_number: serialNumber }),
> });
>
> const data = await response.json();
> ```

> **Visual:** VS Code - file `resources/views/nfc/nfc.blade.php` - JavaScript part 5
>
> **Narrator:**
> "Terakhir, berdasarkan response dari server, saya render hasil ke UI. Jika status 'dikenal', tampilkan pesan selamat datang dengan data warga. Jika 'tidak_dikenal', tampilkan pesan bahwa kartu belum terdaftar."
>
> **🎯 SHOW CODE:**
> ```javascript
> if (data.status === 'dikenal') {
>     hasilEl.className = 'alert alert-success mt-3';
>     hasilEl.innerHTML = `
>         <h5>✅ ${data.pesan}</h5>
>         <p class="mb-1"><b>NIK:</b> ${data.warga.nik}</p>
>         <p class="mb-1"><b>Alamat:</b> ${data.warga.alamat ?? '-'}</p>
>         <small>Serial: ${serialNumber}</small>
>     `;
> } else {
>     hasilEl.className = 'alert alert-danger mt-3';
>     hasilEl.innerHTML = `<h5>❌ ${data.pesan}</h5>`;
> }
> ```

---

## PART 5: VIEWS (08:00 - 10:00)

> **Visual:** VS Code - file `resources/views/nfc/warga.blade.php`
>
> **Narrator:**
> "Untuk halaman daftar warga, saya buat form di kolom kiri dan tabel daftar di kolom kanan. Form menggunakan validation errors dari Laravel untuk feedback jika input tidak valid. NIK dibatasi maxlength 16 di HTML untuk memudahkan user."
>
> **🎯 SHOW CODE:**
> ```blade
> <input type="text" name="nik" maxlength="16"
>        class="form-control @error('nik') is-invalid @enderror" required>
>
> @error('nik')
>     <div class="invalid-feedback">{{ $message }}</div>
> @enderror
> ```

> **Visual:** VS Code - file `resources/views/nfc/riwayat.blade.php`
>
> **Narrator:**
> "Halaman riwayat menampilkan 50 scan terakhir dengan eager loading menggunakan `with('warga')`. Ini mengoptimalkan query - tidak ada N+1 problem. Status badge menggunakan class berbeda untuk dikenal (hijau) dan tidak dikenal (merah)."
>
> **🎯 SHOW CODE:**
> ```blade
> @if($r->status === 'dikenal')
>     <span class="badge badge-gradient-success">✅ Dikenal</span>
> @else
>     <span class="badge badge-gradient-danger">❌ Tidak Dikenal</span>
> @endif
> ```

> **Visual:** VS Code - file `app/Http/Controllers/NfcController.php` - method `riwayat()`
>
> **Narrator:**
> "Di controller, method riwayat menggunakan eager loading dengan take(50) untuk membatasi hasil. latest() mengurutkan dari yang terbaru."
>
> **🎯 SHOW CODE:**
> ```php
> public function riwayat()
> {
>     $riwayat = RiwayatScan::with('warga')
>                            ->latest()
>                            ->take(50)
>                            ->get();
>     return view('nfc.riwayat', compact('riwayat'));
> }
> ```

---

## PART 6: DEMO LIVE (10:00 - 12:00)

> **Visual:** Screen recording browser Chrome di Android
>
> **Narrator:**
> "Sekarang mari kita lihat aplikasi yang berjalan. Saya buka browser Chrome di Android dan akses halaman /nfc.
>
> Saya tekan tombol 'Aktifkan Scanner NFC'. Browser meminta izin untuk menggunakan NFC - ini adalah security feature dari Web NFC API. Saya allow.
>
> Sekarang status berubah 'NFC aktif'. Saya tempelkan e-KTP ke belakang HP."
>
> **🎯 YANG DITUNJUKKAN:**
> - Halaman `/nfc` di browser
> - Klik tombol
> - Allow permission
> - Kartu didekatkan

> **Visual:** Hasil scan muncul di layar
>
> **Narrator:**
> "Kartu terbaca! Serial number adalah 04:A3:B2:C1:D5:E6 dan sistem mengenali kartu ini. Muncul pesan 'Selamat datang, Ahmad Robithoh!' dengan NIK dan alamat lengkap.
>
> Sekarang saya coba kartu lain yang belum terdaftar. Kartu terbaca tapi muncul pesan 'Kartu tidak terdaftar' - sesuai dengan logika yang sudah kita buat."
>
> **🎯 YANG DITUNJUKKAN:**
> - Hasil scan kartu dikenal
> - Hasil scan kartu tidak dikenal

> **Visual:** Halaman riwayat scan
>
> **Narrator:**
> "Saya buka halaman riwayat. Terlihat semua scan tercatat dengan lengkap - waktu, serial, nama, dan status. Untuk kartu dikenal badge hijau, untuk tidak dikenal badge merah. Data tersimpan real-time."

---

## PART 7: PENGUJIAN & HASIL (12:00 - 13:30)

> **Visual:** Tabel hasil pengujian
>
> **Narrator:**
> "Berikut ringkasan pengujian. Test case pertama: pendaftaran warga dengan NIK valid - berhasil. NIK duplikat - ditolak. NIK kurang 16 digit - ditolak.
>
> Test case kedua: scan kartu terdaftar - berhasil menampilkan data. Test case ketiga: scan kartu tidak dikenal - berhasil memberikan pesan error dan tetap mencatat riwayat.
>
> Dari sisi performa, waktu scan kurang dari 1 detik dan response server sekitar 100-300ms."

---

## PART 8: KESIMPULAN (13:30 - 15:00)

> **Visual:** Slide kesimpulan
>
> **Narrator:**
> "Sebagai kesimpulan:
>
> 1. Web NFC API berhasil diimplementasikan untuk membaca serial e-KTP
> 2. Database dengan relasi one-to-many berfungsi baik
> 3. Backend Laravel berhasil memproses dan menyimpan data
> 4. Frontend memberikan feedback real-time
>
> Keterbatasan utama adalah Web NFC API hanya didukung di Chrome Android. Untuk pengembangan lanjut, bisa ditambah fitur jadwal absensi, dashboard statistik, dan export laporan.
>
> Demikian presentasi implementasi sistem absensi berbasis NFC. Terima kasih dan Wassalamu'alaikum."

---

# 📝 CHECKLIST SEBELUM RECORDING

## File yang Harus Dibuka di VS Code
- [ ] `database/migrations/2026_05_26_174640_create_warga_table.php`
- [ ] `database/migrations/2026_05_26_174656_create_riwayat_scan_table.php`
- [ ] `app/Models/Warga.php`
- [ ] `app/Models/RiwayatScan.php`
- [ ] `app/Http/Controllers/WargaController.php`
- [ ] `app/Http/Controllers/NfcController.php`
- [ ] `routes/web.php`
- [ ] `resources/views/nfc/nfc.blade.php`
- [ ] `resources/views/nfc/warga.blade.php`
- [ ] `resources/views/nfc/riwayat.blade.php`

## Setup Demo
- [ ] Laravel server running (`php artisan serve`)
- [ ] Minimal 2 data warga di database
- [ ] 1-2 kartu NFC/e-KTP siap untuk demo
- [ ] Chrome Android terinstall di HP
- [ ] Koneksi internet stabil

---

# 🎯 RINGKASAN CODING YANG DIJELASKAN

| Waktu | File | Line/Kode |
|-------|------|-----------|
| 01:15 | `create_warga_table.php` | `$table->string('nik', 16)->unique()` |
| 01:30 | `create_riwayat_scan_table.php` | `->nullable()` pada warga_id |
| 02:45 | `Warga.php` | `hasMany(RiwayatScan::class)` |
| 03:00 | `RiwayatScan.php` | `belongsTo(Warga::class)` |
| 03:30 | `WargaController.php` | Validasi `digits:16|unique` |
| 04:00 | `NfcController.php` | Logika `scan()` - cari warga & catat |
| 04:45 | `routes/web.php` | 4 route utama |
| 05:30 | `nfc.blade.php` HTML | Tombol & div status/hasil |
| 06:00 | `nfc.blade.php` JS | Cek `NDEFReader in window` |
| 06:30 | `nfc.blade.php` JS | `new NDEFReader().scan()` |
| 07:00 | `nfc.blade.php` JS | `addEventListener('reading')` |
| 07:30 | `nfc.blade.php` JS | `fetch('/api/nfc/scan')` |
| 08:00 | `nfc.blade.php` JS | Response handling & render |
| 08:45 | `warga.blade.php` | Form & validation errors |
| 09:15 | `riwayat.blade.php` | Badge status & eager loading |

---

# 💡 TIPS RECORDING

1. **Saat pindah file** - Beri jeda 2 detik, scroll pelan-pelan ke bagian yang dijelaskan
2. **Saat highlight kode** - Gunakan cursor effect, zoom in 120-150%
3. **Saat demo** - Bicara real-time, jelaskan apa yang terjadi di layar
4. **Transisi bagian** - Announce "Sekarang kita ke..." sebelum pindah topik

---

**Semoga recording lancar! 🎥✨**
