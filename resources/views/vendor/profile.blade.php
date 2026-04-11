@extends('layouts.apps')

@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')
@section('icon', 'mdi mdi-account-edit')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('vendor.dashboard') }}">Dashboard</a>
</li>
<li class="breadcrumb-item active">Edit Profile</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Informasi Akun</h4>

                <form method="POST" action="{{ route('vendor.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label>Nama Kantin</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', Auth::guard('vendor')->user()->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', Auth::guard('vendor')->user()->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>
                    <p class="text-muted" style="font-size:13px">Kosongkan password jika tidak ingin menggantinya.</p>

                    <div class="form-group mb-3">
                        <label>Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2">Simpan Perubahan</button>
                    <a href="{{ route('vendor.dashboard') }}" class="btn btn-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection