<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function show(Post $post)
    {

        $recent_posts = Post::latest()->take(5)->get();
        $categories = Category::where('name', '!=', 'Chưa phân loại')
            ->withCount('posts')
            ->orderBy('created_at', 'DESC')
            ->take(10)->get();
        $tags = Tag::latest()->take(50)->get();

        $category_home = Category::where('name', '!=', 'Chưa phân loại')->take(1)->get();
        $post_category_home0 = Post::where('category_id', $category_home[0]->id)->latest()->take(1)->get();

        /*----- Lấy ra 4 bài viết mới nhất theo các danh mục khác nhau -----*/
        $category_unclassified = Category::where('name', 'Chưa phân loại')->first();
        if (!$category_unclassified) {
            return response()->json(['error' => 'Category "Chưa phân loại" not found'], 404);
        }

        $posts_new = [];
        $posts_new[0] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->first();
        if (!$posts_new[0]) {
            return response()->json(['error' => 'No approved posts found'], 404);
        }

        $posts_new[1] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->where('category_id', '!=', $posts_new[0]->category->id)
            ->first();
        if (!$posts_new[1]) {
            return response()->json(['error' => 'Not enough approved posts found'], 404);
        }

        $posts_new[2] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->where('category_id', '!=', $posts_new[0]->category->id)
            ->where('category_id', '!=', $posts_new[1]->category->id)
            ->first();
        if (!$posts_new[2]) {
            return response()->json(['error' => 'Not enough approved posts found'], 404);
        }

        $posts_new[3] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->where('category_id', '!=', $posts_new[0]->category->id)
            ->where('category_id', '!=', $posts_new[1]->category->id)
            ->where('category_id', '!=', $posts_new[2]->category->id)
            ->first();
        if (!$posts_new[3]) {
            return response()->json(['error' => 'Not enough approved posts found'], 404);
        }
        // Bài viết tương tự
        $postTheSame = Post::latest()->approved()
            ->where('category_id', $post->category->id)
            ->where('id', '!=', $post->id)
            ->take(5)->get();

        // Bài viết nổi bật
        $outstanding_posts = Post::approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->take(5)->get();
        // Tăng lượt xem khi xem bài viết
        $post->views = ($post->views) + 1;
        $post->save();

        return view('post', [
            'post' => $post,
            'recent_posts' => $recent_posts,
            'categories' => $categories,
            'tags' => $tags,
            'category_home' => $category_home,
            'post_category_home0' => $post_category_home0, // Truyền biến này vào view
            'posts_new' => $posts_new,
            'postTheSame' => $postTheSame,
            'outstanding_posts' => $outstanding_posts,
        ]);
    }
}
