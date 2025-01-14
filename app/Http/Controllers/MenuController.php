<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::query();

        // Kategori filtresi
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // İsim/açıklama arama filtresi
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Fiyat aralığı filtresi
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Durum filtresi
        if ($request->filled('status')) {
            $query->where('is_available', $request->status == 'active');
        }

        // Sıralama
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $menus = $query->paginate(10)->withQueryString();
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
