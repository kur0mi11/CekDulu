<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('reports')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new Category]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->saveName($request, new Category);

        return to_route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->saveName($request, $category);

        return to_route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate(['is_active' => ['required', 'boolean']]);
        $category->update($validated);

        $message = $category->is_active
            ? 'Kategori berhasil diaktifkan.'
            : 'Kategori berhasil dinonaktifkan.';

        return to_route('admin.categories.index')->with('success', $message);
    }

    private function saveName(Request $request, Category $category): void
    {
        $duplicateMessage = 'Nama kategori sudah digunakan.';
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique(Category::class, 'name')->ignore($category->getKey())],
        ], ['name.unique' => $duplicateMessage], ['name' => 'nama kategori']);

        try {
            $category->fill($validated)->save();
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['name' => $duplicateMessage]);
        }
    }
}
