<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorySeriesDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_page_lists_all_published_parts_in_order_with_current_flagged(): void
    {
        $part1 = BlogPost::create([
            'title' => 'Part One', 'slug' => 'part-one', 'description' => 'd',
            'content' => 'c', 'publish_status' => true,
        ]);
        $part2 = BlogPost::create([
            'title' => 'Part Two', 'slug' => 'part-two', 'description' => 'd',
            'content' => 'c', 'publish_status' => true,
        ]);
        $part1->series_id = $part1->id;
        $part1->part_number = 1;
        $part1->save();
        $part2->series_id = $part1->id;
        $part2->part_number = 2;
        $part2->save();

        $response = $this->get('/post/part-two');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Blog/Show', false)
            ->where('seriesParts.0.slug', 'part-one')
            ->where('seriesParts.0.is_current', false)
            ->where('seriesParts.1.slug', 'part-two')
            ->where('seriesParts.1.is_current', true)
        );
    }

    public function test_standalone_post_has_empty_series_parts(): void
    {
        BlogPost::create([
            'title' => 'Solo Story', 'slug' => 'solo-story', 'description' => 'd',
            'content' => 'c', 'publish_status' => true,
        ]);

        $response = $this->get('/post/solo-story');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Blog/Show', false)
            ->where('seriesParts', [])
        );
    }

    public function test_unpublished_sibling_part_is_excluded_from_public_series_list(): void
    {
        $part1 = BlogPost::create([
            'title' => 'Part One', 'slug' => 'p1', 'description' => 'd',
            'content' => 'c', 'publish_status' => true,
        ]);
        $draft = BlogPost::create([
            'title' => 'Part Two Draft', 'slug' => 'p2-draft', 'description' => 'd',
            'content' => 'c', 'publish_status' => false,
        ]);
        $part1->series_id = $part1->id;
        $part1->part_number = 1;
        $part1->save();
        $draft->series_id = $part1->id;
        $draft->part_number = 2;
        $draft->save();

        $response = $this->get('/post/p1');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Blog/Show', false)
            ->where('seriesParts', [
                ['slug' => 'p1', 'title' => 'Part One', 'part_number' => 1, 'is_current' => true],
            ])
        );
    }
}
