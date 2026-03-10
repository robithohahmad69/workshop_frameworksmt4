@extends('layouts.apps')

@section('title', 'Edit Buku')
@section('page-title', 'Edit Buku')
@section('icon', 'mdi mdi-pencil')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item"><a href="/buku">Buku</a></li>
<li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Edit Buku</h4>
                <p class="card-description">Edit buku <strong>{{ $buku->judul }}</strong></p>

                {{-- ✅ PERUBAHAN: tambah id="form-buku-edit" pada <form> --}}
                <form id="form-buku-edit" action="/buku/{{ $buku->id_buku }}" method="POST" class="forms-sample">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kode">Kode Buku <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('kode') is-invalid @enderror"
                                       id="kode"
                                       name="kode"
                                       value="{{ old('kode', $buku->kode) }}"
                                       required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_kategori">Kategori <span class="text-danger">*</span></label>
                                <select class="form-control @error('id_kategori') is-invalid @enderror"
                                        id="id_kategori"
                                        name="id_kategori"
                                        required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($kategoris as $kategori)
                                        <option value="{{ $kategori->id_kategori }}"
                                            {{ old('id_kategori', $buku->id_kategori) == $kategori->id_kategori ? 'selected' : '' }}>
                                            {{ $kategori->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="judul">Judul Buku <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('judul') is-invalid @enderror"
                               id="judul"
                               name="judul"
                               value="{{ old('judul', $buku->judul) }}"
                               required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pengarang">Pengarang <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('pengarang') is-invalid @enderror"
                               id="pengarang"
                               name="pengarang"
                               value="{{ old('pengarang', $buku->pengarang) }}"
                               required>
                        @error('pengarang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ✅ PERUBAHAN: tombol submit DIHAPUS dari dalam <form> --}}

                </form>
                {{-- </form> ditutup di sini --}}

                {{-- ✅ PERUBAHAN: tombol submit dipindah ke LUAR <form> --}}
                <div class="mt-3">
                    <button type="button"
                            class="btn btn-gradient-warning me-2 btn-submit"
                            data-form="#form-buku-edit">
                        <span class="btn-text">
                            <i class="mdi mdi-content-save"></i> Update
                        </span>
                        <span class="btn-loader d-none">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Memproses...
                        </span>
                    </button>
                    <a href="/buku" class="btn btn-light">
                        <i class="mdi mdi-arrow-left"></i> Batal
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection