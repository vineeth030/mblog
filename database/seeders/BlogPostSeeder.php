<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        BlogPost::insert([
            [
                'title'          => 'Getting Started with Laravel',
                'description'    => 'A beginner-friendly introduction to the Laravel PHP framework.',
                'category'       => 'Tutorial',
                'author_name'    => 'admin',
                'content'        => 'Laravel is a web application framework with expressive, elegant syntax. It provides tools for routing, authentication, sessions, caching, and more out of the box. In this post we walk through installing Laravel, configuring your environment, and building your first route and view.',
                'publish_status' => true,
                'created_at'     => now()->subDays(10),
                'updated_at'     => now()->subDays(10),
            ],
            [
                'title'          => 'Mastering Vue 3 Composition API',
                'description'    => 'Deep dive into Vue 3 Composition API patterns and best practices.',
                'category'       => 'Vue',
                'author_name'    => 'admin',
                'content'        => 'The Composition API is the modern way to write Vue 3 components. It replaces the Options API with a flexible setup() function that lets you organise logic by feature rather than by option type. We cover ref, reactive, computed, watch, and how to extract reusable composables.',
                'publish_status' => true,
                'created_at'     => now()->subDays(5),
                'updated_at'     => now()->subDays(5),
            ],
            [
                'title'          => 'Building SPAs with Inertia.js',
                'description'    => 'How Inertia.js bridges the gap between server-side Laravel and client-side Vue.',
                'category'       => 'Inertia',
                'author_name'    => 'admin',
                'content'        => 'Inertia.js lets you build fully client-side rendered, single-page apps without building an API. You write controllers and return Inertia responses just like classic server-rendered apps, and Inertia handles the page transitions on the frontend using Vue components. No separate API layer needed.',
                'publish_status' => false,
                'created_at'     => now()->subDay(),
                'updated_at'     => now()->subDay(),
            ],
        ]);
    }
}
