<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('reports.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reported_value' => 'required|string',
            'category_id'    => 'required|exists:categories,id',
            'chronology'     => 'required|string',
            'evidence_file'  => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'reported_value.required' => 'Nomor telepon wajib diisi.',
            'category_id.required'    => 'Pilih kategori laporan.',
            'category_id.exists'      => 'Kategori yang dipilih tidak valid.',
            'chronology.required'     => 'Kronologi kejadian wajib diisi.',
            'evidence_file.required'  => 'Bukti pendukung wajib diunggah.',
            'evidence_file.mimes'     => 'Bukti harus berupa file JPG, PNG, atau PDF.',
            'evidence_file.max'       => 'Ukuran bukti maksimal 2 MB.',
        ]);

        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['reported_value']);
        if (str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '0' . substr($cleanPhone, 2);
        }

        $path = $request->file('evidence_file')->store('evidences', 'local');

        Report::create([
            'user_id'        => auth()->id(),
            'category_id'    => $validated['category_id'],
            'reported_type'  => 'phone',
            'reported_value' => $cleanPhone,
            'chronology'     => $validated['chronology'],
            'evidence_file'  => $path,
            'status'         => 'pending',
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Laporan berhasil dikirim dan menunggu pemeriksaan.');
    }

    public function show(string $id)
    {
        $report = Report::with('category')->findOrFail($id);

        abort_if($report->user_id !== auth()->id(), 403, 'Akses ditolak.');

        return view('reports.show', compact('report'));
    }

    public function evidence(string $id)
    {
        $report = Report::findOrFail($id);

        abort_if($report->user_id !== auth()->id(), 403, 'Akses ditolak.');
        abort_unless(
            $report->evidence_file && Storage::disk('local')->exists($report->evidence_file),
            404,
            'File bukti tidak ditemukan.'
        );

        return Storage::disk('local')->response($report->evidence_file);
    }
  
    public function edit(string $id)
    {
        abort(404);
    }

    public function update(Request $request, string $id)
    {
        abort(404);
    }
}