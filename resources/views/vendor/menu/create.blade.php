@extends('layouts.apps')

@section('title', 'Tambah Menu')
@section('page-title', 'Tambah Menu')
@section('icon', 'mdi mdi-plus-circle')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('vendor.dashboard') }}">Dashboard</a>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('vendor.menu.index') }}">Menu</a>
</li>
<li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Form Tambah Menu</h4>

                <form method="POST" action="{{ route('vendor.menu.store') }}">
                    @csrf

                    <div class="form-group mb-3">
                        <label>Nama Menu</label>
                        <input type="text" name="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama') }}"
                            placeholder="contoh: Nasi Goreng Spesial" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>Kategori</label>
                        <select name="kategori"
                            class="form-control @error('kategori') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Makanan" {{ old('kategori') == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                            <option value="Minuman" {{ old('kategori') == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                            <option value="Snack" {{ old('kategori') == 'Snack' ? 'selected' : '' }}>Snack</option>
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga"
                            class="form-control @error('harga') is-invalid @enderror"
                            value="{{ old('harga') }}"
                            placeholder="contoh: 15000" min="500" required>
                        @error('harga')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2">Simpan Menu</button>
                    <a href="{{ route('vendor.menu.index') }}" class="btn btn-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection