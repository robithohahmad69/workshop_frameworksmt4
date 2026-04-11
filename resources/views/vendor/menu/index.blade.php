@extends('layouts.apps')

@section('title', 'Kelola Menu')
@section('page-title', 'Kelola Menu')
@section('icon', 'mdi mdi-food')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('vendor.dashboard') }}">Dashboard</a>
</li>
<li class="breadcrumb-item active">Menu</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Daftar Menu</h4>
                    <a href="{{ route('vendor.menu.create') }}" class="btn btn-gradient-primary btn-sm">
                        <i class="mdi mdi-plus"></i> Tambah Menu
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Menu</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus as $i => $menu)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $menu->nama }}</td>
                                <td>
                                    <label class="badge badge-gradient-info">{{ $menu->kategori }}</label>
                                </td>
                                <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('vendor.menu.edit', $menu) }}"
                                        class="btn btn-gradient-warning btn-sm">
                                        <i class="mdi mdi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('vendor.menu.destroy', $menu) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus menu ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-gradient-danger btn-sm">
                                            <i class="mdi mdi-delete"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada menu. <a href="{{ route('vendor.menu.create') }}">Tambah sekarang</a>
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