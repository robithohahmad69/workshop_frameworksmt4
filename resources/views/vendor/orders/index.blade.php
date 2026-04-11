@extends('layouts.apps')

@section('title', 'Pesanan Masuk')
@section('icon', 'mdi mdi-receipt')
@section('page-title', 'Pesanan Masuk')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pesanan</li>
@endsection

@section('styles')
<style>
    .tabs-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .tabs {
        display: flex;
        gap: 10px;
        border-bottom: 2px solid #eee;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
    .tab-btn {
        background: #f8f9fa;
        border: 1px solid #ddd;
        padding: 10px 24px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s;
        color: #666;
    }
    .tab-btn:hover {
        background: #e9ecef;
    }
    .tab-btn.active {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        border-color: transparent;
    }

    .order-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.2s;
    }
    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 12px;
        border-bottom: 1px solid #eee;
    }
    .order-id {
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }
    .order-time {
        font-size: 12px;
        color: #999;
    }
    .order-info {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-bottom: 12px;
    }
    .info-item {
        font-size: 13px;
    }
    .info-label {
        color: #888;
        margin-right: 8px;
    }
    .info-value {
        font-weight: 600;
        color: #333;
    }
    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-processing { background: #cce5ff; color: #004085; }
    .status-completed { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }

    .payment-lunas { background: #d4edda; color: #155724; }
    .payment-pending { background: #fff3cd; color: #856404; }

    .order-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
        flex-wrap: wrap;
    }
    .btn-view {
        flex: 1;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: bold;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        min-width: 120px;
    }
    .btn-view:hover { opacity: 0.9; }

    .status-dropdown {
        flex: 1;
        position: relative;
        min-width: 180px;
    }
    .btn-status-trigger {
        width: 100%;
        background: #007bff;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-status-trigger:hover { background: #0056b3; }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        z-index: 1000;
        overflow: hidden;
    }
    .dropdown-menu.show { display: block; }
    .dropdown-item {
        display: block;
        width: 100%;
        padding: 12px 16px;
        border: none;
        background: none;
        text-align: left;
        cursor: pointer;
        font-size: 13px;
        transition: background 0.2s;
        border: none;
    }
    .dropdown-item:hover { background: #f5f5f5; }
    .dropdown-item:first-child { border-radius: 8px 8px 0 0; }
    .dropdown-item:last-child { border-radius: 0 0 8px 8px; }

    .dropdown-item.status-pending { color: #856404; }
    .dropdown-item.status-pending:hover { background: #fff3cd; }
    .dropdown-item.status-processing { color: #004085; }
    .dropdown-item.status-processing:hover { background: #cce5ff; }
    .dropdown-item.status-completed { color: #155724; }
    .dropdown-item.status-completed:hover { background: #d4edda; }
    .dropdown-item.status-cancelled { color: #721c24; }
    .dropdown-item.status-cancelled:hover { background: #f8d7da; }

    .items-preview {
        background: #f9f9f9;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 12px;
    }
    .item-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #666;
        padding: 4px 0;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #aaa;
    }
    .empty-state i {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .history-section {
        margin-top: 30px;
        padding-top: 30px;
        border-top: 2px dashed #ddd;
    }
    .history-title {
        font-size: 20px;
        font-weight: bold;
        color: #666;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            {{-- Tabs for Active Orders and History --}}
            <div class="tabs-container">
                <div class="tabs">
                    <button class="tab-btn active" onclick="switchTab('active')">
                        📋 Pesanan Aktif
                    </button>
                    <button class="tab-btn" onclick="switchTab('history')">
                        📚 Riwayat Pesanan
                    </button>
                </div>

                {{-- Active Orders Tab --}}
                <div id="active-orders-tab">
                    @if($orders->isNotEmpty())
                        @foreach($orders as $order)
                            @if($order->status !== 'completed' && $order->status !== 'cancelled')
                                @include('vendor.orders.order-card', ['order' => $order])
                            @endif
                        @endforeach

                        @php
                            $activeOrders = $orders->filter(function($order) {
                                return $order->status !== 'completed' && $order->status !== 'cancelled';
                            });
                        @endphp

                        @if($activeOrders->isEmpty())
                        <div class="empty-state">
                            <i class="mdi mdi-check-circle"></i>
                            <p>Tidak ada pesanan aktif</p>
                            <small>Semua pesanan telah selesai atau dibatalkan</small>
                        </div>
                        @endif
                    @else
                    <div class="empty-state">
                        <i class="mdi mdi-receipt"></i>
                        <p>Belum ada pesanan</p>
                    </div>
                    @endif
                </div>

                {{-- History Tab --}}
                <div id="history-orders-tab" style="display: none;">
                    @if($orders->isNotEmpty())
                        @php
                            $historyOrders = $orders->filter(function($order) {
                                return $order->status === 'completed' || $order->status === 'cancelled';
                            });
                        @endphp

                        @if($historyOrders->isNotEmpty())
                            @foreach($historyOrders as $order)
                                @include('vendor.orders.order-card', ['order' => $order])
                            @endforeach
                        @else
                        <div class="empty-state">
                            <i class="mdi mdi-history"></i>
                            <p>Belum ada riwayat pesanan</p>
                            <small>Pesanan selesai akan muncul di sini</small>
                        </div>
                        @endif
                    @else
                    <div class="empty-state">
                        <i class="mdi mdi-history"></i>
                        <p>Belum ada riwayat pesanan</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');

    // Show/hide content
    if (tab === 'active') {
        document.getElementById('active-orders-tab').style.display = 'block';
        document.getElementById('history-orders-tab').style.display = 'none';
    } else {
        document.getElementById('active-orders-tab').style.display = 'none';
        document.getElementById('history-orders-tab').style.display = 'block';
    }
}

function toggleStatusDropdown(orderId) {
    // Tutup semua dropdown lain
    document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
        if (menu.id !== 'dropdown-' + orderId) {
            menu.classList.remove('show');
        }
    });

    // Toggle dropdown yang dipilih
    var dropdown = document.getElementById('dropdown-' + orderId);
    dropdown.classList.toggle('show');
}

// Tutup dropdown jika klik di luar
document.addEventListener('click', function(e) {
    if (!e.target.closest('.status-dropdown')) {
        document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
            menu.classList.remove('show');
        });
    }
});
</script>
@endsection
