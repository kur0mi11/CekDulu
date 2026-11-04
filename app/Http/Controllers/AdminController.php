<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $reports = Report::where('status', 'pending_admin_review')->latest()->get();
        return view('admin.index', compact('reports'));
    }

    //verified atau rejected
    public function updateStatus(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $request->validate([
            'status' => 'required|in:verified,rejected',
        ]);

        $report->status = $request->status;
        $report->admin_id = auth()->id();
        $report->save();

        return redirect()->route('admin.index')->with('success', 'Status moderasi akhir berhasil disimpan.');
    }
}