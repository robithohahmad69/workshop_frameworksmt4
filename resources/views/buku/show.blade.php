@extends('layouts.apps')

@section('title', 'Detail Buku')
@section('page-title', 'Detail Buku')
@section('icon', 'mdi mdi-book')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item"><a href="/buku">Buku</a></li>
<li class="breadcrumb-item active" aria-current="page">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Detail Buku</h4>
                
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="text-center mb-4">
                            <div class="bg-gradient-primary text-white p-4 rounded">
                                <i class="mdi mdi-book-open-variant mdi-48px"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-9">
                        <div class="mb-3">
                            <label class="text-muted">Kode Buku</label>
                            <h5 class="mb-0">
                                <span class="badge badge-gradient-dark">{{ $buku->kode }}</span>
                            </h5>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted">Judul Buku</label>
                            <h5 class="mb-0">{{ $buku->judul }}</h5>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted">Pengarang</label>
                            <h5 class="mb-0">{{ $buku->pengarang }}</h5>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted">Kategori</label>
                            <h5 class="mb-0">
                                <span class="badge badge-gradient-info">
                                    {{ $buku->kategori->nama_kategori }}
                                </span>
                            </h5>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted">Dibuat Pada</label>
                            <p class="mb-0">{{ $buku->created_at->format('d F Y, H:i') }} WIB</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted">Terakhir Diupdate</label>
                            <p class="mb-0">{{ $buku->updated_at->format('d F Y, H:i') }} WIB</p>
                        </div>
                        
                        <div class="mt-4">
                            <a href="/buku/{{ $buku->id_buku }}/edit" class="btn btn-gradient-warning btn-sm">
                                <i class="mdi mdi-pencil"></i> Edit
                            </a>
                            <a href="/buku" class="btn btn-light btn-sm">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                            <form action="/buku/{{ $buku->id_buku }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-gradient-danger btn-sm" 
                                    onclick="return confirm('Yakin ingin menghapus buku {{ $buku->judul }}?')">
                                    <i class="mdi mdi-delete"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

