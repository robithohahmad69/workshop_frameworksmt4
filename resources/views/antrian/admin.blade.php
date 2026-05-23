<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Antrian</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; }

        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 15px 30px; display: flex; align-items: center; gap: 15px; }
        .navbar a { color: white; text-decoration: none; font-size: 14px; opacity: 0.8; }
        .navbar a:hover { opacity: 1; }

        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }

        .status-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .status-badge { display: flex; align-items: center; gap: 8px; padding: 8px 16px; background: white; border-radius: 20px; font-size: 13px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status-dot { width: 10px; height: 10px; border-radius: 50%; background: #ff4444; }
        .status-dot.connected { background: #00c851; }

        .row { display: flex; gap: 20px; flex-wrap: wrap; }
        .col { flex: 1; min-width: 300px; }

        .card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .card-header { font-size: 16px; font-weight: 600; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }

        .current-display { text-align: center; padding: 30px; }
        .current-number { font-size: 72px; font-weight: 800; color: #667eea; line-height: 1; }
        .current-name { font-size: 24px; color: #333; margin-top: 10px; }
        .current-empty { color: #ccc; font-size: 48px; }

        .btn-group { display: flex; gap: 10px; margin-top: 20px; }
        .btn { flex: 1; padding: 12px 20px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; color: white; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-primary { background: #667eea; }
        .btn-success { background: #00c853; }
        .btn-warning { background: #ff9800; padding: 8px 16px; font-size: 13px; }
        .btn-warning:hover { background: #f57c00; }

        .queue-list { max-height: 300px; overflow-y: auto; }
        .queue-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px; }
        .queue-left { display: flex; align-items: center; gap: 12px; }
        .queue-num { width: 45px; height: 45px; background: #667eea; color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700; }
        .badge-late { background: #ffebee; color: #c62828; padding: 3px 8px; border-radius: 4px; font-size: 11px; margin-left: 8px; }

        .empty { text-align: center; color: #999; padding: 30px; }
        .count { background: #667eea; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="/">← Kembali ke Dashboard</a>
        <span style="color: white; font-weight: 600;">Admin Antrian</span>
    </div>

    <div class="container">
        <div class="status-bar">
            <h2 style="font-size: 20px;">Kelola Antrian</h2>
            <div class="status-badge">
                <div class="status-dot" id="statusDot"></div>
                <span id="statusText">Menghubungkan...</span>
            </div>
        </div>

        <div class="row">
            <!-- Current Number -->
            <div class="col">
                <div class="card">
                    <div class="card-header">Sedang Dipanggil</div>
                    <div class="current-display" id="currentDisplay">
                        <div class="current-empty">-</div>
                        <div class="current-name" id="currentName"></div>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-primary" onclick="panggil()">Panggil Berikutnya</button>
                        <button class="btn btn-success" onclick="selesai()" id="btnSelesai" disabled>Selesai</button>
                    </div>
                </div>
            </div>

            <!-- Waiting List -->
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <span>Menunggu</span>
                        <span class="count" id="menungguCount">0</span>
                    </div>
                    <div class="queue-list" id="menungguList">
                        <div class="empty">Belum ada antrian</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Late List -->
        <div class="card">
            <div class="card-header">
                <span>Terlambat</span>
                <span class="count" id="terlambatCount">0</span>
            </div>
            <div class="queue-list" id="terlambatList">
                <div class="empty">Tidak ada antrian terlambat</div>
            </div>
        </div>
    </div>

    <script>
        // Hash check untuk mencegah re-render yang tidak perlu
        let lastDataHash = null;

        // SSE Connection variables
        let eventSource = null;
        let reconnectTimeout = null;
        let reconnectDelay = 1000; // Mulai dengan 1 detik
        const MAX_RECONNECT_DELAY = 30000; // Max 30 detik

        // Initial empty state
        updateUI({ dipanggil: [], menunggu: [], terlambat: [] });

        // Start SSE connection
        connectSSE();

        // Tutup SSE saat page ditutup
        window.addEventListener('beforeunload', function() {
            disconnectSSE();
        });

        function disconnectSSE() {
            if (eventSource) {
                eventSource.close();
                eventSource = null;
            }
            if (reconnectTimeout) {
                clearTimeout(reconnectTimeout);
                reconnectTimeout = null;
            }
        }

        function connectSSE() {
            // Tutup koneksi lama jika ada
            disconnectSSE();

            console.log('Menghubungkan ke SSE...');
            updateStatus('connecting');

            eventSource = new EventSource('{{ route('antrian.stream') }}');

            eventSource.addEventListener('antrian-update', function(e) {
                try {
                    const data = JSON.parse(e.data);
                    updateUI(data);

                    // Reset reconnect delay setelah berhasil menerima data
                    reconnectDelay = 1000;
                    updateStatus('connected');
                } catch (err) {
                    console.error('Error parsing SSE data:', err);
                }
            });

            eventSource.onopen = function() {
                console.log('SSE Terhubung');
                updateStatus('connected');
            };

            eventSource.onerror = function(err) {
                console.error('SSE Error:', err);
                updateStatus('disconnected');

                // Tutup koneksi yang error
                if (eventSource) {
                    eventSource.close();
                    eventSource = null;
                }

                // Reconnect dengan exponential backoff
                console.log('Mencoba reconnect dalam ' + reconnectDelay + 'ms...');
                reconnectTimeout = setTimeout(function() {
                    reconnectDelay = Math.min(reconnectDelay * 2, MAX_RECONNECT_DELAY);
                    connectSSE();
                }, reconnectDelay);
            };
        }

        function updateStatus(status) {
            const statusDot = document.getElementById('statusDot');
            const statusText = document.getElementById('statusText');

            switch(status) {
                case 'connected':
                    statusDot.classList.add('connected');
                    statusText.textContent = 'Terhubung';
                    break;
                case 'connecting':
                    statusDot.classList.remove('connected');
                    statusText.textContent = 'Menghubungkan...';
                    break;
                case 'disconnected':
                    statusDot.classList.remove('connected');
                    statusText.textContent = 'Terputus - Reconnecting...';
                    break;
            }
        }

        function updateUI(data) {
            // Hash check - hanya update jika data berubah
            const hash = JSON.stringify(data);
            if (hash === lastDataHash) return;
            lastDataHash = hash;

            console.log('Updating UI dengan data:', data);

            // Current number
            const currentDisplay = document.getElementById('currentDisplay');
            const currentName = document.getElementById('currentName');
            const btnSelesai = document.getElementById('btnSelesai');

            if (data.dipanggil && data.dipanggil.length > 0) {
                const antrian = data.dipanggil[0];
                currentDisplay.innerHTML = `
                    <div class="current-number">${String(antrian.nomor).padStart(3, '0')}</div>
                    <div class="current-name">${antrian.nama}</div>
                `;
                btnSelesai.disabled = false;
            } else {
                currentDisplay.innerHTML = `
                    <div class="current-empty">-</div>
                    <div class="current-name"></div>
                `;
                btnSelesai.disabled = true;
            }

            // Menunggu list
            const menungguList = document.getElementById('menungguList');
            const menungguCount = document.getElementById('menungguCount');
            menungguCount.textContent = data.menunggu ? data.menunggu.length : 0;

            if (data.menunggu && data.menunggu.length > 0) {
                menungguList.innerHTML = data.menunggu.map(item => `
                    <div class="queue-item">
                        <div class="queue-left">
                            <div class="queue-num">${String(item.nomor).padStart(3, '0')}</div>
                            <span>${item.nama}</span>
                        </div>
                    </div>
                `).join('');
            } else {
                menungguList.innerHTML = '<div class="empty">Belum ada antrian</div>';
            }

            // Terlambat list
            const terlambatList = document.getElementById('terlambatList');
            const terlambatCount = document.getElementById('terlambatCount');
            terlambatCount.textContent = data.terlambat ? data.terlambat.length : 0;

            if (data.terlambat && data.terlambat.length > 0) {
                terlambatList.innerHTML = data.terlambat.map(item => `
                    <div class="queue-item">
                        <div class="queue-left">
                            <div class="queue-num">${String(item.nomor).padStart(3, '0')}</div>
                            <span>${item.nama}<span class="badge-late">Terlambat</span></span>
                        </div>
                        <button class="btn btn-warning" onclick="panggilTerlambat(${item.id})">Panggil Ulang</button>
                    </div>
                `).join('');
            } else {
                terlambatList.innerHTML = '<div class="empty">Tidak ada antrian terlambat</div>';
            }
        }

        async function panggil() {
            try {
                const response = await fetch('{{ route('antrian.panggil') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await response.json();
                if (!data.success) {
                    alert(data.message || 'Tidak ada antrian menunggu');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        }

        async function selesai() {
            try {
                const response = await fetch('{{ route('antrian.selesai') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await response.json();
                console.log('Selesai:', data);
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function panggilTerlambat(id) {
            try {
                const response = await fetch('{{ route('antrian.panggilTerlambat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ id: id })
                });
                const data = await response.json();
                if (!data.success) {
                    alert('Gagal memanggil ulang');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        }
    </script>
</body>
</html>
