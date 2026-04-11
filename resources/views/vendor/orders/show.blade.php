<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            background: #f5f5f5;
            padding: 20px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .invoice {
            background: white;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            page-break-inside: avoid;
        }

        /* Header */
        .invoice-header {
            text-align: center;
            border-bottom: 3px dashed #333;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .invoice-header h1 {
            font-size: 36px;
            margin-bottom: 5px;
            color: #333;
        }
        .invoice-header h2 {
            font-size: 20px;
            color: #f5576c;
            margin-bottom: 8px;
        }
        .invoice-header p {
            color: #666;
            font-size: 13px;
        }

        /* Info Box */
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            gap: 20px;
        }
        .info-box {
            flex: 1;
        }
        .info-box p {
            margin: 8px 0;
            font-size: 14px;
            color: #555;
        }
        .info-box strong {
            display: inline-block;
            min-width: 120px;
            color: #333;
        }

        /* Status Badges */
        .status-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .status-lunas {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffeaa7;
        }
        .order-status {
            background: #e7f3ff;
            color: #004085;
            border: 2px solid #b8daff;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #333;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        tr:hover {
            background: #f0f0f0;
        }
        .col-right {
            text-align: right;
        }
        .col-center {
            text-align: center;
        }

        /* Total Section */
        .total-section {
            border-top: 3px dashed #333;
            padding-top: 20px;
            margin-top: 25px;
            text-align: right;
        }
        .total-label {
            font-size: 16px;
            color: #666;
            margin-bottom: 5px;
        }
        .total-amount {
            font-size: 28px;
            font-weight: bold;
            color: #f5576c;
        }

        /* Footer */
        .invoice-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #999;
            font-size: 12px;
        }

        /* Actions */
        .actions {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 0 8px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.2s;
        }
        .btn:hover { background: #555; }
        .btn-print { background: #28a745; }
        .btn-print:hover { background: #218838; }
        .btn-complete {
            background: #007bff;
        }
        .btn-complete:hover { background: #0056b3; }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .invoice {
                box-shadow: none;
                padding: 20px;
                max-width: 100%;
            }
            .actions {
                display: none !important;
            }
            .invoice::after {
                content: "";
                display: block;
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px dashed #999;
                text-align: center;
                font-size: 11px;
                color: #999;
            }
        }

        /* Watermark untuk pesanan selesai/batal */
        .watermark-completed {
            position: relative;
        }
        .watermark-completed::before {
            content: "✅ SELESAI";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(40, 167, 69, 0.08);
            font-weight: bold;
            pointer-events: none;
            z-index: 0;
        }
        .watermark-cancelled {
            position: relative;
        }
        .watermark-cancelled::before {
            content: "❌ DIBATALKAN";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(220, 53, 69, 0.08);
            font-weight: bold;
            pointer-events: none;
            z-index: 0;
        }
        @media print {
            .watermark-completed::before,
            .watermark-cancelled::before {
                display: none;
            }
        }

        .status-actions {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .status-actions h4 {
            margin-bottom: 15px;
            color: #333;
        }
        .status-btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .status-btn {
            flex: 1;
            min-width: 150px;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }
        .status-btn-processing {
            background: #007bff;
            color: white;
        }
        .status-btn-processing:hover { background: #0056b3; }
        .status-btn-completed {
            background: #28a745;
            color: white;
        }
        .status-btn-completed:hover { background: #218838; }
        .status-btn-cancelled {
            background: #dc3545;
            color: white;
        }
        .status-btn-cancelled:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="invoice @if($order->status === 'completed') watermark-completed @elseif($order->status === 'cancelled') watermark-cancelled @endif">
        <div class="invoice-header">
            <h1>🧾 INVOICE</h1>
            <h2>{{ $order->vendor->name }}</h2>
            <p>Kantin Online - {{ $order->vendor->description ?? 'Menu Terbaik Untuk Anda' }}</p>
        </div>

        <div class="invoice-info">
            <div class="info-box">
                <p><strong>No. Order:</strong> #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p><strong>Tanggal:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Payment ID:</strong> {{ $order->payment->midtrans_order_id }}</p>
            </div>
            <div class="info-box" style="text-align: right;">
                <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
                <p><strong>Vendor:</strong> {{ $order->vendor->name }}</p>
            </div>
        </div>

        <div class="status-section">
            <div>
                <span class="status-badge {{ $order->status_bayar === 'lunas' ? 'status-lunas' : 'status-pending' }}">
                    Status Pembayaran: {{ $order->status_bayar === 'lunas' ? 'LUNAS' : 'PENDING' }}
                </span>
            </div>
            @if($order->status)
            <div>
                <span class="status-badge order-status">
                    Status Pesanan:
                    @if($order->status === 'pending')
                        ⏳ ANTRI
                    @elseif($order->status === 'processing')
                        👨‍🍳 SEDANG DIMASAK
                    @elseif($order->status === 'completed')
                        🚚 SUDAH DIANTAR
                    @elseif($order->status === 'cancelled')
                        ❌ DIBATALKAN
                    @else
                        {{ strtoupper($order->status) }}
                    @endif
                </span>
            </div>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Menu</th>
                    <th class="col-center" style="width: 80px;">Qty</th>
                    <th class="col-right" style="width: 120px;">Harga</th>
                    <th class="col-right" style="width: 120px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderItems as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->menu->nama }}</strong>
                        @if($item->menu->kategori)
                        <div style="font-size: 11px; color: #999; margin-top: 2px;">
                            {{ $item->menu->kategori }}
                        </div>
                        @endif
                    </td>
                    <td class="col-center">{{ $item->qty }}</td>
                    <td class="col-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="col-right"><strong>Rp {{ number_format($item->harga * $item->qty, 0, ',', '.') }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-label">TOTAL PEMBAYARAN</div>
            <div class="total-amount">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
        </div>

        <div class="invoice-footer">
            <p>Terima kasih telah memesan di kantin kami!</p>
            <p>{{ now()->format('d/m/Y H:i') }} - Dicetak oleh {{ auth()->guard('vendor')->user()->name ?? 'System' }}</p>
        </div>

        {{-- Status Actions - Hanya tampil untuk pesanan yang belum selesai/dibatalkan --}}
        @if($order->status_bayar === 'lunas' && !in_array($order->status, ['completed', 'cancelled']))
        <div class="status-actions">
            <h4>🔄 Update Status Pesanan</h4>
            <div class="status-btn-group">
                @if(!$order->status || $order->status === 'pending')
                <form method="POST" action="{{ route('vendor.orders.updateStatus', $order->id) }}">
                    @csrf
                    <input type="hidden" name="status" value="processing">
                    <button type="submit" class="status-btn status-btn-processing" onclick="return confirm('Mulai memasak pesanan ini?')">
                        👨‍🍳 Mulai Memasak
                    </button>
                </form>
                @endif

                @if($order->status === 'processing')
                <form method="POST" action="{{ route('vendor.orders.updateStatus', $order->id) }}">
                    @csrf
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="status-btn status-btn-completed" onclick="return confirm('Tandai pesanan ini sebagai sudah diantar?')">
                        🚚 Sudah Diantar
                    </button>
                </form>
                @endif

                @if(in_array($order->status ?? 'pending', ['pending', 'processing']))
                <form method="POST" action="{{ route('vendor.orders.updateStatus', $order->id) }}">
                    @csrf
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="status-btn status-btn-cancelled" onclick="return confirm('Batalkan pesanan ini?')">
                        ❌ Batalkan
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif

        <div class="actions">
            <button onclick="window.print()" class="btn btn-print">
                🖨️ Cetak Invoice
            </button>
            <a href="{{ route('vendor.orders.index') }}" class="btn">
                ← Kembali
            </a>
        </div>
    </div>

    <script>
        // Auto print jika URL ada parameter ?print=1
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === '1') {
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            };
        }
    </script>
</body>
</html>
