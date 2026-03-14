<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DeveloperBlogController extends Controller
{
    public function index()
    {
        $posts = DB::table('blog_posts')
            ->leftJoin('users', 'blog_posts.author_id', '=', 'users.id')
            ->select('blog_posts.*', 'users.name as author_name')
            ->orderByDesc('blog_posts.created_at')
            ->get();

        return view('developer.blog.index', compact('posts'));
    }

    public function create()
    {
        return view('developer.blog.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:255',
            'body'             => 'required|string',
            'slug'             => 'nullable|string|max:255|unique:blog_posts,slug',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'featured_image'   => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'is_published'     => 'nullable|boolean',
        ], [], ['featured_image' => 'öne çıkan görsel']);

        $slug = $request->slug ?: Str::slug($request->title);
        $slug = $this->ensureUniqueSlug($slug);

        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('blog', 'public');
            if ($imagePath === false) {
                return back()->withErrors(['featured_image' => 'Görsel yüklenemedi.'])->withInput();
            }
        }

        $publishedAt = ($request->boolean('is_published')) ? now() : null;

        DB::table('blog_posts')->insert([
            'slug'             => $slug,
            'title'            => $request->title,
            'meta_title'       => $request->meta_title ?: null,
            'meta_description' => $request->meta_description ?: null,
            'body'             => $request->body,
            'featured_image'   => $imagePath,
            'is_published'     => $request->boolean('is_published'),
            'published_at'     => $publishedAt,
            'author_id'        => Auth::id(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return redirect()->route('developer.blog.index')->with('success', 'Yazı oluşturuldu.');
    }

    public function edit(int $id)
    {
        $post = DB::table('blog_posts')->where('id', $id)->first();
        if (!$post) abort(404);

        return view('developer.blog.edit', compact('post'));
    }

    public function update(Request $request, int $id)
    {
        $post = DB::table('blog_posts')->where('id', $id)->first();
        if (!$post) abort(404);

        $request->validate([
            'title'   => 'required|string|max:255',
            'body'    => 'required|string',
            'slug'    => 'nullable|string|max:255|unique:blog_posts,slug,' . $id,
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_published'     => 'nullable|boolean',
        ]);

        $slug = $request->slug ?: Str::slug($request->title);
        if ($slug !== $post->slug) {
            $slug = $this->ensureUniqueSlug($slug, $id);
        }

        $data = [
            'slug'             => $slug,
            'title'            => $request->title,
            'meta_title'       => $request->meta_title ?: null,
            'meta_description' => $request->meta_description ?: null,
            'body'             => $request->body,
            'is_published'     => $request->boolean('is_published'),
            'updated_at'       => now(),
        ];

        if ($request->boolean('is_published') && !$post->published_at) {
            $data['published_at'] = now();
        } elseif (!$request->boolean('is_published')) {
            $data['published_at'] = null;
        }

        $newImagePath = $this->saveFeaturedImage($request->file('featured_image'));
        if ($newImagePath !== null) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = $newImagePath;
        }

        DB::table('blog_posts')->where('id', $id)->update($data);

        return redirect()->route('developer.blog.index')->with('success', 'Yazı güncellendi.');
    }

    public function destroy(int $id)
    {
        $post = DB::table('blog_posts')->where('id', $id)->first();
        if (!$post) abort(404);

        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        DB::table('blog_posts')->where('id', $id)->delete();

        return redirect()->route('developer.blog.index')->with('success', 'Yazı silindi.');
    }

    private function ensureUniqueSlug(string $slug, ?int $excludeId = null): string
    {
        $base = $slug;
        $i = 0;
        while (true) {
            $q = DB::table('blog_posts')->where('slug', $slug);
            if ($excludeId) $q->where('id', '!=', $excludeId);
            if (!$q->exists()) return $slug;
            $slug = $base . '-' . (++$i);
        }
    }

    /**
     * Öne çıkan görseli kaydet. Doğrulama yok, tüm uzantılar kabul.
     * Dosya yoksa veya okunamazsa null döner.
     */
    private function saveFeaturedImage($file): ?string
    {
        if (!$file) {
            return null;
        }

        $originalName = $file->getClientOriginalName();
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($ext === '') {
            $ext = 'svg';
        }
        $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'svg';
        $filename = Str::uuid() . '.' . $ext;
        $path = 'blog/' . $filename;

        try {
            $realPath = $file->getRealPath();
            if ($realPath && is_readable($realPath)) {
                $contents = file_get_contents($realPath);
                if ($contents !== false && Storage::disk('public')->put($path, $contents)) {
                    return $path;
                }
            }
            if ($file->isValid()) {
                $stored = $file->storeAs('blog', $filename, 'public');
                if ($stored) {
                    return $stored;
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return null;
    }
}
