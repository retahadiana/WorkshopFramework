<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LokasiToko;

class KunjunganTokoController extends Controller
{
    public function index()
    {
        $toko = LokasiToko::orderBy('nama_toko')->get();
        return view('kunjungan-toko.index', compact('toko'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barcode' => 'required|string',
            'nama_toko' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric',
        ]);

        LokasiToko::updateOrCreate(
            ['barcode' => $data['barcode']],
            $data
        );

        return back()->with('success', 'Data lokasi toko tersimpan');
    }

    public function getByBarcode($barcode)
    {
        $toko = LokasiToko::where('barcode', $barcode)->first();
        
        if (!$toko) {
            return response()->json(['error' => 'Barcode tidak ditemukan'], 404);
        }

        return response()->json([
            'id' => $toko->id,
            'barcode' => $toko->barcode,
            'nama_toko' => $toko->nama_toko,
            'latitude' => $toko->latitude,
            'longitude' => $toko->longitude,
            'accuracy' => $toko->accuracy,
        ]);
    }

    public function cetakBarcode()
    {
        $toko = LokasiToko::orderBy('nama_toko')->get();
        
        if ($toko->isEmpty()) {
            return back()->with('error', 'Tidak ada data toko untuk dicetak');
        }

        // Generate PDF menggunakan FPDF
        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Barcode Toko', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, date('Y-m-d H:i:s'), 0, 1, 'C');
        $pdf->Ln(5);

        $col = 0;
        $row = 0;
        $perRow = 3;
        $marginLeft = 10;
        $marginTop = 40;
        $cellWidth = 60;
        $cellHeight = 70;

        foreach ($toko as $item) {
            if ($col >= $perRow) {
                $col = 0;
                $row++;
            }

            // Jika sudah penuh (4 baris), tambah halaman
            if ($row >= 4) {
                $pdf->AddPage();
                $col = 0;
                $row = 0;
            }

            $x = $marginLeft + ($col * $cellWidth);
            $y = $marginTop + ($row * $cellHeight);

            // Draw border cell
            $pdf->SetDrawColor(200, 200, 200);
            $pdf->Rect($x, $y, $cellWidth, $cellHeight);

            // QR Code (generate dari API dan simpan temporary)
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=40x40&data=' . urlencode($item->barcode);
            $qrFile = tempnam(sys_get_temp_dir(), 'qr_') . '.png';
            
            try {
                $qrData = @file_get_contents($qrUrl);
                if ($qrData) {
                    file_put_contents($qrFile, $qrData);
                    $pdf->Image($qrFile, $x + 2, $y + 2, 35, 35);
                    @unlink($qrFile);
                }
            } catch (\Exception $e) {
                // Skip jika gagal download QR
            }

            // Barcode text
            $pdf->SetXY($x + 2, $y + 38);
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell($cellWidth - 4, 4, substr($item->barcode, 0, 18), 0, 1, 'C');

            // Nama toko
            $pdf->SetXY($x + 2, $y + 42);
            $pdf->SetFont('Arial', '', 6);
            $pdf->Cell($cellWidth - 4, 4, substr($item->nama_toko, 0, 20), 0, 1, 'C');

            // GPS info
            $pdf->SetXY($x + 2, $y + 46);
            $pdf->SetFont('Arial', '', 5);
            $pdf->Cell($cellWidth - 4, 3, 'Lat: ' . round($item->latitude, 4), 0, 1, 'C');
            $pdf->SetXY($x + 2, $y + 49);
            $pdf->Cell($cellWidth - 4, 3, 'Lng: ' . round($item->longitude, 4), 0, 1, 'C');
            $pdf->SetXY($x + 2, $y + 52);
            $pdf->Cell($cellWidth - 4, 3, 'Acc: ' . round($item->accuracy, 1) . 'm', 0, 1, 'C');

            // Print button area
            $pdf->SetXY($x + 2, $y + 55);
            $pdf->SetFont('Arial', '', 4);
            $pdf->Cell($cellWidth - 4, 3, '[Ambil lokasi]', 0, 1, 'C');

            $col++;
        }

        $pdf->Output('barcode_toko.pdf', 'D');
        exit;
    }
}
