@extends('layouts.apps')

@section('title', 'Select Wilayah - jQuery AJAX')
@section('icon', 'mdi mdi-map-marker-radius')
@section('page-title', 'Select Wilayah (jQuery AJAX)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Wilayah AJAX</li>
@endsection

@section('styles')
{{-- CDN Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Samakan tinggi Select2 dengan form-control Bootstrap */
    .select2-container .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        color: #495057;
        padding-left: 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.75rem + 2px);
    }
    /* Style saat select disabled */
    .select2-container--default.select2-container--disabled .select2-selection--single {
        background-color: #e9ecef;
        cursor: not-allowed;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Pilih Wilayah Administrasi Indonesia</h4>
                <p class="card-description">Menggunakan <strong>jQuery AJAX</strong> + <strong>Select2</strong></p>

                <form class="forms-sample">

                    {{-- LEVEL 1: PROVINSI --}}
                    <div class="form-group">
                        <label for="provinsi">Provinsi</label>
                        <select class="form-control" id="provinsi" style="width:100%">
                            <option value="0">-- Pilih Provinsi --</option>
                        </select>
                    </div>

                    {{-- LEVEL 2: KOTA --}}
                    <div class="form-group">
                        <label for="kota">Kota / Kabupaten</label>
                        <select class="form-control" id="kota" style="width:100%" disabled>
                            <option value="0">-- Pilih Kota --</option>
                        </select>
                    </div>

                    {{-- LEVEL 3: KECAMATAN --}}
                    <div class="form-group">
                        <label for="kecamatan">Kecamatan</label>
                        <select class="form-control" id="kecamatan" style="width:100%" disabled>
                            <option value="0">-- Pilih Kecamatan --</option>
                        </select>
                    </div>

                    {{-- LEVEL 4: KELURAHAN --}}
                    <div class="form-group">
                        <label for="kelurahan">Kelurahan / Desa</label>
                        <select class="form-control" id="kelurahan" style="width:100%" disabled>
                            <option value="0">-- Pilih Kelurahan --</option>
                        </select>
                    </div>

                    {{-- HASIL PILIHAN --}}
                    <div class="form-group mt-4" id="hasil-pilihan" style="display:none;">
                        <label>Wilayah Terpilih:</label>
                        <div class="alert alert-success">
                            <span id="teks-hasil"></span>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- CDN Select2 JS - harus setelah jQuery --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // ============================================================
    // JQUERY AJAX + SELECT2 - SELECT WILAYAH
    // ============================================================
    //
    // PENTING - Hal baru saat pakai Select2:
    // 1. Setiap <select> harus di-init dengan .select2()
    // 2. Event 'change' tetap bisa dipakai, Select2 kompatibel
    // 3. Saat reset, Select2 harus di-destroy dulu lalu di-init ulang
    //    supaya dropdown-nya ikut terupdate dengan data baru
    // ============================================================

    $(document).ready(function () {

        // -------------------------------------------------------
        // FUNGSI HELPER: Inisialisasi Select2 pada sebuah elemen
        // -------------------------------------------------------
        function initSelect2(selector, placeholder) {
            $(selector).select2({
                placeholder: placeholder,
                allowClear: true,  // Tombol X untuk hapus pilihan
                width: '100%'
            });
        }

        // Init semua select saat halaman pertama dibuka
        initSelect2('#provinsi',  '-- Pilih Provinsi --');
        initSelect2('#kota',      '-- Pilih Kota --');
        initSelect2('#kecamatan', '-- Pilih Kecamatan --');
        initSelect2('#kelurahan', '-- Pilih Kelurahan --');


        // -------------------------------------------------------
        // FUNGSI HELPER: Reset select → destroy Select2 → reinit
        //
        // Kenapa perlu destroy dulu?
        // Select2 menyimpan instance-nya sendiri di luar <select>.
        // Kalau langsung ganti HTML-nya tanpa destroy, instance lama
        // masih ada dan dropdown tidak ikut terupdate.
        // -------------------------------------------------------
        function resetSelect(selector, placeholder) {
            // Cek apakah Select2 sudah diinit pada elemen ini
            if ($(selector).hasClass('select2-hidden-accessible')) {
                $(selector).select2('destroy'); // Hapus instance lama
            }
            // Kosongkan options, disable, kembalikan ke placeholder
            $(selector)
                .prop('disabled', true)
                .html('<option value="0">' + placeholder + '</option>');

            // Init ulang Select2
            initSelect2(selector, placeholder);
        }


        // -------------------------------------------------------
        // FUNGSI HELPER: Isi select dengan data baru + reinit Select2
        // -------------------------------------------------------
        function isiSelectDanInit(selector, data, placeholder) {
            // Destroy instance Select2 yang lama
            if ($(selector).hasClass('select2-hidden-accessible')) {
                $(selector).select2('destroy');
            }

            // Aktifkan dan isi dengan data baru
            $(selector)
                .prop('disabled', false)
                .html('<option value="0">' + placeholder + '</option>');

            $.each(data, function (index, item) {
                $(selector).append(
                    '<option value="' + item.id + '">' + item.name + '</option>'
                );
            });

            // Init ulang Select2
            initSelect2(selector, placeholder);
        }


        // -------------------------------------------------------
        // LANGKAH 1: Muat PROVINSI saat halaman dibuka
        // -------------------------------------------------------
        $.ajax({
            url: '/api/provinsi',
            type: 'GET',
            success: function (data) {
                isiSelectDanInit('#provinsi', data, '-- Pilih Provinsi --');
            },
            error: function () {
                alert('Gagal memuat data provinsi. Cek koneksi atau server.');
            }
        });


        // -------------------------------------------------------
        // LANGKAH 2: Event saat PROVINSI berubah → muat KOTA
        // -------------------------------------------------------
        $('#provinsi').on('change', function () {
            var idProvinsi = $(this).val();

            // Reset semua level di bawah provinsi
            resetSelect('#kota',      '-- Pilih Kota --');
            resetSelect('#kecamatan', '-- Pilih Kecamatan --');
            resetSelect('#kelurahan', '-- Pilih Kelurahan --');
            $('#hasil-pilihan').hide();

            // Jika tidak ada yang dipilih (tombol X ditekan atau value 0)
            if (!idProvinsi || idProvinsi == 0) return;

            $.ajax({
                url: '/api/kota/' + idProvinsi,
                type: 'GET',
                success: function (data) {
                    isiSelectDanInit('#kota', data, '-- Pilih Kota --');
                },
                error: function () {
                    alert('Gagal memuat data kota.');
                }
            });
        });


        // -------------------------------------------------------
        // LANGKAH 3: Event saat KOTA berubah → muat KECAMATAN
        // -------------------------------------------------------
        $('#kota').on('change', function () {
            var idKota = $(this).val();

            resetSelect('#kecamatan', '-- Pilih Kecamatan --');
            resetSelect('#kelurahan', '-- Pilih Kelurahan --');
            $('#hasil-pilihan').hide();

            if (!idKota || idKota == 0) return;

            $.ajax({
                url: '/api/kecamatan/' + idKota,
                type: 'GET',
                success: function (data) {
                    isiSelectDanInit('#kecamatan', data, '-- Pilih Kecamatan --');
                },
                error: function () {
                    alert('Gagal memuat data kecamatan.');
                }
            });
        });


        // -------------------------------------------------------
        // LANGKAH 4: Event saat KECAMATAN berubah → muat KELURAHAN
        // -------------------------------------------------------
        $('#kecamatan').on('change', function () {
            var idKecamatan = $(this).val();

            resetSelect('#kelurahan', '-- Pilih Kelurahan --');
            $('#hasil-pilihan').hide();

            if (!idKecamatan || idKecamatan == 0) return;

            $.ajax({
                url: '/api/kelurahan/' + idKecamatan,
                type: 'GET',
                success: function (data) {
                    isiSelectDanInit('#kelurahan', data, '-- Pilih Kelurahan --');
                },
                error: function () {
                    alert('Gagal memuat data kelurahan.');
                }
            });
        });


        // -------------------------------------------------------
        // LANGKAH 5: Tampilkan hasil saat KELURAHAN dipilih
        // -------------------------------------------------------
        $('#kelurahan').on('change', function () {
            var idKelurahan = $(this).val();

            if (!idKelurahan || idKelurahan == 0) {
                $('#hasil-pilihan').hide();
                return;
            }

            var namaProvinsi  = $('#provinsi option:selected').text();
            var namaKota      = $('#kota option:selected').text();
            var namaKecamatan = $('#kecamatan option:selected').text();
            var namaKelurahan = $('#kelurahan option:selected').text();

            $('#teks-hasil').html(
                '<strong>Provinsi:</strong> ' + namaProvinsi + ' &nbsp;|&nbsp; ' +
                '<strong>Kota:</strong> ' + namaKota + ' &nbsp;|&nbsp; ' +
                '<strong>Kecamatan:</strong> ' + namaKecamatan + ' &nbsp;|&nbsp; ' +
                '<strong>Kelurahan:</strong> ' + namaKelurahan
            );
            $('#hasil-pilihan').show();
        });

    }); // end document.ready
</script>
@endsection