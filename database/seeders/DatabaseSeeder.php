<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;

class DatabaseSeeder extends Seeder
{

    public function run()
    {

        $users = \App\Models\User::factory(10)->create();
        \App\Models\User::Factory()->create([
            'name' => 'Doãn Lâm Ngọc',
            'email' => 'doanlamngoc004@gmail.com'
        ]);

        foreach ($users as $user) {
            $user->image()->save(\App\Models\Image::factory()->make());
        }

        \App\Models\Category::factory(10)->create();
        //\App\Models\Category::factory()->create(['name' => 'Chưa phân loại']);
        $Category_defaules = [
            'Thế giới',
            'Xã hội',
            'Kinh tế',
            'Văn hóa',
            'Giáo dục',
            'Thể thao',
            'Giải trí',
            'Pháp luật',
            'Công nghệ',
            'Khoa học',
            'Đời sống',
        ];
        foreach ($Category_defaules as $Category_defaule) {
            \App\Models\Category::factory()->create(['name' => $Category_defaule]);
        }

        $posts = \App\Models\Post::factory(200)->create();

        \App\Models\Comment::factory(100)->create();

        \App\Models\Tag::factory(20)->create();

        foreach ($posts as $post) {
            $tag_ids = [];

            $tag_ids = \App\Models\Tag::all()->random()->id;
            $tag_ids = \App\Models\Tag::all()->random()->id;
            $tag_ids = \App\Models\Tag::all()->random()->id;

            $post->tags()->sync($tag_ids);
            $post->image()->save(\App\Models\Image::factory()->make());
        }


    }
}
