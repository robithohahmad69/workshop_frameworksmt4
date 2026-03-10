@extends('layouts.apps')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')
@section('icon', 'mdi mdi-tag-plus')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item"><a href="/kategori">Kategori</a></li>
<li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Tambah Kategori</h4>
                <p class="card-description">Isi form di bawah untuk menambahkan kategori baru</p>

                {{-- ✅ PERUBAHAN: tambah id="form-kategori-create" pada <form> --}}
                <form id="form-kategori-create" action="/kategori" method="POST" class="forms-sample">
                    @csrf

                    <div class="form-group">
                        <label for="nama_kategori">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('nama_kategori') is-invalid @enderror"
                               id="nama_kategori"
                               name="nama_kategori"
                               value="{{ old('nama_kategori') }}"
                               placeholder="Contoh: Novel, Biografi, Komik"
                               required>
                        @error('nama_kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ✅ PERUBAHAN: tombol submit DIHAPUS dari dalam <form> --}}

                </form>
                {{-- </form> ditutup di sini --}}

                {{-- ✅ PERUBAHAN: tombol submit dipindah ke LUAR <form> --}}
                <div class="mt-3">
                    <button type="button"
                            class="btn btn-gradient-primary me-2 btn-submit"
                            data-form="#form-kategori-create">
                        <span class="btn-text">
                            <i class="mdi mdi-content-save"></i> Simpan
                        </span>
                        <span class="btn-loader d-none">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Memproses...
                        </span>
                    </button>
                    <a href="/kategori" class="btn btn-light">
                        <i class="mdi mdi-arrow-left"></i> Batal
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection