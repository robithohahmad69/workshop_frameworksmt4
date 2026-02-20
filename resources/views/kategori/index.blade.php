@extends('layouts.apps')

@section('title', 'Data Kategori')
@section('page-title', 'Data Kategori')
@section('icon', 'mdi mdi-tag-multiple')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item active" aria-current="page">Kategori</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Daftar Kategori</h4>
                    <a href="/kategori/create" class="btn btn-gradient-primary btn-sm">
                        <i class="mdi mdi-plus"></i> Tambah Kategori
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kategori</th>
                                <th>Jumlah Buku</th>
                                <th>Dibuat Pada</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kategoris as $index => $kategori)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $kategori->nama_kategori }}</td>
                                <td>
                                    <label class="badge badge-gradient-info"> <!-- bukus dari model kategori -->
                                        {{ $kategori->bukus->count() }} Buku
                                    </label>
                                </td>
                                <td>{{ $kategori->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="/kategori/{{ $kategori->id_kategori }}" class="btn btn-gradient-info btn-sm">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="/kategori/{{ $kategori->id_kategori }}/edit" class="btn btn-gradient-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="/kategori/{{ $kategori->id_kategori }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-gradient-danger btn-sm" 
                                            onclick="return confirm('Yakin ingin menghapus kategori {{ $kategori->nama_kategori }}?')">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="mdi mdi-alert-circle-outline mdi-24px"></i><br>
                                    Tidak ada data kategori. <a href="/kategori/create">Tambah kategori pertama</a>
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