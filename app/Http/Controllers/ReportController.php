<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Category;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::where('user_id', auth()->id())->latest()->get();
    
        return view('dashboard', compact('reports'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        
        return view('reports.create', compact('categories'));
    }

    public function store(Request $request) {
    //validasi input
    $validated = $request->validate([
        'reported_value' => 'required|string',
        'category_id'    => 'required|exists:categories,id',
        'chronology'     => 'required|string',
        'evidence_file'  => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    //normalisasi nomor telepon (buang karakter non-angka, ubah format 62xx ke 0xx)
    $cleanPhone = preg_replace('/[^0-9]/', '', $request->reported_value);
    if (str_starts_with($cleanPhone, '62')) {
        $cleanPhone = '0' . substr($cleanPhone, 2);
    }

    //menyimpan file bukti ke private storage
    $path = $request->file('evidence_file')->store('evidences', 'local');

    //menyimpan ke database
    Report::create([
        'user_id'        => auth()->id(),
        'category_id'    => $validated['category_id'],
        'reported_type'  => 'phone', // Terkunci otomatis untuk MVP
        'reported_value' => $cleanPhone,
        'chronology'     => $validated['chronology'],
        'evidence_file'  => $path,
        'status'         => 'pending', // Status awal sesuai alur sistem
    ]);

    return redirect()->route('reports.index')->with('success', 'Laporan berhasil dikirim dan menunggu pemeriksaan!');
    }

    public function show(string $id) {
        $report = Report::with(['category', 'user'])->findOrFail($id);
    
        if ($report->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        return view('reports.show', compact('report'));
        
    }
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}