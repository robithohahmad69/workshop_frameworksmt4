<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tiket Antrian #{{ str_pad($antrian->nomor, 3, '0', STR_PAD_LEFT) }}</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }

        .container { max-width: 420px; width: 100%; }

        .ticket { background: white; border-radius: 20px; padding: 40px 30px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); text-align: center; position: relative; overflow: hidden; }

        .ticket::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 8px; background: repeating-linear-gradient(45deg, #667eea, #667eea 10px, #764ba2 10px, #764ba2 20px); }

        .ticket-header { margin-bottom: 30px; }
        .ticket-header h2 { color: #888; font-size: 14px; font-weight: 500; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; }

        .ticket-number { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 64px; font-weight: 800; padding: 30px; border-radius: 16px; letter-spacing: 8px; margin-bottom: 30px; box-shadow: 0 10px 40px rgba(102, 126, 234, 0.4); }

        .ticket-info { background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .ticket-info-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px dashed #ddd; }
        .ticket-info-row:last-child { border-bottom: none; }
        .ticket-info-label { color: #888; font-size: 13px; font-weight: 500; }
        .ticket-info-value { color: #333; font-size: 15px; font-weight: 600; }

        .ticket-icon { width: 60px; height: 60px; background: #fff4e6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .ticket-icon i { font-size: 30px; color: #ff9800; }

        .ticket-message { color: #555; font-size: 14px; line-height: 1.6; padding: 16px; background: #e8f5e9; border-radius: 10px; border-left: 4px solid #4caf50; }
        .ticket-message i { color: #4caf50; margin-right: 8px; }

        .print-btn { margin-top: 24px; padding: 12px 24px; background: #f5f5f5; color: #666; border: none; border-radius: 10px; font-size: 14px; cursor: pointer; transition: all 0.2s; }
        .print-btn:hover { background: #e0e0e0; }
        .print-btn i { margin-right: 8px; }

        @media print {
            body { background: white; padding: 0; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="ticket">
            <div class="ticket-header">
                <h2>Tiket Antrian</h2>
            </div>

            <div class="ticket-number">
                {{ str_pad($antrian->nomor, 3, '0', STR_PAD_LEFT) }}
            </div>

            <div class="ticket-info">
                <div class="ticket-info-row">
                    <span class="ticket-info-label">Nama</span>
                    <span class="ticket-info-value">{{ $antrian->nama }}</span>
                </div>
                <div class="ticket-info-row">
                    <span class="ticket-info-label">Waktu Daftar</span>
                    <span class="ticket-info-value">{{ now()->format('d M Y, H:i') }}</span>
                </div>
            </div>

            <div class="ticket-icon">
                <i class="mdi mdi-bullhorn"></i>
            </div>

            <div class="ticket-message">
                <i class="mdi mdi-information"></i>
                Silakan menunggu di ruang tunggu. Nomor antrian Anda akan dipanggil dan ditampilkan pada papan pengumuman.
            </div>

            <button class="print-btn" onclick="window.print()">
                <i class="mdi mdi-printer"></i> Cetak Tiket
            </button>
        </div>
    </div>
</body>
</html>
