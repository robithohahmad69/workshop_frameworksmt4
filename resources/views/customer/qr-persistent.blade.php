<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Pesanan #{{ $order->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .qr-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .qr-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .qr-header h1 {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .qr-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .qr-body {
            padding: 40px 30px;
            text-align: center;
        }

        .qr-code-wrapper {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            border: 3px dashed #dee2e6;
        }

        .qr-code-wrapper img {
            width: 280px;
            height: 280px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .qr-id {
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
            margin-top: 15px;
        }

        .qr-instruction {
            font-size: 14px;
            color: #6c757d;
            margin-top: 10px;
        }

        .order-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .order-info h3 {
            color: #667eea;
            font-size: 20px;
            margin-bottom: 15px;
            text-align: center;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
            font-size: 14px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
        }

        .info-value {
            color: #212529;
            font-weight: 500;
        }

        .menu-items {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
        }

        .menu-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 13px;
            color: #495057;
        }

        .menu-item-name {
            flex: 1;
        }

        .menu-item-price {
            font-weight: 600;
            color: #212529;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }

        .status-lunas {
            background: #28a745;
            color: white;
        }

        .status-pending {
            background: #ffc107;
            color: #212529;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-print {
            background: #6c757d;
            color: white;
        }

        .btn-home {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        @media print {
            body {
                background: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .qr-container {
                box-shadow: none;
                border: 1px solid #ddd;
                page-break-inside: avoid;
            }

            .buttons {
                display: none !important;
            }

            .qr-container::after {
                content: "Terima kasih atas pesanan Anda!";
                display: block;
                text-align: center;
                margin-top: 30px;
                font-size: 16px;
                color: #666;
                font-style: italic;
            }
        }

        @media (max-width: 600px) {
            .qr-header h1 {
                font-size: 24px;
            }

            .qr-body {
                padding: 30px 20px;
            }

            .qr-code-wrapper img {
                width: 220px;
                height: 220px;
            }

            .buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <div class="qr-header">
            <h1>🛒 QR Code Pesanan</h1>
            <p>Tunjukkan QR Code ini ke vendor untuk pengambilan pesanan</p>
        </div>

        <div class="qr-body">
            <!-- QR Code -->
            <div class="qr-code-wrapper">
                <img src="{{ $qrBase64 }}" alt="QR Code Pesanan #{{ $order->id }}">
                <div class="qr-id">ID Pesanan: #{{ $order->id }}</div>
                <div class="qr-instruction">
                    Scan QR Code ini di lokasi vendor
                </div>
            </div>

            <!-- Order Information -->
            <div class="order-info">
                <h3>📋 Detail Pesanan</h3>

                <div class="info-row">
                    <span class="info-label">Nama Customer:</span>
                    <span class="info-value">{{ $order->customer_name }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Kantin/Vendor:</span>
                    <span class="info-value">{{ $order->vendor->name }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Waktu Pesan:</span>
                    <span class="info-value">{{ $order->created_at->format('d M Y, H:i') }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Status Bayar:</span>
                    <span class="info-value">
                        @if($order->status_bayar === 'lunas')
                            <span class="status-badge status-lunas">LUNAS ✅</span>
                        @else
                            <span class="status-badge status-pending">PENDING ⏳</span>
                        @endif
                    </span>
                </div>

                <!-- Menu Items -->
                <div class="menu-items">
                    <h4 style="margin-bottom: 10px; color: #667eea;">Menu yang Dipesan:</h4>
                    @foreach($order->orderItems as $item)
                        <div class="menu-item">
                            <span class="menu-item-name">
                                {{ $item->menu->nama }} × {{ $item->qty }}
                            </span>
                            <span class="menu-item-price">
                                Rp {{ number_format($item->harga * $item->qty, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach

                    <div class="info-row" style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #667eea;">
                        <span class="info-label"><strong>Total:</strong></span>
                        <span class="info-value" style="font-size: 18px; font-weight: bold; color: #667eea;">
                            <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="buttons">
                <button onclick="window.print()" class="btn btn-print">
                    🖨️ Cetak QR Code
                </button>
                <a href="{{ route('customer.index') }}" class="btn btn-home">
                    🏠 Pesan Lagi
                </a>
            </div>
        </div>
    </div>
</body>
</html>
