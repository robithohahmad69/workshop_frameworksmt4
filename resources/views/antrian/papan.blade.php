<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Papan Antrian</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); min-height: 100vh; overflow: hidden; }

        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); display: flex; align-items: center; justify-content: center; z-index: 9999; }
        .overlay-content { text-align: center; color: white; }
        .overlay-icon { width: 120px; height: 120px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; animation: pulse 2s infinite; }
        .overlay-icon i { font-size: 60px; }
        .overlay h1 { font-size: 32px; font-weight: 700; margin-bottom: 16px; }
        .overlay p { font-size: 18px; opacity: 0.85; margin-bottom: 30px; }
        .btn-activate { padding: 18px 40px; background: white; color: #1e3c72; border: none; border-radius: 50px; font-size: 18px; font-weight: 700; cursor: pointer; transition: all 0.3s; }
        .btn-activate:hover { transform: scale(1.05); box-shadow: 0 10px 40px rgba(0,0,0,0.3); }

        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }

        .display { display: none; height: 100vh; padding: 30px; }
        .display.active { display: grid; grid-template-rows: auto 1fr auto; gap: 30px; }

        .header { text-align: center; color: white; }
        .header h1 { font-size: 36px; font-weight: 700; margin-bottom: 8px; display: flex; align-items: center; justify-content: center; gap: 16px; }
        .header p { font-size: 18px; opacity: 0.8; }

        .main-content { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: center; }

        .current-card { background: white; border-radius: 30px; padding: 60px 40px; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .current-label { color: #888; font-size: 18px; font-weight: 500; text-transform: uppercase; letter-spacing: 3px; margin-bottom: 20px; }
        .current-number { font-size: 160px; font-weight: 800; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1; margin-bottom: 20px; }
        .current-name { font-size: 42px; color: #333; font-weight: 600; min-height: 50px; }

        .waiting-card { background: rgba(255,255,255,0.95); border-radius: 30px; padding: 40px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .waiting-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .waiting-header h2 { font-size: 24px; font-weight: 600; color: #333; display: flex; align-items: center; gap: 12px; }
        .waiting-count { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 8px 20px; border-radius: 20px; font-size: 18px; font-weight: 700; }

        .waiting-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 16px; max-height: 400px; overflow-y: auto; padding: 10px; }
        .waiting-chip { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px 12px; border-radius: 16px; text-align: center; font-size: 24px; font-weight: 700; transition: transform 0.3s; }
        .waiting-chip:hover { transform: scale(1.1); }
        .waiting-empty { text-align: center; color: #999; padding: 60px 20px; }
        .waiting-empty i { font-size: 60px; opacity: 0.5; margin-bottom: 16px; display: block; }

        .footer { text-align: center; color: rgba(255,255,255,0.7); font-size: 16px; }

        .current-card.new-call .current-number { animation: numberPop 0.6s ease-out; }
        @keyframes numberPop {
            0% { transform: scale(0.5); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }

        @media (max-width: 1200px) {
            .main-content { grid-template-columns: 1fr; }
            .current-number { font-size: 120px; }
            .current-name { font-size: 32px; }
        }
    </style>
</head>
<body>
    <div class="overlay" id="overlay">
        <div class="overlay-content">
            <div class="overlay-icon">
                <i class="mdi mdi-bullhorn"></i>
            </div>
            <h1>Papan Antrian</h1>
            <p>Klik di mana saja untuk mengaktifkan notifikasi suara</p>
            <button class="btn-activate" onclick="activate()">
                <i class="mdi mdi-play"></i> Aktifkan
            </button>
        </div>
    </div>

    <div class="display" id="display">
        <div class="header">
            <h1><i class="mdi mdi-bullhorn"></i> Papan Antrian</h1>
            <p>Silakan menunggu, nomor antrian Anda akan dipanggil</p>
        </div>

        <div class="main-content">
            <div class="current-card" id="currentCard">
                <div class="current-label">Sedang Dipanggil</div>
                <div class="current-number" id="currentNumber">-</div>
                <div class="current-name" id="currentName"></div>
            </div>

            <div class="waiting-card">
                <div class="waiting-header">
                    <h2><i class="mdi mdi-clock-outline"></i> Antrian Menunggu</h2>
                    <span class="waiting-count" id="waitingCount">0</span>
                </div>
                <div class="waiting-grid" id="waitingGrid">
                    <div class="waiting-empty">
                        <i class="mdi mdi-emoticon-happy"></i>
                        <span>Belum ada antrian</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <i class="mdi mdi-information"></i> Mohon menunggu di ruang tunggu hingga nomor Anda dipanggil
        </div>
    </div>

    {{-- FIXED: path audio diperbaiki --}}
   <audio id="dingdong" src="{{ asset('dingdong.mp3') }}" preload="auto"></audio>

    <script>
        let lastDataHash = null;
        let isActive = false;
        let nomorTerakhirDisuarakan = null;
        const dingdong = document.getElementById('dingdong');

        // SSE Connection variables
        let eventSource = null;
        let reconnectTimeout = null;
        let reconnectDelay = 1000;
        const MAX_RECONNECT_DELAY = 30000;

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

        function activate() {
            isActive = true;
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('display').classList.add('active');
            initSSE();
            if ('speechSynthesis' in window) {
                window.speechSynthesis.getVoices();
            }
        }

        // Tutup SSE saat page ditutup
        window.addEventListener('beforeunload', function() {
            disconnectSSE();
        });

        function suarakanNomor(nomor, nama) {
            if (!('speechSynthesis' in window)) return;
            window.speechSynthesis.cancel();

            const utterance = new SpeechSynthesisUtterance(`Nomor antrian ${nomor}. ${nama}, silakan masuk.`);
            utterance.lang = 'id-ID';
            utterance.rate = 0.9;
            utterance.pitch = 1;
            utterance.volume = 1;

            const voices = window.speechSynthesis.getVoices();
            const indonesianVoice = voices.find(v => v.lang.startsWith('id'));
            if (indonesianVoice) utterance.voice = indonesianVoice;

            window.speechSynthesis.speak(utterance);
        }

        function updateUI(data) {
            // FIXED: hash check agar tidak re-render setiap detik
            const hash = JSON.stringify(data);
            if (hash === lastDataHash) return;
            lastDataHash = hash;

            const currentCard = document.getElementById('currentCard');
            const currentNumberEl = document.getElementById('currentNumber');
            const currentNameEl = document.getElementById('currentName');
            const waitingGrid = document.getElementById('waitingGrid');
            const waitingCount = document.getElementById('waitingCount');

            if (data.dipanggil && data.dipanggil.length > 0) {
                const antrian = data.dipanggil[0];
                const nomor = String(antrian.nomor).padStart(3, '0');

                if (nomorTerakhirDisuarakan !== nomor) {
                    dingdong.currentTime = 0;
                    dingdong.play().then(() => {
                        dingdong.onended = () => suarakanNomor(nomor, antrian.nama);
                    }).catch(e => console.error('Audio play failed:', e));

                    currentCard.classList.remove('new-call');
                    void currentCard.offsetWidth;
                    currentCard.classList.add('new-call');

                    nomorTerakhirDisuarakan = nomor;
                }

                currentNumberEl.textContent = nomor;
                currentNameEl.textContent = antrian.nama;
            } else {
                currentNumberEl.textContent = '-';
                currentNameEl.textContent = '';
                currentCard.classList.remove('new-call');
            }

            const menunggu = data.menunggu || [];
            waitingCount.textContent = menunggu.length;

            if (menunggu.length > 0) {
                waitingGrid.innerHTML = menunggu.map(item =>
                    `<div class="waiting-chip">${String(item.nomor).padStart(3, '0')}</div>`
                ).join('');
            } else {
                waitingGrid.innerHTML = `
                    <div class="waiting-empty">
                        <i class="mdi mdi-emoticon-happy"></i>
                        <span>Belum ada antrian</span>
                    </div>
                `;
            }
        }

        function initSSE() {
            // Tutup koneksi lama jika ada
            disconnectSSE();

            console.log('Papan: Menghubungkan ke SSE...');
            eventSource = new EventSource('{{ route('antrian.stream') }}');

            eventSource.addEventListener('antrian-update', function(e) {
                try {
                    const data = JSON.parse(e.data);
                    updateUI(data);
                    // Reset reconnect delay setelah berhasil
                    reconnectDelay = 1000;
                } catch (err) {
                    console.error('Error parsing SSE data:', err);
                }
            });

            eventSource.onopen = function() {
                console.log('Papan: SSE Terhubung');
            };

            eventSource.onerror = function(err) {
                console.error('Papan: SSE Error, reconnecting...', err);

                // Tutup koneksi yang error
                if (eventSource) {
                    eventSource.close();
                    eventSource = null;
                }

                // Reconnect dengan exponential backoff
                reconnectTimeout = setTimeout(function() {
                    reconnectDelay = Math.min(reconnectDelay * 2, MAX_RECONNECT_DELAY);
                    initSSE();
                }, reconnectDelay);
            };
        }

        if ('speechSynthesis' in window) {
            speechSynthesis.onvoiceschanged = () => {
                window.speechSynthesis.getVoices();
            };
        }
    </script>
</body>
</html>