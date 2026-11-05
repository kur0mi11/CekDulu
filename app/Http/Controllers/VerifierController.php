<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
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
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', '%' . $search . '%');
                      });
                });
            })
            ->latest()
            ->get();

        return view('verifier.index', compact('antrean'));
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

        //mencatat verifikator yang memeriksa
        $report->verifier_id = auth()->id();
        $report->save();

        return redirect()->route('verifier.index')->with('success', 'Status laporan berhasil diperbarui.');
    }
}