<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

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

        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 9);

        $marginKiri = 12;
        $marginAtas = 15;
        $lebarLabel = 38;
        $tinggiLabel = 14;
        $tampilkanGrid = $request->boolean('tampilkan_grid');

        if ($tampilkanGrid) {
            $this->gambarGridPanduan($pdf, $marginKiri, $marginAtas, $lebarLabel, $tinggiLabel);
        }

        // Langsung set nilai awal col dan row (dikurangi 1 karena index array mulai dari 0)
        $col = (int) $request->koordinat_x - 1;
        $row = (int) $request->koordinat_y - 1;

        foreach ($barang as $item) {
            // Jika baris sudah lebih dari batas (kertas habis), pindah halaman baru
            if ($row >= 8) {
                $pdf->AddPage();
                $row = 0; // Reset ke baris paling atas
                $col = 0; // Reset ke kolom paling kiri

                if ($tampilkanGrid) {
                    $this->gambarGridPanduan($pdf, $marginKiri, $marginAtas, $lebarLabel, $tinggiLabel);
                }
            }

            // Hitung titik koordinat (Selalu bergerak dari kiri ke kanan berurutan)
            $x = $marginKiri + ($col * $lebarLabel);
            $y = $marginAtas + ($row * $tinggiLabel);

            // Cetak ID Barang
            $pdf->SetXY($x, $y);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell($lebarLabel, 4, (string) $item->id_barang, 0, 0, 'L');

            // Cetak Nama Barang
            $pdf->SetXY($x, $y + 4);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($lebarLabel, 4, substr($item->nama, 0, 18), 0, 0, 'L');

            // Cetak Harga Barang
            $pdf->SetXY($x, $y + 8);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell($lebarLabel, 4, 'Rp ' . number_format($item->harga, 0, ',', '.'), 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 9);

            // Geser posisi ke kolom berikutnya
            $col++;
            
            // Jika kolom sudah mencapai 5 (mentok kanan), turun ke baris baru dan kembali ke kolom pertama
            if ($col >= 5) {
                $col = 0;
                $row++;
            }
        }

        $pdf->Output('I', 'Label_Harga_TnJ_108.pdf');
        exit;
    }

    private function gambarGridPanduan($pdf, $marginKiri, $marginAtas, $lebarLabel, $tinggiLabel): void
    {
        $pdf->SetDrawColor(220, 220, 220);

        for ($r = 0; $r < 8; $r++) {
            for ($c = 0; $c < 5; $c++) {
                $x = $marginKiri + ($c * $lebarLabel);
                $y = $marginAtas + ($r * $tinggiLabel);
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
}
