@extends('layouts.apps')

@section('title', 'Kasir - Axios')
@section('icon', 'mdi mdi-cash-register')
@section('page-title', 'Kasir (Axios)')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Kasir Axios</li>
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
    .pos-input::placeholder { color: var(--pos-muted) !important; }

    #status-barang {
        font-size: 11px;
        letter-spacing: 1px;
        font-weight: 700;
        min-height: 20px;
        margin-top: 4px;
    }
    .status-found    { color: var(--pos-green); }
    .status-notfound { color: var(--pos-red); }
    .status-loading  { color: var(--pos-amber); }

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

    .pos-table-wrap {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid var(--pos-border);
    }
    .pos-table { margin-bottom: 0; background: transparent; }
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
    .pos-table .td-subtotal { color: var(--pos-green); font-weight: 700; }

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
        cursor: pointer;
    }
    .btn-hapus:hover {
        background: var(--pos-red);
        color: #fff;
    }

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

    .empty-state {
        padding: 48px 0;
        text-align: center;
        color: var(--pos-muted);
    }
    .empty-state i { font-size: 40px; opacity: 0.3; display: block; margin-bottom: 12px; }
    .empty-state span { font-size: 12px; letter-spacing: 2px; text-transform: uppercase; }

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

                <div class="mb-3">
                    <label class="pos-label">Kode Barang <span style="color:var(--pos-amber)">[Enter]</span></label>
                    <input type="text" id="kode-barang" class="form-control pos-input"
                           placeholder="Scan / ketik kode..." autocomplete="off" maxlength="8">
                    <div id="status-barang"></div>
                </div>

                <div class="mb-3">
                    <label class="pos-label">Nama Barang</label>
                    <input type="text" id="nama-barang" class="form-control pos-input" readonly placeholder="—">
                </div>

                <div class="mb-3">
                    <label class="pos-label">Harga Satuan</label>
                    <input type="text" id="harga-barang" class="form-control pos-input" readonly placeholder="—">
                </div>

                <div class="mb-4">
                    <label class="pos-label">Jumlah</label>
                    <input type="number" id="jumlah" class="form-control pos-input"
                           min="1" value="1" placeholder="1" disabled>
                </div>

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

                <div class="pos-table-wrap mb-4">
                    <table class="table pos-table">
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
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ================================================================
// KASIR POS — Axios (async/await)
// ================================================================
// Perbedaan vs versi AJAX:
// - axios.get / axios.post menggantikan $.ajax
// - try/catch menggantikan error: function(){}
// - response.data sudah otomatis JSON, tidak perlu parse manual
// - Event listener pakai addEventListener (Vanilla JS), bukan jQuery
// ================================================================

// Setup Axios: tambahkan CSRF token otomatis ke semua POST request
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content
    || '{{ csrf_token() }}';
axios.defaults.headers.post['Content-Type'] = 'application/json';

// State utama
let keranjang      = [];   // Array keranjang belanja
let barangDitemukan = null; // Objek barang yang sedang dicari


// ============================================================
// [1] CARI BARANG — trigger saat Enter ditekan
// ============================================================
document.getElementById('kode-barang').addEventListener('keydown', async function (e) {
    if (e.key !== 'Enter') return;
    e.preventDefault();

    const kode = this.value.trim();
    if (!kode) return;

    // Reset state barang
    barangDitemukan = null;
    document.getElementById('nama-barang').value  = '';
    document.getElementById('harga-barang').value = '';
    document.getElementById('jumlah').value       = 1;
    document.getElementById('jumlah').disabled    = true;
    document.getElementById('btn-tambah').disabled = true;
    setStatus('loading', '<i class="mdi mdi-loading mdi-spin"></i> Mencari...');

    try {
        // axios.get → otomatis parse JSON, tidak perlu dataType
        const response = await axios.get('/api/barang/' + kode);
        const data = response.data;

        if (!data || !data.id_barang) {
            setStatus('notfound', '✗ Barang tidak ditemukan');
            return;
        }

        barangDitemukan = data;
        document.getElementById('nama-barang').value  = data.nama;
        document.getElementById('harga-barang').value = formatRupiah(data.harga);
        document.getElementById('jumlah').value       = 1;
        document.getElementById('jumlah').disabled    = false;
        document.getElementById('btn-tambah').disabled = false;
        setStatus('found', '✓ Barang ditemukan');
        document.getElementById('jumlah').focus();

    } catch (error) {
        // axios membungkus HTTP error (404, 500) ke dalam catch
        if (error.response && error.response.status === 404) {
            setStatus('notfound', '✗ Barang tidak ditemukan');
        } else {
            setStatus('notfound', '✗ Gagal: ' + error.message);
        }
    }
});


// ============================================================
// [2] VALIDASI JUMLAH
// ============================================================
document.getElementById('jumlah').addEventListener('input', function () {
    const qty = parseInt(this.value);
    document.getElementById('btn-tambah').disabled = !(barangDitemukan && qty > 0);
});


// ============================================================
// [3] TAMBAHKAN KE KERANJANG
// ============================================================
document.getElementById('btn-tambah').addEventListener('click', function () {
    if (!barangDitemukan) return;

    const jumlah   = parseInt(document.getElementById('jumlah').value);
    if (jumlah < 1) return;

    const kode     = barangDitemukan.id_barang;
    const harga    = barangDitemukan.harga;

    // Cek duplikat (poin f)
    const idx = keranjang.findIndex(item => item.id_barang === kode);

    if (idx >= 0) {
        keranjang[idx].jumlah   += jumlah;
        keranjang[idx].subtotal  = keranjang[idx].harga * keranjang[idx].jumlah;
    } else {
        keranjang.push({
            id_barang : kode,
            nama      : barangDitemukan.nama,
            harga     : harga,
            jumlah    : jumlah,
            subtotal  : harga * jumlah
        });
    }

    renderTabel();
    resetInputBarang();
    document.getElementById('kode-barang').focus();
});


