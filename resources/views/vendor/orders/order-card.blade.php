{{-- Single Order Card Component --}}
<div class="order-card @if($order->status === 'completed' || $order->status === 'cancelled') opacity-75 @endif">
    <div class="order-header">
        <div>
            <div class="order-id">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="order-time">{{ $order->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div style="text-align: right;">
            <div style="margin-bottom: 6px;">
                <span class="status-badge payment-{{ $order->status_bayar }}">
                    {{ $order->status_bayar === 'lunas' ? '✅ LUNAS' : '⏳ PENDING' }}
                </span>
            </div>
            <div>
                <span class="status-badge status-{{ $order->status ?? 'pending' }}">
                    @if($order->status === 'pending')
                        ⏳ ANTRI
                    @elseif($order->status === 'processing')
                        👨‍🍳 SEDANG DIMASAK
                    @elseif($order->status === 'completed')
                        🚚 SUDAH DIANTAR
                    @elseif($order->status === 'cancelled')
                        ❌ BATAL
                    @else
                        ⏳ ANTRI
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div class="order-info">
        <div class="info-item">
            <span class="info-label">Customer:</span>
            <span class="info-value">{{ $order->customer_name }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Total:</span>
            <span class="info-value" style="color: #f5576c;">
                Rp {{ number_format($order->total, 0, ',', '.') }}
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Items:</span>
            <span class="info-value">{{ $order->orderItems->count() }} menu</span>
        </div>
        <div class="info-item">
            <span class="info-label">Payment:</span>
            <span class="info-value" style="font-size: 11px;">
                {{ substr($order->payment->midtrans_order_id, -10) }}
            </span>
        </div>
    </div>

    {{-- Items Preview --}}
    @if($order->orderItems->isNotEmpty())
    <div class="items-preview">
        @foreach($order->orderItems->take(3) as $item)
        <div class="item-row">
            <span>{{ $item->menu->nama }} × {{ $item->qty }}</span>
            <span>Rp {{ number_format($item->harga * $item->qty, 0, ',', '.') }}</span>
        </div>
        @endforeach
        @if($order->orderItems->count() > 3)
        <div style="text-align: center; font-size: 11px; color: #999; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #ddd;">
            +{{ $order->orderItems->count() - 3 }} item lainnya
        </div>
        @endif
    </div>
    @endif

    <div class="order-actions">
        {{-- Tombol Lihat Invoice --}}
        <a href="{{ route('vendor.orders.show', $order->id) }}" class="btn-view">
            📄 Invoice
        </a>

        {{-- Status Dropdown - Hanya untuk order yang sudah LUNAS dan belum COMPLETED/CANCELLED --}}
        @if($order->status_bayar === 'lunas' && !in_array($order->status, ['completed', 'cancelled']))
        <div class="status-dropdown">
            <button type="button" class="btn-status-trigger" onclick="toggleStatusDropdown({{ $order->id }})">
                🔄 Ubah Status
                <span style="font-size: 10px;">▼</span>
            </button>
            <div class="dropdown-menu" id="dropdown-{{ $order->id }}">

                {{-- Jika status PENDING atau null → Bisa pindah ke PROSES atau BATAL --}}
                @if(!$order->status || $order->status === 'pending')
                <form method="POST" action="{{ route('vendor.orders.updateStatus', $order->id) }}">
                    @csrf
                    <input type="hidden" name="status" value="processing">
                    <button type="submit" class="dropdown-item status-processing" onclick="return confirm('Mulai memasak pesanan ini?')">
                        👨‍🍳 Mulai Memasak
                    </button>
                </form>
                <form method="POST" action="{{ route('vendor.orders.updateStatus', $order->id) }}">
                    @csrf
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="dropdown-item status-cancelled" onclick="return confirm('Batalkan pesanan ini?')">
                        ❌ Batalkan
                    </button>
                </form>
                @endif

                {{-- Jika status PROCESSING → Bisa pindah ke SELESAI atau BATAL --}}
                @if($order->status === 'processing')
                <form method="POST" action="{{ route('vendor.orders.updateStatus', $order->id) }}">
                    @csrf
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="dropdown-item status-completed" onclick="return confirm('Tandai pesanan ini sebagai sudah diantar? Pesanan akan masuk ke riwayat.')">
                        🚚 Sudah Diantar
                    </button>
                </form>
                <form method="POST" action="{{ route('vendor.orders.updateStatus', $order->id) }}">
                    @csrf
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="dropdown-item status-cancelled" onclick="return confirm('Batalkan pesanan ini?')">
                        ❌ Batalkan
                    </button>
                </form>
                @endif

            </div>
        </div>
        @endif

        {{-- Badge untuk pesanan yang sudah selesai/dibatalkan --}}
        @if(in_array($order->status, ['completed', 'cancelled']))
        <div style="flex: 1; text-align: center; padding: 10px; border-radius: 8px; @if($order->status === 'completed') background: #d4edda; color: #155724; @else background: #f8d7da; color: #721c24; @endif font-weight: bold; font-size: 13px;">
            @if($order->status === 'completed')
                🚚 Sudah Diantar
            @else
                ❌ Dibatalkan
            @endif
        </div>
        @endif

        {{-- Info untuk pesanan yang belum lunas --}}
        @if($order->status_bayar === 'pending')
        <div style="flex: 1; text-align: center; padding: 10px; background: #fff3cd; color: #856404; border-radius: 8px; font-weight: bold; font-size: 13px;">
            ⏳ Menunggu Pembayaran
        </div>
        @endif
    </div>
</div>
