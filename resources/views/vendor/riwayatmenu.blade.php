@extends('layouts.apps')

@section('title', 'Riwayat Menu')
@section('page-title', 'Riwayat Menu')
@section('icon', 'mdi mdi-history')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('vendor.dashboard') }}">Dashboard</a>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('vendor.menu.index') }}">Menu</a>
</li>
<li class="breadcrumb-item active">Riwayat</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Riwayat Perubahan Menu</h4>
                    <div class="d-flex gap-2">
                        <button class="btn btn-gradient-secondary btn-sm" onclick="filterHistory('all')">
                            <i class="mdi mdi-filter"></i> Semua
                        </button>
                        <button class="btn btn-gradient-warning btn-sm" onclick="filterHistory('updated')">
                            <i class="mdi mdi-pencil"></i> Diupdate
                        </button>
                        <button class="btn btn-gradient-danger btn-sm" onclick="filterHistory('deleted')">
                            <i class="mdi mdi-delete"></i> Dihapus
                        </button>
                    </div>
                </div>

                {{-- Filter Info --}}
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-information-outline mdi-24px me-2"></i>
                        <div>
                            <strong>Informasi:</strong>
                            <span class="ms-2">Menampilkan riwayat perubahan menu dalam 30 hari terakhir</span>
                        </div>
                    </div>
                </div>

                @forelse($menuHistory ?? [] as $history)
                <div class="card mb-3 border {{ $history['action'] === 'deleted' ? 'border-danger' : 'border-warning' }}" data-action="{{ $history['action'] }}">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                @if($history['action'] === 'deleted')
                                    <div class="badge badge-gradient-danger me-2">
                                        <i class="mdi mdi-delete"></i> Dihapus
                                    </div>
                                @elseif($history['action'] === 'updated')
                                    <div class="badge badge-gradient-warning me-2">
                                        <i class="mdi mdi-pencil"></i> Diupdate
                                    </div>
                                @elseif($history['action'] === 'created')
                                    <div class="badge badge-gradient-success me-2">
                                        <i class="mdi mdi-plus"></i> Dibuat
                                    </div>
                                @endif

                                <strong>{{ $history['menu_name'] }}</strong>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">{{ $history['date'] }}</small>
                                @if($history['action'] === 'updated')
                                    <small class="text-primary">
                                        <i class="mdi mdi-arrow-up"></i>
                                        Rp {{ number_format($history['old_price'], 0, ',', '.') }}
                                        →
                                        Rp {{ number_format($history['new_price'], 0, ',', '.') }}
                                    </small>
                                @elseif($history['action'] === 'deleted')
                                    <small class="text-danger">
                                        Harga: Rp {{ number_format($history['price'], 0, ',', '.') }}
                                    </small>
                                @endif
                            </div>
                        </div>

                        @if(isset($history['category']))
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge badge-gradient-info">{{ $history['category'] }}</span>
                            <small class="text-muted">
                                <i class="mdi mdi-clock-outline"></i> {{ $history['time'] }}
                            </small>
                        </div>
                        @endif

                        @if($history['action'] === 'updated' && isset($history['changes']))
                        <div class="mt-2 pt-2 border-top">
                            <small class="text-muted mb-1 d-block">Perubahan:</small>
                            @foreach($history['changes'] as $field => $change)
                            <div class="d-flex align-items-center mb-1">
                                <span class="text-muted me-2" style="min-width: 80px;">
                                    {{ ucfirst($field) }}:
                                </span>
                                <span>
                                    <del class="text-danger me-1">{{ $change['old'] }}</del>
                                    <i class="mdi mdi-arrow-right text-primary mx-1"></i>
                                    <ins class="text-success">{{ $change['new'] }}</ins>
                                </span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="mdi mdi-history mdi-48px"></i>
                    <p class="mt-3">Belum ada riwayat perubahan menu</p>
                    <a href="{{ route('vendor.menu.create') }}" class="btn btn-gradient-primary btn-sm mt-2">
                        <i class="mdi mdi-plus"></i> Tambah Menu Baru
                    </a>
                </div>
                @endforelse

                {{-- Pagination --}}
                @if(isset($menuHistory) && count($menuHistory) > 0)
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <small class="text-muted">
                        Menampilkan {{ count($menuHistory) }} riwayat
                    </small>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">← Sebelumnya</a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">3</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">Selanjutnya →</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<style>
.border-danger {
    border-left: 4px solid #f44336 !important;
}

.border-warning {
    border-left: 4px solid #ff9800 !important;
}

.card[data-action="deleted"] {
    background-color: #fff5f5;
}

.card[data-action="updated"] {
    background-color: #fffbf0;
}

.card[data-action="created"] {
    background-color: #f0fff4;
}
</style>

<script>
function filterHistory(action) {
    const cards = document.querySelectorAll('[data-action]');

    cards.forEach(card => {
        if (action === 'all') {
            card.style.display = 'block';
        } else {
            if (card.getAttribute('data-action') === action) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        }
    });

    // Update button states
    const buttons = document.querySelectorAll('.btn-gradient-secondary, .btn-gradient-warning, .btn-gradient-danger');
    buttons.forEach(btn => {
        btn.classList.remove('btn-shadow');
    });
}
</script>
@endsection