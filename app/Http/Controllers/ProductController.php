<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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

        // View counts per product (tablo migration ile oluşturulur; öncesinde boş döner)
        $viewCounts = collect();
        if (Schema::hasTable('product_views')) {
            $viewCounts = DB::table('product_views')
                ->where('tenant_id', $tenantId)
                ->select('product_id', DB::raw('COUNT(*) as view_count'))
                ->groupBy('product_id')
                ->pluck('view_count', 'product_id');
        }

        return view('products.index', compact('products', 'categories', 'viewCounts'));
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
        $hasWeightColumn = Schema::hasColumn('products', 'weight_grams');
        $hasBaseWeightColumn = Schema::hasColumn('products', 'base_weight_grams');
        $hasExtraStepWeightColumn = Schema::hasColumn('products', 'extra_weight_step_grams');
        $hasExtraStepPriceColumn = Schema::hasColumn('products', 'extra_weight_step_price');

        $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'weight_grams'=> 'nullable|integer|min:1|max:100000',
            'base_weight_grams'       => 'nullable|integer|min:1|max:100000',
            'extra_weight_step_grams' => 'nullable|integer|min:1|max:100000|required_with:extra_weight_step_price',
            'extra_weight_step_price' => 'nullable|numeric|min:0.01|max:100000|required_with:extra_weight_step_grams',
            'image'       => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
        ]);

        $hasDynamicRuleInput = $request->filled('extra_weight_step_grams') || $request->filled('extra_weight_step_price');
        if ($hasDynamicRuleInput && !$request->filled('base_weight_grams')) {
            throw ValidationException::withMessages([
                'base_weight_grams' => [__('products.base_weight_required_with_rule')],
            ]);
        }

        $tenantId = session('tenant_id');

        if (!DB::table('categories')->where('id', $request->category_id)->where('tenant_id', $tenantId)->exists()) {
            abort(403);
        }

        $imagePath = null;
        $productId = null;

        try {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store("tenants/{$tenantId}/products", 'uploads');
                if ($imagePath === false) {
                    return back()->withErrors(['image' => __('messages.upload_failed')])->withInput();
                }
            }

            DB::transaction(function () use ($tenantId, $request, $imagePath, &$productId, $hasWeightColumn, $hasBaseWeightColumn, $hasExtraStepWeightColumn, $hasExtraStepPriceColumn) {
                $maxOrder = DB::table('products')->where('tenant_id', $tenantId)->max('sort_order') ?? 0;

                $insertData = [
                    'tenant_id'   => $tenantId,
                    'category_id' => $request->category_id,
                    'name'        => $request->name,
                    'description' => $request->description,
                    'price'       => $request->price,
                    'image'       => $imagePath,
                    'sort_order'  => $maxOrder + 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];

                if ($hasWeightColumn) {
                    $insertData['weight_grams'] = $request->filled('weight_grams') ? (int) $request->weight_grams : null;
                }
                if ($hasBaseWeightColumn) {
                    $insertData['base_weight_grams'] = $request->filled('base_weight_grams') ? (int) $request->base_weight_grams : null;
                }
                if ($hasExtraStepWeightColumn) {
                    $insertData['extra_weight_step_grams'] = $request->filled('extra_weight_step_grams') ? (int) $request->extra_weight_step_grams : null;
                }
                if ($hasExtraStepPriceColumn) {
                    $insertData['extra_weight_step_price'] = $request->filled('extra_weight_step_price') ? (float) $request->extra_weight_step_price : null;
                }

                $productId = DB::table('products')->insertGetId($insertData);
            });
        } catch (\Throwable $e) {
            if ($imagePath) {
                Storage::disk('uploads')->delete($imagePath);
            }

            throw $e;
        }

        Log::info('Yeni ürün eklendi.', ['tenant_id' => $tenantId, 'product_id' => $productId]);

        return redirect()->route('products.index')->with('success', __('messages.product_added'));
    }

    public function storeBulk(Request $request)
    {
        $hasWeightColumn = Schema::hasColumn('products', 'weight_grams');
        $hasBaseWeightColumn = Schema::hasColumn('products', 'base_weight_grams');
        $hasExtraStepWeightColumn = Schema::hasColumn('products', 'extra_weight_step_grams');
        $hasExtraStepPriceColumn = Schema::hasColumn('products', 'extra_weight_step_price');

        $request->validate([
            'products' => 'required|array|min:1',
            'products.*.category_id' => 'nullable|integer',
            'products.*.name'        => 'nullable|string|max:255',
            'products.*.description' => 'nullable|string|max:5000',
            'products.*.price'       => 'nullable|numeric|min:0',
            'products.*.weight_grams'=> 'nullable|integer|min:1|max:100000',
            'products.*.base_weight_grams'       => 'nullable|integer|min:1|max:100000',
            'products.*.extra_weight_step_grams' => 'nullable|integer|min:1|max:100000|required_with:products.*.extra_weight_step_price',
            'products.*.extra_weight_step_price' => 'nullable|numeric|min:0.01|max:100000|required_with:products.*.extra_weight_step_grams',
        ], [], [
            'products.*.category_id' => __('products.category'),
            'products.*.name'        => __('products.name'),
            'products.*.price'       => __('products.price_tl'),
            'products.*.weight_grams'=> __('products.weight_grams'),
            'products.*.base_weight_grams'       => __('products.base_weight_grams'),
            'products.*.extra_weight_step_grams' => __('products.extra_weight_step_grams'),
            'products.*.extra_weight_step_price' => __('products.extra_weight_step_price'),
        ]);

        foreach (($request->products ?? []) as $rowIdx => $row) {
            $hasRule = !empty($row['extra_weight_step_grams']) || !empty($row['extra_weight_step_price']);
            if ($hasRule && empty($row['base_weight_grams'])) {
                throw ValidationException::withMessages([
                    "products.{$rowIdx}.base_weight_grams" => [__('products.base_weight_required_with_rule')],
                ]);
            }
        }

        $tenantId = session('tenant_id');
        $products = array_values(array_filter($request->products, function ($p) {
            return is_array($p) && !empty(trim((string) ($p['name'] ?? '')));
        }));

        if (empty($products)) {
            return back()->withErrors(['products' => __('products.bulk_at_least_one')])->withInput();
        }

        $categoryIds = DB::table('categories')->where('tenant_id', $tenantId)->pluck('id')->toArray();

        $inserted = 0;
        DB::transaction(function () use ($tenantId, $products, $categoryIds, &$inserted, $hasWeightColumn, $hasBaseWeightColumn, $hasExtraStepWeightColumn, $hasExtraStepPriceColumn) {
            $maxOrder = DB::table('products')->where('tenant_id', $tenantId)->max('sort_order') ?? 0;
            $now = now();
            $rows = [];
            foreach ($products as $i => $p) {
                $catId = (int) ($p['category_id'] ?? 0);
                if (!in_array($catId, $categoryIds, true)) {
                    continue;
                }
                $row = [
                    'tenant_id'   => $tenantId,
                    'category_id' => $catId,
                    'name'        => trim($p['name']),
                    'description' => isset($p['description']) ? trim($p['description']) : null,
                    'price'       => (float) ($p['price'] ?? 0),
                    'image'       => null,
                    'sort_order'  => $maxOrder + count($rows) + 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
                if ($hasWeightColumn) {
                    $row['weight_grams'] = !empty($p['weight_grams']) ? (int) $p['weight_grams'] : null;
                }
                if ($hasBaseWeightColumn) {
                    $row['base_weight_grams'] = !empty($p['base_weight_grams']) ? (int) $p['base_weight_grams'] : null;
                }
                if ($hasExtraStepWeightColumn) {
                    $row['extra_weight_step_grams'] = !empty($p['extra_weight_step_grams']) ? (int) $p['extra_weight_step_grams'] : null;
                }
                if ($hasExtraStepPriceColumn) {
                    $row['extra_weight_step_price'] = !empty($p['extra_weight_step_price']) ? (float) $p['extra_weight_step_price'] : null;
                }
                $rows[] = $row;
            }
            if (!empty($rows)) {
                DB::table('products')->insert($rows);
                $inserted = count($rows);
            }
        });

        if ($inserted === 0) {
            return back()->withErrors(['products' => __('products.bulk_no_valid_category')])->withInput();
        }

        $msg = $inserted === 1 ? __('messages.product_added') : __('products.bulk_saved', ['count' => $inserted]);
        return redirect()->route('products.index')->with('success', $msg);
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
        $hasWeightColumn = Schema::hasColumn('products', 'weight_grams');
        $hasBaseWeightColumn = Schema::hasColumn('products', 'base_weight_grams');
        $hasExtraStepWeightColumn = Schema::hasColumn('products', 'extra_weight_step_grams');
        $hasExtraStepPriceColumn = Schema::hasColumn('products', 'extra_weight_step_price');

        $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'weight_grams'=> 'nullable|integer|min:1|max:100000',
            'base_weight_grams'       => 'nullable|integer|min:1|max:100000',
            'extra_weight_step_grams' => 'nullable|integer|min:1|max:100000|required_with:extra_weight_step_price',
            'extra_weight_step_price' => 'nullable|numeric|min:0.01|max:100000|required_with:extra_weight_step_grams',
            'image'       => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
        ]);

        $hasDynamicRuleInput = $request->filled('extra_weight_step_grams') || $request->filled('extra_weight_step_price');
        if ($hasDynamicRuleInput && !$request->filled('base_weight_grams')) {
            throw ValidationException::withMessages([
                'base_weight_grams' => [__('products.base_weight_required_with_rule')],
            ]);
        }

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
        if ($hasWeightColumn) {
            $data['weight_grams'] = $request->filled('weight_grams') ? (int) $request->weight_grams : null;
        }
        if ($hasBaseWeightColumn) {
            $data['base_weight_grams'] = $request->filled('base_weight_grams') ? (int) $request->base_weight_grams : null;
        }
        if ($hasExtraStepWeightColumn) {
            $data['extra_weight_step_grams'] = $request->filled('extra_weight_step_grams') ? (int) $request->extra_weight_step_grams : null;
        }
        if ($hasExtraStepPriceColumn) {
            $data['extra_weight_step_price'] = $request->filled('extra_weight_step_price') ? (float) $request->extra_weight_step_price : null;
        }

        $newImagePath = null;
        $oldImageToDelete = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $request->file('image')->store("tenants/{$tenantId}/products", 'uploads');
                if ($newImagePath === false) {
                    return back()->withErrors(['image' => __('messages.upload_failed')])->withInput();
                }
                $data['image'] = $newImagePath;
                $oldImageToDelete = $product->image ?: null;
            } elseif ($request->boolean('remove_image') && $product->image) {
                $data['image'] = null;
                $oldImageToDelete = $product->image;
            }

            DB::transaction(function () use ($tenantId, $id, $data) {
                DB::table('products')->where('id', $id)->where('tenant_id', $tenantId)->update($data);
            });

            if ($oldImageToDelete) {
                Storage::disk('uploads')->delete($oldImageToDelete);
            }
        } catch (\Throwable $e) {
            if ($newImagePath) {
                Storage::disk('uploads')->delete($newImagePath);
            }

            throw $e;
        }

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

        DB::transaction(function () use ($tenantId, $id) {
            DB::table('products')->where('id', $id)->where('tenant_id', $tenantId)->delete();
        });

        if ($product->image) {
            Storage::disk('uploads')->delete($product->image);
        }

        Log::info('Ürün silindi.', ['tenant_id' => $tenantId, 'product_id' => $id]);

        return redirect()->route('products.index')->with('success', __('messages.product_deleted'));
    }

    /** AJAX: inline update (name, description, price, image, category) */
    public function inlineUpdate(Request $request, int $id)
    {
        $tenantId = session('tenant_id');
        $hasWeightColumn = Schema::hasColumn('products', 'weight_grams');
        $hasBaseWeightColumn = Schema::hasColumn('products', 'base_weight_grams');
        $hasExtraStepWeightColumn = Schema::hasColumn('products', 'extra_weight_step_grams');
        $hasExtraStepPriceColumn = Schema::hasColumn('products', 'extra_weight_step_price');
        $product  = DB::table('products')->where('id', $id)->where('tenant_id', $tenantId)->first();

        if (!$product) {
            return response()->json(['error' => 'Bulunamadı.'], 404);
        }

        $data = ['updated_at' => now()];

        if ($request->filled('name')) {
            $data['name'] = substr(strip_tags($request->name), 0, 255);
        }
        if ($request->has('description')) {
            $data['description'] = $request->description
                ? substr(strip_tags($request->description), 0, 1000)
                : null;
        }
        if ($request->filled('price')) {
            $data['price'] = max(0, (float) $request->price);
        }
        if ($hasWeightColumn && $request->has('weight_grams')) {
            $grams = trim((string) $request->weight_grams);
            if ($grams === '') {
                $data['weight_grams'] = null;
            } else {
                $data['weight_grams'] = max(1, min(100000, (int) $grams));
            }
        }
        if ($hasBaseWeightColumn && $request->has('base_weight_grams')) {
            $base = trim((string) $request->base_weight_grams);
            $data['base_weight_grams'] = $base === '' ? null : max(1, min(100000, (int) $base));
        }
        if ($hasExtraStepWeightColumn && $request->has('extra_weight_step_grams')) {
            $step = trim((string) $request->extra_weight_step_grams);
            $data['extra_weight_step_grams'] = $step === '' ? null : max(1, min(100000, (int) $step));
        }
        if ($hasExtraStepPriceColumn && $request->has('extra_weight_step_price')) {
            $delta = trim((string) $request->extra_weight_step_price);
            $data['extra_weight_step_price'] = $delta === '' ? null : max(0.01, min(100000, (float) $delta));
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

        $newImagePath = null;
        $oldImageToDelete = null;

        try {
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'file|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
                ]);
                $newImagePath = $request->file('image')->store("tenants/{$tenantId}/products", 'uploads');
                if ($newImagePath === false) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.upload_failed'),
                        'errors'  => ['image' => [__('messages.upload_failed')]],
                    ], 422);
                }
                $data['image'] = $newImagePath;
                $oldImageToDelete = $product->image ?: null;
            } elseif ($request->boolean('remove_image') && $product->image) {
                $data['image'] = null;
                $oldImageToDelete = $product->image;
            }

            DB::transaction(function () use ($tenantId, $id, $data) {
                DB::table('products')->where('id', $id)->where('tenant_id', $tenantId)->update($data);
            });

            if ($oldImageToDelete) {
                Storage::disk('uploads')->delete($oldImageToDelete);
            }
        } catch (ValidationException $e) {
            if ($newImagePath ?? null) {
                Storage::disk('uploads')->delete($newImagePath);
            }
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            if ($newImagePath) {
                Storage::disk('uploads')->delete($newImagePath);
            }

            throw $e;
        }

        $updated = DB::table('products')->where('id', $id)->where('tenant_id', $tenantId)->first();
        if (!$updated) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }
        $category = DB::table('categories')->where('id', $updated->category_id)->where('tenant_id', $tenantId)->first();

        return response()->json([
            'success'           => true,
            'name'              => $updated->name,
            'description'       => $updated->description,
            'description_short' => $updated->description ? Str::limit($updated->description, 60) : null,
            'price'             => number_format($updated->price, 2, ',', '.'),
            'raw_price'         => $updated->price,
            'weight_grams'      => $hasWeightColumn ? ($updated->weight_grams ?? null) : null,
            'base_weight_grams' => $hasBaseWeightColumn ? ($updated->base_weight_grams ?? null) : null,
            'extra_weight_step_grams' => $hasExtraStepWeightColumn ? ($updated->extra_weight_step_grams ?? null) : null,
            'extra_weight_step_price' => $hasExtraStepPriceColumn ? ($updated->extra_weight_step_price ?? null) : null,
            'image_url'         => $updated->image ? asset('uploads/' . $updated->image) : null,
            'category_name'     => $category->name ?? '',
        ]);
    }

    /** Ürünü kopyala (fotoğraf hariç) */
    public function duplicate(int $id)
    {
        $tenantId = session('tenant_id');
        $hasWeightColumn = Schema::hasColumn('products', 'weight_grams');
        $hasBaseWeightColumn = Schema::hasColumn('products', 'base_weight_grams');
        $hasExtraStepWeightColumn = Schema::hasColumn('products', 'extra_weight_step_grams');
        $hasExtraStepPriceColumn = Schema::hasColumn('products', 'extra_weight_step_price');
        $product  = DB::table('products')->where('id', $id)->where('tenant_id', $tenantId)->first();

        if (!$product) {
            abort(404);
        }

        $maxOrder = DB::table('products')->where('tenant_id', $tenantId)->max('sort_order') ?? 0;

        $insertData = [
            'tenant_id'   => $tenantId,
            'category_id' => $product->category_id,
            'name'        => $product->name . ' ' . __('products.copy_suffix'),
            'description' => $product->description,
            'price'       => $product->price,
            'image'       => null,
            'sort_order'  => $maxOrder + 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
        if ($hasWeightColumn) {
            $insertData['weight_grams'] = $product->weight_grams ?? null;
        }
        if ($hasBaseWeightColumn) {
            $insertData['base_weight_grams'] = $product->base_weight_grams ?? null;
        }
        if ($hasExtraStepWeightColumn) {
            $insertData['extra_weight_step_grams'] = $product->extra_weight_step_grams ?? null;
        }
        if ($hasExtraStepPriceColumn) {
            $insertData['extra_weight_step_price'] = $product->extra_weight_step_price ?? null;
        }
        DB::table('products')->insert($insertData);

        return redirect()->route('products.index')->with('success', __('products.duplicated'));
    }

    /** AJAX: stok durumu toggle (is_available) */
    public function toggleAvailability(int $id): \Illuminate\Http\JsonResponse
    {
        $tenantId = session('tenant_id');
        $product  = DB::table('products')->where('id', $id)->where('tenant_id', $tenantId)->first();

        if (!$product) {
            return response()->json(['error' => 'Bulunamadı.'], 404);
        }

        $newValue = !$product->is_available;
        DB::table('products')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->update(['is_available' => $newValue, 'updated_at' => now()]);

        return response()->json(['success' => true, 'is_available' => $newValue]);
    }

    /** AJAX: drag-drop reorder */
    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);

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
