<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title'          => 'Getting Started with Laravel',
                'description'    => 'A beginner-friendly introduction to the Laravel PHP framework.',
                'category'       => 'Tutorial',
                'author'         => 'admin',
                'content'        => 'Laravel is a web application framework with expressive, elegant syntax. It provides tools for routing, authentication, sessions, caching, and more out of the box. In this post we walk through installing Laravel, configuring your environment, and building your first route and view.',
                'publish_status' => true,
                'days_ago'       => 10,
            ],
            [
                'title'          => 'Mastering Vue 3 Composition API',
                'description'    => 'Deep dive into Vue 3 Composition API patterns and best practices.',
                'category'       => 'Vue',
                'author'         => 'admin',
                'content'        => 'The Composition API is the modern way to write Vue 3 components. It replaces the Options API with a flexible setup() function that lets you organise logic by feature rather than by option type. We cover ref, reactive, computed, watch, and how to extract reusable composables.',
                'publish_status' => true,
                'days_ago'       => 5,
            ],
            [
                'title'          => 'Building SPAs with Inertia.js',
                'description'    => 'How Inertia.js bridges the gap between server-side Laravel and client-side Vue.',
                'category'       => 'Inertia',
                'author'         => 'admin',
                'content'        => 'Inertia.js lets you build fully client-side rendered, single-page apps without building an API. You write controllers and return Inertia responses just like classic server-rendered apps, and Inertia handles the page transitions on the frontend using Vue components. No separate API layer needed.',
                'publish_status' => false,
                'days_ago'       => 1,
            ],
        ];

        foreach ($posts as $post) {
            $author = Author::firstOrCreate(['name' => $post['author']]);
            $category = Category::firstOrCreate(['name' => $post['category']]);

            BlogPost::create([
                'title'          => $post['title'],
                'slug'           => Str::slug($post['title']),
                'description'    => $post['description'],
                'category_id'    => $category->id,
                'author_id'      => $author->id,
                'content'        => $post['content'],
                'publish_status' => $post['publish_status'],
                'cover_image'    => null,
                'created_at'     => now()->subDays($post['days_ago']),
                'updated_at'     => now()->subDays($post['days_ago']),
            ]);
        }
    }
}
