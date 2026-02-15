@extends('layouts.apps')

@section('title', 'Data Buku')
@section('page-title', 'Data Buku')
@section('icon', 'mdi mdi-book-open-variant')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item active" aria-current="page">Buku</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Daftar Buku</h4>
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
                                <th>Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bukus as $index => $buku)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge badge-gradient-dark">{{ $buku->kode }}</span></td>
                                <td>{{ $buku->judul }}</td>
                                <td>{{ $buku->pengarang }}</td>
                                <td>
                                    <label class="badge badge-gradient-info">
                                        {{ $buku->kategori->nama_kategori }}
                                    </label>
                                </td>
                                <td>
                                    <a href="/buku/{{ $buku->id_buku }}" class="btn btn-gradient-info btn-sm">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="/buku/{{ $buku->id_buku }}/edit" class="btn btn-gradient-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="/buku/{{ $buku->id_buku }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-gradient-danger btn-sm" 
                                            onclick="return confirm('Yakin ingin menghapus buku {{ $buku->judul }}?')">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="mdi mdi-alert-circle-outline mdi-24px"></i><br>
                                    Tidak ada data buku. <a href="/buku/create">Tambah buku pertama</a>
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