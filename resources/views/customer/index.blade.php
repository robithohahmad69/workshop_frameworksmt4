<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kantin Online</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; min-height: 100vh; }

        /* HEADER */
        .header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 24px 20px; }
        .header-top { display: flex; justify-content: space-between; align-items: center; max-width: 800px; margin: 0 auto; }
        .header h1 { font-size: 24px; margin-bottom: 4px; }
        .header p { opacity: 0.85; font-size: 14px; }
        .header-actions { display: flex; gap: 10px; align-items: center; }
        .btn-vendor { background: white; color: #f5576c; border: none; padding: 8px 16px; border-radius: 20px; font-weight: bold; font-size: 13px; cursor: pointer; text-decoration: none; white-space: nowrap; }
        .btn-vendor:hover { background: #fff0f0; }


        /* CONTAINER */
        .container { max-width: 800px; margin: 28px auto; padding: 0 20px 40px; }
        h2 { margin-bottom: 16px; color: #333; font-size: 18px; }

        /* VENDOR GRID */
        .vendor-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
        .vendor-card { background: white; border-radius: 10px; padding: 24px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-decoration: none; color: inherit; transition: transform 0.2s, box-shadow 0.2s; display: block; }
        .vendor-card:hover { transform: translateY(-4px); box-shadow: 0 6px 20px rgba(0,0,0,0.12); }
        .vendor-icon { font-size: 42px; margin-bottom: 10px; }
        .vendor-card h3 { color: #333; margin-bottom: 4px; font-size: 15px; }
        .vendor-card p { color: #aaa; font-size: 12px; }
        .empty { text-align: center; color: #aaa; padding: 60px 0; }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-top">
            <div>
                <h1>🍽️ Kantin Online</h1>
                <p>Pesan makanan & minuman favoritmu</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('vendor.login') }}" class="btn-vendor">🏪 Portal Vendor</a>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>Pilih Kantin</h2>

        @if($vendors->isEmpty())
            <div class="empty">
                <p style="font-size:48px">😴</p>
                <p>Belum ada kantin tersedia</p>
            </div>
        @else
            <div class="vendor-grid">
                @foreach($vendors as $vendor)
                <a href="{{ route('customer.menu', $vendor->id) }}" class="vendor-card">
                    <div class="vendor-icon">🏪</div>
                    <h3>{{ $vendor->name }}</h3>
                    <p>Lihat menu →</p>
                </a>
                @endforeach
            </div>
        @endif
    </div>

</body>
</html>