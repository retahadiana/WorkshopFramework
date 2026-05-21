<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntrianController extends Controller
{
    public function viewGuest() { 
        return view('guest'); 
    }
    
    public function viewAdmin() { 
        return view('admin'); 
    }
    
    public function viewPapan() { 
        return view('papan'); 
    }

    public function stream(Request $request)
    {
        set_time_limit(0);
        
        // Lepaskan session lock agar tidak memblokir request lain dari browser yang sama
        if ($request->hasSession()) {
            $request->session()->save();
        }

        return response()->stream(function () {
            while (true) {
                $data = Cache::get('antrian_data', ['queues' => [], 'current' => null]);
                echo "event: queue-update\n";
                echo 'data: ' . json_encode($data) . "\n\n";
                ob_flush();
                flush();
                if (connection_aborted()) break;
                sleep(1);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        $data = Cache::get('antrian_data', ['queues' => [], 'current' => null]);
        $nomor_urut = count($data['queues']) + 1;
        
        $newQueue = [
            'id' => uniqid(),
            'nomor_urut' => $nomor_urut,
            'nama_guest' => $request->nama,
            'status' => 'active'
        ];
        $data['queues'][] = $newQueue;
        
        Cache::put('antrian_data', $data);
        return response()->json($newQueue);
    }

    public function panggil(Request $request)
    {
        $data = Cache::get('antrian_data', ['queues' => [], 'current' => null]);
        $firstActiveIndex = null;
        foreach ($data['queues'] as $index => $q) {
            if ($q['status'] === 'active') {
                $firstActiveIndex = $index;
                break;
            }
        }
        if ($firstActiveIndex !== null) {
            // Mark previous as finished if it was just called
            if ($data['current']) {
                foreach ($data['queues'] as $index => $q) {
                    if ($q['id'] === $data['current']['id'] && $q['status'] === 'called') {
                        $data['queues'][$index]['status'] = 'finished';
                        break;
                    }
                }
            }

            $data['queues'][$firstActiveIndex]['status'] = 'called';
            $data['current'] = $data['queues'][$firstActiveIndex];
            $data['current']['call_time'] = microtime(true);
            Cache::put('antrian_data', $data);
        }
        return response()->json(['success' => true]);
    }

    public function skip(Request $request)
    {
        $data = Cache::get('antrian_data', ['queues' => [], 'current' => null]);
        $id = $request->id;
        foreach ($data['queues'] as $index => $q) {
            if ($q['id'] === $id) {
                $data['queues'][$index]['status'] = 'terlambat';
                if ($data['current'] && $data['current']['id'] === $id) {
                    $data['current'] = null; // Clear if we skip the current one
                }
                break;
            }
        }
        Cache::put('antrian_data', $data);
        return response()->json(['success' => true]);
    }

    public function recall(Request $request)
    {
        $data = Cache::get('antrian_data', ['queues' => [], 'current' => null]);
        $id = $request->id;
        foreach ($data['queues'] as $index => $q) {
            if ($q['id'] === $id) {
                // If recall, mark as called again
                $data['queues'][$index]['status'] = 'called';
                
                if ($data['current']) {
                    foreach ($data['queues'] as $i => $prev) {
                        if ($prev['id'] === $data['current']['id'] && $prev['status'] === 'called') {
                            $data['queues'][$i]['status'] = 'finished';
                        }
                    }
                }
                
                $data['current'] = $data['queues'][$index];
                $data['current']['call_time'] = microtime(true);
                break;
            }
        }
        Cache::put('antrian_data', $data);
        return response()->json(['success' => true]);
    }

    public function reset(Request $request)
    {
        Cache::put('antrian_data', ['queues' => [], 'current' => null]);
        return response()->json(['success' => true]);
    }
}
