@extends('layouts.apps')

@section('title', 'Manajemen Barang - DataTables')
@section('icon', 'mdi mdi-package-variant')
@section('page-title', 'Manajemen Barang')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ url('/') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Barang - DataTables</li>
@endsection

@section('styles')
{{-- CSS DataTables --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap4.min.css">

<style>
    /* Row tabel bisa diklik - tampilkan pointer */
    #tableBarang tbody tr {
        cursor: pointer;
    }
</style>
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

    {{-- CARD TABEL DATATABLES --}}
    <div class="col-12 d-none" id="tableCard">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Barang</h4>
                <p class="card-description">Klik baris untuk mengedit atau menghapus data. Tabel mendukung pencarian dan pengurutan.</p>

                <div class="table-responsive">
                    <table class="table table-hover" id="tableBarang" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID Barang</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Row ditambahkan via DataTables --}}
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

@push('scripts')
{{-- jQuery (pastikan belum di-load oleh vendor.bundle.base.js) --}}
{{-- Jika jQuery sudah ada di vendor.bundle.base.js, hapus baris ini --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

{{-- DataTables JS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap4.min.js"></script>

<script>
    let idCounter    = 1;
    let dataTable    = null;
    let selectedRowNode = null;
    let selectedRowData = null;

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

        // Tampilkan card tabel sebelum inisialisasi DataTable
        document.getElementById('tableCard').classList.remove('d-none');

        // Inisialisasi DataTable hanya sekali
        if (!dataTable) {
            dataTable = $('#tableBarang').DataTable({
                language: {
                    search         : "Cari:",
                    lengthMenu     : "Tampilkan _MENU_ data",
                    info           : "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty      : "Tidak ada data",
                    zeroRecords    : "Data tidak ditemukan",
                    paginate: {
                        first    : "Pertama",
                        last     : "Terakhir",
                        next     : "Berikutnya",
                        previous : "Sebelumnya"
                    }
                }
            });
        }

        // Tambah row ke DataTable
        const newRow = dataTable.row.add([
            idBarang,
            nama,
            hargaFmt
        ]).draw().node();

        // Simpan data asli di attribute node row
        $(newRow).attr('data-id',    idBarang);
        $(newRow).attr('data-nama',  nama);
        $(newRow).attr('data-harga', harga);

        idCounter++;

        // Kosongkan input
        document.getElementById('inputNama').value  = '';
        document.getElementById('inputHarga').value = '';
    });

    // =====================
    // KLIK ROW → BUKA MODAL
    // Pakai event delegation karena DataTables render ulang DOM
    // =====================
    $(document).on('click', '#tableBarang tbody tr', function () {
        selectedRowNode = this;
        selectedRowData = {
            id    : $(this).attr('data-id'),
            nama  : $(this).attr('data-nama'),
            harga : $(this).attr('data-harga')
        };
        bukaModal(selectedRowData);
    });

    // =====================
    // BUKA MODAL
    // =====================
    function bukaModal(data) {
        document.getElementById('editId').value    = data.id;
        document.getElementById('editNama').value  = data.nama;
        document.getElementById('editHarga').value = data.harga;

        // Reset error modal
        document.getElementById('errEditNama').classList.add('d-none');
        document.getElementById('errEditHarga').classList.add('d-none');
        document.getElementById('editNama').classList.remove('is-invalid');
        document.getElementById('editHarga').classList.remove('is-invalid');

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

        const hargaFmt = 'Rp ' + parseInt(harga).toLocaleString('id-ID');
        const idBarang = document.getElementById('editId').value;

        // Update data di DataTable
        dataTable.row(selectedRowNode).data([
            idBarang,
            nama,
            hargaFmt
        ]).draw();

        // Update ulang attribute setelah draw (DOM berubah, cari ulang row-nya)
        $('#tableBarang tbody tr').each(function () {
            if ($(this).find('td').eq(0).text() === idBarang) {
                $(this).attr('data-id',    idBarang);
                $(this).attr('data-nama',  nama);
                $(this).attr('data-harga', harga);
            }
        });

        bootstrap.Modal.getInstance(document.getElementById('modalEdit')).hide();
    });

    // =====================
    // HAPUS ROW
    // =====================
    document.getElementById('btnDelete').addEventListener('click', function () {
        if (selectedRowNode && dataTable) {
            dataTable.row(selectedRowNode).remove().draw();
        }
        bootstrap.Modal.getInstance(document.getElementById('modalEdit')).hide();
    });
</script>
@endpush    