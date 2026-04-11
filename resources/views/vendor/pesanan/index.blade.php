@extends('layouts.apps')

@section('title', 'Pesanan Lunas')
@section('page-title', 'Pesanan Lunas')
@section('icon', 'mdi mdi-clipboard-check')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('vendor.dashboard') }}">Dashboard</a>
</li>
<li class="breadcrumb-item active">Pesanan Lunas</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Daftar Pesanan Lunas</h4>

                @forelse($pesanan as $order)
                <div class="card mb-3 border">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $order->customer_name }}</strong>
                                <span class="text-muted ms-2" style="font-size:13px">
                                    {{ $order->created_at->format('d M Y H:i') }}
                                </span>
                            </div>
                            <div>
                                <label class="badge badge-gradient-success">Lunas</label>
                                <strong class="ms-2">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Menu</th>
                                        <th>Qty</th>
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>{{ $item->menu->nama }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item->harga * $item->qty, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="mdi mdi-clipboard-off mdi-48px"></i>
                    <p class="mt-2">Belum ada pesanan lunas</p>
                </div>
                @endforelse

            </div>
        </div>
    </div>
</div>
@endsection