@extends('layout.master')

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $title ?? 'Tambah Customer' }}</h4>
                <p class="card-description">{{ $description ?? 'Lengkapi data customer dan ambil foto menggunakan kamera.' }}</p>

                <form action="{{ $submitRoute }}" method="post" class="forms-sample" autocomplete="off">
                    @csrf

                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" class="form-control" placeholder="Nama customer" required>
                            </div>
                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea id="alamat" name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap" required>{{ old('alamat') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="provinsi">Provinsi</label>
                                <select id="provinsi" name="provinsi" class="form-control" required>
                                    <option value="">Pilih Provinsi</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="kota">Kota</label>
                                <select id="kota" name="kota" class="form-control" required disabled>
                                    <option value="">Pilih Kota</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="kecamatan">Kecamatan</label>
                                <select id="kecamatan" name="kecamatan" class="form-control" required disabled>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="kelurahan">Kelurahan / Desa</label>
                                <select id="kelurahan" name="kelurahan" class="form-control" required disabled>
                                    <option value="">Pilih Kelurahan / Desa</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="kodepos">Kodepos</label>
                                <input type="text" id="kodepos" name="kodepos" value="{{ old('kodepos') }}" class="form-control" placeholder="Masukkan kodepos" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Preview Foto</label>
                                <div class="border rounded p-3 text-center" style="min-height: 320px;">
                                    <img id="previewFoto" src="https://via.placeholder.com/260x260?text=Preview+Foto" alt="Preview Foto" class="img-fluid" style="max-height: 260px; object-fit: contain;" />
                                </div>
                            </div>
                            <div class="mt-3 text-center">
                                <button type="button" id="openCameraButton" class="btn btn-primary">Ambil Foto</button>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="fotoData" name="foto_data" value="">
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success me-2">Simpan Customer</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kamera -->
<div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cameraModalLabel">Ambil Foto Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-7 mb-3">
                        <div class="mb-3">
                            <label for="cameraSelect" class="form-label">Pilihan Kamera</label>
                            <select id="cameraSelect" class="form-select"></select>
                        </div>
                        <div class="border rounded overflow-hidden" style="background:#000; min-height:360px; display:flex; align-items:center; justify-content:center;">
                            <video id="cameraVideo" playsinline autoplay muted style="width:100%; height:100%; object-fit:cover;"></video>
                        </div>
                        <div class="mt-3 d-flex justify-content-between">
                            <button type="button" id="snapshotButton" class="btn btn-primary">Snapshot</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="mb-3">
                            <label class="form-label">Preview Snapshot</label>
                            <div class="border rounded p-2 text-center" style="min-height:360px; background:#f8f9fa;">
                                <img id="snapshotPreview" src="https://via.placeholder.com/320x240?text=Preview+Snapshot" alt="Snapshot Preview" class="img-fluid" style="max-height:340px; object-fit:contain;" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="savePhotoButton" class="btn btn-success" data-bs-dismiss="modal">Simpan Foto</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
    const openCameraButton = document.getElementById('openCameraButton');
    const cameraModalElement = document.getElementById('cameraModal');
    const cameraVideo = document.getElementById('cameraVideo');
    const cameraSelect = document.getElementById('cameraSelect');
    const snapshotButton = document.getElementById('snapshotButton');
    const snapshotPreview = document.getElementById('snapshotPreview');
    const previewFoto = document.getElementById('previewFoto');
    const fotoDataInput = document.getElementById('fotoData');
    const savePhotoButton = document.getElementById('savePhotoButton');

    let currentStream = null;
    let cameraModal = null;

    async function getCameraDevices() {
        const devices = await navigator.mediaDevices.enumerateDevices();
        return devices.filter(device => device.kind === 'videoinput');
    }

    function populateCameraSelect(cameras) {
        cameraSelect.innerHTML = '';
        if (cameras.length === 0) {
            const option = document.createElement('option');
            option.text = 'Tidak ada kamera terdeteksi';
            option.value = '';
            cameraSelect.appendChild(option);
            cameraSelect.disabled = true;
            return;
        }
        cameraSelect.disabled = false;
        cameras.forEach((camera, index) => {
            const option = document.createElement('option');
            option.value = camera.deviceId;
            option.text = camera.label || `Kamera ${index + 1}`;
            cameraSelect.appendChild(option);
        });
    }

    async function startCamera(deviceId = null) {
        stopCamera();

        const constraints = {
            video: {
                width: { ideal: 1280 },
                height: { ideal: 720 },
                deviceId: deviceId ? { exact: deviceId } : undefined,
            },
            audio: false,
        };

        try {
            currentStream = await navigator.mediaDevices.getUserMedia(constraints);
            cameraVideo.srcObject = currentStream;
            await cameraVideo.play();
        } catch (error) {
            console.error('Gagal mengakses kamera:', error);
        }
    }

    function stopCamera() {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
        cameraVideo.srcObject = null;
    }

    async function initCameraModal() {
        try {
            const cameras = await getCameraDevices();
            populateCameraSelect(cameras);
            if (cameras.length > 0) {
                await startCamera(cameras[0].deviceId);
            }
        } catch (error) {
            console.error('Error inisialisasi kamera:', error);
        }
    }

    openCameraButton.addEventListener('click', async () => {
        if (!cameraModal) {
            cameraModal = new bootstrap.Modal(cameraModalElement, { backdrop: 'static', keyboard: false });
            cameraModalElement.addEventListener('hidden.bs.modal', () => {
                stopCamera();
            });
        }

        await initCameraModal();
        cameraModal.show();
    });

    cameraSelect.addEventListener('change', async () => {
        if (cameraSelect.value) {
            await startCamera(cameraSelect.value);
        }
    });

    snapshotButton.addEventListener('click', () => {
        if (!currentStream) {
            return;
        }

        const canvas = document.createElement('canvas');
        canvas.width = cameraVideo.videoWidth;
        canvas.height = cameraVideo.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(cameraVideo, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/png');
        snapshotPreview.src = dataUrl;
        fotoDataInput.value = dataUrl;
    });

    savePhotoButton.addEventListener('click', () => {
        if (fotoDataInput.value) {
            previewFoto.src = fotoDataInput.value;
        }
    });

    const provinsiSelect = document.getElementById('provinsi');
    const kotaSelect = document.getElementById('kota');
    const kecamatanSelect = document.getElementById('kecamatan');
    const kelurahanSelect = document.getElementById('kelurahan');
    const kodeposSelect = document.getElementById('kodepos');

    const oldWilayah = {
        provinsi: @json(old('provinsi')),
        kota: @json(old('kota')),
        kecamatan: @json(old('kecamatan')),
        kelurahan: @json(old('kelurahan')),
        kodepos: @json(old('kodepos')),
    };

    const apiBase = 'https://www.emsifa.com/api-wilayah-indonesia/api';

    function populateSelect(selectElement, items, selectedValue = '', valueKey = 'name', labelKey = 'name') {
        selectElement.innerHTML = `<option value="">Pilih ${selectElement.previousElementSibling.textContent.trim()}</option>`;
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueKey] ?? '';
            option.textContent = item[labelKey] ?? '';
            if (item.id) {
                option.dataset.id = item.id;
            }
            if (item[valueKey] === selectedValue) {
                option.selected = true;
            }
            selectElement.appendChild(option);
        });
        selectElement.disabled = items.length === 0;
    }

    async function fetchWilayah(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Gagal memuat data wilayah');
            }
            return await response.json();
        } catch (error) {
            console.error(error);
            return [];
        }
    }

    async function loadProvinces() {
        const provinces = await fetchWilayah(`${apiBase}/provinces.json`);
        populateSelect(provinsiSelect, provinces, oldWilayah.provinsi);
        if (oldWilayah.provinsi) {
            const selectedProvince = provinces.find(item => item.name === oldWilayah.provinsi);
            if (selectedProvince) {
                await loadCities(selectedProvince.id);
            }
        }
    }

    async function loadCities(provinceId) {
        if (!provinceId) {
            populateSelect(kotaSelect, [], '');
            populateSelect(kecamatanSelect, [], '');
            populateSelect(kelurahanSelect, [], '');
            return;
        }

        const cities = await fetchWilayah(`${apiBase}/regencies/${provinceId}.json`);
        populateSelect(kotaSelect, cities, oldWilayah.kota);
        populateSelect(kecamatanSelect, [], '');
        populateSelect(kelurahanSelect, [], '');

        if (oldWilayah.kota) {
            const selectedCity = cities.find(item => item.name === oldWilayah.kota);
            if (selectedCity) {
                await loadDistricts(selectedCity.id);
            }
        }
    }

    async function loadDistricts(cityId) {
        if (!cityId) {
            populateSelect(kecamatanSelect, [], '');
            populateSelect(kelurahanSelect, [], '');
            return;
        }

        const districts = await fetchWilayah(`${apiBase}/districts/${cityId}.json`);
        populateSelect(kecamatanSelect, districts, oldWilayah.kecamatan);
        populateSelect(kelurahanSelect, [], '');

        if (oldWilayah.kecamatan) {
            const selectedDistrict = districts.find(item => item.name === oldWilayah.kecamatan);
            if (selectedDistrict) {
                await loadVillages(selectedDistrict.id);
            }
        }
    }

    async function loadVillages(districtId) {
        if (!districtId) {
            populateSelect(kelurahanSelect, [], '');
            return;
        }

        const villages = await fetchWilayah(`${apiBase}/villages/${districtId}.json`);
        populateSelect(kelurahanSelect, villages, oldWilayah.kelurahan);
    }

    provinsiSelect.addEventListener('change', async () => {
        const provinceId = provinsiSelect.options[provinsiSelect.selectedIndex]?.dataset.id;
        oldWilayah.provinsi = provinsiSelect.value;
        oldWilayah.kota = '';
        oldWilayah.kecamatan = '';
        oldWilayah.kelurahan = '';
        oldWilayah.kodepos = '';
        await loadCities(provinceId);
    });

    kotaSelect.addEventListener('change', async () => {
        const cityId = kotaSelect.options[kotaSelect.selectedIndex]?.dataset.id;
        oldWilayah.kota = kotaSelect.value;
        oldWilayah.kecamatan = '';
        oldWilayah.kodepos = '';
        await loadDistricts(cityId);
    });

    kecamatanSelect.addEventListener('change', async () => {
        const districtId = kecamatanSelect.options[kecamatanSelect.selectedIndex]?.dataset.id;
        oldWilayah.kecamatan = kecamatanSelect.value;
        oldWilayah.kelurahan = '';
        oldWilayah.kodepos = '';
        await loadVillages(districtId);
    });

    kelurahanSelect.addEventListener('change', () => {
        oldWilayah.kelurahan = kelurahanSelect.value;
    });

    kodeposSelect.addEventListener('input', () => {
        oldWilayah.kodepos = kodeposSelect.value;
    });

    document.addEventListener('DOMContentLoaded', async () => {
        await loadProvinces();
    });
</script>
@endpush
