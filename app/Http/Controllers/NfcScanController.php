<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NfcScanController extends Controller
{
    public function index()
    {
        return view('nfc-scan');
    }
}
