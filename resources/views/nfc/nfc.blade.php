@extends('layouts.apps')

@section('title', 'Scanner NFC e-KTP')
@section('icon', 'mdi mdi-nfc')
@section('page-title', 'Scanner NFC e-KTP')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Scanner NFC</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title">📡 Scanner NFC e-KTP</h4>
                <p class="card-description text-muted">
                    Gunakan HP Android Chrome. Tekan tombol lalu tempelkan e-KTP.
                </p>

                <button id="tombol-scan" onclick="startScan()"
                        class="btn btn-gradient-success btn-lg btn-block mb-3">
                    <i class="mdi mdi-nfc"></i> Aktifkan Scanner NFC
                </button>

                <div id="status" class="alert alert-secondary">
                    Belum aktif.
                </div>

                <div id="hasil" style="display:none"></div>

                <hr>
                <a href="{{ route('nfc.riwayat') }}" class="btn btn-outline-info btn-sm">
                    <i class="mdi mdi-history"></i> Lihat Riwayat Scan
                </a>
                <a href="{{ route('warga.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="mdi mdi-account-multiple"></i> Daftar Warga
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';

    async function startScan() {

        if (!('NDEFReader' in window)) {
            setStatus('danger', '❌ Browser tidak mendukung Web NFC. Gunakan Android Chrome.');
            return;
        }

        const tombol  = document.getElementById('tombol-scan');
        const hasilEl = document.getElementById('hasil');

        tombol.disabled = true;
        tombol.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Scanner aktif...';
        hasilEl.style.display = 'none';

        try {
            const ndef = new NDEFReader();
            await ndef.scan();

            setStatus('success', '✅ NFC aktif. Tempelkan e-KTP ke belakang HP...');

            ndef.addEventListener('reading', async ({ serialNumber, message }) => {

                setStatus('warning', '📖 Kartu terbaca! Memproses...');

                // Debug — lihat di Remote DevTools laptop
                console.log('Serial Number:', serialNumber);
                console.log('Jumlah record:', message.records.length);

                try {
                    const response = await fetch('{{ route("nfc.scan") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ serial_number: serialNumber }),
                    });

                    const data = await response.json();

                    if (data.status === 'dikenal') {
                        hasilEl.className = 'alert alert-success mt-3';
                        hasilEl.innerHTML = `
                            <h5>✅ ${data.pesan}</h5>
                            <hr>
                            <p class="mb-1"><b>NIK:</b> ${data.warga.nik}</p>
                            <p class="mb-1"><b>Alamat:</b> ${data.warga.alamat ?? '-'}</p>
                            <small class="text-muted">Serial: ${serialNumber}</small>
                        `;
                    } else {
                        hasilEl.className = 'alert alert-danger mt-3';
                        hasilEl.innerHTML = `
                            <h5>❌ ${data.pesan}</h5>
                            <small>Kartu ini belum terdaftar di sistem.</small>
                        `;
                    }

                    hasilEl.style.display = 'block';
                    setStatus('secondary', 'Scan selesai. Tempelkan kartu lain untuk scan lagi.');

                } catch (fetchError) {
                    console.error('Fetch error:', fetchError);
                    setStatus('danger', '❌ Gagal menghubungi server. Cek koneksi.');
                }
            });

            ndef.addEventListener('readingerror', () => {
                setStatus('warning', '⚠️ Kartu tidak terbaca. Coba lagi.');
            });

        } catch (err) {
            console.error('NFC Error:', err);
            setStatus('danger', '❌ Error: ' + err.message);
            tombol.disabled = false;
            tombol.innerHTML = '<i class="mdi mdi-nfc"></i> Aktifkan Scanner NFC';
        }
    }

    // Helper: ganti isi dan warna status
    function setStatus(type, pesan) {
        const el = document.getElementById('status');
        el.className = 'alert alert-' + type;
        el.textContent = pesan;
    }
</script>
@endsection