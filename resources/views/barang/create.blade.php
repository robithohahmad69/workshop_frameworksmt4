@extends('layouts.apps')

@section('title', 'Tambah Barang')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
    <h2 class="mb-0">➕ Tambah Barang Baru</h2>
</div>

<div class="card shadow-sm" style="max-width: 600px">
    <div class="card-body p-4">

        {{-- ✅ PERUBAHAN: tambah id="form-barang-create" pada <form> --}}
        <form id="form-barang-create" action="{{ route('barang.store') }}" method="POST">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                    placeholder="Contoh: Buku Tulis A5"
                    value="{{ old('nama') }}"
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
                        placeholder="Contoh: 5000"
                        value="{{ old('harga') }}"
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
                    class="btn btn-success btn-submit"
                    data-form="#form-barang-create">
                <span class="btn-text">
                    ➕ Tambah Barang
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