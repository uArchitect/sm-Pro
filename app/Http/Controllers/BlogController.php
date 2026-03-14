<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function index()
    {
        $posts = DB::table('blog_posts')
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('blog.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = DB::table('blog_posts')
            ->leftJoin('users', 'blog_posts.author_id', '=', 'users.id')
            ->where('blog_posts.slug', $slug)
            ->where('blog_posts.is_published', true)
            ->whereNotNull('blog_posts.published_at')
            ->where('blog_posts.published_at', '<=', now())
            ->select('blog_posts.*', 'users.name as author_name')
            ->first();

        if (!$post) abort(404);

        return view('blog.show', compact('post'));
    }
}
