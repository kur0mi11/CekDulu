<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Notifications\ReportReadyForModerationNotification;
use App\Notifications\ReportStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class VerifierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $antrean = Report::whereIn('status', ['pending', 'need_revision'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reported_value', 'like', '%' . $search . '%')
                      ->orWhere('bank_name', 'like', '%' . $search . '%')
                      ->orWhere('account_holder', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', '%' . $search . '%');
                      });
                });
            })
            ->latest()
            ->get();

        $riwayat = Report::where('verifier_id', auth()->id())
            ->whereNotIn('status', ['pending', 'need_revision'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reported_value', 'like', '%' . $search . '%')
                      ->orWhere('bank_name', 'like', '%' . $search . '%')
                      ->orWhere('account_holder', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', '%' . $search . '%');
                      });
                });
            })
            ->latest()
            ->get();

        return view('verifier.index', compact('antrean', 'riwayat'));
    }

    public function show($id)
    {
        $report = Report::with(['user', 'category'])->findOrFail($id);
        return view('verifier.show', compact('report'));
    }

    public function updateStatus(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending_admin_review,need_revision,rejected',
            'revision_note' => 'required_if:status,need_revision|nullable|string',
        ]);

        $report->status = $request->status;
        
        if ($request->status === 'need_revision') {
            $report->revision_note = $request->revision_note;
        } else {
            $report->revision_note = null;
        }

        $report->verifier_id = auth()->id();
        $report->save();

        // Notifikasi ke pelapor
        $report->user?->notify(new ReportStatusChanged($report, $report->status, $report->revision_note));

        // Jika diteruskan ke admin, kirim notifikasi ke seluruh admin
        if ($report->status === 'pending_admin_review') {
            $admins = User::whereHas('role', fn($q) => $q->where('name', 'Admin'))->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new ReportReadyForModerationNotification($report));
            }
        }

        return redirect()->route('verifier.index')->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function showEvidence($id)
    {
        $report = Report::findOrFail($id);
        if (!$report->evidence_file || !Storage::exists($report->evidence_file)) {
            abort(404, 'File bukti tidak ditemukan di server.');
        }

        return Storage::response($report->evidence_file);
    }
}