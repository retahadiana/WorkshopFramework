<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

class KasirController extends Controller
{
    public function index()
    {
        return view('kasir.index');
    }

    public function laporan(Request $request)
    {
        $schema = $this->resolveSalesSchema();
        $dateColumn = $schema['penjualan_date_col'];
        $pkColumn = $schema['penjualan_pk'];
        $invoiceColumn = $schema['penjualan_invoice_col'];

        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $todayTotal = 0;
        $todayCount = 0;
        $weekTotal = 0;
        $weekCount = 0;

        if ($dateColumn) {
            $todayTotal = (int) DB::table('penjualan')
                ->whereDate($dateColumn, $today)
                ->sum('total');

            $todayCount = (int) DB::table('penjualan')
                ->whereDate($dateColumn, $today)
                ->count();

            $weekTotal = (int) DB::table('penjualan')
                ->whereBetween($dateColumn, [$weekStart, $weekEnd])
                ->sum('total');

            $weekCount = (int) DB::table('penjualan')
                ->whereBetween($dateColumn, [$weekStart, $weekEnd])
                ->count();
        }

        $latestTransactionsQuery = DB::table('penjualan')->select([
            "{$pkColumn} as id_penjualan",
            'total',
        ]);

        if ($invoiceColumn) {
            $latestTransactionsQuery->addSelect("{$invoiceColumn} as no_invoice");
        } else {
            $latestTransactionsQuery->addSelect(DB::raw("NULL as no_invoice"));
        }

        if ($dateColumn) {
            $latestTransactionsQuery->addSelect("{$dateColumn} as tanggal")->orderByDesc($dateColumn);
        } else {
            $latestTransactionsQuery->addSelect(DB::raw('NULL as tanggal'))->orderByDesc($pkColumn);
        }

        $latestTransactions = $latestTransactionsQuery->limit(20)->get();

        return view('kasir.laporan', compact(
            'todayTotal',
            'todayCount',
            'weekTotal',
            'weekCount',
            'latestTransactions'
        ));
    }