// ============================================================
// [4] RENDER TABEL
// ============================================================
function renderTabel() {
    const tbody = document.getElementById('tbody-keranjang');

    if (keranjang.length === 0) {
        tbody.innerHTML = `
            <tr id="empty-row">
                <td colspan="7">
                    <div class="empty-state">
                        <i class="mdi mdi-cart-outline"></i>
                        <span>Keranjang kosong</span>
                    </div>
                </td>
            </tr>`;
        updateTotal();
        return;
    }

    tbody.innerHTML = keranjang.map((item, index) => `
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
    `).join('');

    updateTotal();
    document.getElementById('btn-bayar').disabled = false;

    // Pasang event ke qty dan hapus setelah render
    // Pakai event delegation via tbody
}


// ============================================================
// [5] UBAH JUMLAH DI TABEL — event delegation via tbody
// ============================================================
document.getElementById('tbody-keranjang').addEventListener('input', function (e) {
    if (!e.target.classList.contains('qty-tabel')) return;

    const idx = parseInt(e.target.dataset.index);
    let qty   = parseInt(e.target.value);

    if (isNaN(qty) || qty < 1) {
        qty = 1;
        e.target.value = 1;
    }

    keranjang[idx].jumlah   = qty;
    keranjang[idx].subtotal = keranjang[idx].harga * qty;

    // Update hanya kolom subtotal baris itu
    const row = e.target.closest('tr');
    row.querySelector('.subtotal-cell').textContent = formatRupiah(keranjang[idx].subtotal);

    updateTotal(); // Poin h
});


// ============================================================
// [6] HAPUS BARIS — event delegation via tbody
// ============================================================
document.getElementById('tbody-keranjang').addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-hapus-row');
    if (!btn) return;

    const idx = parseInt(btn.dataset.index);
    keranjang.splice(idx, 1);
    renderTabel();
});


// ============================================================
// [7] BAYAR — simpan ke database via Axios POST (poin i)
// ============================================================
document.getElementById('btn-bayar').addEventListener('click', async function () {
    if (keranjang.length === 0) return;

    const total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);

    const konfirmasi = await Swal.fire({
        title: 'Konfirmasi Pembayaran',
        html: `Total: <strong>${formatRupiah(total)}</strong><br>Proses pembayaran?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Bayar!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#10b981',
        background: '#1a1d27',
        color: '#e2e8f0'
    });

    if (!konfirmasi.isConfirmed) return;

    // Loader button
    const btn = document.getElementById('btn-bayar');
    btn.disabled   = true;
    btn.innerHTML  = '<i class="mdi mdi-loading mdi-spin me-2"></i> Menyimpan...';

    try {
        // axios.post otomatis set Content-Type: application/json
        await axios.post('/kasir/simpan', {
            total  : total,
            detail : keranjang.map(item => ({
                id_barang : item.id_barang,
                jumlah    : item.jumlah,
                subtotal  : item.subtotal
            }))
        });

        // Berhasil → SweetAlert2 (poin j)
        await Swal.fire({
            title: 'Pembayaran Berhasil!',
            html: `Transaksi <strong>${formatRupiah(total)}</strong> berhasil disimpan.`,
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#10b981',
            background: '#1a1d27',
            color: '#e2e8f0'
        });

        resetSemua(); // Kosongkan semua (poin j)

    } catch (error) {
        Swal.fire({
            title: 'Gagal!',
            text: 'Terjadi kesalahan: ' + (error.response?.data?.message || error.message),
            icon: 'error',
            confirmButtonColor: '#ef4444',
            background: '#1a1d27',
            color: '#e2e8f0'
        });
        btn.disabled  = false;
        btn.innerHTML = '<i class="mdi mdi-cash me-2"></i> Bayar';
    }
});


// ============================================================
// HELPER FUNCTIONS
// ============================================================

function updateTotal() {
    const total = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
    document.getElementById('total-harga').textContent = formatRupiah(total);
    document.getElementById('btn-bayar').disabled = keranjang.length === 0;
}

function formatRupiah(angka) {
    return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
}

function setStatus(tipe, pesan) {
    const el = document.getElementById('status-barang');
    el.className = '';
    el.classList.add({
        found    : 'status-found',
        notfound : 'status-notfound',
        loading  : 'status-loading'
    }[tipe]);
    el.innerHTML = pesan;
}

function resetInputBarang() {
    barangDitemukan = null;
    document.getElementById('kode-barang').value   = '';
    document.getElementById('nama-barang').value   = '';
    document.getElementById('harga-barang').value  = '';
    document.getElementById('jumlah').value        = 1;
    document.getElementById('jumlah').disabled     = true;
    document.getElementById('btn-tambah').disabled = true;
    document.getElementById('status-barang').textContent = '';
    document.getElementById('status-barang').className   = '';
}

function resetSemua() {
    keranjang       = [];
    barangDitemukan = null;
    resetInputBarang();
    renderTabel();
    document.getElementById('btn-bayar').innerHTML = '<i class="mdi mdi-cash me-2"></i> Bayar';
    document.getElementById('kode-barang').focus();
}
</script>
@endsection