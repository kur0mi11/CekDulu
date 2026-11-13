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
        $validated = $request->validate($this->rules(evidenceRequired: true), $this->messages());

        $path = $request->file('evidence_file')->store('evidences', 'local');

        Report::create([
            'user_id'        => auth()->id(),
            'category_id'    => $validated['category_id'],
            'reported_type'  => 'phone',
            'reported_value' => $this->normalizePhone($validated['reported_value']),
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
        $report = $this->ownedReport($id, ['category']);

        return view('reports.show', compact('report'));
    }

    public function evidence(string $id)
    {
        $report = $this->ownedReport($id);

        abort_unless(
            $report->evidence_file && Storage::disk('local')->exists($report->evidence_file),
            404,
            'File bukti tidak ditemukan.'
        );

        return Storage::disk('local')->response($report->evidence_file);
    }

    public function edit(string $id)
    {
        $report = $this->ownedReport($id, ['category']);

        $this->ensureRevisable($report);

        $categories = Category::orderBy('name')->get();

        return view('reports.edit', compact('report', 'categories'));
    }

    public function update(Request $request, string $id)
    {
        $report = $this->ownedReport($id);

        $this->ensureRevisable($report);

        $validated = $request->validate($this->rules(evidenceRequired: false), $this->messages());

        $oldFile = $report->evidence_file;
        $newFile = null;

        if ($request->hasFile('evidence_file')) {
            $newFile = $request->file('evidence_file')->store('evidences', 'local');
        }

        $report->update([
            'category_id'    => $validated['category_id'],
            'reported_value' => $this->normalizePhone($validated['reported_value']),
            'chronology'     => $validated['chronology'],
            'evidence_file'  => $newFile ?? $oldFile,
            'status'         => 'pending',
        ]);

        // File lama dihapus setelah data baru berhasil tersimpan
        if ($newFile && $oldFile) {
            Storage::disk('local')->delete($oldFile);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Perbaikan laporan berhasil dikirim dan menunggu pemeriksaan ulang.');
    }

    private function ownedReport(string $id, array $with = []): Report
    {
        $report = Report::with($with)->findOrFail($id);

        abort_if($report->user_id !== auth()->id(), 403, 'Akses ditolak.');

        return $report;
    }

    private function ensureRevisable(Report $report): void
    {
        abort_if(
            $report->status !== 'need_revision',
            403,
            'Laporan ini tidak sedang diminta untuk direvisi.'
        );
    }

    private function rules(bool $evidenceRequired): array
    {
        return [
            'reported_value' => 'required|string|max:30',
            'category_id'    => 'required|exists:categories,id',
            'chronology'     => 'required|string|max:5000',
            'evidence_file'  => ($evidenceRequired ? 'required' : 'nullable')
                                . '|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    private function messages(): array
    {
        return [
            'reported_value.required' => 'Nomor telepon wajib diisi.',
            'reported_value.max'      => 'Nomor telepon terlalu panjang.',
            'category_id.required'    => 'Pilih kategori laporan.',
            'category_id.exists'      => 'Kategori yang dipilih tidak valid.',
            'chronology.required'     => 'Kronologi kejadian wajib diisi.',
            'chronology.max'          => 'Kronologi maksimal 5000 karakter.',
            'evidence_file.required'  => 'Bukti pendukung wajib diunggah.',
            'evidence_file.file'      => 'Bukti harus berupa file.',
            'evidence_file.uploaded'  => 'Bukti gagal diunggah. Pastikan ukuran maksimal 2 MB.',
            'evidence_file.mimes'     => 'Bukti harus berupa file JPG, PNG, atau PDF.',
            'evidence_file.max'       => 'Ukuran bukti maksimal 2 MB.',
        ];
    }

    private function normalizePhone(string $raw): string
    {
        $clean = preg_replace('/[^0-9]/', '', $raw);

        if (str_starts_with($clean, '62')) {
            $clean = '0' . substr($clean, 2);
        }

        return $clean;
    }
}