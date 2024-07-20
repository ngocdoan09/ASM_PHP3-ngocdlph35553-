<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Image;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->approved()->withCount('comments')->paginate(8);
        $recent_posts = Post::latest()->take(5)->get();
        $categories = Category::where('name', '!=', 'Chưa phân loại')->orderBy('created_at', 'DESC')->take(10)->get();
        $tags = Tag::latest()->take(50)->get();

        $category_unclassified = Category::where('name', 'Chưa phân loại')->first();

        if ($category_unclassified) {
            $posts_new = [];
            $posts_new[0] = Post::latest()->approved()->where('category_id', '!=', $category_unclassified->id)->take(1)->get();

            if ($posts_new[0]->isNotEmpty()) {
                $posts_new[1] = Post::latest()->approved()
                    ->where('category_id', '!=', $category_unclassified->id)
                    ->where('category_id', '!=', $posts_new[0][0]->category->id)
                    ->take(1)->get();
            }

            if (!empty($posts_new[1]) && $posts_new[1]->isNotEmpty()) {
                $posts_new[2] = Post::latest()->approved()
                    ->where('category_id', '!=', $category_unclassified->id)
                    ->where('category_id', '!=', $posts_new[0][0]->category->id)
                    ->where('category_id', '!=', $posts_new[1][0]->category->id)
                    ->take(1)->get();
            }

            if (!empty($posts_new[2]) && $posts_new[2]->isNotEmpty()) {
                $posts_new[3] = Post::latest()->approved()
                    ->where('category_id', '!=', $category_unclassified->id)
                    ->where('category_id', '!=', $posts_new[0][0]->category->id)
                    ->where('category_id', '!=', $posts_new[1][0]->category->id)
                    ->where('category_id', '!=', $posts_new[2][0]->category->id)
                    ->take(1)->get();
            }
        } else {
            // Xử lý khi không tìm thấy danh mục "Chưa phân loại"
            $posts_new = [];
        }

        // Lấy ra tin nổi bật -- Lấy theo views
        $outstanding_posts = Post::orderBy('views', 'DESC')->take(5)->get();

        // Lấy ra tất cả danh mục tin tức
        $stt_home = 0;
        $category_home = Category::where('name', '!=', 'Chưa phân loại')->orderBy('created_at', 'DESC')->take(10)->get();

        // Khởi tạo mảng để chứa dữ liệu
        $post_categories = [];

        foreach ($category_home as $category_item) {
            // Tạo tin tức mới nhất cho layout master
            $stt_home++;
            $post_categories[$stt_home - 1] = Post::latest()->approved()->withCount('comments')->where('category_id', $category_item->id)->take(5)->get();
        }

        // Truyền dữ liệu vào view
        return view('home', [
            'posts' => $posts,
            'recent_posts' => $recent_posts,
            'categories' => $categories,
            'tags' => $tags,
            'posts_new' => $posts_new,
            'outstanding_posts' => $outstanding_posts,
            'post_categories' => $post_categories
        ]);
    }
    public function search(Request $request)
    {

        $recent_posts = Post::latest()->take(5)->get();
        $categories = Category::where('name', '!=', 'Chưa phân loại')->withCount('posts')->orderBy('created_at', 'DESC')->take(10)->get();

        /*----- Lấy ra 4 bài viết mới nhất theo các danh mục khác nhau -----*/
        $category_unclassified = Category::where('name', 'Chưa phân loại')->first();
        $posts_new[0] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->take(1)->get();
        $posts_new[1] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->where('category_id', '!=', $posts_new[0][0]->category->id)
            ->take(1)->get();
        $posts_new[2] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->where('category_id', '!=', $posts_new[0][0]->category->id)
            ->where('category_id', '!=', $posts_new[1][0]->category->id)
            ->take(1)->get();
        $posts_new[3] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->where('category_id', '!=', $posts_new[0][0]->category->id)
            ->where('category_id', '!=', $posts_new[1][0]->category->id)
            ->where('category_id', '!=', $posts_new[2][0]->category->id)
            ->take(1)->get();

        // Bài viết nổi bật
        $outstanding_posts = Post::approved()->where('category_id', '!=', $category_unclassified->id)->take(5)->get();

        $key = $request->search;
        // tìm kiếm kết quả danh mục
        // $cat = Category::where('name','like' , '%'.$key.'%')->first();
        // $pro = Category::where('name','like' , '%'.$key.'%')->first();

        $posts = Post::latest()->withCount('comments')->approved()->where('title', 'like', '%' . $key . '%')->paginate(30);

        $title = 'Kết quả tìm kiếm';
        $title_t = 'Kết quả tìm kiếm theo';
        $time = '(0,36 giây) ';

        return view('search', compact('posts', 'title', 'time', 'recent_posts', 'categories', 'key', 'posts_new', 'outstanding_posts'));
    }

    public function newPost()
    {

        // Bài viết mới nhất
        $recent_posts = Post::latest()->take(5)->get();
        $categories = Category::where('name', '!=', 'Chưa phân loại')->withCount('posts')->orderBy('created_at', 'DESC')->take(10)->get();

        /*----- Lấy ra 4 bài viết mới nhất theo các danh mục khác nhau -----*/
        $category_unclassified = Category::where('name', 'Chưa phân loại')->first();
        $posts_new[0] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->take(1)->get();
        $posts_new[1] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->where('category_id', '!=', $posts_new[0][0]->category->id)
            ->take(1)->get();
        $posts_new[2] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->where('category_id', '!=', $posts_new[0][0]->category->id)
            ->where('category_id', '!=', $posts_new[1][0]->category->id)
            ->take(1)->get();
        $posts_new[3] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->where('category_id', '!=', $posts_new[0][0]->category->id)
            ->where('category_id', '!=', $posts_new[1][0]->category->id)
            ->where('category_id', '!=', $posts_new[2][0]->category->id)
            ->take(1)->get();

        // Bài viết nổi bật
        $outstanding_posts = Post::approved()->where('category_id', '!=', $category_unclassified->id)->take(5)->get();


        // Bài viết mới nhất
        $newPosts_category = Post::latest()->approved()->where('category_id', '!=', $category_unclassified->id)->take(20)->get();

        return view(
            'newPost',
            compact(
                'recent_posts',
                'categories',
                'posts_new',
                'outstanding_posts',
                'newPosts_category'
            )
        );
    }

    public function hotPost()
    {

        // Bài viết mới nhất
        $recent_posts = Post::latest()->take(5)->get();
        $categories = Category::where('name', '!=', 'Chưa phân loại')->withCount('posts')->orderBy('created_at', 'DESC')->take(10)->get();

        /*----- Lấy ra 4 bài viết mới nhất theo các danh mục khác nhau -----*/
        $category_unclassified = Category::where('name', 'Chưa phân loại')->first();
        $posts_new[0] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->take(1)->get();
        $posts_new[1] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->where('category_id', '!=', $posts_new[0][0]->category->id)
            ->take(1)->get();
        $posts_new[2] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->where('category_id', '!=', $posts_new[0][0]->category->id)
            ->where('category_id', '!=', $posts_new[1][0]->category->id)
            ->take(1)->get();
        $posts_new[3] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->where('category_id', '!=', $posts_new[0][0]->category->id)
            ->where('category_id', '!=', $posts_new[1][0]->category->id)
            ->where('category_id', '!=', $posts_new[2][0]->category->id)
            ->take(1)->get();

        // Bài viết nổi bật
        $outstanding_posts = Post::approved()->where('category_id', '!=', $category_unclassified->id)->take(5)->get();


        // Bài viết mới nhất
        $category_phap_luat = Category::where('name', 'Pháp luật')->first();
        $category_kinh_te = Category::where('name', 'Kinh tế')->first();
        $category_xa_hoi = Category::where('name', 'Xã hội')->first();
        $category_khoa_hoc = Category::where('name', 'Khoa học')->first();
        $category_the_gioi = Category::where('name', 'Thế giới')->first();

        $hotPosts_category[0] = Post::approved()->where('category_id', $category_phap_luat->id)->orderBy('created_at', 'DESC')->take(4)->get();
        $hotPosts_category[1] = Post::approved()->where('category_id', $category_kinh_te->id)->orderBy('created_at', 'DESC')->take(4)->get();
        $hotPosts_category[2] = Post::approved()->where('category_id', $category_xa_hoi->id)->orderBy('created_at', 'DESC')->take(4)->get();
        $hotPosts_category[3] = Post::approved()->where('category_id', $category_khoa_hoc->id)->orderBy('created_at', 'DESC')->take(4)->get();
        $hotPosts_category[4] = Post::approved()->where('category_id', $category_the_gioi->id)->orderBy('created_at', 'DESC')->take(4)->get();

        return view(
            'hotPost',
            compact(
                'recent_posts',
                'categories',
                'posts_new',
                'outstanding_posts',
                'hotPosts_category'
            )
        );
    }

    public function viewPost()
    {

        // Bài viết mới nhất
        $recent_posts = Post::latest()->take(5)->get();
        $categories = Category::where('name', '!=', 'Chưa phân loại')->withCount('posts')->orderBy('created_at', 'DESC')->take(10)->get();

        /*----- Lấy ra 4 bài viết mới nhất theo các danh mục khác nhau -----*/
        $category_unclassified = Category::where('name', 'Chưa phân loại')->first();
        $posts_new[0] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->take(1)->get();
        $posts_new[1] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->where('category_id', '!=', $posts_new[0][0]->category->id)
            ->take(1)->get();
        $posts_new[2] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->where('category_id', '!=', $posts_new[0][0]->category->id)
            ->where('category_id', '!=', $posts_new[1][0]->category->id)
            ->take(1)->get();
        $posts_new[3] = Post::latest()->approved()
            ->where('category_id', '!=', $category_unclassified->id)
            ->where('category_id', '!=', $posts_new[0][0]->category->id)
            ->where('category_id', '!=', $posts_new[1][0]->category->id)
            ->where('category_id', '!=', $posts_new[2][0]->category->id)
            ->take(1)->get();

        // Bài viết nổi bật
        $outstanding_posts = Post::approved()->where('category_id', '!=', $category_unclassified->id)->take(5)->get();

        // Bài viết mới nhất
        $viewPosts_category = Post::approved()->where('category_id', '!=', $category_unclassified->id)->orderBy('views', 'DESC')->take(20)->get();

        return view(
            'viewPost',
            compact(
                'recent_posts',
                'categories',
                'posts_new',
                'outstanding_posts',
                'viewPosts_category'
            )
        );
    }

    private $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'image' => 'nullable|file|mimes:jpg,png,webp,svg,jpeg|dimensions:max-width:300,max-height:300',
    ];

}
