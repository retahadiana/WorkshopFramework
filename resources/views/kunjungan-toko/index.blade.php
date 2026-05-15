@extends('layout.master')

@section('title','Kunjungan Toko')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title">Kunjungan Toko</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6>Instruksi Kunjungan Toko</h6>
                        <p>Gunakan <strong>Scanner Barcode Toko</strong> untuk memindai barcode yang tercetak pada data toko. Setelah berhasil discan, ringkasan data toko akan muncul dan Anda dapat melanjutkan pencatatan kunjungan sesuai prosedur operasional.</p>
                        <ul>
                            <li>Pastikan kamera perangkat memiliki izin akses.</li>
                            <li>Arahkan kamera ke barcode pada label/Toko hingga muncul tanda terdeteksi.</li>
                            <li>Jika toko ditemukan, detail akan tampil. Tutup modal untuk melanjutkan.</li>
                        </ul>

                        <div class="mt-3">
                            <button type="button" id="btn-scanner" class="btn btn-info" onclick="openScannerModal()">
                                <i class="mdi mdi-barcode-scan"></i> Scanner Barcode Toko
                            </button>
                        </div>

                        <div id="last-scan-summary" class="mt-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Scanner -->
<div class="modal fade" id="scannerModal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Barcode Scanner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="qr-reader" style="width: 100%; max-width: 520px; margin: 0 auto; border: 2px solid #ccc; border-radius: 8px; min-height: 280px;"></div>
                <div id="scan-result" class="mt-2"></div>
                <div class="mt-3">
                    <button id="start-scan" class="btn btn-primary">Start Scan</button>
                    <button id="stop-scan" class="btn btn-secondary" disabled>Stop Scan</button>
                </div>
                <p id="scan-status" class="mt-2 text-muted">Tekan Start Scan untuk memulai.</p>
            </div>
        </div>
    </div>
</div>

