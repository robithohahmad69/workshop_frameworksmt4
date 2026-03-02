@extends('layouts.apps')

@section('title', 'Detail Barang')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
    <h2 class="mb-0">🔍 Detail Barang</h2>
</div>

<div class="card shadow-sm" style="max-width: 600px">
    <div class="card-body p-4">

        <table class="table table-bordered mb-4">
            <tr>
                <th width="35%" class="bg-light">ID Barang</th>
                <td>{{ $barang->id_barang }}</td>
            </tr>
            <tr>
                <th class="bg-light">Nama Barang</th>
                <td>{{ $barang->nama }}</td>
            </tr>
            <tr>
                <th class="bg-light">Harga</th>
                <td>
                    <strong class="fs-5">Rp {{ number_format($barang->harga, 0, ',', '.') }}</strong>
                </td>
            </tr>
            <tr>
                <th class="bg-light">Ditambahkan</th>
                <td>{{ $barang->created_at->translatedFormat('d F Y H:i') }}</td>
            </tr>
            <tr>
                <th class="bg-light">Terakhir Diubah</th>
                <td>{{ $barang->updated_at->translatedFormat('d F Y H:i') }}</td>
            </tr>
        </table>

        <div class="d-flex gap-2">
            <a href="{{ route('barang.edit', $barang->id_barang) }}"
               class="btn btn-warning">✏️ Edit Barang</a>

            <form action="{{ route('barang.destroy', $barang->id_barang) }}"
                  method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">🗑️ Hapus Barang</button>
            </form>
        </div>

    </div>
</div>

@endsection