<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string',
        ]);

        $student = Student::where('nfc_serial_number', $request->serial_number)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak terdaftar atau kartu tidak dikenali.',
            ], 404);
        }

        // Catat absensi
        $attendance = Attendance::create([
            'student_id' => $student->id,
            'scanned_at' => now(),
            'status' => 'hadir',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat.',
            'data' => [
                'student_name' => $student->name,
                'nim' => $student->nim,
                'scanned_at' => $attendance->scanned_at->format('Y-m-d H:i:s'),
            ]
        ], 200);
    }
}
