@extends('layout.master')

@section('title', 'Sistem Absensi NFC')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title mb-0">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-nfc"></i>
                </span>
                Scan Kehadiran
            </h3>
        </div>
    </div>
</div>

<style>
    :root {
        --nfc-primary: #4f46e5;
        --nfc-hover: #4338ca;
        --nfc-success: #10b981;
        --nfc-error: #ef4444;
    }

    .nfc-container {
        position: relative;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 24px;
        padding: 40px;
        max-width: 450px;
        margin: 0 auto;
        text-align: center;
        box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .icon-container {
        width: 80px;
        height: 80px;
        background: rgba(79, 70, 229, 0.1);
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto 24px;
        border: 2px solid var(--nfc-primary);
        position: relative;
    }

    .icon-container svg {
        width: 40px;
        height: 40px;
        fill: var(--nfc-primary);
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    .subtitle {
        color: #64748b;
        font-size: 14px;
        margin-bottom: 32px;
        line-height: 1.5;
    }

    .scan-btn {
        width: 100%;
        padding: 16px;
        background: var(--nfc-primary);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.39);
    }

    .scan-btn:hover {
        background: var(--nfc-hover);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.23);
    }

    .scan-btn:disabled {
        background: #cbd5e1;
        cursor: not-allowed;
        box-shadow: none;
        color: #475569;
    }

    .status-box {
        margin-top: 24px;
        padding: 16px;
        border-radius: 12px;
        font-size: 14px;
        display: none;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s ease;
    }

    .status-box.show {
        display: block;
        animation: slideUp 0.3s forwards;
    }

    @keyframes slideUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .status-success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        color: #047857;
    }

    .status-error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #b91c1c;
    }

    .status-info {
        background: rgba(56, 189, 248, 0.1);
        border: 1px solid rgba(56, 189, 248, 0.2);
        color: #0369a1;
    }

    .student-details {
        margin-top: 12px;
        text-align: left;
        background: #f8fafc;
        padding: 16px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        display: none;
    }
    
    .student-details.show {
        display: block;
    }

    .student-details p {
        margin: 4px 0;
        font-size: 14px;
        color: #475569;
    }
    
    .student-details strong {
        color: #1e293b;
    }

    .scanning .icon-container::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 2px solid var(--nfc-primary);
        animation: ripple 1.5s infinite ease-out;
    }

    @keyframes ripple {
        0% { transform: scale(1); opacity: 1; }
        100% { transform: scale(1.5); opacity: 0; }
    }
