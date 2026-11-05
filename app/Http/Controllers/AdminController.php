<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    private const VISIBLE_STATUSES = ['pending_admin_review', 'verified', 'rejected'];

    public function index()
    {
        $queue = Report::with(['category', 'user', 'verifier'])
            ->where('status', 'pending_admin_review')
            ->oldest()
            ->get();

        $history = Report::with(['category', 'admin'])
            ->whereIn('status', ['verified', 'rejected'])
            ->latest('updated_at')
            ->get();

        return view('admin.index', compact('queue', 'history'));
    }

    public function show($id)
    {
        $report = Report::with(['user', 'category', 'verifier', 'admin'])
            ->whereIn('status', self::VISIBLE_STATUSES)
            ->findOrFail($id);

        return view('admin.show', compact('report'));
    }

    public function evidence($id)
    {
        $report = Report::whereIn('status', self::VISIBLE_STATUSES)->findOrFail($id);

        abort_unless(
            $report->evidence_file && Storage::disk('local')->exists($report->evidence_file),
            404,
            'File bukti tidak ditemukan di server.'
        );

        return Storage::disk('local')->response($report->evidence_file);
    }

    public function approve(Request $request, $id)
    {
        $report = Report::where('status', 'pending_admin_review')->findOrFail($id);

        $validated = $request->validate([
            'public_summary' => 'required|string|min:20|max:500',
        ], [
            'public_summary.required' => 'Ringkasan untuk publik wajib diisi.',
            'public_summary.min'      => 'Ringkasan minimal 20 karakter.',
            'public_summary.max'      => 'Ringkasan maksimal 500 karakter.',
        ]);

        $report->update([
            'status'         => 'verified',
            'admin_id'       => auth()->id(),
            'public_summary' => $validated['public_summary'],
            'revision_note'  => null,
        ]);

        return redirect()
            ->route('admin.index')
            ->with('success', 'Laporan untuk nomor ' . $report->reported_value . ' disetujui dan kini tampil di pencarian publik.');
    }

    public function reject(Request $request, $id)
    {
        $report = Report::where('status', 'pending_admin_review')->findOrFail($id);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min'      => 'Alasan penolakan minimal 10 karakter.',
            'rejection_reason.max'      => 'Alasan penolakan maksimal 1000 karakter.',
        ]);

        $report->update([
            'status'         => 'rejected',
            'admin_id'       => auth()->id(),
            'revision_note'  => $validated['rejection_reason'],
            'public_summary' => null,
        ]);

        return redirect()
            ->route('admin.index')
            ->with('success', 'Laporan untuk nomor ' . $report->reported_value . ' ditolak dan tidak dipublikasikan.');
    }
}