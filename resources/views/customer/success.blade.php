<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan Berhasil</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .box { background: white; border-radius: 12px; padding: 40px 32px; max-width: 420px; width: 100%; box-shadow: 0 4px 20px rgba(0,0,0,0.1); text-align: center; }
        .icon { font-size: 64px; margin-bottom: 16px; }
        .box h2 { color: #28a745; margin-bottom: 8px; }
        .box p { color: #888; margin-bottom: 24px; font-size: 14px; }
        .info { background: #f9f9f9; border-radius: 8px; padding: 16px; margin-bottom: 24px; text-align: left; }
        .info-row { display: flex; justify-content: space-between; font-size: 14px; padding: 6px 0; }
        .info-row span:first-child { color: #888; }
        .info-row span:last-child { font-weight: bold; }
        .item-row { font-size: 13px; color: #555; padding: 4px 0; display: flex; justify-content: space-between; }
        .badge-lunas { background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .btn { display: inline-block; padding: 12px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none; border-radius: 8px; font-size: 15px; font-weight: bold; cursor: pointer; text-decoration: none; }
        .btn-print { background: #6c757d; margin-right: 0; }
        .btn-print:hover { background: #5a6268; }
        .buttons-wrapper { display: flex; gap: 10px; margin-top: 16px; flex-wrap: wrap; }
        .buttons-wrapper .btn { flex: 1; text-align: center; min-width: 120px; }
        .qr-wrapper { margin: 0 0 24px 0; padding: 20px 16px; background: #f9f9f9; border-radius: 8px; }
        .qr-wrapper img { width: 180px; height: 180px; }
        .qr-wrapper .qr-label { font-size: 12px; color: #888; margin-top: 10px; }
        .qr-wrapper .qr-id { font-size: 13px; font-weight: bold; color: #333; margin-top: 4px; }

        @media print {
            body { background: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .box { box-shadow: none; border: 1px solid #ddd; page-break-inside: avoid; }
            .buttons-wrapper { display: none !important; }
            .box::after {
                content: "Terima kasih atas pesanan Anda!";
                display: block;
                text-align: center;
                margin-top: 30px;
                font-size: 14px;
                color: #666;
                font-style: italic;
            }
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="icon">✅</div>
        <h2>Pesanan Berhasil!</h2>
        <p>Terima kasih, pesananmu sedang diproses oleh kantin.</p>

        <div class="info">
            <div class="info-row">
                <span>Nama</span>
                <span>{{ $order->customer_name }}</span>
            </div>
            <div class="info-row">
                <span>Kantin</span>
                <span>{{ $order->vendor->name }}</span>
            </div>
            <div class="info-row">
                <span>Status</span>
                <span><span class="badge-lunas">{{ strtoupper($order->status_bayar) }}</span></span>
            </div>
            <hr style="margin: 10px 0; border: none; border-top: 1px solid #eee;">
            @foreach($order->orderItems as $item)
            <div class="item-row">
                <span>{{ $item->menu->nama }} × {{ $item->qty }}</span>
                <span>Rp {{ number_format($item->harga * $item->qty, 0, ',', '.') }}</span>
            </div>
            @endforeach
            <hr style="margin: 10px 0; border: none; border-top: 1px solid #eee;">
            <div class="info-row">
                <span><strong>Total</strong></span>
                <span><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></span>
            </div>
        </div>

        {{-- QR Code hanya muncul kalau sudah lunas --}}
        @if($order->status_bayar === 'lunas')
        <div class="qr-wrapper">
            <img src="{{ $qrBase64 }}" alt="QR Code Pesanan #{{ $order->id }}">
            <div class="qr-label">Tunjukkan QR Code ini ke vendor</div>
            <div class="qr-id">ID Pesanan: #{{ $order->id }}</div>
        </div>
        @endif

        <div class="buttons-wrapper">
            <button onclick="window.print()" class="btn btn-print">
                🖨️ Cetak Invoice
            </button>
            <a href="{{ route('customer.qr', $order->id) }}" class="btn" target="_blank">
                📱 Buka QR Code
            </a>
            <a href="{{ route('customer.index') }}" class="btn">Pesan Lagi</a>
        </div>
    </div>
</body>
</html>