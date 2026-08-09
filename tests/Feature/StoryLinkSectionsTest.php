<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoryLinkSectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_page_fills_all_three_link_sections_without_duplicates(): void
    {
        $category = Category::create(['name' => 'Romance']);
        $author = Author::create(['name' => 'Priya']);

        $main = BlogPost::create([
            'title' => 'Main Story', 'slug' => 'main-story', 'description' => 'd',
            'content' => 'c', 'publish_status' => true,
            'category_id' => $category->id, 'author_id' => $author->id,
        ]);

        // Enough same-category/same-author siblings to fill all three
        // sections (8 related + 6 by-author + 6 in-category = 20) without
        // running out.
        for ($i = 1; $i <= 22; $i++) {
            BlogPost::create([
                'title' => "Sibling {$i}", 'slug' => "sibling-{$i}", 'description' => 'd',
                'content' => 'c', 'publish_status' => true,
                'category_id' => $category->id, 'author_id' => $author->id,
            ]);
        }

        $response = $this->get('/post/main-story');

        $response->assertStatus(200);
        $response->assertInertia(function ($page) {
            $page->component('Blog/Show', false);

            $props = $page->toArray()['props'];
            $related = collect($props['relatedPosts']);
            $byAuthor = collect($props['moreByAuthor']);
            $inCategory = collect($props['moreInCategory']);

            $this->assertCount(8, $related);
            $this->assertCount(6, $byAuthor);
            $this->assertCount(6, $inCategory);

            $allSlugs = $related->pluck('slug')
                ->merge($byAuthor->pluck('slug'))
                ->merge($inCategory->pluck('slug'));

            $this->assertCount(20, $allSlugs->unique(), 'No story should appear in more than one section.');
        });
    }

    public function test_link_sections_degrade_gracefully_without_author_or_category(): void
    {
        BlogPost::create([
            'title' => 'Orphan Story', 'slug' => 'orphan-story', 'description' => 'd',
            'content' => 'c', 'publish_status' => true,
            'category_id' => null, 'author_id' => null,
        ]);

        $response = $this->get('/post/orphan-story');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Blog/Show', false)
            ->where('moreByAuthor', [])
            ->where('moreInCategory', [])
        );
    }
}
