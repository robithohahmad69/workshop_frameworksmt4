@extends('layouts.apps')

@section('title', 'Kasir - jQuery AJAX')
@section('icon', 'mdi mdi-cash-register')
@section('page-title', 'Kasir (jQuery AJAX)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Kasir AJAX</li>
@endsection

@section('styles')
{{-- SweetAlert2 --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    /* =============================================
       KASIR POS - DARK INDUSTRIAL THEME
       ============================================= */
    :root {
        --pos-bg:        #0f1117;
        --pos-surface:   #1a1d27;
        --pos-border:    #2a2d3e;
        --pos-amber:     #f59e0b;
        --pos-amber-dim: #92400e;
        --pos-green:     #10b981;
        --pos-red:       #ef4444;
        --pos-text:      #e2e8f0;
        --pos-muted:     #64748b;
        --pos-input-bg:  #0d1117;
    }

    /* Override card untuk area kasir */
    .kasir-wrap .card {
        background: var(--pos-surface);
        border: 1px solid var(--pos-border);
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    }
    .kasir-wrap .card-title {
        color: var(--pos-amber);
        font-family: 'Courier New', monospace;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 3px;
        text-transform: uppercase;
        border-bottom: 1px solid var(--pos-border);
        padding-bottom: 12px;
        margin-bottom: 20px;
    }
    .kasir-wrap .card-title i {
        color: var(--pos-amber);
    }

    /* INPUT AREA */
    .pos-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--pos-muted);
        margin-bottom: 6px;
        display: block;
    }
    .pos-input {
        background: var(--pos-input-bg) !important;
        border: 1px solid var(--pos-border) !important;
        border-radius: 8px !important;
        color: var(--pos-text) !important;
        font-family: 'Courier New', monospace !important;
        font-size: 15px !important;
        padding: 10px 14px !important;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .pos-input:focus {
        border-color: var(--pos-amber) !important;
        box-shadow: 0 0 0 3px rgba(245,158,11,0.15) !important;
        outline: none !important;
    }
    .pos-input[readonly] {
        background: #1e2130 !important;
        color: var(--pos-amber) !important;
        font-weight: 700;
    }
    .pos-input::placeholder {
        color: var(--pos-muted) !important;
    }

    /* STATUS BADGE */
    #status-barang {
        font-size: 11px;
        letter-spacing: 1px;
        font-weight: 700;
        min-height: 20px;
        margin-top: 4px;
    }
    .status-found  { color: var(--pos-green); }
    .status-notfound { color: var(--pos-red); }
    .status-loading  { color: var(--pos-amber); }

    /* TOMBOL */
    .btn-pos-add {
        background: var(--pos-amber);
        color: #000;
        font-weight: 800;
        font-size: 12px;
        letter-spacing: 2px;
        text-transform: uppercase;
        border: none;
        border-radius: 8px;
        padding: 10px 24px;
        transition: all 0.2s;
        width: 100%;
    }
    .btn-pos-add:hover:not(:disabled) {
        background: #fbbf24;
        box-shadow: 0 4px 20px rgba(245,158,11,0.4);
        transform: translateY(-1px);
    }
    .btn-pos-add:disabled {
        background: var(--pos-border);
        color: var(--pos-muted);
        cursor: not-allowed;
        transform: none;
    }
    .btn-pos-pay {
        background: linear-gradient(135deg, var(--pos-green), #059669);
        color: #fff;
        font-weight: 800;
        font-size: 14px;
        letter-spacing: 2px;
        text-transform: uppercase;
        border: none;
        border-radius: 10px;
        padding: 14px 32px;
        transition: all 0.2s;
        width: 100%;
    }
    .btn-pos-pay:hover:not(:disabled) {
        box-shadow: 0 6px 24px rgba(16,185,129,0.45);
        transform: translateY(-2px);
    }
    .btn-pos-pay:disabled {
        background: var(--pos-border);
        color: var(--pos-muted);
        cursor: not-allowed;
    }

    /* TABEL */
    .pos-table-wrap {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid var(--pos-border);
    }
    .pos-table {
        margin-bottom: 0;
        background: transparent;
    }
    .pos-table thead th {
        background: #12151e;
        color: var(--pos-muted);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        border-bottom: 1px solid var(--pos-border);
        border-top: none;
        padding: 12px 14px;
    }
    .pos-table tbody tr {
        border-bottom: 1px solid var(--pos-border);
        transition: background 0.15s;
    }
    .pos-table tbody tr:last-child { border-bottom: none; }
    .pos-table tbody tr:hover { background: rgba(245,158,11,0.04); }
    .pos-table tbody td {
        color: var(--pos-text);
        font-size: 13px;
        padding: 11px 14px;
        vertical-align: middle;
        border-top: none;
    }
    .pos-table .td-code {
        color: var(--pos-amber);
        font-family: 'Courier New', monospace;
        font-weight: 700;
        font-size: 12px;
    }
    .pos-table .td-subtotal {
        color: var(--pos-green);
        font-weight: 700;
    }
    .qty-input {
        background: var(--pos-input-bg) !important;
        border: 1px solid var(--pos-border) !important;
        border-radius: 6px !important;
        color: var(--pos-text) !important;
        font-family: 'Courier New', monospace !important;
        font-weight: 700;
        width: 70px;
        text-align: center;
        padding: 4px 8px !important;
        font-size: 13px !important;
    }
    .qty-input:focus {
        border-color: var(--pos-amber) !important;
        outline: none !important;
    }
    .btn-hapus {
        background: transparent;
        border: 1px solid var(--pos-red);
        color: var(--pos-red);
        border-radius: 6px;
        padding: 3px 10px;
        font-size: 11px;
        transition: all 0.15s;
    }
    .btn-hapus:hover {
        background: var(--pos-red);
        color: #fff;
    }

    /* TOTAL AREA */
    .total-area {
        background: #12151e;
        border: 1px solid var(--pos-border);
        border-radius: 10px;
        padding: 20px 24px;
    }
    .total-label {
        font-size: 11px;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: var(--pos-muted);
        font-weight: 700;
    }
    .total-value {
        font-family: 'Courier New', monospace;
        font-size: 32px;
        font-weight: 900;
        color: var(--pos-amber);
        letter-spacing: -1px;
    }

    /* EMPTY STATE */
    .empty-state {
        padding: 48px 0;
        text-align: center;
        color: var(--pos-muted);
    }
    .empty-state i {
        font-size: 40px;
        opacity: 0.3;
        display: block;
        margin-bottom: 12px;
    }
    .empty-state span {
        font-size: 12px;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    /* ROW HIGHLIGHT animasi saat baris baru ditambahkan */
    @keyframes rowFlash {
        0%   { background: rgba(245,158,11,0.2); }
        100% { background: transparent; }
    }
    .row-flash { animation: rowFlash 0.6s ease-out; }
</style>
@endsection

@section('content')
<div class="kasir-wrap">
<div class="row">

    {{-- ==================== KIRI: INPUT BARANG ==================== --}}
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card w-100">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="mdi mdi-barcode-scan me-2"></i> Input Barang
                </h4>

                {{-- Kode Barang --}}
                <div class="mb-3">
                    <label class="pos-label">Kode Barang <span style="color:var(--pos-amber)">[Enter]</span></label>
                    <input type="text" id="kode-barang" class="form-control pos-input"
                           placeholder="Scan / ketik kode..." autocomplete="off" maxlength="8">
                    <div id="status-barang"></div>
                </div>

                {{-- Nama Barang --}}
                <div class="mb-3">
                    <label class="pos-label">Nama Barang</label>
                    <input type="text" id="nama-barang" class="form-control pos-input"
                           readonly placeholder="—">
                </div>

                {{-- Harga Satuan --}}
                <div class="mb-3">
                    <label class="pos-label">Harga Satuan</label>
                    <input type="text" id="harga-barang" class="form-control pos-input"
                           readonly placeholder="—">
                </div>

                {{-- Jumlah --}}
                <div class="mb-4">
                    <label class="pos-label">Jumlah</label>
                    <input type="number" id="jumlah" class="form-control pos-input"
                           min="1" value="1" placeholder="1" disabled>
                </div>

                {{-- Tombol Tambahkan --}}
                <button type="button" id="btn-tambah" class="btn-pos-add" disabled>
                    <i class="mdi mdi-plus-circle-outline me-1"></i> Tambahkan
                </button>
            </div>
        </div>
    </div>

    {{-- ==================== KANAN: TABEL + TOTAL ==================== --}}
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card w-100">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="mdi mdi-receipt me-2"></i> Keranjang Belanja
                </h4>

                {{-- Tabel --}}
                <div class="pos-table-wrap mb-4">
                    <table class="table pos-table" id="tabel-keranjang">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th style="width:90px">Jumlah</th>
                                <th>Subtotal</th>
                                <th style="width:70px"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-keranjang">
                            <tr id="empty-row">
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="mdi mdi-cart-outline"></i>
                                        <span>Keranjang kosong</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Total + Bayar --}}
                <div class="total-area mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="total-label">Total Pembayaran</div>
                            <div class="total-value" id="total-harga">Rp 0</div>
                        </div>
                        <div style="min-width:180px">
                            <button type="button" id="btn-bayar" class="btn-pos-pay" disabled>
                                <i class="mdi mdi-cash me-2"></i> Bayar
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
</div>
@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ================================================================
// KASIR POS — jQuery AJAX
// ================================================================
// Struktur data keranjang (array of object di memori JS):
// keranjang = [
//   { id_barang, nama, harga, jumlah, subtotal },
//   ...
// ]
// ================================================================

$(document).ready(function () {

    var keranjang = [];      // Penyimpanan data keranjang di JS
    var barangDitemukan = null; // Data barang yang sedang aktif


    // ============================================================
    // [1] CARI BARANG — trigger saat Enter ditekan di input kode
    // ============================================================
    $('#kode-barang').on('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault(); // Cegah submit form

        var kode = $(this).val().trim();
        if (!kode) return;

        // Reset state
        barangDitemukan = null;
        $('#nama-barang').val('');
        $('#harga-barang').val('');
        $('#jumlah').val(1).prop('disabled', true);
        $('#btn-tambah').prop('disabled', true);
        setStatus('loading', '<i class="mdi mdi-loading mdi-spin"></i> Mencari...');

        $.ajax({
            url: '/api/barang/' + kode,   // Route: GET /api/barang/{kode}
            type: 'GET',
            success: function (data) {
                if (!data || !data.id_barang) {
                    setStatus('notfound', '✗ Barang tidak ditemukan');
                    return;
                }
                // Barang ditemukan
                barangDitemukan = data;
                $('#nama-barang').val(data.nama);
                $('#harga-barang').val(formatRupiah(data.harga));
                $('#jumlah').val(1).prop('disabled', false);
                $('#btn-tambah').prop('disabled', false);
                setStatus('found', '✓ Barang ditemukan');
                $('#jumlah').focus(); // Pindah fokus ke input jumlah
            },
            error: function (xhr) {
                if (xhr.status === 404) {
                    setStatus('notfound', '✗ Barang tidak ditemukan');
                } else {
                    setStatus('notfound', '✗ Gagal memuat data');
                }
            }
        });
    });


    // ============================================================
    // [2] VALIDASI JUMLAH — pastikan btn-tambah disable jika < 1
    // ============================================================
    $('#jumlah').on('input', function () {
        var qty = parseInt($(this).val());
        if (barangDitemukan && qty > 0) {
            $('#btn-tambah').prop('disabled', false);
        } else {
            $('#btn-tambah').prop('disabled', true);
        }
    });


    // ============================================================
    // [3] TAMBAHKAN KE KERANJANG
    // ============================================================
    $('#btn-tambah').on('click', function () {
        if (!barangDitemukan) return;

        var jumlah = parseInt($('#jumlah').val());
        if (jumlah < 1) return;

        var kode      = barangDitemukan.id_barang;
        var harga     = barangDitemukan.harga;
        var subtotal  = harga * jumlah;

        // Cek apakah kode barang sudah ada di keranjang
        var idx = keranjang.findIndex(function (item) {
            return item.id_barang === kode;
        });

        if (idx >= 0) {
            // Sudah ada → update jumlah dan subtotal saja (poin f)
            keranjang[idx].jumlah   += jumlah;
            keranjang[idx].subtotal  = keranjang[idx].harga * keranjang[idx].jumlah;
        } else {
            // Belum ada → tambah baris baru
            keranjang.push({
                id_barang : kode,
                nama      : barangDitemukan.nama,
                harga     : harga,
                jumlah    : jumlah,
                subtotal  : subtotal
            });
        }

        renderTabel();
        resetInputBarang();
        $('#kode-barang').focus();
    });


    // ============================================================
    // [4] RENDER TABEL dari array keranjang
    // ============================================================
    function renderTabel() {
        var tbody = $('#tbody-keranjang');
        tbody.empty();

        if (keranjang.length === 0) {
            tbody.html(`
                <tr id="empty-row">
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="mdi mdi-cart-outline"></i>
                            <span>Keranjang kosong</span>
                        </div>
                    </td>
                </tr>
            `);
            updateTotal();
            return;
        }

        keranjang.forEach(function (item, index) {
            var row = `
                <tr data-index="${index}" class="row-flash">
                    <td>${index + 1}</td>
                    <td class="td-code">${item.id_barang}</td>
                    <td>${item.nama}</td>
                    <td>${formatRupiah(item.harga)}</td>
                    <td>
                        <input type="number" class="form-control qty-input qty-tabel"
                               data-index="${index}" value="${item.jumlah}" min="1">
                    </td>
                    <td class="td-subtotal subtotal-cell">${formatRupiah(item.subtotal)}</td>
                    <td>
                        <button class="btn-hapus btn-hapus-row" data-index="${index}">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        updateTotal();
        $('#btn-bayar').prop('disabled', keranjang.length === 0);
    }


    // ============================================================
    // [5] UBAH JUMLAH di tabel (poin g)
    // Pakai event delegation karena baris dibuat dinamis
    // ============================================================
    $('#tbody-keranjang').on('input', '.qty-tabel', function () {
        var idx = parseInt($(this).data('index'));
        var qty = parseInt($(this).val());

        if (isNaN(qty) || qty < 1) {
            $(this).val(1);
            qty = 1;
        }

        keranjang[idx].jumlah  = qty;
        keranjang[idx].subtotal = keranjang[idx].harga * qty;

        // Update hanya subtotal di baris yang berubah (tidak re-render semua)
        var row = $(this).closest('tr');
        row.find('.subtotal-cell').text(formatRupiah(keranjang[idx].subtotal));

        updateTotal(); // Hitung ulang total (poin h)
    });


    // ============================================================
    // [6] HAPUS BARIS dari tabel (poin g)
    // ============================================================
    $('#tbody-keranjang').on('click', '.btn-hapus-row', function () {
        var idx = parseInt($(this).data('index'));
        keranjang.splice(idx, 1); // Hapus dari array
        renderTabel();            // Re-render tabel
    });


    // ============================================================
    // [7] BAYAR — simpan ke database via AJAX (poin i)
    // ============================================================
    $('#btn-bayar').on('click', function () {
        if (keranjang.length === 0) return;

        // Konfirmasi dulu
        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: 'Total: <strong>' + $('#total-harga').text() + '</strong><br>Proses pembayaran?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Bayar!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10b981',
            background: '#1a1d27',
            color: '#e2e8f0'
        }).then(function (result) {
            if (!result.isConfirmed) return;

            // Hitung total
            var total = keranjang.reduce(function (sum, item) {
                return sum + item.subtotal;
            }, 0);

            // Aktifkan loader button
            var $btn = $('#btn-bayar');
            $btn.prop('disabled', true)
                .html('<i class="mdi mdi-loading mdi-spin me-2"></i> Menyimpan...');

            $.ajax({
                url: '/kasir/simpan',     // Route: POST /kasir/simpan
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    _token  : '{{ csrf_token() }}',
                    total   : total,
                    detail  : keranjang.map(function (item) {
                        return {
                            id_barang : item.id_barang,
                            jumlah    : item.jumlah,
                            subtotal  : item.subtotal
                        };
                    })
                }),
                success: function () {
                    // Notifikasi SweetAlert2 berhasil (poin j)
                    Swal.fire({
                        title: 'Pembayaran Berhasil!',
                        html: 'Transaksi <strong>' + $('#total-harga').text() + '</strong> berhasil disimpan.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#10b981',
                        background: '#1a1d27',
                        color: '#e2e8f0'
                    }).then(function () {
                        resetSemua(); // Kosongkan semua (poin j)
                    });
                },
                error: function () {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Terjadi kesalahan saat menyimpan transaksi.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444',
                        background: '#1a1d27',
                        color: '#e2e8f0'
                    });
                    $btn.prop('disabled', false)
                        .html('<i class="mdi mdi-cash me-2"></i> Bayar');
                }
            });
        });
    });


    // ============================================================
    // HELPER: Hitung dan tampilkan TOTAL (poin h)
    // ============================================================
    function updateTotal() {
        var total = keranjang.reduce(function (sum, item) {
            return sum + item.subtotal;
        }, 0);
        $('#total-harga').text(formatRupiah(total));
        $('#btn-bayar').prop('disabled', keranjang.length === 0);
    }


    // ============================================================
    // HELPER: Format angka ke Rupiah
    // ============================================================
    function formatRupiah(angka) {
        return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
    }


    // ============================================================
    // HELPER: Set status teks pencarian barang
    // ============================================================
    function setStatus(tipe, pesan) {
        var kelas = {
            found    : 'status-found',
            notfound : 'status-notfound',
            loading  : 'status-loading'
        };
        $('#status-barang')
            .removeClass('status-found status-notfound status-loading')
            .addClass(kelas[tipe])
            .html(pesan);
    }


    // ============================================================
    // HELPER: Reset input barang (kiri) setelah tambah
    // ============================================================
    function resetInputBarang() {
        barangDitemukan = null;
        $('#kode-barang').val('');
        $('#nama-barang').val('');
        $('#harga-barang').val('');
        $('#jumlah').val(1).prop('disabled', true);
        $('#btn-tambah').prop('disabled', true);
        $('#status-barang').text('').removeClass('status-found status-notfound status-loading');
    }


    // ============================================================
    // HELPER: Reset SEMUA setelah bayar berhasil (poin j)
    // ============================================================
    function resetSemua() {
        keranjang = [];
        barangDitemukan = null;
        resetInputBarang();
        renderTabel();
        $('#btn-bayar').html('<i class="mdi mdi-cash me-2"></i> Bayar');
        $('#kode-barang').focus();
    }

}); // end document.ready
</script>
@endsection