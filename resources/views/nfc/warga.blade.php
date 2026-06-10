@extends('layouts.apps')

@section('title', 'Daftar Warga - NFC')
@section('icon', 'mdi mdi-account-multiple')
@section('page-title', 'Daftar Warga')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Daftar Warga</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Warga</h4>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Berhasil!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('warga.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama') }}"
                               class="form-control @error('nama') is-invalid @enderror" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>NIK (16 digit)</label>
                        <input type="text" name="nik" value="{{ old('nik') }}"
                               maxlength="16"
                               class="form-control @error('nik') is-invalid @enderror" required>
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Serial NFC e-KTP</label>
                        <input type="text" name="nfc_serial" value="{{ old('nfc_serial') }}"
                               placeholder="Contoh: 04:AB:CD:EF:12:34:56"
                               class="form-control @error('nfc_serial') is-invalid @enderror" required>
                        @error('nfc_serial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Alamat (opsional)</label>
                        <textarea name="alamat" rows="2"
                                  class="form-control">{{ old('alamat') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2">
                        <i class="mdi mdi-content-save"></i> Simpan
                    </button>
                    <a href="{{ route('nfc.index') }}" class="btn btn-gradient-success">
                        <i class="mdi mdi-nfc"></i> Ke Scanner
                    </a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Data Warga Terdaftar</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>Serial NFC</th>
                                <th>Alamat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($warga as $w)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $w->nama }}</td>
                                <td>{{ $w->nik }}</td>
                                <td><code>{{ $w->nfc_serial }}</code></td>
                                <td>{{ $w->alamat ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Belum ada warga terdaftar.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection