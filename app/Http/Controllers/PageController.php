<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Support\Bank;
use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $type       = $request->input('tipe', 'phone');
        $query      = trim((string) $request->input('nomor', ''));
        $bank       = trim((string) $request->input('bank', ''));
        $normalized = null;
        $error      = null;
        $laporan    = collect();
        $targetInfo = null;

        if ($query !== '') {
            if ($type === 'bank_account') {
                $cleanRek = preg_replace('/[^0-9A-Za-z]/', '', $query);
                if (strlen($cleanRek) < 5) {
                    $error = 'Format nomor rekening tidak valid. Masukkan nomor rekening atau akun minimal 5 karakter.';
                } else {
                    $normalized = $cleanRek;
                    $reportQuery = Report::with('category:id,name')
                        ->where('reported_type', 'bank_account')
                        ->where('reported_value', $cleanRek)
                        ->where('status', 'verified');

                    if ($bank !== '') {
                        $reportQuery->where('bank_name', $bank);
                    }

                    $reportsList = $reportQuery->latest()->get();
                    $firstReport = $reportsList->first();

                    if ($firstReport) {
                        $targetInfo = [
                            'bank_name' => Bank::label($firstReport->bank_name),
                            'account_holder' => $firstReport->account_holder,
                        ];
                    }

                    $laporan = $reportsList->map(fn ($report) => [
                        'kategori' => $report->category->name ?? 'Tidak diketahui',
                        'bank_name' => Bank::label($report->bank_name),
                        'account_holder' => $report->account_holder,
                        'ringkasan' => $report->public_summary
                            ?? Str::limit($report->chronology, 200),
                    ]);
                }
            } else {
                $type = 'phone';
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
        }

        $banks = Bank::all();

        return view('welcome', compact('laporan', 'query', 'normalized', 'error', 'type', 'bank', 'banks', 'targetInfo'));
    }
}
