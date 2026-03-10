@extends('layouts.apps')

@section('title', 'Edit Barang')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
    <h2 class="mb-0">✏️ Edit Barang</h2>
</div>

<div class="card shadow-sm" style="max-width: 600px">
    <div class="card-body p-4">

        {{-- ✅ PERUBAHAN: tambah id="form-barang-edit" pada <form> --}}
        <form id="form-barang-edit" action="{{ route('barang.update', $barang->id_barang) }}" method="POST">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ID Barang - read only --}}
            <div class="mb-3">
                <label class="form-label fw-bold">ID Barang</label>
                <input
                    type="text"
                    class="form-control bg-secondary-subtle"
                    value="{{ $barang->id_barang }}"
                    disabled
                >
                <div class="form-text">ID tidak dapat diubah.</div>
            </div>

            {{-- Nama Barang --}}
            <div class="mb-3">
                <label for="nama" class="form-label fw-bold">
                    Nama Barang <span class="text-danger">*</span>
                </label>
                <input
                    type="text"
                    name="nama"
                    id="nama"
                    class="form-control @error('nama') is-invalid @enderror"
                    value="{{ old('nama', $barang->nama) }}"
                    required
                >
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Harga --}}
            <div class="mb-4">
                <label for="harga" class="form-label fw-bold">
                    Harga <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input
                        type="number"
                        name="harga"
                        id="harga"
                        class="form-control @error('harga') is-invalid @enderror"
                        value="{{ old('harga', $barang->harga) }}"
                        min="0"
                        required
                    >
                    @error('harga')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- ✅ PERUBAHAN: tombol submit DIHAPUS dari dalam <form> --}}

        </form>
        {{-- </form> ditutup di sini --}}

        {{-- ✅ PERUBAHAN: tombol submit dipindah ke LUAR <form> --}}
        <div class="d-flex gap-2 mt-3">
            <button type="button"
                    class="btn btn-warning btn-submit"
                    data-form="#form-barang-edit">
                <span class="btn-text">
                    💾 Simpan Perubahan
                </span>
                <span class="btn-loader d-none">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Memproses...
                </span>
            </button>
            <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>

    </div>
</div>

@endsection