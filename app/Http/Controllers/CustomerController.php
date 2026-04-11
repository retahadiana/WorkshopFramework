<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderByDesc('created_at')->get();

        return view('customer.index', compact('customers'));
    }

    public function createBlob()
    {
        return view('customer.create', [
            'mode' => 'blob',
            'title' => 'Tambah Customer 1',
            'description' => 'Ambil foto customer dan simpan hasil snapshot sebagai BLOB di database.',
            'submitRoute' => route('customer.store.blob'),
        ]);
    }

    public function createPath()
    {
        return view('customer.create', [
            'mode' => 'path',
            'title' => 'Tambah Customer 2',
            'description' => 'Ambil foto customer dan simpan hasil snapshot sebagai file gambar, lalu simpan path ke database.',
            'submitRoute' => route('customer.store.path'),
        ]);
    }

    public function storeBlob(Request $request)
    {
        return $this->saveCustomer($request, 'blob');
    }

    public function storePath(Request $request)
    {
        return $this->saveCustomer($request, 'path');
    }

    public function postalCodes(Request $request)
    {
        $kelurahan = trim($request->query('kelurahan', ''));
        $kecamatan = trim($request->query('kecamatan', ''));
        $kota = trim($request->query('kota', ''));
        $provinsi = trim($request->query('provinsi', ''));

        if ($kelurahan === '') {
            return response()->json([]);
        }

        $query = $kelurahan;
        if ($kecamatan !== '') {
            $query .= ' ' . $kecamatan;
        }
        if ($kota !== '') {
            $query .= ' ' . $kota;
        }
        if ($provinsi !== '') {
            $query .= ' ' . $provinsi;
        }

        $response = Http::timeout(10)->get('https://kodepos.vercel.app/search', [
            'q' => $query,
        ]);

        if (! $response->ok()) {
            return response()->json([]);
        }

        $body = $response->json();
        $items = [];
        $seen = [];

        foreach ($body['data'] ?? [] as $row) {
            if (empty($row['code']) || in_array($row['code'], $seen, true)) {
                continue;
            }
            $seen[] = $row['code'];
            $items[] = [
                'code' => (string) $row['code'],
                'label' => sprintf('%s - %s', $row['code'], $row['village'] ?? $kelurahan),
            ];
        }

        return response()->json($items);
    }

    private function saveCustomer(Request $request, string $storageMethod)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'provinsi' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kodepos' => 'required|string|max:20',
            'foto_data' => 'nullable|string',
        ]);

        $fotoPath = null;
        $fotoBlob = null;

        if (!empty($data['foto_data']) && preg_match('/^data:image\/(png|jpeg);base64,(.*)$/', $data['foto_data'], $matches)) {
            $imageData = base64_decode($matches[2]);

            if ($storageMethod === 'path') {
                $directory = public_path('uploads/customers');
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }

                $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
                $filename = 'customer_' . time() . '_' . uniqid() . '.' . $extension;
                $fotoPath = 'uploads/customers/' . $filename;
                file_put_contents(public_path($fotoPath), $imageData);
            } else {
                $fotoBlob = $imageData;
            }
        }

        Customer::create([
            'nama' => $data['nama'],
            'alamat' => $data['alamat'],
            'provinsi' => $data['provinsi'],
            'kota' => $data['kota'],
            'kecamatan' => $data['kecamatan'],
            'kodepos' => $data['kodepos'],
            'foto_path' => $fotoPath,
            'foto_blob' => $fotoBlob,
            'storage_method' => $storageMethod,
        ]);

        return redirect()->route('customer.index')->with('status', 'Customer berhasil disimpan.');
    }
}
