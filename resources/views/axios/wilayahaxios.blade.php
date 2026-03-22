@extends('layouts.apps')

@section('title', 'Select Wilayah - Axios')
@section('icon', 'mdi mdi-map-marker-radius')
@section('page-title', 'Select Wilayah (Axios)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Wilayah Axios</li>
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
    .select2-container--default.select2-container--disabled .select2-selection--single {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    /* Loading spinner di dalam label */
    .label-loading {
        display: none;
        margin-left: 8px;
        font-size: 12px;
        color: #6c757d;
    }
    .label-loading.aktif {
        display: inline;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Pilih Wilayah Administrasi Indonesia</h4>
                <p class="card-description">Menggunakan <strong>Axios</strong> + <strong>Select2</strong></p>

                <form class="forms-sample">

                    {{-- LEVEL 1: PROVINSI --}}
                    <div class="form-group">
                        <label for="provinsi">
                            Provinsi
                            <span class="label-loading aktif" id="loading-provinsi">
                                <i class="mdi mdi-loading mdi-spin"></i> Memuat...
                            </span>
                        </label>
                        <select class="form-control" id="provinsi" style="width:100%">
                            <option value="0">-- Pilih Provinsi --</option>
                        </select>
                    </div>

                    {{-- LEVEL 2: KOTA --}}
                    <div class="form-group">
                        <label for="kota">
                            Kota / Kabupaten
                            <span class="label-loading" id="loading-kota">
                                <i class="mdi mdi-loading mdi-spin"></i> Memuat...
                            </span>
                        </label>
                        <select class="form-control" id="kota" style="width:100%" disabled>
                            <option value="0">-- Pilih Kota --</option>
                        </select>
                    </div>

                    {{-- LEVEL 3: KECAMATAN --}}
                    <div class="form-group">
                        <label for="kecamatan">
                            Kecamatan
                            <span class="label-loading" id="loading-kecamatan">
                                <i class="mdi mdi-loading mdi-spin"></i> Memuat...
                            </span>
                        </label>
                        <select class="form-control" id="kecamatan" style="width:100%" disabled>
                            <option value="0">-- Pilih Kecamatan --</option>
                        </select>
                    </div>

                    {{-- LEVEL 4: KELURAHAN --}}
                    <div class="form-group">
                        <label for="kelurahan">
                            Kelurahan / Desa
                            <span class="label-loading" id="loading-kelurahan">
                                <i class="mdi mdi-loading mdi-spin"></i> Memuat...
                            </span>
                        </label>
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

                    {{-- PESAN ERROR --}}
                    <div class="form-group" id="pesan-error" style="display:none;">
                        <div class="alert alert-danger" id="teks-error"></div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- CDN Axios --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
{{-- CDN Select2 JS - harus setelah jQuery --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // ============================================================
    // AXIOS + SELECT2 - SELECT WILAYAH
    // ============================================================
    //
    // Kombinasi Axios (async/await) + Select2
    // Axios → handle HTTP request
    // Select2 → tampilan dropdown dengan fitur search
    // jQuery minimal → hanya untuk init Select2 (Select2 butuh jQuery)
    // ============================================================

    // -------------------------------------------------------
    // FUNGSI HELPER: Init Select2
    // -------------------------------------------------------
    function initSelect2(selectorId, placeholder) {
        $('#' + selectorId).select2({
            placeholder: placeholder,
            allowClear: true,
            width: '100%'
        });
    }

    // -------------------------------------------------------
    // FUNGSI HELPER: Reset select → destroy Select2 → reinit
    // -------------------------------------------------------
    function resetSelect(selectorId, placeholder) {
        const $el = $('#' + selectorId);

        // Destroy Select2 jika sudah diinit
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }

        // Disable dan kosongkan
        $el.prop('disabled', true)
           .html('<option value="0">' + placeholder + '</option>');

        // Init ulang Select2
        initSelect2(selectorId, placeholder);
    }

    // -------------------------------------------------------
    // FUNGSI HELPER: Isi select dengan data + reinit Select2
    // -------------------------------------------------------
    function isiSelectDanInit(selectorId, data, placeholder) {
        const $el = $('#' + selectorId);

        // Destroy Select2 lama
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }

        // Aktifkan dan isi options
        $el.prop('disabled', false)
           .html('<option value="0">' + placeholder + '</option>');

        data.forEach(function (item) {
            $el.append('<option value="' + item.id + '">' + item.name + '</option>');
        });

        // Init ulang Select2
        initSelect2(selectorId, placeholder);
    }

    // -------------------------------------------------------
    // FUNGSI HELPER: Tampilkan / sembunyikan loading di label
    // -------------------------------------------------------
    function setLoading(loadingId, aktif) {
        const el = document.getElementById(loadingId);
        if (aktif) {
            el.classList.add('aktif');
        } else {
            el.classList.remove('aktif');
        }
    }

    // -------------------------------------------------------
    // FUNGSI HELPER: Tampilkan pesan error
    // -------------------------------------------------------
    function tampilkanError(pesan) {
        document.getElementById('teks-error').textContent = pesan;
        document.getElementById('pesan-error').style.display = 'block';
        setTimeout(() => {
            document.getElementById('pesan-error').style.display = 'none';
        }, 4000);
    }


    // ============================================================
    // Init Select2 semua select saat halaman dibuka
    // ============================================================
    $(document).ready(function () {
        initSelect2('provinsi',  '-- Pilih Provinsi --');
        initSelect2('kota',      '-- Pilih Kota --');
        initSelect2('kecamatan', '-- Pilih Kecamatan --');
        initSelect2('kelurahan', '-- Pilih Kelurahan --');
    });


    // ============================================================
    // LANGKAH 1: Muat PROVINSI saat halaman dibuka (async/await)
    // ============================================================
    async function muatProvinsi() {
        setLoading('loading-provinsi', true);
        try {
            const response = await axios.get('/api/provinsi');
            // response.data sudah otomatis array JSON, tidak perlu JSON.parse
            isiSelectDanInit('provinsi', response.data, '-- Pilih Provinsi --');
        } catch (error) {
            tampilkanError('Gagal memuat provinsi: ' + error.message);
        } finally {
            // finally selalu jalan, baik sukses maupun error
            setLoading('loading-provinsi', false);
        }
    }

    muatProvinsi();


    // ============================================================
    // LANGKAH 2: Event PROVINSI berubah → muat KOTA
    // ============================================================
    $(document).on('change', '#provinsi', async function () {
        // Pakai $(document).on karena Select2 kadang re-render elemen
        const idProvinsi = $(this).val();

        resetSelect('kota',      '-- Pilih Kota --');
        resetSelect('kecamatan', '-- Pilih Kecamatan --');
        resetSelect('kelurahan', '-- Pilih Kelurahan --');
        document.getElementById('hasil-pilihan').style.display = 'none';

        if (!idProvinsi || idProvinsi == 0) return;

        setLoading('loading-kota', true);
        try {
            const response = await axios.get('/api/kota/' + idProvinsi);
            isiSelectDanInit('kota', response.data, '-- Pilih Kota --');
        } catch (error) {
            tampilkanError('Gagal memuat kota: ' + error.message);
        } finally {
            setLoading('loading-kota', false);
        }
    });


    // ============================================================
    // LANGKAH 3: Event KOTA berubah → muat KECAMATAN
    // ============================================================
    $(document).on('change', '#kota', async function () {
        const idKota = $(this).val();

        resetSelect('kecamatan', '-- Pilih Kecamatan --');
        resetSelect('kelurahan', '-- Pilih Kelurahan --');
        document.getElementById('hasil-pilihan').style.display = 'none';

        if (!idKota || idKota == 0) return;

        setLoading('loading-kecamatan', true);
        try {
            const response = await axios.get('/api/kecamatan/' + idKota);
            isiSelectDanInit('kecamatan', response.data, '-- Pilih Kecamatan --');
        } catch (error) {
            tampilkanError('Gagal memuat kecamatan: ' + error.message);
        } finally {
            setLoading('loading-kecamatan', false);
        }
    });


    // ============================================================
    // LANGKAH 4: Event KECAMATAN berubah → muat KELURAHAN
    // ============================================================
    $(document).on('change', '#kecamatan', async function () {
        const idKecamatan = $(this).val();

        resetSelect('kelurahan', '-- Pilih Kelurahan --');
        document.getElementById('hasil-pilihan').style.display = 'none';

        if (!idKecamatan || idKecamatan == 0) return;

        setLoading('loading-kelurahan', true);
        try {
            const response = await axios.get('/api/kelurahan/' + idKecamatan);
            isiSelectDanInit('kelurahan', response.data, '-- Pilih Kelurahan --');
        } catch (error) {
            tampilkanError('Gagal memuat kelurahan: ' + error.message);
        } finally {
            setLoading('loading-kelurahan', false);
        }
    });


    // ============================================================
    // LANGKAH 5: Tampilkan hasil saat KELURAHAN dipilih
    // ============================================================
    $(document).on('change', '#kelurahan', function () {
        const idKelurahan = $(this).val();

        if (!idKelurahan || idKelurahan == 0) {
            document.getElementById('hasil-pilihan').style.display = 'none';
            return;
        }

        const namaProvinsi  = $('#provinsi option:selected').text();
        const namaKota      = $('#kota option:selected').text();
        const namaKecamatan = $('#kecamatan option:selected').text();
        const namaKelurahan = $('#kelurahan option:selected').text();

        document.getElementById('teks-hasil').innerHTML =
            '<strong>Provinsi:</strong> ' + namaProvinsi + ' &nbsp;|&nbsp; ' +
            '<strong>Kota:</strong> ' + namaKota + ' &nbsp;|&nbsp; ' +
            '<strong>Kecamatan:</strong> ' + namaKecamatan + ' &nbsp;|&nbsp; ' +
            '<strong>Kelurahan:</strong> ' + namaKelurahan;

        document.getElementById('hasil-pilihan').style.display = 'block';
    });
</script>
@endsection