<!-- Result Modal -->
<div class="modal fade" id="resultModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-3">
            <div class="modal-body">
                <div id="result-icon" style="font-size:48px; margin-bottom:8px"></div>
                <h3 id="result-title"></h3>
                <p id="result-body" class="mb-0"></p>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    let qrCodeReader;
    let scanningActive = false;
    let scannerModal = null;

    try {
        const modalEl = document.getElementById('scannerModal');
        if (modalEl) scannerModal = new bootstrap.Modal(modalEl, {backdrop: 'static'});
    } catch (e) {
        console.error('Error initializing modal:', e);
    }

    window.openScannerModal = function(){
        if (!scannerModal) return alert('Modal tidak tersedia. Refresh halaman dan coba lagi.');
        scanningActive = false;
        lastScanned = '';
        document.getElementById('start-scan').disabled = false;
        document.getElementById('stop-scan').disabled = true;
        document.getElementById('scan-status').textContent = 'Tekan Start Scan untuk memulai.';
        document.getElementById('scan-result').innerHTML = '';
        scannerModal.show();
    };

    document.getElementById('start-scan').addEventListener('click', startScanning);
    document.getElementById('stop-scan').addEventListener('click', stopScanning);

    function startScanning(){
        if (scanningActive) return;
        scanningActive = true;
        document.getElementById('start-scan').disabled = true;
        document.getElementById('stop-scan').disabled = false;
        document.getElementById('scan-status').textContent = '🎥 Kamera aktif - arahkan barcode ke kamera';

        qrCodeReader = new Html5Qrcode('qr-reader');
        qrCodeReader.start({facingMode: 'environment'}, {fps: 10, qrbox: {width:250, height:250}}, onScanSuccess, onScanError)
        .catch(err => {
            document.getElementById('scan-status').textContent = '❌ Error: ' + err.message;
            scanningActive = false;
            document.getElementById('start-scan').disabled = false;
            document.getElementById('stop-scan').disabled = true;
        });
    }

    let lastScanned = '';
    function onScanSuccess(decodedText){
        if (decodedText === lastScanned) return;
        lastScanned = decodedText;
        document.getElementById('scan-status').textContent = '✓ Barcode terdeteksi: ' + decodedText;
        playBeep();

        // alur: ambil data toko -> ambil lokasi perangkat (akurat) -> hitung jarak -> tampilkan hasil
        fetchStoreLocation(decodedText).then(data => {
            if (!data || data.error) {
                const summary = document.getElementById('last-scan-summary');
                summary.innerHTML = '<div class="text-muted">Toko tidak ditemukan.</div>';
                stopScanning();
                showResultAfterClosingScanner(false, null, null, 'Toko tidak ditemukan');
                return;
            }

            // tampilkan ringkasan sementara
            const summary = document.getElementById('last-scan-summary');
            summary.innerHTML = `<div class="card p-3"><h5>${escapeHtml(data.nama_toko || '-')}</h5><p class="mb-1">${escapeHtml(data.alamat || '')}</p><small>Barcode: <code>${escapeHtml(data.barcode)}</code></small></div>`;

            // hentikan scanner; coba posisi cepat dulu (3s) untuk tampilkan hasil awal,
            // lalu jalankan pengambilan posisi akurat di background untuk update
            stopScanning();
            if (navigator.geolocation) {
                const quickOpts = { enableHighAccuracy: true, timeout: 3000, maximumAge: 0 };
                navigator.geolocation.getCurrentPosition(function(posQuick) {
                    const devLat = posQuick.coords.latitude;
                    const devLon = posQuick.coords.longitude;
                    const devAcc = posQuick.coords.accuracy || 0;

                    const storeLat = parseFloat(data.latitude) || 0;
                    const storeLon = parseFloat(data.longitude) || 0;
                    const storeAcc = parseFloat(data.accuracy) || 0;

                    const distance = Math.round(haversine(storeLat, storeLon, devLat, devLon));
                    const BASE_THRESHOLD = 300;
                    const effective = Math.round(BASE_THRESHOLD + storeAcc + devAcc);
                    const accepted = distance <= effective;

                    // tampilkan hasil awal segera
                    showResultAfterClosingScanner(accepted, distance, effective);

                    // refine di background
                    getAccuratePosition(50, 20000).then(function(posAcc) {
                        const aLat = posAcc.coords.latitude;
                        const aLon = posAcc.coords.longitude;
                        const aAcc = posAcc.coords.accuracy || 0;
                        const distance2 = Math.round(haversine(storeLat, storeLon, aLat, aLon));
                        const effective2 = Math.round(BASE_THRESHOLD + storeAcc + aAcc);
                        const accepted2 = distance2 <= effective2;
                        updateResultModalContent(accepted2, distance2, effective2);
                    }).catch(function(){ /* ignore background failure */ });

                }, function(errQuick) {
                    // quick failed -> tampilkan pesan processing, lalu pakai posisi akurat
                    showResultAfterClosingScanner(false, null, null, 'Mencari lokasi perangkat...');
                    getAccuratePosition(50, 20000).then(function(posAcc) {
                        const aLat = posAcc.coords.latitude;
                        const aLon = posAcc.coords.longitude;
                        const aAcc = posAcc.coords.accuracy || 0;
                        const storeLat = parseFloat(data.latitude) || 0;
                        const storeLon = parseFloat(data.longitude) || 0;
                        const distance2 = Math.round(haversine(storeLat, storeLon, aLat, aLon));
                        const effective2 = Math.round(300 + (parseFloat(data.accuracy)||0) + aAcc);
                        updateResultModalContent(distance2 <= effective2, distance2, effective2);
                    }).catch(function(){
                        showResultAfterClosingScanner(false, null, null, 'Gagal mengambil lokasi perangkat');
                    });
                }, quickOpts);
            } else {
                showResultAfterClosingScanner(false, null, null, 'Geolocation tidak tersedia');
            }

        }).catch(err => {
            console.warn('Fetch store failed', err);
            stopScanning();
            showResultAfterClosingScanner(false, null, null, 'Gagal mengambil data toko');
        });
    }

    function onScanError(){}

    function stopScanning(){
        if (qrCodeReader && scanningActive) {
            qrCodeReader.stop().then(() => {
                scanningActive = false;
                document.getElementById('start-scan').disabled = false;
                document.getElementById('stop-scan').disabled = true;
                document.getElementById('scan-status').textContent = '⏹️ Scanner dihentikan.';
            }).catch(err => console.error(err));
        }
    }

    // Ambil posisi yang cukup akurat menggunakan watchPosition sampai mencapai targetAccuracy atau timeout
    function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) return reject(new Error('Geolocation tidak tersedia'));
            let best = null;
            const start = Date.now();
            const id = navigator.geolocation.watchPosition(pos => {
                const acc = pos.coords.accuracy || 0;
                if (!best || acc < (best.coords.accuracy || Infinity)) {
                    best = pos;
                }
                if (acc <= targetAccuracy) {
                    navigator.geolocation.clearWatch(id);
                    return resolve(best);
                }
                if (Date.now() - start >= maxWait) {
                    navigator.geolocation.clearWatch(id);
                    if (best) return resolve(best);
                    return reject(new Error('Timeout, tidak dapat memperoleh posisi yang akurat'));
                }
            }, err => {
                navigator.geolocation.clearWatch(id);
                reject(err);
            }, { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait });
        });
    }

    // Haversine distance (meters)
    function haversine(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const toRad = d => d * Math.PI / 180;
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // Tampilkan modal hasil kunjungan
    function showResultModal(accepted, distanceMeter, effectiveThreshold, extraMessage) {
        const modalEl = document.getElementById('resultModal');
        const title = document.getElementById('result-title');
        const body = document.getElementById('result-body');
        const icon = document.getElementById('result-icon');
        console.log('showResultModal', {accepted, distanceMeter, effectiveThreshold, extraMessage});
        if (accepted) {
            icon.innerHTML = '✅';
            title.textContent = 'Kunjungan DITERIMA';
            body.innerHTML = `Jarak Aktual: <strong>${distanceMeter ?? '-'} m</strong><br>Batas Toleransi: <strong>${effectiveThreshold ?? '-'} m</strong>`;
        } else {
            icon.innerHTML = '❌';
            title.textContent = 'Kunjungan DITOLAK';
            body.innerHTML = (extraMessage ? escapeHtml(extraMessage) + '<br>' : '') + `Jarak Aktual: <strong>${distanceMeter ?? '-'} m</strong><br>Batas Toleransi: <strong>${effectiveThreshold ?? '-'} m</strong>`;
        }
        try {
            // Prefer getOrCreateInstance if available (Bootstrap 5.3+)
            let rm = null;
            if (bootstrap && typeof bootstrap.Modal !== 'undefined') {
                if (typeof bootstrap.Modal.getOrCreateInstance === 'function') {
                    rm = bootstrap.Modal.getOrCreateInstance(modalEl);
                } else {
                    rm = new bootstrap.Modal(modalEl);
                }
            }
            if (rm && typeof rm.show === 'function') {
                rm.show();
                // focus for accessibility
                modalEl.focus && modalEl.focus();
                return;
            }
        } catch (e) {
            console.warn('Bootstrap modal show failed', e);
        }
        // fallback: simple alert so user still gets feedback
        try {
            alert((accepted? 'Kunjungan DITERIMA' : 'Kunjungan DITOLAK') + '\n' + (extraMessage? extraMessage + '\n' : '') + 'Jarak: ' + (distanceMeter ?? '-') + ' m');
        } catch (e) {
            console.error('Fallback alert failed', e);
        }
    }

    // Update modal content without re-opening
    function updateResultModalContent(accepted, distanceMeter, effectiveThreshold, extraMessage) {
        const title = document.getElementById('result-title');
        const body = document.getElementById('result-body');
        const icon = document.getElementById('result-icon');
        if (accepted) {
            icon.innerHTML = '✅';
            title.textContent = 'Kunjungan DITERIMA';
            body.innerHTML = `Jarak Aktual: <strong>${distanceMeter ?? '-'} m</strong><br>Batas Toleransi: <strong>${effectiveThreshold ?? '-'} m</strong>`;
        } else {
            icon.innerHTML = '❌';
            title.textContent = 'Kunjungan DITOLAK';
            body.innerHTML = (extraMessage ? escapeHtml(extraMessage) + '<br>' : '') + `Jarak Aktual: <strong>${distanceMeter ?? '-'} m</strong><br>Batas Toleransi: <strong>${effectiveThreshold ?? '-'} m</strong>`;
        }
    }

    // Hide scanner modal first, then show result modal to avoid overlapping modal behavior
    function showResultAfterClosingScanner(accepted, distanceMeter, effectiveThreshold, extraMessage) {
        const scannerEl = document.getElementById('scannerModal');
        if (scannerEl && scannerModal) {
            const onHidden = function() {
                scannerEl.removeEventListener('hidden.bs.modal', onHidden);
                showResultModal(accepted, distanceMeter, effectiveThreshold, extraMessage);
            };
            scannerEl.addEventListener('hidden.bs.modal', onHidden);
            try { scannerModal.hide(); } catch (e) { console.warn('Failed to hide scanner modal', e); onHidden(); }
        } else {
            showResultModal(accepted, distanceMeter, effectiveThreshold, extraMessage);
        }
    }

    // fetch data toko dari API dan tampilkan di modal
    function fetchStoreLocation(barcode){
        return fetch(`/api/lokasi-toko/${encodeURIComponent(barcode)}`).then(r => r.json()).then(data => {
            const scanResultEl = document.getElementById('scan-result');
            if (data && !data.error) {
                if (scanResultEl) scanResultEl.innerHTML = `<div class="card p-2"><strong>${escapeHtml(data.nama_toko || '-')}</strong><br><small>${escapeHtml(data.alamat || '')}</small><br><code>${escapeHtml(data.barcode)}</code></div>`;
                return data;
            } else {
                if (scanResultEl) scanResultEl.innerHTML = '<div class="text-muted">Toko tidak ditemukan.</div>';
                return { error: true };
            }
        }).catch(err => {
            const scanResultEl = document.getElementById('scan-result');
            if (scanResultEl) scanResultEl.innerHTML = '<div class="text-danger">Gagal mengambil data toko.</div>';
            return Promise.reject(err);
        });
    }

    function escapeHtml(unsafe){
        if (!unsafe) return '';
        return String(unsafe).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    }

    function playBeep(){
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const o = ctx.createOscillator();
            const g = ctx.createGain();
            o.type = 'sine'; o.frequency.value = 900; o.connect(g); g.connect(ctx.destination);
            o.start(); g.gain.setValueAtTime(0.001, ctx.currentTime);
            g.gain.exponentialRampToValueAtTime(0.5, ctx.currentTime + 0.01);
            g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
            setTimeout(()=>{ o.stop(); ctx.close(); },150);
        } catch (e) {}
    }

});
</script>
@endsection
