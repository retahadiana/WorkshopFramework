<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class GeneratePdfController extends Controller
{
    public function index()
    {
        // Legacy single-page view removed. Redirect to certificate page.
        return redirect()->route('pdf.certificate');
    }

    public function certificate()
    {
        $path = $this->pdfPath('certificate');

        return view('pdf.certificate_page', [
            'exists' => Storage::disk('local')->exists($path),
        ]);
    }

    public function invitation()
    {
        $path = $this->pdfPath('invitation');

        return view('pdf.invitation_page', [
            'exists' => Storage::disk('local')->exists($path),
        ]);
    }

    public function generate(Request $request)
    {
        // Legacy combined generation removed. Use per-document generation routes instead.
        return redirect()->route('pdf.certificate')
            ->with('status', 'Gunakan halaman terpisah untuk membuat file PDF.');
    }

    public function generateCertificate(Request $request)
    {
        $certificate = Pdf::loadView('pdf.certificate', [
            'name' => $request->user()->name ?? 'Peserta',
            'role' => 'Wakil Ketua Pelaksana',
            'event' => 'Seminar Nasional Kesehatan Masyarakat',
            'date' => 'Sabtu, 18 Oktober 2025',
            'number' => '3353/B/2025',
        ])->setPaper('a4', 'landscape');

        Storage::disk('local')->put($this->pdfPath('certificate'), $certificate->output());

        return redirect()->route('pdf.certificate')
            ->with('status', 'File Sertifikat berhasil dibuat.');
    }

    public function generateInvitation(Request $request)
    {
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

        Storage::disk('local')->put($this->pdfPath('invitation'), $invitation->output());

        return redirect()->route('pdf.invitation')
            ->with('status', 'File Undangan berhasil dibuat.');
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
