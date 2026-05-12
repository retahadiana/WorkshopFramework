@extends('layout.master')

@section('content')
<div class="container">
    <h1>Scan Barcode</h1>
    <div class="row">
        <div class="col-md-8">
            <div id="qr-reader" style="width: 100%; border: 2px solid #ccc; border-radius: 8px;"></div>
            <div class="mt-3">
                <button id="start-scan" class="btn btn-primary">Start Scan</button>
                <button id="stop-scan" class="btn btn-secondary" disabled>Stop Scan</button>
            </div>
            <p id="scan-status" class="mt-3 text-muted" style="font-size: 14px;">Tekan Start Scan untuk memulai kamera.</p>
        </div>
        <div class="col-md-4">
            <div id="result" class="alert alert-info" style="display: none;">
                <h5>✓ Barcode Terdeteksi</h5>
                <hr>
                <p><strong>Kode:</strong> <span id="scanned-code" style="font-family: monospace; font-weight: bold;"></span></p>
                <p><strong>ID Barang:</strong> <span id="id-barang"></span></p>
                <p><strong>Nama:</strong> <span id="nama-barang"></span></p>
                <p><strong>Harga:</strong> <span id="harga-barang" style="font-weight: bold; color: green;"></span></p>
                <p id="not-found-message" class="text-danger mt-2" style="display: none;">⚠️ Barang tidak ditemukan untuk kode ini.</p>
            </div>
        </div>
    </div>
</div>

<audio id="beep-sound" src="{{ asset('sounds/dragon-studio-censor-beep-1-372459.mp3') }}" preload="auto"></audio>

<!-- HTML5 QRCode Library -->
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startScanBtn = document.getElementById('start-scan');
    const stopScanBtn = document.getElementById('stop-scan');
    const resultDiv = document.getElementById('result');
    const beepSound = document.getElementById('beep-sound');
    const scanStatus = document.getElementById('scan-status');
    const scannedCode = document.getElementById('scanned-code');
    const notFoundMessage = document.getElementById('not-found-message');

    let qrCodeReader;
    let scanning = false;
    let lastScannedCode = '';

    startScanBtn.addEventListener('click', startScanning);
    stopScanBtn.addEventListener('click', stopScanning);

    function startScanning() {
        if (scanning) return;

        scanning = true;
        startScanBtn.disabled = true;
        stopScanBtn.disabled = false;
        scanStatus.textContent = '🎥 Kamera aktif - arahkan barcode ke camera';
        resultDiv.style.display = 'none';
        lastScannedCode = '';

        qrCodeReader = new Html5Qrcode('qr-reader');

        qrCodeReader.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onScanSuccess,
            onScanError
        ).catch(error => {
            console.error('Gagal memulai scanner:', error);
            scanStatus.textContent = '❌ Error: ' + error.message;
            scanStatus.classList.add('text-danger');
            scanning = false;
            startScanBtn.disabled = false;
            stopScanBtn.disabled = true;
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        // Hindari multiple detection of same code
        if (decodedText === lastScannedCode) return;
        
        lastScannedCode = decodedText;
        console.log('Barcode detected:', decodedText);
        
        scanStatus.textContent = `✓ Barcode terdeteksi: ${decodedText}`;
        scanStatus.classList.remove('text-danger');
        scannedCode.textContent = decodedText;

        // Play beep sound
        beepSound.play().catch(() => {
            console.warn('Beep sound could not play.');
        });

        // Stop scanning
        stopScanning();

        // Fetch barang data
        fetch(`/api/barang/${decodedText}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    document.getElementById('id-barang').textContent = '-';
                    document.getElementById('nama-barang').textContent = '-';
                    document.getElementById('harga-barang').textContent = '-';
                    notFoundMessage.style.display = 'block';
                    resultDiv.style.display = 'block';
                    scanStatus.textContent = '⚠️ Barang tidak ditemukan di database';
                    scanStatus.classList.add('text-warning');
                } else {
                    document.getElementById('id-barang').textContent = data.id_barang;
                    document.getElementById('nama-barang').textContent = data.nama;
                    document.getElementById('harga-barang').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.harga);
                    notFoundMessage.style.display = 'none';
                    resultDiv.style.display = 'block';
                    scanStatus.textContent = '✓ Data barang berhasil dimuat';
                }
            })
            .catch(error => {
                console.error('Error fetching barang:', error);
                scanStatus.textContent = '❌ Error: Gagal mengambil data barang';
                scanStatus.classList.add('text-danger');
            });
    }

    function onScanError(errorMessage) {
        // Suppress error messages for continuous scanning
        // Only log if there's a real error
    }

    function stopScanning() {
        if (qrCodeReader && scanning) {
            qrCodeReader.stop().then(() => {
                scanning = false;
                startScanBtn.disabled = false;
                stopScanBtn.disabled = true;
                scanStatus.textContent = '⏹️ Scanner dihentikan. Klik Start Scan untuk mencoba lagi.';
                scanStatus.classList.remove('text-danger', 'text-warning');
            }).catch(error => {
                console.error('Error stopping scanner:', error);
                scanning = false;
                startScanBtn.disabled = false;
                stopScanBtn.disabled = true;
            });
        }
    }

    // Cleanup when page unloads
    window.addEventListener('beforeunload', () => {
        if (scanning) {
            stopScanning();
        }
    });
});
</script>

<style>
    #qr-reader {
        min-height: 400px;
        background-color: #f0f0f0;
    }
    
    #result {
        position: sticky;
        top: 20px;
    }
</style>
@endsection