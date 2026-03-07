<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $tenantId = session('tenant_id');
        $products = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.tenant_id', $tenantId)
            ->select('products.*', 'categories.name as category_name')
            ->orderBy('products.sort_order')
            ->orderBy('products.name')
            ->get();

        // Categories for inline category change
        $categories = DB::table('categories')
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $tenantId   = session('tenant_id');
        $categories = DB::table('categories')
            ->where('tenant_id', $tenantId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'image'       => 'nullable|image|max:2048',
        ]);

        $tenantId = session('tenant_id');

        if (!DB::table('categories')->where('id', $request->category_id)->where('tenant_id', $tenantId)->exists()) {
            abort(403);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store("tenants/{$tenantId}/products", 'public');
        }

        $maxOrder = DB::table('products')->where('tenant_id', $tenantId)->max('sort_order') ?? 0;

        $productId = DB::table('products')->insertGetId([
            'tenant_id'   => $tenantId,
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'image'       => $imagePath,
            'sort_order'  => $maxOrder + 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        Log::info('Yeni ürün eklendi.', ['tenant_id' => $tenantId, 'product_id' => $productId]);

        return redirect()->route('products.index')->with('success', __('messages.product_added'));
    }

    public function edit(int $id)
    {
        $tenantId = session('tenant_id');
        $product  = DB::table('products')->where('id', $id)->where('tenant_id', $tenantId)->first();

        if (!$product) {
            abort(404);
        }

        $categories = DB::table('categories')
            ->where('tenant_id', $tenantId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'image'       => 'nullable|image|max:2048',
        ]);

        $tenantId = session('tenant_id');
        $product  = DB::table('products')->where('id', $id)->where('tenant_id', $tenantId)->first();

        if (!$product) {
            abort(404);
        }

        if (!DB::table('categories')->where('id', $request->category_id)->where('tenant_id', $tenantId)->exists()) {
            abort(403);
        }

        $data = [
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'updated_at'  => now(),
        ];

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store("tenants/{$tenantId}/products", 'public');
        }

        if ($request->boolean('remove_image') && $product->image) {
            Storage::disk('public')->delete($product->image);
            $data['image'] = null;
        }

        DB::table('products')->where('id', $id)->where('tenant_id', $tenantId)->update($data);

        Log::info('Ürün güncellendi.', ['tenant_id' => $tenantId, 'product_id' => $id]);

        return redirect()->route('products.index')->with('success', __('messages.product_updated'));
    }

    public function destroy(int $id)
    {
        $tenantId = session('tenant_id');
        $product  = DB::table('products')->where('id', $id)->where('tenant_id', $tenantId)->first();

        if (!$product) {
            abort(404);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        DB::table('products')->where('id', $id)->where('tenant_id', $tenantId)->delete();

        Log::info('Ürün silindi.', ['tenant_id' => $tenantId, 'product_id' => $id]);

        return redirect()->route('products.index')->with('success', __('messages.product_deleted'));
    }

    /** AJAX: inline update (name, price, image, category) */
    public function inlineUpdate(Request $request, int $id)
    {
        $tenantId = session('tenant_id');
        $product  = DB::table('products')->where('id', $id)->where('tenant_id', $tenantId)->first();

        if (!$product) {
            return response()->json(['error' => 'Bulunamadı.'], 404);
        }

        $data = ['updated_at' => now()];

        if ($request->filled('name')) {
            $data['name'] = substr(strip_tags($request->name), 0, 255);
        }
        if ($request->filled('price')) {
            $data['price'] = max(0, (float) $request->price);
        }
        if ($request->filled('category_id')) {
            $catOk = DB::table('categories')
                ->where('id', $request->category_id)
                ->where('tenant_id', $tenantId)
                ->exists();
            if ($catOk) {
                $data['category_id'] = (int) $request->category_id;
            }
        }

        if ($request->hasFile('image')) {
            $request->validate(['image' => 'image|max:2048']);
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store("tenants/{$tenantId}/products", 'public');
        }

        DB::table('products')->where('id', $id)->where('tenant_id', $tenantId)->update($data);

        $updated  = DB::table('products')->find($id);
        $category = DB::table('categories')->find($updated->category_id);

        return response()->json([
            'success'       => true,
            'name'          => $updated->name,
            'price'         => number_format($updated->price, 2, ',', '.'),
            'image_url'     => $updated->image ? asset('storage/' . $updated->image) : null,
            'category_name' => $category->name ?? '',
        ]);
    }

    /** AJAX: drag-drop reorder */
    public function reorder(Request $request)
    {
        $tenantId = session('tenant_id');
        $order    = $request->input('order', []);

        foreach ($order as $i => $id) {
            DB::table('products')
                ->where('id', $id)
                ->where('tenant_id', $tenantId)
                ->update(['sort_order' => $i + 1, 'updated_at' => now()]);
        }

        return response()->json(['success' => true]);
    }
}
