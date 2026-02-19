<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class GeneratePdfController extends Controller
{
    public function index()
    {
        $files = [
            'certificate' => $this->pdfPath('certificate'),
            'invitation' => $this->pdfPath('invitation'),
        ];

        return view('pdf.generate', [
            'files' => [
                'certificate' => Storage::disk('local')->exists($files['certificate']),
                'invitation' => Storage::disk('local')->exists($files['invitation']),
            ],
        ]);
    }

    public function generate(Request $request)
    {
        $certificate = Pdf::loadView('pdf.certificate', [
            'name' => $request->user()->name ?? 'Peserta',
            'role' => 'Wakil Ketua Pelaksana',
            'event' => 'Seminar Nasional Kesehatan Masyarakat',
            'date' => 'Sabtu, 18 Oktober 2025',
            'number' => '3353/B/2025',
        ])->setPaper('a4', 'landscape');

        $invitation = Pdf::loadView('pdf.invitation', [
            'number' => '021/HIMA-D4TI/FV-UA/II/2026',
            'date' => 'Senin, 24 Februari 2026',
            'subject' => 'Undangan Rapat HIMA D4 Teknik Informatika',
            'place' => 'Ruang Rapat HIMA D4 TI, Fakultas Vokasi Universitas Airlangga',
            'time' => '13.00 - 15.30 WIB',
            'event' => 'Rapat Koordinasi Program Kerja Semester Genap 2025/2026',
            'organization' => 'Fakultas Vokasi Universitas Airlangga',
            'recipient' => 'Reta Hadiana Unggula',
        ])->setPaper('a4', 'portrait');

        Storage::disk('local')->put($this->pdfPath('certificate'), $certificate->output());
        Storage::disk('local')->put($this->pdfPath('invitation'), $invitation->output());

        return redirect()->route('pdf.generate')
            ->with('status', 'File PDF berhasil dibuat.');
    }

    public function download(string $type)
    {
        $path = $this->pdfPath($type);

        if (! Storage::disk('local')->exists($path)) {
            return redirect()->route('pdf.generate')
                ->withErrors(['pdf' => 'File PDF belum tersedia. Silakan generate terlebih dulu.']);
        }

        $filename = $type === 'certificate' ? 'sertifikat.pdf' : 'undangan.pdf';

        return response()->download(Storage::disk('local')->path($path), $filename);
    }

    private function pdfPath(string $type): string
    {
        return 'pdf/' . $type . '.pdf';
    }
}
