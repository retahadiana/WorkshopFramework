<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::orderByDesc('timestamp')->get();

        return view('barang.index', compact('barang'));
    }

    public function create()
    {
        $nextIdBarang = $this->generateNextIdBarang();

        return view('barang.create', compact('nextIdBarang'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'timestamp' => 'required|date',
        ]);

        $data['id_barang'] = $this->generateNextIdBarang();
        $data['timestamp'] = date('Y-m-d H:i:s', strtotime($data['timestamp']));

        Barang::create($data);

        return redirect('/barang')->with('status', 'Barang berhasil ditambahkan.');
    }

    public function edit($id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->firstOrFail();

        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->firstOrFail();

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer|min:0',
            'timestamp' => 'required|date',
        ]);

        $data['timestamp'] = date('Y-m-d H:i:s', strtotime($data['timestamp']));

        $barang->update($data);

        return redirect('/barang')->with('status', 'Barang berhasil diperbarui.');
    }

    public function destroy($id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->firstOrFail();
        $barang->delete();

        return redirect('/barang')->with('status', 'Barang berhasil dihapus.');
    }

public function cetak(Request $request)
    {
        $request->validate([
            'id_barang' => 'nullable|array',
            'id_barang.*' => 'string',
            'koordinat_x' => 'required|integer|min:1|max:5',
            'koordinat_y' => 'required|integer|min:1|max:8',
            'tampilkan_grid' => 'nullable|boolean',
        ]);

        if (empty($request->id_barang) || count($request->id_barang) === 0) {
            return back()->with('error', 'Pilih minimal satu barang untuk dicetak.')->withInput();
        }

        $barang = Barang::whereIn('id_barang', $request->id_barang)->get();

        if ($barang->isEmpty()) {
            return back()->with('error', 'Data barang tidak ditemukan.')->withInput();
        }

        $jumlahKolom = 5;
        $jumlahBaris = 8;
        $marginSampingTarget = 3.5;

        $lebarLabel = 38;
        $tinggiLabel = 18;
        $jarakHorizontal = ((210 - (2 * $marginSampingTarget)) - ($jumlahKolom * $lebarLabel)) / ($jumlahKolom - 1);
        $jarakVertikal = 1.2;

        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 9);

        $marginKiri = $marginSampingTarget;
        $marginAtas = 2.8;
        $paddingX = 1.2;
        $paddingTop = 1.4;
        $lebarAreaCetak = $lebarLabel - ($paddingX * 2);
        $tampilkanGrid = $request->boolean('tampilkan_grid');

        if ($tampilkanGrid) {
            $this->gambarGridPanduan($pdf, $marginKiri, $marginAtas, $lebarLabel, $tinggiLabel, $jarakHorizontal, $jarakVertikal, $jumlahKolom, $jumlahBaris);
        }

        // Langsung set nilai awal col dan row (dikurangi 1 karena index array mulai dari 0)
        $col = (int) $request->koordinat_x - 1;
        $row = (int) $request->koordinat_y - 1;

        foreach ($barang as $item) {
            // Jika baris sudah lebih dari batas (kertas habis), pindah halaman baru
            if ($row >= $jumlahBaris) {
                $pdf->AddPage();
                $row = 0; // Reset ke baris paling atas
                $col = 0; // Reset ke kolom paling kiri

                if ($tampilkanGrid) {
                    $this->gambarGridPanduan($pdf, $marginKiri, $marginAtas, $lebarLabel, $tinggiLabel, $jarakHorizontal, $jarakVertikal, $jumlahKolom, $jumlahBaris);
                }
            }

            // Hitung titik koordinat (Selalu bergerak dari kiri ke kanan berurutan)
            $x = $marginKiri + ($col * ($lebarLabel + $jarakHorizontal));
            $y = $marginAtas + ($row * ($tinggiLabel + $jarakVertikal));
            $xKonten = $x + $paddingX;
            $yKonten = $y + $paddingTop;

            // Cetak Barcode 1D di atas id_barang
            $generator = new BarcodeGeneratorPNG();
            $barcodeData = $generator->getBarcode((string) $item->id_barang, $generator::TYPE_CODE_128, 2, 30);
            $barcodeFile = tempnam(sys_get_temp_dir(), 'barcode_') . '.png';
            file_put_contents($barcodeFile, $barcodeData);
            $pdf->Image($barcodeFile, $xKonten, $yKonten, $lebarAreaCetak, 7);
            @unlink($barcodeFile);

            // Cetak ID Barang
            $pdf->SetXY($xKonten, $yKonten + 7.4);
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell($lebarAreaCetak, 4, (string) $item->id_barang, 0, 0, 'L');

            // Cetak Nama Barang
            $pdf->SetXY($xKonten, $yKonten + 10.2);
            $pdf->SetFont('Arial', 'B', 6);
            $pdf->Cell($lebarAreaCetak, 4, substr($item->nama, 0, 18), 0, 0, 'L');

            // Cetak Harga Barang
            $pdf->SetXY($xKonten, $yKonten + 13.2);
            $pdf->SetFont('Arial', '', 5);
            $pdf->Cell($lebarAreaCetak, 4, 'Rp ' . number_format($item->harga, 0, ',', '.'), 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 7);

            // Geser posisi ke kolom berikutnya
            $col++;
            
            // Jika kolom sudah mencapai 5 (mentok kanan), turun ke baris baru dan kembali ke kolom pertama
            if ($col >= $jumlahKolom) {
                $col = 0;
                $row++;
            }
        }

        $pdf->Output('I', 'Label_Harga_TnJ_108.pdf');
        exit;
    }

    private function gambarGridPanduan($pdf, $marginKiri, $marginAtas, $lebarLabel, $tinggiLabel, $jarakHorizontal = 0, $jarakVertikal = 0, $jumlahKolom = 5, $jumlahBaris = 8): void
    {
        $pdf->SetDrawColor(220, 220, 220);

        for ($r = 0; $r < $jumlahBaris; $r++) {
            for ($c = 0; $c < $jumlahKolom; $c++) {
                $x = $marginKiri + ($c * ($lebarLabel + $jarakHorizontal));
                $y = $marginAtas + ($r * ($tinggiLabel + $jarakVertikal));
                $pdf->Rect($x, $y, $lebarLabel, $tinggiLabel);
            }
        }

        $pdf->SetDrawColor(0, 0, 0);
    }

    private function generateNextIdBarang(): string
    {
        $prefix = date('ymd');
        $maxUrutan = (int) Barang::query()
            ->selectRaw('MAX(CAST(RIGHT(id_barang, 2) AS UNSIGNED)) as max_urutan')
            ->value('max_urutan');

        $nextUrutan = $maxUrutan + 1;
        $candidateId = $prefix . str_pad((string) $nextUrutan, 2, '0', STR_PAD_LEFT);

        while (Barang::where('id_barang', $candidateId)->exists()) {
            $nextUrutan++;
            $candidateId = $prefix . str_pad((string) $nextUrutan, 2, '0', STR_PAD_LEFT);
        }

        return $candidateId;
    }

    public function scan()
    {
        return view('barang.scan');
    }

    public function show($id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->first();

        if (!$barang) {
            return response()->json(['error' => 'Barang tidak ditemukan'], 404);
        }

        return response()->json([
            'id_barang' => $barang->id_barang,
            'nama' => $barang->nama,
            'harga' => $barang->harga,
        ]);
    }
}
