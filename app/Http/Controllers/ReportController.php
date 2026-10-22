<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Category;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['user', 'category'])->get();

        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        $categories = Category::all();
        
        return view('reports.create', compact('categories'));
    }

    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}