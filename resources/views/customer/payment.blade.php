<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .box { background: white; border-radius: 12px; padding: 32px; max-width: 440px; width: 100%; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .box h2 { text-align: center; margin-bottom: 6px; color: #333; }
        .box .sub { text-align: center; color: #888; font-size: 14px; margin-bottom: 24px; }
        .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .info-row:last-of-type { border-bottom: none; }
        .info-row span:first-child { color: #888; }
        .info-row span:last-child { font-weight: bold; color: #333; }
        .total-row { display: flex; justify-content: space-between; padding: 14px 0; margin-top: 10px; border-top: 2px solid #f0f0f0; }
        .total-row span:first-child { font-weight: bold; }
        .total-row span:last-child { font-size: 20px; font-weight: bold; color: #f5576c; }
        .btn-pay { width: 100%; margin-top: 24px; padding: 14px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; }
        .btn-pay:hover { opacity: 0.9; }
        .item-list { margin: 16px 0; }
        .item-row { display: flex; justify-content: space-between; font-size: 13px; padding: 6px 0; color: #555; }
    </style>
</head>
<body>
    <div class="box">
        <h2>💳 Pembayaran</h2>
        <p class="sub">Selesaikan pembayaran untuk pesananmu</p>

        <div class="info-row">
            <span>Nama Customer</span>
            <span>{{ $order->customer_name }}</span>
        </div>
        <div class="info-row">
            <span>Kantin</span>
            <span>{{ $order->vendor->name }}</span>
        </div>

        <div class="item-list">
            @foreach($order->orderItems as $item)
            <div class="item-row">
                <span>{{ $item->menu->nama }} × {{ $item->qty }}</span>
                <span>Rp {{ number_format($item->harga * $item->qty, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>

        <div class="total-row">
            <span>Total</span>
            <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>

        <button class="btn-pay" id="btn-pay" onclick="bayar()">Bayar Sekarang</button>
        <div id="payment-status" style="display: none; text-align: center; margin-top: 16px; padding: 12px; background: #d4edda; color: #155724; border-radius: 8px;">
            ✅ Pembayaran berhasil! Mengalihkan...
        </div>
    </div>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script>
        function bayar() {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    // Tampilkan status sukses
                    document.getElementById('payment-status').style.display = 'block';
                    document.getElementById('btn-pay').disabled = true;
                    document.getElementById('btn-pay').textContent = 'Memproses...';

                    // Redirect langsung - Midtrans sudah memproses pembayaran
                    setTimeout(function() {
                        window.location.href = '{{ route("customer.success", $order->id) }}';
                    }, 1500);
                },
                onPending: function(result) {
                    alert('Pembayaran pending, selesaikan segera!');
                },
                onError: function(result) {
                    alert('Pembayaran gagal, coba lagi.');
                },
                onClose: function() {
                    // User menutup popup pembayaran tanpa menyelesaikan
                    if (!document.getElementById('payment-status').style.display || document.getElementById('payment-status').style.display === 'none') {
                        alert('Kamu menutup popup pembayaran.');
                    }
                }
            });
        }
    </script>
</body>
</html>