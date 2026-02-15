@extends('layouts.apps')

@section('title', 'Detail Kategori')
@section('page-title', 'Detail Kategori')
@section('icon', 'mdi mdi-tag')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item"><a href="/kategori">Kategori</a></li>
<li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
<div class="row">
    <!-- Info Kategori -->
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Informasi Kategori</h4>
                
                <div class="mt-4">
                    <div class="mb-3">
                        <label class="text-muted">Nama Kategori</label>
                        <h5 class="mb-0">{{ $kategori->nama_kategori }}</h5>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted">Jumlah Buku</label>
                        <h5 class="mb-0">
                            <span class="badge badge-gradient-info">
                                {{ $kategori->bukus->count() }} Buku
                            </span>
                        </h5>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted">Dibuat Pada</label>
                        <p class="mb-0">{{ $kategori->created_at->format('d F Y, H:i') }} WIB</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-muted">Terakhir Diupdate</label>
                        <p class="mb-0">{{ $kategori->updated_at->format('d F Y, H:i') }} WIB</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="/kategori/{{ $kategori->id_kategori }}/edit" class="btn btn-gradient-warning btn-sm">
                        <i class="mdi mdi-pencil"></i> Edit
                    </a>
                    <a href="/kategori" class="btn btn-light btn-sm">
                        <i class="mdi mdi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Daftar Buku -->
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Daftar Buku dalam Kategori Ini</h4>
                    <a href="/buku/create" class="btn btn-gradient-primary btn-sm">
                        <i class="mdi mdi-plus"></i> Tambah Buku
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kategori->bukus as $index => $buku)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge badge-gradient-dark">{{ $buku->kode }}</span>
                                </td>
                                <td>{{ $buku->judul }}</td>
                                <td>{{ $buku->pengarang }}</td>
                                <td>
                                    <a href="/buku/{{ $buku->id_buku }}" class="btn btn-gradient-info btn-sm">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="/buku/{{ $buku->id_buku }}/edit" class="btn btn-gradient-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="mdi mdi-alert-circle-outline mdi-24px"></i><br>
                                    Belum ada buku dalam kategori ini
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