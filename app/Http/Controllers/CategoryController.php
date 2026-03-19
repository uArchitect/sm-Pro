<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
        $tenantId  = session('tenant_id');

        $request->validate([
            'name'      => 'required|string|max:255',
            'parent_id' => 'nullable|integer',
            'image'     => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg',
        ]);

        $parentError = $this->validateParentCategory($tenantId, $request->parent_id ? (int) $request->parent_id : null);
        if ($parentError) {
            return back()->withErrors(['parent_id' => $parentError])->withInput();
        }

        $imagePath = null;

        try {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store("tenants/{$tenantId}/categories", 'uploads');
                if ($imagePath === false) {
                    return back()->withErrors(['image' => __('messages.upload_failed')])->withInput();
                }
            }

            DB::transaction(function () use ($tenantId, $request, $imagePath) {
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
            });
        } catch (\Throwable $e) {
            if ($imagePath) {
                Storage::disk('uploads')->delete($imagePath);
            }

            throw $e;
        }

        Log::info('Yeni kategori oluşturuldu.', ['tenant_id' => $tenantId, 'name' => $request->name]);

        return redirect()->route('categories.index')->with('success', __('messages.category_added'));
    }

    public function storeBulk(Request $request)
    {
        $request->validate([
            'names'     => 'required|array|min:1',
            'names.*'   => 'nullable|string|max:255',
            'parent_id' => 'nullable|integer',
        ], [], ['names.*' => __('categories.name')]);

        $names = array_values(array_filter(array_map(function ($n) {
            return is_string($n) ? trim($n) : '';
        }, (array) $request->names)));
        if (empty($names)) {
            return back()->withErrors(['names' => __('categories.bulk_at_least_one')])->withInput();
        }

        $tenantId = session('tenant_id');
        $parentId = $request->parent_id ? (int) $request->parent_id : null;
        $parentError = $this->validateParentCategory($tenantId, $parentId);
        if ($parentError) {
            return back()->withErrors(['parent_id' => $parentError])->withInput();
        }

        $maxOrder = DB::table('categories')->where('tenant_id', $tenantId)->max('sort_order') ?? 0;
        $now = now();
        $rows = [];
        foreach ($names as $i => $name) {
            if ($name === '') {
                continue;
            }
            $rows[] = [
                'tenant_id'  => $tenantId,
                'parent_id'  => $parentId,
                'name'       => $name,
                'image'      => null,
                'sort_order' => $maxOrder + $i + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        if (!empty($rows)) {
            DB::table('categories')->insert($rows);
        }
        $count = count($rows);
        $msg = $count === 1 ? __('messages.category_added') : __('categories.bulk_saved', ['count' => $count]);
        return redirect()->route('categories.index')->with('success', $msg);
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
        $tenantId = session('tenant_id');

        $request->validate([
            'name'      => 'required|string|max:255',
            'parent_id' => 'nullable|integer',
            'image'     => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg',
        ]);

        $category = DB::table('categories')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$category) {
            abort(404);
        }

        $parentId = $request->parent_id ?: null;
        $parentError = $this->validateParentCategory($tenantId, $parentId ? (int) $parentId : null, $id);
        if ($parentError) {
            return back()->withErrors(['parent_id' => $parentError])->withInput();
        }

        $data = [
            'name'       => $request->name,
            'parent_id'  => $parentId,
            'updated_at' => now(),
        ];

        $newImagePath = null;
        $oldImageToDelete = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $request->file('image')->store("tenants/{$tenantId}/categories", 'uploads');
                if ($newImagePath === false) {
                    return back()->withErrors(['image' => __('messages.upload_failed')])->withInput();
                }
                $data['image'] = $newImagePath;
                $oldImageToDelete = $category->image ?: null;
            } elseif ($request->boolean('remove_image') && $category->image) {
                $data['image'] = null;
                $oldImageToDelete = $category->image;
            }

            DB::transaction(function () use ($tenantId, $id, $data) {
                DB::table('categories')->where('id', $id)->where('tenant_id', $tenantId)->update($data);
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

        $categoryIds = DB::table('categories')
            ->where('tenant_id', $tenantId)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)->orWhere('parent_id', $id);
            })
            ->pluck('id');

        $categoryImages = DB::table('categories')
            ->whereIn('id', $categoryIds)
            ->whereNotNull('image')
            ->pluck('image')
            ->filter()
            ->all();

        $productImages = DB::table('products')
            ->where('tenant_id', $tenantId)
            ->whereIn('category_id', $categoryIds)
            ->whereNotNull('image')
            ->pluck('image')
            ->filter()
            ->all();

        DB::transaction(function () use ($tenantId, $categoryIds) {
            DB::table('products')
                ->where('tenant_id', $tenantId)
                ->whereIn('category_id', $categoryIds)
                ->delete();

            DB::table('categories')
                ->whereIn('id', $categoryIds)
                ->where('tenant_id', $tenantId)
                ->delete();
        });

        foreach (array_merge($categoryImages, $productImages) as $path) {
            Storage::disk('uploads')->delete($path);
        }

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

        $newImagePath = null;
        $oldImageToDelete = null;

        try {
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'file|mimes:jpg,jpeg,png,gif,webp,svg',
                ]);
                $newImagePath = $request->file('image')->store("tenants/{$tenantId}/categories", 'uploads');
                if ($newImagePath === false) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.upload_failed'),
                        'errors'  => ['image' => [__('messages.upload_failed')]],
                    ], 422);
                }
                $data['image'] = $newImagePath;
                $oldImageToDelete = $category->image ?: null;
            }

            DB::transaction(function () use ($tenantId, $id, $data) {
                DB::table('categories')->where('id', $id)->where('tenant_id', $tenantId)->update($data);
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

        $updated = DB::table('categories')->where('id', $id)->where('tenant_id', $tenantId)->first();
        if (!$updated) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        return response()->json([
            'success'   => true,
            'name'      => $updated->name,
            'image_url' => $updated->image ? asset('uploads/' . $updated->image) : null,
        ]);
    }

    /** AJAX: drag-drop reorder */
    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);

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

    private function validateParentCategory(int $tenantId, ?int $parentId, ?int $currentId = null): ?string
    {
        if (!$parentId) {
            return null;
        }

        if ($currentId && $parentId === $currentId) {
            return 'Kategori kendisine üst kategori olamaz.';
        }

        $parent = DB::table('categories')
            ->where('id', $parentId)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$parent) {
            return 'Geçersiz üst kategori.';
        }

        if ($parent->parent_id !== null) {
            return 'Sadece ana kategoriler üst kategori olabilir.';
        }

        if ($currentId && DB::table('categories')->where('tenant_id', $tenantId)->where('parent_id', $currentId)->exists()) {
            return 'Alt kategorisi olan bir kategori başka bir kategorinin altına taşınamaz.';
        }

        return null;
    }
}
