@extends('layouts.apps')

@section('title', 'Edit Menu')
@section('page-title', 'Edit Menu')
@section('icon', 'mdi mdi-pencil')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('vendor.dashboard') }}">Dashboard</a>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('vendor.menu.index') }}">Menu</a>
</li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Form Edit Menu</h4>

                <form method="POST" action="{{ route('vendor.menu.update', $menu) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label>Nama Menu</label>
                        <input type="text" name="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama', $menu->nama) }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>Kategori</label>
                        <select name="kategori"
                            class="form-control @error('kategori') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Makanan" {{ old('kategori', $menu->kategori) == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                            <option value="Minuman" {{ old('kategori', $menu->kategori) == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                            <option value="Snack" {{ old('kategori', $menu->kategori) == 'Snack' ? 'selected' : '' }}>Snack</option>
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga"
                            class="form-control @error('harga') is-invalid @enderror"
                            value="{{ old('harga', $menu->harga) }}"
                            min="500" required>
                        @error('harga')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2">Update Menu</button>
                    <a href="{{ route('vendor.menu.index') }}" class="btn btn-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection