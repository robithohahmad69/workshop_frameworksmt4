@extends('layouts.apps')

@section('title', 'Tambah Barang')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali</a>
    <h2 class="mb-0">➕ Tambah Barang Baru</h2>
</div>

<div class="card shadow-sm" style="max-width: 600px">
    <div class="card-body p-4">

        <form action="{{ route('barang.store') }}" method="POST">
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

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">➕ Tambah Barang</button>
                <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>

        </form>

    </div>
</div>

@endsection