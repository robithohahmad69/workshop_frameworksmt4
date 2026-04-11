<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Debug Order Status</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .box { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        h2 { color: #333; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 14px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f8f8; font-weight: bold; }
        .badge { padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .badge-lunas { background: #d4edda; color: #155724; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-settlement { background: #d4edda; color: #155724; }
        .btn { padding: 10px 20px; background: #f5576c; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin-right: 10px; font-size: 14px; }
        .btn:hover { opacity: 0.9; }
        .alert { padding: 15px; background: #d1ecf1; border-left: 4px solid #0c5460; margin-bottom: 20px; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <div class="alert">
            <strong>📋 Info Debug:</strong> Halaman ini menampilkan data asli dari database untuk mendiagnosa masalah webhook dan status pembayaran.
        </div>

        <div class="box">
            <h2>🔍 Tabel Orders (Database)</h2>
            <p style="margin-bottom: 10px; color: #666;">Menampilkan 10 order terbaru</p>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status Bayar</th>
                        <th>Status Order</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $ord)
                    <tr>
                        <td><strong>{{ $ord->id }}</strong></td>
                        <td>{{ $ord->customer_name }}</td>
                        <td>Rp {{ number_format($ord->total) }}</td>
                        <td>
                            <span class="badge {{ $ord->status_bayar === 'lunas' ? 'badge-lunas' : 'badge-pending' }}">
                                {{ strtoupper($ord->status_bayar) }}
                            </span>
                        </td>
                        <td>{{ $ord->status ?? 'NULL' }}</td>
                        <td style="font-size: 12px;">{{ $ord->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="box">
            <h2>💳 Tabel Payments (Database)</h2>
            <p style="margin-bottom: 10px; color: #666;">Menampilkan 10 payment terbaru</p>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Order ID</th>
                        <th>Midtrans Order ID</th>
                        <th>Status Payment</th>
                        <th>Has Snap Token</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $pay)
                    <tr>
                        <td>{{ $pay->id }}</td>
                        <td><strong>{{ $pay->order_id }}</strong></td>
                        <td><code>{{ $pay->midtrans_order_id }}</code></td>
                        <td>
                            <span class="badge {{ $pay->status === 'settlement' ? 'badge-settlement' : 'badge-pending' }}">
                                {{ strtoupper($pay->status) }}
                            </span>
                        </td>
                        <td>{{ $pay->snap_token ? '✅ Ya' : '❌ Tidak' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="box">
            <h2>🔧 Update Manual (Untuk Testing)</h2>
            <p style="margin-bottom: 15px; color: #666;">
                Jika ada order dengan <code>status_bayar = pending</code>, gunakan tombol di bawah untuk update manual.
            </p>

            @foreach($orders as $ord)
                @if($ord->status_bayar === 'pending')
                    <div style="display: inline-block; margin-right: 15px; margin-bottom: 10px;">
                        <form method="POST" action="/debug/simulate-webhook/{{ $ord->id }}" style="display: inline;" onsubmit="return confirm('Simulasikan webhook untuk order #{{ $ord->id }}?');">
                            @csrf
                            <button type="submit" class="btn" style="background: #28a745;">
                                🔄 Order #{{ $ord->id }} → LUNAS (Simulasi Webhook)
                            </button>
                        </form>
                    </div>
                @endif
            @endforeach

            @if($orders->filter(fn($o) => $o->status_bayar === 'pending')->isEmpty())
                <p style="color: #888;">✅ Tidak ada order yang perlu di-update (semua sudah LUNAS).</p>
            @endif
        </div>

        <div class="box">
            <h2>📋 Diagnosis</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 300px;">Kondisi</th>
                        <th>Artinya</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>status_bayar = pending</code> + <code>payment.status = settlement</code></td>
                        <td>❌ Webhook tidak berjalan. Payment sudah sukses di Midtrans tapi database tidak ter-update.</td>
                    </tr>
                    <tr>
                        <td><code>status_bayar = lunas</code></td>
                        <td>✅ Webhook berjalan dengan benar. Tombol "Ubah Status" seharusnya muncul di vendor orders.</td>
                    </tr>
                    <tr>
                        <td><code>payment.status = pending</code></td>
                        <td>⏳ Payment belum selesai di Midtrans. Customer perlu menyelesaikan pembayaran.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="box">
            <h2>🔗 Link Penting</h2>
            <a href="/vendor/orders" class="btn">Buka Vendor Orders</a>
            <a href="/vendor/dashboard" class="btn">Vendor Dashboard</a>
            <a href="/order" class="btn">Customer Order Page</a>
        </div>

        <div class="box">
            <h2>⚠️ Masalah Webhook</h2>
            <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin-bottom: 15px;">
                <strong>Masalah Utama:</strong> Midtrans TIDAK BISA mengirim webhook ke <code>127.0.0.1:8000</code> karena itu adalah localhost.
            </div>

            <h3>Solusi:</h3>
            <ol style="margin-left: 20px; line-height: 2;">
                <li>
                    <strong>Gunakan ngrok atau tunnel serupa:</strong><br>
                    <code>ngrok http 8000</code><br>
                    Kemudian update webhook URL di Midtrans Dashboard ke URL ngrok.
                </li>
                <li>
                    <strong>Atau gunakan manual update:</strong><br>
                    Setelah pembayaran sukses di Midtrans, gunakan tombol "Manual Update" di atas ini untuk mengubah status secara manual.
                </li>
            </ol>

            <h3> Cara Setup ngrok:</h3>
            <ol style="margin-left: 20px; line-height: 2;">
                <li>Download ngrok dari <a href="https://ngrok.com/">https://ngrok.com/</a></li>
                <li>Jalankan: <code>ngrok http 8000</code></li>
                <li>Copy URL HTTPS yang diberikan (contoh: <code>https://abc123.ngrok.io</code>)</li>
                <li>Update webhook Midtrans: <code>https://abc123.ngrok.io/midtrans/webhook</code></li>
                <li>Test pembayaran lagi</li>
            </ol>
        </div>

        <div class="box">
            <h2>📝 Cara Menggunakan (Tanpa Webhook)</h2>
            <ol style="margin-left: 20px; line-height: 1.8;">
                <li>Buat order baru sebagai customer</li>
                <li>Selesaikan pembayaran di Midtrans Sandbox</li>
                <li>Kembali ke halaman ini untuk cek status</li>
                <li>Jika masih <code>pending</code>, gunakan tombol "Manual Update" di atas</li>
                <li>Buka <a href="/vendor/orders">/vendor/orders</a> untuk cek apakah tombol "Ubah Status" muncul</li>
            </ol>
        </div>
    </div>
</body>
</html>
