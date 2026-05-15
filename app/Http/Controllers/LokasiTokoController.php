<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LokasiToko;

class LokasiTokoController extends Controller
{
    public function index()
    {
        $toko = LokasiToko::orderBy('id','desc')->get();
        return view('data-toko.index', compact('toko'));
    }

    public function create()
    {
        return view('data-toko.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barcode' => 'required|string|unique:lokasi_toko,barcode',
            'nama_toko' => 'nullable|string',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric',
        ]);

        LokasiToko::create($data);

        return redirect()->route('data-toko.index')->with('success', 'Data toko berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = LokasiToko::findOrFail($id);
        return view('data-toko.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = LokasiToko::findOrFail($id);

        $data = $request->validate([
            'barcode' => 'required|string|unique:lokasi_toko,barcode,'.$item->id,
            'nama_toko' => 'nullable|string',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric',
        ]);

        $item->update($data);

        return redirect()->route('data-toko.index')->with('success', 'Data toko berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $item = LokasiToko::findOrFail($id);
        $item->delete();
        return redirect()->route('data-toko.index')->with('success', 'Data toko berhasil dihapus.');
    }

    public function print($id)
    {
        $item = LokasiToko::findOrFail($id);
        return view('data-toko.print', compact('item'));
    }
}
