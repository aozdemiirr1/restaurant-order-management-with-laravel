<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::latest()->paginate(10);
        $categories = Category::all();
        return view('admin.menus.index', compact('menus', 'categories'));
    }

    public function create()
    {
        return view('admin.menus.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        // is_available değeri gönderilmezse false olarak ayarla
        $validated['is_available'] = $request->has('is_available');

        Menu::create($validated);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menü başarıyla eklendi.');
    }

    public function edit(Menu $menu)
    {
        return response()->json([
            'id' => $menu->id,
            'name' => $menu->name,
            'description' => $menu->description,
            'price' => $menu->price,
            'category_id' => $menu->category_id,
            'image' => $menu->image,
            'is_available' => $menu->is_available
        ]);
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            // Eski görseli sil
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }

            // Yeni görseli kaydet
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        // is_available değeri gönderilmezse false olarak ayarla
        $validated['is_available'] = $request->has('is_available');

        $menu->update($validated);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menü başarıyla güncellendi.');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menü başarıyla silindi.');
    }
}
