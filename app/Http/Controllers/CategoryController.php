<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $tenantId = session('tenant_id');

        // Ana kategoriler (parent_id IS NULL), sort_order'a göre
        $parents = DB::table('categories')
            ->where('tenant_id', $tenantId)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Alt kategoriler, parent_id'ye göre gruplanmış
        $children = DB::table('categories')
            ->where('tenant_id', $tenantId)
            ->whereNotNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('parent_id');

        return view('categories.index', compact('parents', 'children'));
    }

    public function create()
    {
        $tenantId = session('tenant_id');
        $parents  = DB::table('categories')
            ->where('tenant_id', $tenantId)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'image'     => 'nullable|image|max:2048',
        ]);

        $tenantId  = session('tenant_id');
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store("tenants/{$tenantId}/categories", 'public');
        }

        $maxOrder = DB::table('categories')
            ->where('tenant_id', $tenantId)
            ->max('sort_order') ?? 0;

        DB::table('categories')->insert([
            'tenant_id'  => $tenantId,
            'parent_id'  => $request->parent_id ?: null,
            'name'       => $request->name,
            'image'      => $imagePath,
            'sort_order' => $maxOrder + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('Yeni kategori oluşturuldu.', ['tenant_id' => $tenantId, 'name' => $request->name]);

        return redirect()->route('categories.index')->with('success', __('messages.category_added'));
    }

    public function edit(int $id)
    {
        $tenantId = session('tenant_id');
        $category = DB::table('categories')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$category) {
            abort(404);
        }

        $parents = DB::table('categories')
            ->where('tenant_id', $tenantId)
            ->whereNull('parent_id')
            ->where('id', '!=', $id)
            ->orderBy('name')
            ->get();

        return view('categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'image'     => 'nullable|image|max:2048',
        ]);

        $tenantId = session('tenant_id');
        $category = DB::table('categories')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$category) {
            abort(404);
        }

        $data = [
            'name'       => $request->name,
            'parent_id'  => $request->parent_id ?: null,
            'updated_at' => now(),
        ];

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store("tenants/{$tenantId}/categories", 'public');
        }

        if ($request->boolean('remove_image') && $category->image) {
            Storage::disk('public')->delete($category->image);
            $data['image'] = null;
        }

        DB::table('categories')->where('id', $id)->where('tenant_id', $tenantId)->update($data);

        Log::info('Kategori güncellendi.', ['tenant_id' => $tenantId, 'category_id' => $id]);

        return redirect()->route('categories.index')->with('success', __('messages.category_updated'));
    }

    public function destroy(int $id)
    {
        $tenantId = session('tenant_id');
        $category = DB::table('categories')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$category) {
            abort(404);
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        DB::table('categories')->where('id', $id)->where('tenant_id', $tenantId)->delete();

        Log::info('Kategori silindi.', ['tenant_id' => $tenantId, 'category_id' => $id]);

        return redirect()->route('categories.index')->with('success', __('messages.category_deleted'));
    }

    /** AJAX: inline update (name + image) */
    public function inlineUpdate(Request $request, int $id)
    {
        $tenantId = session('tenant_id');
        $category = DB::table('categories')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$category) {
            return response()->json(['error' => 'Bulunamadı.'], 404);
        }

        $data = ['updated_at' => now()];

        if ($request->filled('name')) {
            $data['name'] = substr(strip_tags($request->name), 0, 255);
        }

        if ($request->hasFile('image')) {
            $request->validate(['image' => 'image|max:2048']);
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store("tenants/{$tenantId}/categories", 'public');
        }

        DB::table('categories')->where('id', $id)->where('tenant_id', $tenantId)->update($data);

        $updated = DB::table('categories')->find($id);

        return response()->json([
            'success'   => true,
            'name'      => $updated->name,
            'image_url' => $updated->image ? asset('storage/' . $updated->image) : null,
        ]);
    }

    /** AJAX: drag-drop reorder */
    public function reorder(Request $request)
    {
        $tenantId = session('tenant_id');
        $order    = $request->input('order', []);

        foreach ($order as $i => $id) {
            DB::table('categories')
                ->where('id', $id)
                ->where('tenant_id', $tenantId)
                ->update(['sort_order' => $i + 1, 'updated_at' => now()]);
        }

        return response()->json(['success' => true]);
    }
}