    public function cariBarang($id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'status' => false,
                'message' => 'Barang tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id_barang' => $barang->id_barang,
                'nama' => $barang->nama,
                'harga' => (int) $barang->harga,
            ],
        ]);
    }

    public function cariKode(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '') {
            return response()->json([
                'status' => true,
                'data' => [],
            ]);
        }

        $items = Barang::query()
            ->where('id_barang', 'like', '%' . $q . '%')
            ->orderBy('id_barang')
            ->limit(12)
            ->get(['id_barang', 'nama', 'harga']);

        return response()->json([
            'status' => true,
            'data' => $items,
        ]);
    }

    public function storeTransaksi(Request $request)
    {
        $validated = $request->validate([
            'keranjang' => ['required', 'array', 'min:1'],
            'keranjang.*.id_barang' => ['required', 'string', 'exists:barang,id_barang'],
            'keranjang.*.harga' => ['required', 'integer', 'min:0'],
            'keranjang.*.jumlah' => ['required', 'integer', 'min:1'],
            'keranjang.*.subtotal' => ['required', 'integer', 'min:0'],
            'grand_total' => ['required', 'integer', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            $schema = $this->resolveSalesSchema();

            $penjualanPayload = [
                'total' => $validated['grand_total'],
            ];

            $noInvoice = null;

            if ($schema['penjualan_invoice_col']) {
                $noInvoice = $this->generateNoInvoice($schema);
                $penjualanPayload[$schema['penjualan_invoice_col']] = $noInvoice;
            }

            if ($schema['penjualan_has_tanggal']) {
                $penjualanPayload['tanggal'] = now();
            }

            if ($schema['penjualan_has_timestamp']) {
                $penjualanPayload['timestamp'] = now();
            }

            if ($schema['penjualan_has_created_at']) {
                $penjualanPayload['created_at'] = now();
            }

            if ($schema['penjualan_has_updated_at']) {
                $penjualanPayload['updated_at'] = now();
            }

            $penjualanId = DB::table('penjualan')->insertGetId($penjualanPayload, $schema['penjualan_pk']);

            $detailRows = [];
            foreach ($validated['keranjang'] as $item) {
                $detailPayload = [
                    'id_barang' => $item['id_barang'],
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $item['subtotal'],
                ];

                if ($schema['detail_has_id_penjualan_legacy']) {
                    $detailPayload['id_penjualan'] = $penjualanId;
                }

                if ($schema['detail_has_penjualan_id']) {
                    $detailPayload['penjualan_id'] = $penjualanId;
                }

                if ($schema['detail_has_harga']) {
                    $detailPayload['harga'] = $item['harga'];
                }

                if ($schema['detail_has_created_at']) {
                    $detailPayload['created_at'] = now();
                }

                if ($schema['detail_has_updated_at']) {
                    $detailPayload['updated_at'] = now();
                }

                $detailRows[] = $detailPayload;
            }

            if (!empty($detailRows)) {
                DB::table('penjualan_detail')->insert($detailRows);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Transaksi berhasil disimpan',
                'penjualan_id' => $penjualanId,
                'no_invoice' => $noInvoice,
                'print_url' => route('kasir.struk', ['id' => $penjualanId]),
                'success_url' => route('kasir.success', ['id' => $penjualanId]),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => config('app.debug')
                    ? ('Gagal menyimpan transaksi: ' . $th->getMessage())
                    : 'Gagal menyimpan transaksi',
            ], 500);
        }
    }

    public function struk($id)
    {
        $schema = $this->resolveSalesSchema();
        $pkColumn = $schema['penjualan_pk'];
        $dateColumn = $schema['penjualan_date_col'];
        $invoiceColumn = $schema['penjualan_invoice_col'];
        $detailFkColumn = $schema['detail_fk_col'];

        $penjualanQuery = DB::table('penjualan')
            ->where($pkColumn, $id)
            ->select([
                "{$pkColumn} as id_penjualan",
                'total',
            ]);

        if ($invoiceColumn) {
            $penjualanQuery->addSelect("{$invoiceColumn} as no_invoice");
        } else {
            $penjualanQuery->addSelect(DB::raw("NULL as no_invoice"));
        }

        if ($dateColumn) {
            $penjualanQuery->addSelect("{$dateColumn} as tanggal");
        } else {
            $penjualanQuery->addSelect(DB::raw('NULL as tanggal'));
        }

        $penjualan = $penjualanQuery->first();

        abort_if(!$penjualan, 404);

        $qrCode = QrCode::create(sprintf('IDPENJUALAN:%s', $penjualan->id_penjualan))
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(220)
            ->setMargin(10);
        $writer = new PngWriter();
        $qrCodeDataUri = $writer->write($qrCode)->getDataUri();

        $detailQuery = DB::table('penjualan_detail as pd')
            ->leftJoin('barang as b', 'pd.id_barang', '=', 'b.id_barang')
            ->where("pd.{$detailFkColumn}", $id)
            ->select([
                'pd.id_barang',
                DB::raw('COALESCE(b.nama, pd.id_barang) as nama_barang'),
                'pd.jumlah',
                'pd.subtotal',
            ]);

        if ($schema['detail_has_harga']) {
            $detailQuery->addSelect('pd.harga');
        } else {
            $detailQuery->addSelect(DB::raw('CASE WHEN pd.jumlah > 0 THEN FLOOR(pd.subtotal / pd.jumlah) ELSE 0 END as harga'));
        }

        $details = $detailQuery->get();

        return view('kasir.struk', compact('penjualan', 'details', 'qrCodeDataUri'));
    }

    public function success($id)
    {
        $schema = $this->resolveSalesSchema();
        $pkColumn = $schema['penjualan_pk'];
        $dateColumn = $schema['penjualan_date_col'];
        $invoiceColumn = $schema['penjualan_invoice_col'];
        $detailFkColumn = $schema['detail_fk_col'];

        $penjualanQuery = DB::table('penjualan')
            ->where($pkColumn, $id)
            ->select([
                "{$pkColumn} as id_penjualan",
                'total',
            ]);

        if ($invoiceColumn) {
            $penjualanQuery->addSelect("{$invoiceColumn} as no_invoice");
        } else {
            $penjualanQuery->addSelect(DB::raw("NULL as no_invoice"));
        }

        if ($dateColumn) {
            $penjualanQuery->addSelect("{$dateColumn} as tanggal");
        } else {
            $penjualanQuery->addSelect(DB::raw('NULL as tanggal'));
        }

        $penjualan = $penjualanQuery->first();

        abort_if(!$penjualan, 404);

        $qrCode = QrCode::create(sprintf('IDPENJUALAN:%s', $penjualan->id_penjualan))
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(300)
            ->setMargin(10);
        $writer = new PngWriter();
        $qrCodeDataUri = $writer->write($qrCode)->getDataUri();

        $detailQuery = DB::table('penjualan_detail as pd')
            ->leftJoin('barang as b', 'pd.id_barang', '=', 'b.id_barang')
            ->where("pd.{$detailFkColumn}", $id)
            ->select([
                'pd.id_barang',
                DB::raw('COALESCE(b.nama, pd.id_barang) as nama_barang'),
                'pd.jumlah',
                'pd.subtotal',
            ]);

        if ($schema['detail_has_harga']) {
            $detailQuery->addSelect('pd.harga');
        } else {
            $detailQuery->addSelect(DB::raw('CASE WHEN pd.jumlah > 0 THEN FLOOR(pd.subtotal / pd.jumlah) ELSE 0 END as harga'));
        }

        $details = $detailQuery->get();

        return view('kasir.success', compact('penjualan', 'details', 'qrCodeDataUri'));
    }

    private function resolveSalesSchema(): array
    {
        $penjualanHasTanggal = Schema::hasColumn('penjualan', 'tanggal');
        $penjualanHasTimestamp = Schema::hasColumn('penjualan', 'timestamp');
        $penjualanHasCreatedAt = Schema::hasColumn('penjualan', 'created_at');
        $penjualanHasUpdatedAt = Schema::hasColumn('penjualan', 'updated_at');
        $penjualanPk = Schema::hasColumn('penjualan', 'id_penjualan') ? 'id_penjualan' : 'id';
        $penjualanInvoiceCol = Schema::hasColumn('penjualan', 'no_invoice') ? 'no_invoice' : null;

        $detailHasIdPenjualanLegacy = Schema::hasColumn('penjualan_detail', 'id_penjualan');
        $detailHasPenjualanId = Schema::hasColumn('penjualan_detail', 'penjualan_id');
        $detailHasHarga = Schema::hasColumn('penjualan_detail', 'harga');
        $detailHasCreatedAt = Schema::hasColumn('penjualan_detail', 'created_at');
        $detailHasUpdatedAt = Schema::hasColumn('penjualan_detail', 'updated_at');
        $detailFkCol = $detailHasIdPenjualanLegacy ? 'id_penjualan' : 'penjualan_id';

        $penjualanDateCol = null;
        if ($penjualanHasTanggal) {
            $penjualanDateCol = 'tanggal';
        } elseif ($penjualanHasTimestamp) {
            $penjualanDateCol = 'timestamp';
        } elseif ($penjualanHasCreatedAt) {
            $penjualanDateCol = 'created_at';
        }

        return [
            'penjualan_has_tanggal' => $penjualanHasTanggal,
            'penjualan_has_timestamp' => $penjualanHasTimestamp,
            'penjualan_has_created_at' => $penjualanHasCreatedAt,
            'penjualan_has_updated_at' => $penjualanHasUpdatedAt,
            'penjualan_pk' => $penjualanPk,
            'penjualan_invoice_col' => $penjualanInvoiceCol,
            'penjualan_date_col' => $penjualanDateCol,
            'detail_has_id_penjualan_legacy' => $detailHasIdPenjualanLegacy,
            'detail_has_penjualan_id' => $detailHasPenjualanId,
            'detail_has_harga' => $detailHasHarga,
            'detail_has_created_at' => $detailHasCreatedAt,
            'detail_has_updated_at' => $detailHasUpdatedAt,
            'detail_fk_col' => $detailFkCol,
        ];
    }

    private function generateNoInvoice(array $schema): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';
        $last = DB::table('penjualan')
            ->where($schema['penjualan_invoice_col'], 'like', $prefix . '%')
            ->orderByDesc($schema['penjualan_pk'])
            ->value($schema['penjualan_invoice_col']);

        $next = 1;
        if ($last) {
            $parts = explode('-', $last);
            $lastNumber = (int) end($parts);
            $next = $lastNumber + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
