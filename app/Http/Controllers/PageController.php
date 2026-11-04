<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('nomor');
        $laporan = null;

        if ($query) {
            //normalisasi nomor yang dicari
            $cleanPhone = preg_replace('/[^0-9]/', '', $query);
            if (str_starts_with($cleanPhone, '62')) {
                $cleanPhone = '0' . substr($cleanPhone, 2);
            }

            //pencarian data dengan batasan ketat (cuma phone dan verified)
            $laporan = Report::where('reported_type', 'phone') // Kunci ke tipe nomor telepon
                            ->where('reported_value', $cleanPhone)
                            ->where('status', 'verified') // WAJIB: Hanya tampilkan yang disetujui Admin
                            ->first();
        }

        //kirim data ke ui publik
        return view('welcome', compact('laporan', 'query')); 
    }
}