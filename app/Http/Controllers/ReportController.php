<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use App\Notifications\ReportSubmittedNotification;
use App\Rules\PhoneNumber;
use App\Support\Bank;
use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
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
        $banks = Bank::all();

        return view('reports.create', compact('categories', 'banks'));
    }

    public function store(Request $request)
    {
        $type = $request->input('reported_type', 'phone');
        $validated = $request->validate($this->rules($type, evidenceRequired: true), $this->messages($type));

        $path = $request->file('evidence_file')->store('evidences', 'local');

        $reportedValue = $type === 'phone'
            ? Phone::normalize($validated['reported_value'])
            : preg_replace('/[^0-9A-Za-z]/', '', $validated['reported_value']);

        $report = Report::create([
            'user_id'        => auth()->id(),
            'category_id'    => $validated['category_id'],
            'reported_type'  => $type,
            'bank_name'      => $type === 'bank_account' ? $validated['bank_name'] : null,
            'reported_value' => $reportedValue,
            'account_holder' => $type === 'bank_account' ? trim($validated['account_holder']) : null,
            'chronology'     => $validated['chronology'],
            'evidence_file'  => $path,
            'status'         => 'pending',
        ]);

        $verifiers = User::whereHas('role', fn($q) => $q->where('name', 'Verifier'))->get();
        if ($verifiers->isNotEmpty()) {
            Notification::send($verifiers, new ReportSubmittedNotification($report, false));
        }

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
        $banks = Bank::all();

        return view('reports.edit', compact('report', 'categories', 'banks'));
    }

    public function update(Request $request, string $id)
    {
        $report = $this->ownedReport($id);

        $this->ensureRevisable($report);

        $type = $request->input('reported_type', $report->reported_type ?? 'phone');
        $validated = $request->validate($this->rules($type, evidenceRequired: false), $this->messages($type));

        $oldFile = $report->evidence_file;
        $newFile = null;

        if ($request->hasFile('evidence_file')) {
            $newFile = $request->file('evidence_file')->store('evidences', 'local');
        }

        $reportedValue = $type === 'phone'
            ? Phone::normalize($validated['reported_value'])
            : preg_replace('/[^0-9A-Za-z]/', '', $validated['reported_value']);

        $report->update([
            'category_id'    => $validated['category_id'],
            'reported_type'  => $type,
            'bank_name'      => $type === 'bank_account' ? $validated['bank_name'] : null,
            'reported_value' => $reportedValue,
            'account_holder' => $type === 'bank_account' ? trim($validated['account_holder']) : null,
            'chronology'     => $validated['chronology'],
            'evidence_file'  => $newFile ?? $oldFile,
            'status'         => 'pending',
        ]);

        // File lama dihapus setelah data baru berhasil tersimpan
        if ($newFile && $oldFile) {
            Storage::disk('local')->delete($oldFile);
        }

        $verifiers = User::whereHas('role', fn($q) => $q->where('name', 'Verifier'))->get();
        if ($verifiers->isNotEmpty()) {
            Notification::send($verifiers, new ReportSubmittedNotification($report, true));
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

    private function rules(string $type, bool $evidenceRequired): array
    {
        $rules = [
            'reported_type' => 'nullable|in:phone,bank_account',
            'category_id'   => 'required|exists:categories,id',
            'chronology'    => 'required|string|max:5000',
            'evidence_file' => ($evidenceRequired ? 'required' : 'nullable')
                                . '|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        if ($type === 'bank_account') {
            $rules['bank_name']      = 'required|string|max:50';
            $rules['account_holder'] = 'required|string|min:2|max:100';
            $rules['reported_value'] = 'required|string|min:5|max:30|regex:/^[0-9A-Za-z\-]+$/';
        } else {
            $rules['reported_value'] = ['required', 'string', 'max:30', new PhoneNumber];
        }

        return $rules;
    }

    private function messages(string $type): array
    {
        return [
            'reported_type.required'   => 'Jenis laporan wajib dipilih.',
            'reported_type.in'         => 'Jenis laporan tidak valid.',
            'reported_value.required'  => $type === 'bank_account' ? 'Nomor rekening atau nomor akun wajib diisi.' : 'Nomor telepon wajib diisi.',
            'reported_value.min'       => 'Nomor rekening minimal 5 karakter.',
            'reported_value.max'       => 'Nomor yang dilaporkan terlalu panjang (maksimal 30 karakter).',
            'reported_value.regex'     => 'Format nomor rekening hanya boleh angka dan huruf.',
            'bank_name.required'       => 'Nama bank atau dompet digital wajib dipilih.',
            'account_holder.required'  => 'Nama pemilik rekening wajib diisi.',
            'account_holder.min'       => 'Nama pemilik rekening minimal 2 karakter.',
            'account_holder.max'       => 'Nama pemilik rekening maksimal 100 karakter.',
            'category_id.required'     => 'Pilih kategori laporan.',
            'category_id.exists'       => 'Kategori yang dipilih tidak valid.',
            'chronology.required'      => 'Kronologi kejadian wajib diisi.',
            'chronology.max'          => 'Kronologi maksimal 5000 karakter.',
            'evidence_file.required'  => 'Bukti pendukung wajib diunggah.',
            'evidence_file.file'      => 'Bukti harus berupa file.',
            'evidence_file.uploaded'  => 'Bukti gagal diunggah. Pastikan ukuran maksimal 2 MB.',
            'evidence_file.mimes'     => 'Bukti harus berupa file JPG, PNG, atau PDF.',
            'evidence_file.max'       => 'Ukuran bukti maksimal 2 MB.',
        ];
    }
}
