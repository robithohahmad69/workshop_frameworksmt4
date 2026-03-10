@extends('layouts.apps')

@section('title', 'Manajemen Barang - Tabel')
@section('icon', 'mdi mdi-package-variant')
@section('page-title', 'Manajemen Barang')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ url('/') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Barang - HTML Table</li>
@endsection

@section('content')
<div class="row">

    {{-- CARD FORM INPUT --}}
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Barang</h4>
                <p class="card-description">Isi form di bawah untuk menambahkan barang baru</p>

                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="inputNama">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="inputNama"
                                   placeholder="Contoh: Laptop Asus">
                            <small class="text-danger d-none" id="errNama">Nama barang wajib diisi!</small>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="inputHarga">Harga Barang <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control"
                                   id="inputHarga"
                                   placeholder="Contoh: 5000000">
                            <small class="text-danger d-none" id="errHarga">Harga barang wajib diisi!</small>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="button" id="btnSubmit" class="btn btn-gradient-primary btn-fw w-100">
                                <i class="mdi mdi-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD TABEL --}}
    <div class="col-12 d-none" id="tableCard">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Barang</h4>
                <p class="card-description">Klik baris untuk mengedit atau menghapus data</p>

                <div class="table-responsive">
                    <table class="table table-hover" id="tableBarang">
                        <thead>
                            <tr>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            {{-- Row ditambahkan via JavaScript --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- MODAL EDIT / HAPUS --}}
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel">
                    <i class="mdi mdi-pencil me-1"></i> Edit / Hapus Barang
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>ID Barang</label>
                    <input type="text" class="form-control" id="editId" disabled>
                </div>
                <div class="form-group">
                    <label>Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="editNama">
                    <small class="text-danger d-none" id="errEditNama">Nama barang wajib diisi!</small>
                </div>
                <div class="form-group">
                    <label>Harga Barang <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="editHarga">
                    <small class="text-danger d-none" id="errEditHarga">Harga barang wajib diisi!</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-gradient-success" id="btnSave">
                    <i class="mdi mdi-content-save"></i> Simpan
                </button>
                <button type="button" class="btn btn-gradient-danger" id="btnDelete">
                    <i class="mdi mdi-delete"></i> Hapus
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Row tabel bisa diklik - tampilkan pointer */
    #tableBarang tbody tr {
        cursor: pointer;
    }
</style>
@endsection

@section('scripts')
<script>
    let idCounter = 1;
    let selectedRow = null;

    // =====================
    // SUBMIT - Tambah Barang
    // =====================
    document.getElementById('btnSubmit').addEventListener('click', function () {
        const nama  = document.getElementById('inputNama').value.trim();
        const harga = document.getElementById('inputHarga').value.trim();

        // Reset error
        document.getElementById('errNama').classList.add('d-none');
        document.getElementById('errHarga').classList.add('d-none');
        document.getElementById('inputNama').classList.remove('is-invalid');
        document.getElementById('inputHarga').classList.remove('is-invalid');

        let valid = true;

        // Validasi required
        if (!nama) {
            document.getElementById('errNama').classList.remove('d-none');
            document.getElementById('inputNama').classList.add('is-invalid');
            valid = false;
        }
        if (!harga) {
            document.getElementById('errHarga').classList.remove('d-none');
            document.getElementById('inputHarga').classList.add('is-invalid');
            valid = false;
        }
        if (!valid) return;

        const idBarang = 'BRG-' + String(idCounter).padStart(3, '0');
        const hargaFmt = 'Rp ' + parseInt(harga).toLocaleString('id-ID');

        // Tambah row ke tabel
        const tbody = document.getElementById('tableBody');
        const row   = tbody.insertRow();

        row.insertCell(0).textContent = idBarang;
        row.insertCell(1).textContent = nama;
        row.insertCell(2).textContent = hargaFmt;

        // Simpan data asli di dataset row
        row.dataset.id    = idBarang;
        row.dataset.nama  = nama;
        row.dataset.harga = harga;

        // Klik row → buka modal edit
        row.addEventListener('click', function () {
            bukaModal(this);
        });

        idCounter++;

        // Kosongkan input
        document.getElementById('inputNama').value  = '';
        document.getElementById('inputHarga').value = '';

        // Tampilkan card tabel
        document.getElementById('tableCard').classList.remove('d-none');
    });

    // =====================
    // BUKA MODAL EDIT
    // =====================
    function bukaModal(row) {
        selectedRow = row;
        document.getElementById('editId').value    = row.dataset.id;
        document.getElementById('editNama').value  = row.dataset.nama;
        document.getElementById('editHarga').value = row.dataset.harga;

        // Reset error modal
        document.getElementById('errEditNama').classList.add('d-none');
        document.getElementById('errEditHarga').classList.add('d-none');
        document.getElementById('editNama').classList.remove('is-invalid');
        document.getElementById('editHarga').classList.remove('is-invalid');

        // Tampilkan modal Bootstrap
        const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
        modal.show();
    }

    // =====================
    // SIMPAN EDIT
    // =====================
    document.getElementById('btnSave').addEventListener('click', function () {
        const nama  = document.getElementById('editNama').value.trim();
        const harga = document.getElementById('editHarga').value.trim();

        document.getElementById('errEditNama').classList.add('d-none');
        document.getElementById('errEditHarga').classList.add('d-none');
        document.getElementById('editNama').classList.remove('is-invalid');
        document.getElementById('editHarga').classList.remove('is-invalid');

        let valid = true;
        if (!nama) {
            document.getElementById('errEditNama').classList.remove('d-none');
            document.getElementById('editNama').classList.add('is-invalid');
            valid = false;
        }
        if (!harga) {
            document.getElementById('errEditHarga').classList.remove('d-none');
            document.getElementById('editHarga').classList.add('is-invalid');
            valid = false;
        }
        if (!valid) return;

        // Update tampilan cell
        selectedRow.cells[1].textContent = nama;
        selectedRow.cells[2].textContent = 'Rp ' + parseInt(harga).toLocaleString('id-ID');

        // Update dataset
        selectedRow.dataset.nama  = nama;
        selectedRow.dataset.harga = harga;

        // Tutup modal
        bootstrap.Modal.getInstance(document.getElementById('modalEdit')).hide();
    });

    // =====================
    // HAPUS ROW
    // =====================
    document.getElementById('btnDelete').addEventListener('click', function () {
        if (selectedRow) {
            selectedRow.remove();
            selectedRow = null;
        }

        // Sembunyikan tabel jika kosong
        if (document.getElementById('tableBody').rows.length === 0) {
            document.getElementById('tableCard').classList.add('d-none');
        }

        bootstrap.Modal.getInstance(document.getElementById('modalEdit')).hide();
    });
</script>
@endsection