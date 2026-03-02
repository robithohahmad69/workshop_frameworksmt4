@extends('layouts.apps')

@section('title', 'Data Barang')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">📦 Data Barang</h2>
    <a href="{{ route('barang.create') }}" class="btn btn-success">➕ Tambah Barang</a>
</div>

{{-- Notifikasi sukses --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        ✅ {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Tombol cetak --}}
<button class="btn btn-primary mb-3" onclick="bukaModalCetak()">
    🖨️ Cetak Label Terpilih
</button>

{{-- Tabel --}}
<div class="card shadow-sm">
    <div class="card-body">
        <table id="tabelBarang" class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th style="width:40px">
                        <input type="checkbox" id="checkAll" title="Pilih Semua">
                    </th>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th style="width:200px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barang as $b)
                <tr>
                    <td><input type="checkbox" class="check-item" value="{{ $b->id_barang }}"></td>
                    <td>{{ $b->id_barang }}</td>
                    <td>{{ $b->nama }}</td>
                    <td>Rp {{ number_format($b->harga, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('barang.show', $b->id_barang) }}"
                         class="btn btn-gradient-info btn-sm">
                        <i class="mdi mdi-eye"></i></a>

                        <a href="{{ route('barang.edit', $b->id_barang) }}"
                          class="btn btn-gradient-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i></a>

                        <form action="{{ route('barang.destroy', $b->id_barang) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-gradient-danger btn-sm">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ===================== MODAL CETAK ===================== --}}
<div class="modal fade" id="modalCetak" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🖨️ Pengaturan Cetak Label</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('barang.pdf') }}" id="formCetak" target="_blank">
                @csrf
                <div id="hiddenIds"></div>

                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Kertas TnJ No.108 memiliki <strong>5 kolom × 8 baris = 40 label</strong>.
                        Masukkan posisi label pertama yang akan diisi.
                    </p>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label fw-bold">Kolom X (1–5)</label>
                            <input type="number" name="start_x" id="start_x"
                                   class="form-control" min="1" max="5" value="1" required>
                            <div class="form-text">Posisi kolom dari kiri</div>
                        </div>
                        <div class="col">
                            <label class="form-label fw-bold">Baris Y (1–8)</label>
                            <input type="number" name="start_y" id="start_y"
                                   class="form-control" min="1" max="8" value="1" required>
                            <div class="form-text">Posisi baris dari atas</div>
                        </div>
                    </div>

                    <div id="infoLabel" class="alert alert-info py-2 mb-3"></div>

                    <label class="form-label fw-bold">Preview Kertas:</label>
                    <div id="previewGrid"
                         style="display:grid; grid-template-columns: repeat(5, 1fr); gap:4px;">
                    </div>

                    <div class="d-flex gap-3 mt-2" style="font-size:12px">
                        <span><span style="display:inline-block;width:14px;height:14px;background:#e9ecef;border:1px solid #ccc;border-radius:2px"></span> Terlewati</span>
                        <span><span style="display:inline-block;width:14px;height:14px;background:#d1e7dd;border:1px solid #ccc;border-radius:2px"></span> Akan diisi</span>
                        <span><span style="display:inline-block;width:14px;height:14px;background:#fff;border:1px solid #ccc;border-radius:2px"></span> Kosong</span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">📄 Generate PDF</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Aktifkan DataTables
    $('#tabelBarang').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        },
        columnDefs: [
            { orderable: false, targets: [0, 4] }
        ]
    });

    // Checkbox pilih semua
    $('#checkAll').on('change', function () {
        $('.check-item').prop('checked', this.checked);
    });

    // Buka modal cetak
    function bukaModalCetak() {
        let dipilih = $('.check-item:checked');
        if (dipilih.length === 0) {
            alert('⚠️ Pilih minimal 1 barang terlebih dahulu!');
            return;
        }

        // Isi hidden inputs
        let hiddenDiv = $('#hiddenIds');
        hiddenDiv.empty();
        dipilih.each(function () {
            hiddenDiv.append(
                `<input type="hidden" name="selected_ids[]" value="${$(this).val()}">`
            );
        });

        updatePreview();
        new bootstrap.Modal('#modalCetak').show();
    }

    // Update preview saat X/Y berubah
    $('#start_x, #start_y').on('input', updatePreview);

    function updatePreview() {
        let x = Math.min(Math.max(parseInt($('#start_x').val()) || 1, 1), 5);
        let y = Math.min(Math.max(parseInt($('#start_y').val()) || 1, 1), 8);

        let startIndex = (y - 1) * 5 + (x - 1);
        let jumlah     = $('.check-item:checked').length;
        let sisa       = 40 - startIndex;

        // Render grid 40 kotak
        let grid = $('#previewGrid');
        grid.empty();
        for (let i = 0; i < 40; i++) {
            let tipe, teks;
            if (i < startIndex)              { tipe = 'terlewat'; teks = '–'; }
            else if (i < startIndex + jumlah){ tipe = 'terisi';   teks = (i - startIndex + 1); }
            else                             { tipe = 'kosong';   teks = ''; }

            grid.append(`
                <div style="height:28px;border:1px solid #dee2e6;border-radius:4px;
                            display:flex;align-items:center;justify-content:center;
                            font-size:10px;
                            background:${tipe==='terlewat'?'#e9ecef':tipe==='terisi'?'#d1e7dd':'#fff'};
                            color:${tipe==='terisi'?'#0f5132':tipe==='terlewat'?'#adb5bd':'#000'};
                            font-weight:${tipe==='terisi'?'bold':'normal'}">
                    ${teks}
                </div>
            `);
        }

        let warna = jumlah > sisa ? 'danger' : 'info';
        let pesan = jumlah > sisa
            ? `⚠️ ${jumlah} label dipilih tapi hanya ada ${sisa} ruang tersisa!`
            : `✅ ${jumlah} label akan dicetak mulai kolom ${x}, baris ${y}. Sisa: ${sisa - jumlah} label.`;

        $('#infoLabel').removeClass('alert-info alert-danger').addClass(`alert-${warna}`).html(pesan);
    }
</script>
@endpush