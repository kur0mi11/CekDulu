<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query      = trim((string) $request->input('nomor', ''));
        $normalized = null;
        $error      = null;
        $laporan    = collect();

        if ($query !== '') {
            $normalized = Phone::normalize($query);

            if ($normalized === null) {
                $error = 'Format nomor tidak valid. Masukkan nomor seluler Indonesia, contoh 081234567890 atau +6281234567890.';
            } else {
                $laporan = Report::with('category:id,name')
                    ->where('reported_type', 'phone')
                    ->where('reported_value', $normalized)
                    ->where('status', 'verified')
                    ->latest()
                    ->get(['id', 'category_id', 'chronology', 'public_summary'])
                    ->map(fn ($report) => [
                        'kategori' => $report->category->name ?? 'Tidak diketahui',
                        'ringkasan' => $report->public_summary
                            ?? Str::limit($report->chronology, 200),
                    ]);
            }
        }
        return view('welcome', compact('laporan', 'query', 'normalized', 'error'));
    }
}