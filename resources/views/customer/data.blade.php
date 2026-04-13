<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Customer</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; min-height: 100vh; }
        .header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 24px 20px; }
        .header-top { display: flex; justify-content: space-between; align-items: center; max-width: 1000px; margin: 0 auto; }
        .header h1 { font-size: 22px; margin-bottom: 4px; }
        .header p { opacity: 0.85; font-size: 13px; }
        .btn-group { display: flex; gap: 8px; }
        .btn { display: inline-block; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: bold; text-decoration: none; border: none; cursor: pointer; }
        .btn-pink { background: white; color: #f5576c; }
        .btn-pink:hover { background: #fff0f0; }
        .container { max-width: 1000px; margin: 28px auto; padding: 0 20px 40px; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; background: #d4edda; color: #155724; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        thead { background: #333; color: white; }
        th, td { padding: 12px 14px; text-align: left; font-size: 13px; border-bottom: 1px solid #f0f0f0; }
        td img { width: 52px; height: 52px; object-fit: cover; border-radius: 6px; border: 1px solid #eee; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge-blob { background: #cfe2ff; color: #084298; }
        .badge-file { background: #d1e7dd; color: #0f5132; }
        .empty { text-align: center; color: #aaa; padding: 40px; }
        .no-foto { color: #ccc; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <div>
                <h1>👤 Data Customer</h1>
                <p>Daftar lengkap data customer yang pernah memesan makanan</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('customer.index') }}" class="btn btn-pink">📋 pesan makanan</a>
                <a href="{{ route('customer-data.create-blob') }}" class="btn btn-pink">🖼️ Tambah BLOB</a>
                <a href="{{ route('customer-data.create-file') }}" class="btn btn-pink">📁 Tambah File</a>
            </div>
        </div>
    </div>

    <div class="container">
        @if(session('success'))
            <div class="alert">✅ {{ session('success') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Kota</th>
                    <th>Provinsi</th>
                    <th>Kodepos</th>
                    <th>Tipe Foto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $c)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($c->foto_blob)
                            <img src="data:image/png;base64,{{ $c->foto_blob }}" alt="foto" width="52" height="52">
                        @elseif($c->foto_path)
                            <img src="{{ asset('storage/' . $c->foto_path) }}" alt="foto" width="52" height="52">
                        @else
                            <span class="no-foto">—</span>
                        @endif
                    </td>
                    <td>{{ $c->nama }}</td>
                    <td>{{ $c->alamat ?? '—' }}</td>
                    <td>{{ $c->kota ?? '—' }}</td>
                    <td>{{ $c->provinsi ?? '—' }}</td>
                    <td>{{ $c->kodepos_kelurahan ?? '—' }}</td>
                    <td>
                        @if($c->foto_blob)
                            <span class="badge badge-blob">BLOB</span>
                        @elseif($c->foto_path)
                            <span class="badge badge-file">File</span>
                        @else
                            —
                        @endif
                    </td>
                </tr>
                @empty
                    <tr><td colspan="8" class="empty">😴 Belum ada data customer</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