</style>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="nfc-container" id="app-container">
                    <div class="icon-container" id="nfc-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M4.222 3.808l1.435 1.435c-2.31 2.31-3.657 5.39-3.657 8.757 0 3.367 1.347 6.447 3.657 8.757l-1.435 1.435a14.28 14.28 0 01-4.222-10.192 14.28 14.28 0 014.222-10.192zm15.556 0a14.28 14.28 0 014.222 10.192 14.28 14.28 0 01-4.222 10.192l-1.435-1.435c2.31-2.31 3.657-5.39 3.657-8.757 0-3.367-1.347-6.447-3.657-8.757l1.435-1.435zm-2.828 2.828a10.28 10.28 0 013.05 7.364 10.28 10.28 0 01-3.05 7.364l-1.414-1.414a8.28 8.28 0 002.464-5.95 8.28 8.28 0 00-2.464-5.95l1.414-1.414zm-12.728 0l1.414 1.414a8.28 8.28 0 00-2.464 5.95 8.28 8.28 0 002.464 5.95l-1.414 1.414a10.28 10.28 0 01-3.05-7.364 10.28 10.28 0 013.05-7.364zm9.9 2.828a6.28 6.28 0 011.85 4.536 6.28 6.28 0 01-1.85 4.536l-1.414-1.414a4.28 4.28 0 001.264-3.122 4.28 4.28 0 00-1.264-3.122l1.414-1.414zm-7.072 0l1.414 1.414a4.28 4.28 0 00-1.264 3.122 4.28 4.28 0 001.264 3.122l-1.414 1.414a6.28 6.28 0 01-1.85-4.536 6.28 6.28 0 011.85-4.536zm4.242 2.828a2.28 2.28 0 01.636 1.708 2.28 2.28 0 01-.636 1.708l-1.414-1.414a.28.28 0 00.05-.294.28.28 0 00-.05-.294l1.414-1.414z"/>
                        </svg>
                    </div>
                    
                    <h3 class="font-weight-bold mb-2">Scan Kehadiran</h3>
                    <p class="subtitle">Dekatkan kartu mahasiswa ke bagian belakang perangkat (NFC) untuk mencatat kehadiran.</p>
                    
                    <button class="scan-btn" id="scanButton">Mulai Scan NFC</button>
                    
                    <div id="statusBox" class="status-box"></div>
                    
                    <div id="studentDetails" class="student-details">
                        <p><strong>Nama:</strong> <span id="detailName">-</span></p>
                        <p><strong>NIM:</strong> <span id="detailNim">-</span></p>
                        <p><strong>Waktu:</strong> <span id="detailTime">-</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // Setup Axios CSRF Token using the meta tag usually present in layout.master, 
    // or just fallback to providing it.
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfTokenMeta) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfTokenMeta.getAttribute('content');
    } else {
        // Fallback for layouts missing csrf token meta
        axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
    }

    const scanButton = document.getElementById('scanButton');
    const statusBox = document.getElementById('statusBox');
    const studentDetails = document.getElementById('studentDetails');
    const appContainer = document.getElementById('app-container');

    let isScanning = false;
    let ndef = null;

    function showStatus(message, type) {
        statusBox.textContent = message;
        statusBox.className = `status-box status-${type} show`;
        
        if (type !== 'success') {
            studentDetails.classList.remove('show');
        }
    }

    function showStudentDetails(data) {
        document.getElementById('detailName').textContent = data.student_name;
        document.getElementById('detailNim').textContent = data.nim;
        document.getElementById('detailTime').textContent = data.scanned_at;
        studentDetails.classList.add('show');
    }

    scanButton.addEventListener('click', async () => {
        if (!('NDEFReader' in window)) {
            showStatus('Browser atau perangkat ini tidak mendukung Web NFC.', 'error');
            return;
        }

        if (isScanning) return;

        try {
            if (!ndef) {
                ndef = new NDEFReader();
            }
            
            await ndef.scan();
            isScanning = true;
            
            scanButton.textContent = 'Memindai...';
            scanButton.disabled = true;
            appContainer.classList.add('scanning');
            showStatus('Mendengarkan kartu NFC...', 'info');

            ndef.addEventListener("readingerror", () => {
                showStatus('Gagal membaca kartu NFC. Coba dekatkan lagi.', 'error');
            });

            ndef.addEventListener("reading", async ({ message, serialNumber }) => {
                showStatus('Kartu terdeteksi! Memproses...', 'info');
                
                const formattedSerial = serialNumber.replace(/:/g, '').toUpperCase();
                
                try {
                    const response = await axios.post('/nfc-scan/store', {
                        serial_number: formattedSerial
                    });

                    if (response.data.success) {
                        showStatus(response.data.message, 'success');
                        showStudentDetails(response.data.data);
                    }
                } catch (error) {
                    if (error.response && error.response.data) {
                        showStatus(error.response.data.message, 'error');
                    } else {
                        showStatus('Terjadi kesalahan koneksi ke server.', 'error');
                    }
                }
            });

        } catch (error) {
            isScanning = false;
            scanButton.textContent = 'Mulai Scan NFC';
            scanButton.disabled = false;
            appContainer.classList.remove('scanning');
            
            if (error.name === 'NotAllowedError') {
                showStatus('Izin NFC ditolak oleh pengguna.', 'error');
            } else if (error.name === 'NotSupportedError') {
                showStatus('NFC tidak didukung di perangkat ini.', 'error');
            } else {
                showStatus('Gagal memulai scan NFC: ' + error.message, 'error');
            }
        }
    });
</script>
@endsection
