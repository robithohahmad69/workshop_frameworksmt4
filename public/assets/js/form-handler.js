/**
 * form-handler.js
 * ─────────────────────────────────────────────────────────────────────────
 * Reusable script untuk semua form CRUD.
 * Lokasi file ini: public/js/form-handler.js
 *
 * CARA KERJA OTOMATIS:
 *   Script ini mencari semua elemen dengan class "btn-submit" di halaman.
 *   Tidak perlu konfigurasi tambahan — cukup ikuti pola HTML di bawah.
 *
 * POLA HTML YANG HARUS DIIKUTI DI SETIAP FORM:
 *
 *   1. <form> harus punya: id unik + class="forms-sample" (sudah ada di project)
 *
 *   2. Tombol submit diletakkan di LUAR </form>, dengan atribut:
 *      - class="btn-submit"
 *      - data-form="#idForm"   ← tanda # wajib ada
 *
 *   3. Struktur tombol:
 *      <button type="button" class="btn btn-gradient-primary btn-submit" data-form="#idForm">
 *          <span class="btn-text">
 *              <i class="mdi mdi-content-save"></i> Simpan
 *          </span>
 *          <span class="btn-loader d-none">
 *              <span class="spinner-border spinner-border-sm me-1"></span>
 *              Memproses...
 *          </span>
 *      </button>
 * ─────────────────────────────────────────────────────────────────────────
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        // Cari semua tombol yang punya class btn-submit di halaman ini
        var buttons = document.querySelectorAll('.btn-submit');

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {

                // ── LANGKAH 1: Temukan form yang dituju ─────────────────
                var formId = button.getAttribute('data-form');
                var form   = document.querySelector(formId);

                if (!form) {
                    console.error('[form-handler.js] Form tidak ditemukan:', formId);
                    return;
                }

                // ── LANGKAH 2: Cek apakah semua input required terisi ───
                // checkValidity() hanya return true/false, tidak tampilkan pesan
                var valid = form.checkValidity();

                if (!valid) {
                    // reportValidity() = tampilkan tooltip/highlight field kosong
                    // Ini fitur bawaan browser, tidak perlu CSS tambahan
                    form.reportValidity();
                    return; // Berhenti di sini, jangan submit
                }

                // ── LANGKAH 3: Semua valid → aktifkan spinner ───────────
                showSpinner(button);

                // ── LANGKAH 4: Submit form via JavaScript ───────────────
                form.submit();
            });
        });
    });

    /**
     * Mengubah tombol menjadi spinner dan disable.
     * Inilah yang mencegah double submit —
     * tombol disabled tidak bisa diklik lagi oleh browser.
     */
    function showSpinner(button) {
        var textEl   = button.querySelector('.btn-text');
        var loaderEl = button.querySelector('.btn-loader');

        button.disabled = true;                     // ← kunci anti double submit
        if (textEl)   textEl.classList.add('d-none');
        if (loaderEl) loaderEl.classList.remove('d-none');
    }

    /**
     * Reset tombol ke kondisi semula.
     * Dipanggil manual jika dibutuhkan, contoh setelah error AJAX.
     * Cara pakai: FormHandler.reset(document.querySelector('.btn-submit'))
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