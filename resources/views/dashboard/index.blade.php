@extends('layouts.apps')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('icon', 'mdi mdi-home')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
</li>
@endsection

@section('content')
<!-- Statistik Cards -->
<div class="row">
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-danger card-img-holder text-white">
            <div class="card-body">
                <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                <h4 class="font-weight-normal mb-3">Total Buku 
                    <i class="mdi mdi-book-open-variant mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5">{{ $totalBuku }}</h2>
                <h6 class="card-text">Buku tersedia di perpustakaan</h6>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-info card-img-holder text-white">
            <div class="card-body">
                <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                <h4 class="font-weight-normal mb-3">Total Kategori 
                    <i class="mdi mdi-tag-multiple mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5">{{ $totalKategori }}</h2>
                <h6 class="card-text">Kategori buku yang tersedia</h6>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 stretch-card grid-margin">
        <div class="card bg-gradient-success card-img-holder text-white">
            <div class="card-body">
                <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                <h4 class="font-weight-normal mb-3">Buku Terbaru 
                    <i class="mdi mdi-diamond mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5">{{ $bukuBulanIni }}</h2>
                <h6 class="card-text">Ditambahkan bulan ini</h6>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Buku Terbaru & Kategori -->
<div class="row">
    <div class="col-md-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Buku Terbaru</h4>
                    <a href="/buku" class="btn btn-gradient-primary btn-sm">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bukuTerbaru as $buku)
                            <tr>
                                <td>
                                    <span class="badge badge-gradient-dark">{{ $buku->kode }}</span>
                                </td>
                                <td>{{ Str::limit($buku->judul, 30) }}</td>
                                <td>
                                    <label class="badge badge-gradient-info">{{ $buku->kategori->nama_kategori }}</label>
                                </td>
                                <td>{{ $buku->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    Belum ada buku
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Kategori Populer</h4>
                    <a href="/kategori" class="btn btn-gradient-primary btn-sm">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Jumlah Buku</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kategoriPopuler as $kategori)
                            <tr>
                                <td>{{ $kategori->nama_kategori }}</td>
                                <td>
                                    <label class="badge badge-gradient-success">
                                        {{ $kategori->bukus_count }} Buku
                                    </label>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">
                                    Belum ada kategori
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

<!-- Quick Actions -->
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Quick Actions</h4>
                <p class="card-description">Akses cepat ke fitur-fitur utama</p>
                <div class="row mt-4">
                    <div class="col-md-3">
                        <a href="/buku/create" class="text-decoration-none">
                            <div class="card bg-gradient-primary text-white text-center p-4">
                                <i class="mdi mdi-plus-circle mdi-48px"></i>
                                <h5 class="mt-3">Tambah Buku</h5>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/kategori/create" class="text-decoration-none">
                            <div class="card bg-gradient-success text-white text-center p-4">
                                <i class="mdi mdi-tag-plus mdi-48px"></i>
                                <h5 class="mt-3">Tambah Kategori</h5>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/buku" class="text-decoration-none">
                            <div class="card bg-gradient-info text-white text-center p-4">
                                <i class="mdi mdi-book-open-page-variant mdi-48px"></i>
                                <h5 class="mt-3">Lihat Buku</h5>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="/kategori" class="text-decoration-none">
                            <div class="card bg-gradient-warning text-white text-center p-4">
                                <i class="mdi mdi-format-list-bulleted mdi-48px"></i>
                                <h5 class="mt-3">Lihat Kategori</h5>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection