<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Auth::user()->categories()->orderBy('type')->orderBy('id')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:expense,income'],
            'color'=> ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon' => ['required', 'string', 'max:50'],
        ]);

        // Store name as translatable JSON with same value for both locales
        Auth::user()->categories()->create([
            'name'  => ['ru' => $data['name'], 'en' => $data['name']],
            'type'  => $data['type'],
            'color' => $data['color'],
            'icon'  => $data['icon'],
        ]);

        return back()->with('success', __('app.category_created'));
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'color'=> ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon' => ['required', 'string', 'max:50'],
        ]);

        $category->update([
            'name'  => ['ru' => $data['name'], 'en' => $data['name']],
            'color' => $data['color'],
            'icon'  => $data['icon'],
        ]);

        return back()->with('success', __('app.category_updated'));
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        $category->delete();
        return back()->with('success', __('app.category_deleted'));
    }
}
