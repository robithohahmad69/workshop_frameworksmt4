@extends('layouts.apps')

@section('title', 'Dashboard Vendor')
@section('page-title', 'Dashboard Vendor')
@section('icon', 'mdi mdi-store')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    <span></span>Overview <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
</li>
@endsection

@section('content')
<!-- Statistik Cards -->
<div class="row">
    <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-primary card-img-holder text-white">
            <div class="card-body">
                <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle">
                <h4 class="font-weight-normal mb-3">Total Menu
                    <i class="mdi mdi-food mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5">{{ $totalMenu }}</h2>
                <h6 class="card-text">Menu tersedia di kantin</h6>
            </div>
        </div>
    </div>

    <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-info card-img-holder text-white">
            <div class="card-body">
                <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle">
                <h4 class="font-weight-normal mb-3">Total Pesanan
                    <i class="mdi mdi-cart mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5">{{ $totalPesanan }}</h2>
                <h6 class="card-text">Semua pesanan masuk</h6>
            </div>
        </div>
    </div>

    <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-success card-img-holder text-white">
            <div class="card-body">
                <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle">
                <h4 class="font-weight-normal mb-3">Pesanan Lunas
                    <i class="mdi mdi-check-circle mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5">{{ $pesananLunas }}</h2>
                <h6 class="card-text">Pembayaran sukses</h6>
            </div>
        </div>
    </div>

    <div class="col-md-3 stretch-card grid-margin">
        <div class="card bg-gradient-danger card-img-holder text-white">
            <div class="card-body">
                <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle">
                <h4 class="font-weight-normal mb-3">Pendapatan
                    <i class="mdi mdi-currency-usd mdi-24px float-end"></i>
                </h4>
                <h2 class="mb-5">Rp {{ number_format($pendapatan, 0, ',', '.') }}</h2>
                <h6 class="card-text">Total dari pesanan lunas</h6>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Pesanan Terbaru & Quick Actions -->
<div class="row">
    <div class="col-md-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Pesanan Terbaru</h4>
                    <div>
                        <a href="{{ route('vendor.orders.index') }}" class="btn btn-gradient-success btn-sm me-2">📋 Pesanan</a>
                        <a href="{{ route('vendor.pesanan.index') }}" class="btn btn-gradient-primary btn-sm">Riwayat</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesananTerbaru as $order)
                            <tr>
                                <td>{{ $order->customer_name }}</td>
                                <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td>
                                    @if($order->status_bayar === 'lunas')
                                        <label class="badge badge-gradient-success">Lunas</label>
                                    @else
                                        <label class="badge badge-gradient-warning">Pending</label>
                                    @endif
                                </td>
                                <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada pesanan</td>
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
                <h4 class="card-title">Quick Actions</h4>
                <p class="card-description">Akses cepat fitur utama</p>
                <div class="row mt-3">
                    <div class="col-6 mb-3">
                        <a href="{{ route('vendor.menu.create') }}" class="text-decoration-none">
                            <div class="card bg-gradient-primary text-white text-center p-3">
                                <i class="mdi mdi-plus-circle mdi-36px"></i>
                                <h6 class="mt-2 mb-0">Tambah Menu</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('vendor.menu.index') }}" class="text-decoration-none">
                            <div class="card bg-gradient-info text-white text-center p-3">
                                <i class="mdi mdi-food mdi-36px"></i>
                                <h6 class="mt-2 mb-0">Kelola Menu</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('vendor.orders.index') }}" class="text-decoration-none">
                            <div class="card bg-gradient-success text-white text-center p-3">
                                <i class="mdi mdi-receipt mdi-36px"></i>
                                <h6 class="mt-2 mb-0">Pesanan</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('vendor.pesanan.index') }}" class="text-decoration-none">
                            <div class="card bg-gradient-info text-white text-center p-3">
                                <i class="mdi mdi-history mdi-36px"></i>
                                <h6 class="mt-2 mb-0">Riwayat</h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('vendor.profile') }}" class="text-decoration-none">
                            <div class="card bg-gradient-warning text-white text-center p-3">
                                <i class="mdi mdi-account-edit mdi-36px"></i>
                                <h6 class="mt-2 mb-0">Edit Profile</h6>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection