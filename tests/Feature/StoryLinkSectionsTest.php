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

    public function test_show_page_fills_all_four_link_sections_without_duplicates(): void
    {
        $category = Category::create(['name' => 'Romance']);
        $author = Author::create(['name' => 'Priya']);

        $main = BlogPost::create([
            'title' => 'Main Story', 'slug' => 'main-story', 'description' => 'd',
            'content' => 'c', 'publish_status' => true,
            'category_id' => $category->id, 'author_id' => $author->id,
        ]);

        // Enough same-category/same-author siblings to fill related/by-author/
        // in-category (8 + 6 + 6 = 20), plus 6 more unrelated posts so the
        // latest-stories section has its own pool to draw from.
        for ($i = 1; $i <= 22; $i++) {
            BlogPost::create([
                'title' => "Sibling {$i}", 'slug' => "sibling-{$i}", 'description' => 'd',
                'content' => 'c', 'publish_status' => true,
                'category_id' => $category->id, 'author_id' => $author->id,
            ]);
        }

        for ($i = 1; $i <= 6; $i++) {
            BlogPost::create([
                'title' => "Other {$i}", 'slug' => "other-{$i}", 'description' => 'd',
                'content' => 'c', 'publish_status' => true,
                'category_id' => null, 'author_id' => null,
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
            $latest = collect($props['latestStories']);

            $this->assertCount(8, $related);
            $this->assertCount(6, $byAuthor);
            $this->assertCount(6, $inCategory);
            $this->assertCount(6, $latest);

            $allSlugs = $related->pluck('slug')
                ->merge($byAuthor->pluck('slug'))
                ->merge($inCategory->pluck('slug'))
                ->merge($latest->pluck('slug'));

            $this->assertCount(26, $allSlugs->unique(), 'No story should appear in more than one section.');
        });
    }

    public function test_latest_stories_are_ordered_by_published_date_descending(): void
    {
        $category = Category::create(['name' => 'Romance']);
        $author = Author::create(['name' => 'Priya']);

        $main = BlogPost::create([
            'title' => 'Main Story', 'slug' => 'main-story', 'description' => 'd',
            'content' => 'c', 'publish_status' => true,
            'category_id' => $category->id, 'author_id' => $author->id,
        ]);

        // 8 same-category/same-author siblings outrank the two unrelated
        // posts below on relevance, so they fully occupy the related (and,
        // once excluded, by-author / in-category) sections — leaving only
        // "older"/"newer" as candidates for the latest-stories section.
        for ($i = 1; $i <= 8; $i++) {
            BlogPost::create([
                'title' => "Sibling {$i}", 'slug' => "sibling-{$i}", 'description' => 'd',
                'content' => 'c', 'publish_status' => true,
                'category_id' => $category->id, 'author_id' => $author->id,
            ]);
        }

        $older = BlogPost::create([
            'title' => 'Older Story', 'slug' => 'older-story', 'description' => 'd',
            'content' => 'c', 'publish_status' => true,
        ]);
        $older->forceFill(['created_at' => now()->subDays(5)])->save();

        $newer = BlogPost::create([
            'title' => 'Newer Story', 'slug' => 'newer-story', 'description' => 'd',
            'content' => 'c', 'publish_status' => true,
        ]);
        $newer->forceFill(['created_at' => now()->subDay()])->save();

        $response = $this->get('/post/main-story');

        $response->assertStatus(200);
        $response->assertInertia(function ($page) {
            $page->component('Blog/Show', false);

            $slugs = collect($page->toArray()['props']['latestStories'])->pluck('slug')->all();

            $this->assertSame(['newer-story', 'older-story'], $slugs);
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
