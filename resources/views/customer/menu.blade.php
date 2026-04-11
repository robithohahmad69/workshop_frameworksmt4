<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu {{ $vendor->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 20px; }
        .header a { color: white; text-decoration: none; opacity: 0.8; font-size: 14px; }
        .header h1 { margin-top: 8px; font-size: 22px; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .kategori-title { font-size: 16px; font-weight: bold; color: #555; margin: 24px 0 12px; text-transform: uppercase; letter-spacing: 1px; }
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px; }
        .menu-card { background: white; border-radius: 10px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
        .menu-card h3 { font-size: 15px; margin-bottom: 6px; color: #333; }
        .menu-card .harga { color: #f5576c; font-weight: bold; margin-bottom: 12px; }
        .qty-control { display: flex; align-items: center; gap: 10px; }
        .qty-control button { width: 28px; height: 28px; border: 1px solid #ddd; background: white; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .qty-control button:hover { background: #f5576c; color: white; border-color: #f5576c; }
        .qty-control span { min-width: 20px; text-align: center; font-weight: bold; }
        .cart-bar { position: fixed; bottom: 0; left: 0; right: 0; background: white; padding: 16px 20px; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .cart-info { font-size: 14px; color: #555; }
        .cart-info strong { color: #f5576c; font-size: 18px; }
        .cart-actions { display: flex; gap: 10px; }
        .btn-order { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none; padding: 12px 28px; border-radius: 8px; font-size: 15px; font-weight: bold; cursor: pointer; }
        .btn-order:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-cancel { background: #f44336; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-size: 15px; font-weight: bold; cursor: pointer; }
        .btn-cancel:hover { background: #d32f2f; }
        .btn-cancel:disabled { opacity: 0.5; cursor: not-allowed; background: #ccc; }
        .pb { padding-bottom: 80px; }
    </style>
</head>
<body>
    <div class="header">
        <a href="{{ route('customer.index') }}">← Kembali</a>
        <h1>🏪 {{ $vendor->name }}</h1>
    </div>

    <div class="container pb">
        @forelse($menus as $kategori => $items)
            <div class="kategori-title">{{ $kategori }}</div>
            <div class="menu-grid">
                @foreach($items as $menu)
                <div class="menu-card">
                    <h3>{{ $menu->nama }}</h3>
                    <div class="harga">Rp {{ number_format($menu->harga, 0, ',', '.') }}</div>
                    <div class="qty-control">
                        <button onclick="changeQty({{ $menu->id }}, -1)">−</button>
                        <span id="qty-{{ $menu->id }}">0</span>
                        <button onclick="changeQty({{ $menu->id }}, 1)">+</button>
                    </div>
                </div>
                @endforeach
            </div>
        @empty
            <p style="text-align:center; color:#aaa; margin-top:60px;">Belum ada menu tersedia</p>
        @endforelse
    </div>

    <div class="cart-bar">
        <div class="cart-info">
            <div id="total-item">0 item dipilih</div>
            <strong id="total-harga">Rp 0</strong>
        </div>
        <div class="cart-actions">
            <button class="btn-cancel" id="btn-cancel" onclick="resetCart()" disabled>
                🗑️ Reset
            </button>
            <button class="btn-order" id="btn-order" onclick="submitOrder()" disabled>
                Pesan Sekarang
            </button>
        </div>
    </div>

    <form id="order-form" method="POST" action="{{ route('customer.checkout', $vendor->id) }}">
        @csrf
        <div id="form-items"></div>
    </form>

    <script>
        // Simpan data menu dari blade ke JS
        const menuData = {
            @foreach($menus->flatten() as $menu)
            {{ $menu->id }}: { nama: "{{ $menu->nama }}", harga: {{ $menu->harga }} },
            @endforeach
        };

        let cart = {};

        function changeQty(menuId, delta) {
            cart[menuId] = (cart[menuId] || 0) + delta;
            if (cart[menuId] < 0) cart[menuId] = 0;

            // Jika qty menjadi 0, hapus item dari cart
            if (cart[menuId] === 0) {
                delete cart[menuId];
            }

            document.getElementById('qty-' + menuId).textContent = cart[menuId] || 0;
            updateCart();
        }

        function updateCart() {
            let totalItem = 0;
            let totalHarga = 0;
            let hasItems = false;

            for (let id in cart) {
                if (cart[id] > 0) {
                    totalItem  += cart[id];
                    totalHarga += cart[id] * menuData[id].harga;
                    hasItems = true;
                }
            }

            document.getElementById('total-item').textContent = totalItem + ' item dipilih';
            document.getElementById('total-harga').textContent = 'Rp ' + totalHarga.toLocaleString('id-ID');
            document.getElementById('btn-order').disabled = !hasItems;
            document.getElementById('btn-cancel').disabled = !hasItems;
        }

        function resetCart() {
            if (!confirm('Apakah Anda yakin ingin menghapus semua item dari keranjang?')) {
                return;
            }

            // Reset cart ke objek kosong
            cart = {};

            // Reset semua tampilan quantity ke 0
            for (let id in menuData) {
                document.getElementById('qty-' + id).textContent = '0';
            }

            updateCart();
        }

        function submitOrder() {
            const form = document.getElementById('order-form');
            const container = document.getElementById('form-items');
            container.innerHTML = '';

            let i = 0;
            for (let id in cart) {
                if (cart[id] > 0) {
                    container.innerHTML += `<input type="hidden" name="items[${i}][id]" value="${id}">`;
                    container.innerHTML += `<input type="hidden" name="items[${i}][qty]" value="${cart[id]}">`;
                    i++;
                }
            }

            form.submit();
        }
    </script>
</body>
</html>