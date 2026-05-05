<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner - Vendor</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .scanner-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }

        .scanner-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .scanner-header h2 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .scanner-header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .scanner-body {
            padding: 30px;
        }

        #reader {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            background: #000;
        }

        #reader video {
            object-fit: cover;
            border-radius: 12px;
        }

        #reader__scan_region {
            background: #000 !important;
        }

        #reader__dashboard {
            padding: 20px !important;
        }

        .result-container {
            display: none;
            margin-top: 30px;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .result-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .result-card h4 {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
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
        }

        .menu-item {
            background: white;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            border-left: 4px solid #667eea;
        }

        .menu-item-name {
            font-weight: bold;
            color: #212529;
        }

        .menu-item-details {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }

        .badge-lunas {
            background: #28a745;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-pending {
            background: #ffc107;
            color: #212529;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-scan-again {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-scan-again:hover {
            transform: scale(1.05);
        }

        .btn-back {
            background: #6c757d;
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }

        .error-message {
            display: none;
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border: 1px solid #f5c6cb;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>
<body>
    <div class="button-back">
        <a href="{{ route('vendor.dashboard') }}" class="btn btn-secondary">
            ← Kembali
        </a>
    </div>

    <div class="scanner-container">
        <div class="scanner-header">
            <h2>📷 QR Code Scanner</h2>
            <p>Scan QR Code pesanan customer untuk melihat detail</p>
        </div>

        <div class="scanner-body">
            <!-- Area Scanner -->
            <div id="reader"></div>

            <!-- Loading Spinner -->
            <div class="loading-spinner" id="loadingSpinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Memuat detail pesanan...</p>
            </div>

            <!-- Error Message -->
            <div class="error-message" id="errorMessage"></div>

            <!-- Result Container -->
            <div class="result-container" id="resultContainer">
                <div class="result-card">
                    <h4>📋 Detail Pesanan</h4>

                    <div class="info-row">
                        <span class="info-label">ID Pesanan:</span>
                        <span class="info-value" id="orderId">#</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Customer:</span>
                        <span class="info-value" id="customerName">-</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Waktu Pesan:</span>
                        <span class="info-value" id="orderTime">-</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Status Bayar:</span>
                        <span class="info-value" id="paymentStatus">-</span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Total:</span>
                        <span class="info-value" id="totalAmount">-</span>
                    </div>
                </div>

                <div class="result-card">
                    <h4>🍽️ Menu yang Dipesan</h4>
                    <div id="menuItems"></div>
                </div>

                <div class="btn-group">
                    <button class="btn-scan-again" onclick="startScanner()">
                        🔄 Scan Lagi
                    </button>
                    <a href="{{ route('vendor.dashboard') }}" class="btn-back">
                        ← Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- html5-qrcode Library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        // ============================================
        // KONFIGURASI
        // ============================================

        // Path ke file beep sound
        const BEEP_SOUND_PATH = '{{ asset('jokowi-oamatiketu-version.mp3') }}';

        // API Endpoint untuk lookup pesanan
        const API_ENDPOINT = '{{ route('vendor.api.getOrder') }}';

        // ============================================
        // VARIABEL GLOBAL
        // ============================================

        let html5QrCode = null;
        let isScanning = false;
        let beepSound = null;

        // ============================================
        // INISIALISASI
        // ============================================

        document.addEventListener('DOMContentLoaded', function() {
            // Load beep sound
            beepSound = new Audio(BEEP_SOUND_PATH);

            // Auto start scanner saat page load
            startScanner();
        });

        // ============================================
        // FUNGSI SCANNER
        // ============================================

        /**
         * Memulai QR Code scanner
         * Mengakses kamera dan memulai proses scanning
         */
        async function startScanner() {
            try {
                // Reset UI
                document.getElementById('resultContainer').style.display = 'none';
                document.getElementById('errorMessage').style.display = 'none';
                document.getElementById('loadingSpinner').style.display = 'none';

                // Inisialisasi html5-qrcode
                html5QrCode = new Html5Qrcode("reader");

                // Konfigurasi kamera (prioritas: kamera belakang)
                const config = {
                    fps: 10,                    // Frame per second
                    qrbox: { width: 250, height: 250 },  // Area scan
                    aspectRatio: 1.0
                };

                // Mulai scanning dengan kamera belakang (environment)
                await html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    onScanSuccess,
                    onScanFailure
                );

                isScanning = true;
                console.log('✅ Scanner started successfully');

            } catch (error) {
                console.error('❌ Error starting scanner:', error);
                showError('Gagal mengakses kamera. Pastikan Anda telah memberikan izin kamera.');
            }
        }

        /**
         * Berhenti scanning
         * Dipanggil setelah berhasil membaca QR code
         */
        async function stopScanner() {
            if (html5QrCode && isScanning) {
                try {
                    await html5QrCode.stop();
                    isScanning = false;
                    console.log('🛑 Scanner stopped');
                } catch (error) {
                    console.error('❌ Error stopping scanner:', error);
                }
            }
        }

        // ============================================
        // CALLBACK SCANNER
        // ============================================

        /**
         * Dipanggil saat QR code berhasil dibaca
         * @param {string} decodedText - Isi QR code (order ID)
         * @param {object} decodedResult - Detail hasil scan
         */
        async function onScanSuccess(decodedText, decodedResult) {
            console.log('✅ QR Code detected:', decodedText);

            // 1. Mainkan bunyi beep
            playBeep();

            // 2. Stop scanner
            await stopScanner();

            // 3. Tampilkan loading
            document.getElementById('loadingSpinner').style.display = 'block';

            // 4. Fetch data pesanan dari backend
            await fetchOrderData(decodedText);
        }

        /**
         * Dipanggil setiap frame saat gagal membaca QR
         * (Normal, tidak perlu ditangani)
         */
        function onScanFailure(error) {
            // Console log di-hide agar tidak spam
            // console.warn('⚠️ No QR code detected:', error);
        }

        // ============================================
        // FUNGSI AUDIO
        // ============================================

        /**
         * Memainkan bunyi beep pendek
         */
        function playBeep() {
            if (beepSound) {
                beepSound.currentTime = 0;  // Reset ke awal
                beepSound.play().catch(error => {
                    console.warn('⚠️ Gagal memainkan beep:', error);
                });
            }
        }

        // ============================================
        // FUNGSI API & DATA
        // ============================================

        /**
         * Mengambil data pesanan dari backend API
         * @param {string} orderId - ID pesanan dari QR code
         */
        async function fetchOrderData(orderId) {
            try {
                // Validasi bahwa QR code berisi angka (order ID)
                if (!isValidOrderId(orderId)) {
                    throw new Error('QR Code tidak valid. Pastikan Anda memindai QR Code pesanan.');
                }

                // Kirim request ke backend
                const response = await fetch(API_ENDPOINT, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        order_id: orderId
                    })
                });

                const result = await response.json();

                // Sembunyikan loading
                document.getElementById('loadingSpinner').style.display = 'none';

                if (result.success) {
                    // Tampilkan data pesanan
                    displayOrderData(result.data);
                } else {
                    // Tampilkan pesan error dari backend
                    showError(result.message || 'Pesanan tidak ditemukan');
                }

            } catch (error) {
                console.error('❌ Error fetching order:', error);
                document.getElementById('loadingSpinner').style.display = 'none';
                showError(error.message || 'Gagal mengambil data pesanan. Silakan coba lagi.');
            }
        }

        /**
         * Menampilkan data pesanan ke UI
         * @param {object} orderData - Data pesanan dari backend
         */
        function displayOrderData(orderData) {
            // Update informasi pesanan
            document.getElementById('orderId').textContent = '#' + orderData.order_id;
            document.getElementById('customerName').textContent = orderData.customer_name;
            document.getElementById('orderTime').textContent = orderData.created_at;

            // Update status pembayaran dengan badge
            const paymentStatusEl = document.getElementById('paymentStatus');
            if (orderData.status_bayar === 'lunas') {
                paymentStatusEl.innerHTML = '<span class="badge-lunas">LUNAS ✅</span>';
            } else {
                paymentStatusEl.innerHTML = '<span class="badge-pending">PENDING ⏳</span>';
            }

            // Update total
            document.getElementById('totalAmount').textContent = formatRupiah(orderData.total);

            // Update menu items
            const menuItemsContainer = document.getElementById('menuItems');
            menuItemsContainer.innerHTML = '';

            if (orderData.menu_items && orderData.menu_items.length > 0) {
                orderData.menu_items.forEach(item => {
                    const menuItemHtml = `
                        <div class="menu-item">
                            <div class="menu-item-name">${item.menu_nama}</div>
                            <div class="menu-item-details">
                                ${item.qty} x ${formatRupiah(item.harga)} = ${formatRupiah(item.subtotal)}
                            </div>
                        </div>
                    `;
                    menuItemsContainer.innerHTML += menuItemHtml;
                });
            } else {
                menuItemsContainer.innerHTML = '<p class="text-muted">Tidak ada item menu</p>';
            }

            // Tampilkan result container
            document.getElementById('resultContainer').style.display = 'block';
        }

        // ============================================
        // FUNGSI UTILITY
        // ============================================

        /**
         * Validasi apakah string adalah order ID yang valid
         * @param {string} orderId - Order ID untuk divalidasi
         * @returns {boolean}
         */
        function isValidOrderId(orderId) {
            // Order ID harus berupa angka positif
            const orderIdNum = parseInt(orderId);
            return !isNaN(orderIdNum) && orderIdNum > 0;
        }

        /**
         * Menampilkan pesan error
         * @param {string} message - Pesan error
         */
        function showError(message) {
            const errorEl = document.getElementById('errorMessage');
            errorEl.textContent = '❌ ' + message;
            errorEl.style.display = 'block';
        }

        /**
         * Format angka ke Rupiah
         * @param {number} amount - Jumlah uang
         * @returns {string} - Format Rupiah
         */
        function formatRupiah(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }

        // ============================================
        // CLEANUP
        // ============================================

        /**
         * Stop scanner saat page akan di-unload
         * Untuk melepaskan akses kamera
         */
        window.addEventListener('beforeunload', function() {
            if (html5QrCode && isScanning) {
                html5QrCode.stop().catch(err => {
                    console.error('Error stopping scanner:', err);
                });
            }
        });
    </script>
</body>
</html>
