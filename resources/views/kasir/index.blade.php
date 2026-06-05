@extends('layout.master')

@section('title', 'Halaman Kasir (POS)')

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-cash-register"></i>
            </span>
            Halaman Kasir (Point of Sales)
        </h3>
    </div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Form Transaksi</h5>
                </div>
                <div class="card-body">
                    <form id="formKasir" onsubmit="return false;">
                        <div class="row g-3 align-items-start">
                            <div class="col-md-3">
                                <label for="kode_barang" class="form-label">Kode Barang</label>
                                <input type="text" id="kode_barang" class="form-control" list="daftarKodeBarang" placeholder="Ketik kode barang" autocomplete="off">
                                <datalist id="daftarKodeBarang"></datalist>
                            </div>
                            <div class="col-md-3">
                                <label for="nama_barang" class="form-label">Nama Barang</label>
                                <input type="text" id="nama_barang" class="form-control" readonly>
                            </div>
                            <div class="col-md-2">
                                <label for="harga_barang" class="form-label">Harga Barang</label>
                                <input type="text" id="harga_barang" class="form-control" readonly>
                            </div>
                            <div class="col-md-2">
                                <label for="jumlah_barang" class="form-label">Jumlah</label>
                                <input type="number" id="jumlah_barang" class="form-control" min="1" value="1">
                            </div>
                            <div class="col-md-2 d-grid">
                                <button type="button" class="btn btn-primary" id="btnTambah" disabled>Tambahkan</button>
                            </div>
                            <div class="col-12">
                                <small class="text-muted">Ketik beberapa digit kode, lalu pilih saran atau tekan Enter.</small>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-4">
                        <table class="table table-bordered align-middle" id="tabelKeranjang">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th class="text-end">Harga</th>
                                    <th style="width: 150px;">Jumlah</th>
                                    <th class="text-end">Subtotal</th>
                                    <th style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <h4 class="mb-0">Total: <span id="grandTotal">Rp 0</span></h4>
                        <button type="button" class="btn btn-success" id="btnBayar">Bayar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function () {
            if (window.__kasirPosInitialized) {
                return;
            }
            window.__kasirPosInitialized = true;

            const $kode = $('#kode_barang');
            const $nama = $('#nama_barang');
            const $harga = $('#harga_barang');
            const $jumlah = $('#jumlah_barang');
            const $btnTambah = $('#btnTambah');
            const $btnBayar = $('#btnBayar');
            const $tbody = $('#tabelKeranjang tbody');
            const $grandTotal = $('#grandTotal');
            const $kodeList = $('#daftarKodeBarang');
            const storeUrl = '{{ url('/penjualan/store') }}';
            const searchKodeUrl = '{{ route('kasir.cari-kode') }}';
            let isSubmitting = false;
            let debounceTimer = null;

            function formatRupiah(number) {
                return 'Rp ' + Number(number || 0).toLocaleString('id-ID');
            }

            function toInt(value) {
                return parseInt(value, 10) || 0;
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function isiFormBarang(barang) {
                $kode.val(barang.id_barang);
                $nama.val(barang.nama);
                $harga.val(barang.harga);
                $jumlah.val(1);
                $btnTambah.prop('disabled', false);
                $jumlah.focus();
                $jumlah.select();
            }

            function loadBarangByKode(kode, showNotFoundAlert = true) {
                if (!kode) {
                    return;
                }

                $btnTambah.prop('disabled', true);

                $.ajax({
                    url: `{{ url('/api/barang') }}/${encodeURIComponent(kode)}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        const barang = response.data || response;
                        isiFormBarang(barang);
                    },
                    error: function () {
                        if (showNotFoundAlert) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Barang tidak ditemukan',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                        resetFormInput();
                    }
                });
            }

            function cariSaranKode(query) {
                $.ajax({
                    url: searchKodeUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: { q: query },
                    success: function (response) {
                        const options = (response.data || []).map((item) => {
                            const label = `${item.id_barang} - ${item.nama} (Rp ${Number(item.harga || 0).toLocaleString('id-ID')})`;
                            return `<option value="${escapeHtml(item.id_barang)}" label="${escapeHtml(label)}"></option>`;
                        });

                        $kodeList.html(options.join(''));
                    }
                });
            }

            function resetFormInput() {
                $kode.val('');
                $nama.val('');
                $harga.val('');
                $jumlah.val('');
                $btnTambah.prop('disabled', true);
                $kode.focus();
            }

            function hitungTotal() {
                let total = 0;

                $tbody.find('tr').each(function () {
                    total += toInt($(this).find('.line-subtotal').data('value'));
                });

                $grandTotal.text(formatRupiah(total)).attr('data-value', total);
                return total;
            }

            function updateSubtotalBaris($row) {
                const harga = toInt($row.find('.line-harga').data('value'));
                const jumlah = Math.max(1, toInt($row.find('.input-jumlah').val()));
                const subtotal = harga * jumlah;

                $row.find('.input-jumlah').val(jumlah);
                $row.find('.line-subtotal')
                    .text(formatRupiah(subtotal))
                    .attr('data-value', subtotal)
                    .data('value', subtotal);
            }

            $kode.on('keydown', function (event) {
                if (event.key !== 'Enter') {
                    return;
                }

                event.preventDefault();

                const kode = $kode.val().trim();
                if (!kode) {
                    return;
                }

                loadBarangByKode(kode, true);
            });

            $kode.on('input', function () {
                const query = $kode.val().trim();

                clearTimeout(debounceTimer);
                if (query.length < 2) {
                    $kodeList.html('');
                    return;
                }

                debounceTimer = setTimeout(function () {
                    cariSaranKode(query);
                }, 250);
            });

            $kode.on('change', function () {
                const kode = $kode.val().trim();

                if (!kode) {
                    return;
                }

                loadBarangByKode(kode, false);
            });

            $btnTambah.on('click', function () {
                const kode = $kode.val().trim();
                const nama = $nama.val().trim();
                const harga = toInt($harga.val());
                const jumlahInput = Math.max(1, toInt($jumlah.val()));

                if (!kode || !nama || harga <= 0) {
                    return;
                }

                const $existingRow = $tbody.find(`tr[data-id="${kode}"]`);

                if ($existingRow.length) {
                    const currentJumlah = Math.max(1, toInt($existingRow.find('.input-jumlah').val()));
                    $existingRow.find('.input-jumlah').val(currentJumlah + jumlahInput);
                    updateSubtotalBaris($existingRow);
                } else {
                    const subtotal = harga * jumlahInput;

                    const rowHtml = `
                        <tr data-id="${kode}">
                            <td class="line-kode">${kode}</td>
                            <td class="line-nama">${nama}</td>
                            <td class="text-end line-harga" data-value="${harga}">${formatRupiah(harga)}</td>
                            <td>
                                <input type="number" class="form-control input-jumlah" min="1" value="${jumlahInput}">
                            </td>
                            <td class="text-end line-subtotal" data-value="${subtotal}">${formatRupiah(subtotal)}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm btn-hapus">Hapus</button>
                            </td>
                        </tr>
                    `;

                    $tbody.append(rowHtml);
                }

                hitungTotal();
                resetFormInput();
            });

            $tbody.on('input change', '.input-jumlah', function () {
                const $row = $(this).closest('tr');
                updateSubtotalBaris($row);
                hitungTotal();
            });

            $tbody.on('click', '.btn-hapus', function () {
                $(this).closest('tr').remove();
                hitungTotal();
            });

            $btnBayar.on('click', function () {
                if (isSubmitting) {
                    return;
                }

                const keranjang = [];

                $tbody.find('tr').each(function () {
                    const $row = $(this);
                    keranjang.push({
                        id_barang: $row.data('id'),
                        harga: toInt($row.find('.line-harga').data('value')),
                        jumlah: Math.max(1, toInt($row.find('.input-jumlah').val())),
                        subtotal: toInt($row.find('.line-subtotal').data('value')),
                    });
                });

                if (!keranjang.length) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Keranjang masih kosong',
                        text: 'Tambahkan minimal satu barang sebelum bayar.'
                    });
                    return;
                }

                const grandTotal = hitungTotal();
                const originalButtonHtml = $btnBayar.html();
                isSubmitting = true;

                $btnBayar.prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Memproses...
                `);

                $.ajax({
                    url: storeUrl,
                    type: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        keranjang: keranjang,
                        grand_total: grandTotal
                    },
                    success: function (response) {
                        isSubmitting = false;
                        $btnBayar.prop('disabled', false).html(originalButtonHtml);

                        const invoiceText = response.no_invoice
                            ? `No Invoice: ${response.no_invoice}`
                            : `ID Penjualan: ${response.penjualan_id}`;

                        $tbody.empty();
                        hitungTotal();
                        resetFormInput();

                        Swal.fire({
                            icon: 'success',
                            title: 'Transaksi Berhasil Disimpan',
                            text: invoiceText,
                            showCancelButton: true,
                            confirmButtonText: 'Lihat Bukti Transaksi',
                            cancelButtonText: 'Tutup'
                        }).then((result) => {
                            if (result.isConfirmed && response.success_url) {
                                window.open(response.success_url, '_blank');
                            }
                        });
                    },
                    error: function (xhr) {
                        isSubmitting = false;
                        $btnBayar.prop('disabled', false).html(originalButtonHtml);

                        const message = xhr.responseJSON?.message || 'Gagal menyimpan transaksi.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Transaksi Gagal',
                            text: message
                        });
                    }
                });
            });

            hitungTotal();

            // Catatan versi Axios:
            // Untuk mengganti jQuery AJAX ke Axios, ubah setiap $.ajax menjadi axios.get/axios.post
            // lalu kirim CSRF melalui header: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }.
        })();
    </script>
@endpush
