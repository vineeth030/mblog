<?php

namespace Tests\Feature\Admin;

use App\Models\Author;
use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeriesLinkingTest extends TestCase
{
    use RefreshDatabase;

    private function asAdmin(): static
    {
        $this->withSession(['admin_authenticated' => true]);

        return $this;
    }

    private function validPayload(array $overrides = []): array
    {
        $category = Category::first() ?? Category::create(['name' => 'General', 'slug' => 'general']);
        $author = Author::first() ?? Author::create(['name' => 'Author', 'slug' => 'author']);

        return array_merge([
            'title'          => 'Untitled',
            'slug'           => 'untitled-' . uniqid(),
            'description'    => 'desc',
            'category_id'    => $category->id,
            'author_id'      => $author->id,
            'content'        => 'content',
            'publish_status' => true,
        ], $overrides);
    }

    public function test_linking_a_new_part_to_a_standalone_post_forms_a_series(): void
    {
        $this->asAdmin();

        $this->post(route('admin.blog-posts.store'), $this->validPayload([
            'title' => 'Part One', 'slug' => 'part-one',
        ]))->assertRedirect(route('admin.blog-posts.index'));

        $partOne = BlogPost::where('slug', 'part-one')->firstOrFail();

        $this->post(route('admin.blog-posts.store'), $this->validPayload([
            'title' => 'Part Two', 'slug' => 'part-two', 'previous_part_id' => $partOne->id,
        ]))->assertRedirect(route('admin.blog-posts.index'));

        $partOne->refresh();
        $partTwo = BlogPost::where('slug', 'part-two')->firstOrFail();

        $this->assertSame($partOne->id, $partOne->series_id);
        $this->assertSame(1, $partOne->part_number);
        $this->assertSame($partOne->id, $partTwo->series_id);
        $this->assertSame(2, $partTwo->part_number);
    }

    public function test_third_part_inherits_series_and_increments_part_number(): void
    {
        $this->asAdmin();

        $this->post(route('admin.blog-posts.store'), $this->validPayload(['title' => 'P1', 'slug' => 'p1']));
        $p1 = BlogPost::where('slug', 'p1')->firstOrFail();

        $this->post(route('admin.blog-posts.store'), $this->validPayload([
            'title' => 'P2', 'slug' => 'p2', 'previous_part_id' => $p1->id,
        ]));
        $p2 = BlogPost::where('slug', 'p2')->firstOrFail();

        $this->post(route('admin.blog-posts.store'), $this->validPayload([
            'title' => 'P3', 'slug' => 'p3', 'previous_part_id' => $p2->id,
        ]));
        $p3 = BlogPost::where('slug', 'p3')->firstOrFail();

        $this->assertSame($p1->id, $p3->series_id);
        $this->assertSame(3, $p3->part_number);
    }

    public function test_resaving_a_linked_part_unchanged_does_not_bump_part_number(): void
    {
        $this->asAdmin();

        $this->post(route('admin.blog-posts.store'), $this->validPayload(['title' => 'P1', 'slug' => 'p1']));
        $p1 = BlogPost::where('slug', 'p1')->firstOrFail();

        $this->post(route('admin.blog-posts.store'), $this->validPayload([
            'title' => 'P2', 'slug' => 'p2', 'previous_part_id' => $p1->id,
        ]));
        $p2 = BlogPost::where('slug', 'p2')->firstOrFail();

        $this->assertSame(2, $p2->part_number);

        // Re-save Part 2 with the same previous_part_id — should be a no-op for series linkage.
        $this->put(route('admin.blog-posts.update', $p2->slug), $this->validPayload([
            'title' => 'P2', 'slug' => 'p2', 'previous_part_id' => $p1->id,
        ]));

        $p2->refresh();
        $this->assertSame(2, $p2->part_number);
    }

    public function test_root_series_linkage_survives_resaving_with_no_previous_part(): void
    {
        // A series root's own "previous part" field is always empty (it has
        // no predecessor by definition), so resaving it — the only way its
        // edit form can behave — must never break the series for its parts.
        $this->asAdmin();

        $this->post(route('admin.blog-posts.store'), $this->validPayload(['title' => 'P1', 'slug' => 'p1']));
        $p1 = BlogPost::where('slug', 'p1')->firstOrFail();

        $this->post(route('admin.blog-posts.store'), $this->validPayload([
            'title' => 'P2', 'slug' => 'p2', 'previous_part_id' => $p1->id,
        ]));

        $this->put(route('admin.blog-posts.update', $p1->slug), $this->validPayload([
            'title' => 'P1 Updated', 'slug' => 'p1', 'previous_part_id' => null,
        ]))->assertRedirect(route('admin.blog-posts.index'));

        $p1->refresh();
        $p2 = BlogPost::where('slug', 'p2')->firstOrFail();

        $this->assertSame($p1->id, $p1->series_id);
        $this->assertSame($p1->id, $p2->series_id);
        $this->assertSame(2, $p2->part_number);
    }

    public function test_detaching_a_non_root_part_leaves_the_rest_of_the_series_intact(): void
    {
        $this->asAdmin();

        $this->post(route('admin.blog-posts.store'), $this->validPayload(['title' => 'P1', 'slug' => 'p1']));
        $p1 = BlogPost::where('slug', 'p1')->firstOrFail();

        $this->post(route('admin.blog-posts.store'), $this->validPayload([
            'title' => 'P2', 'slug' => 'p2', 'previous_part_id' => $p1->id,
        ]));
        $p2 = BlogPost::where('slug', 'p2')->firstOrFail();

        $this->put(route('admin.blog-posts.update', $p2->slug), $this->validPayload([
            'title' => 'P2', 'slug' => 'p2', 'previous_part_id' => null,
        ]))->assertRedirect(route('admin.blog-posts.index'));

        $p2->refresh();
        $p1->refresh();

        $this->assertNull($p2->series_id);
        $this->assertNull($p2->part_number);
        $this->assertSame($p1->id, $p1->series_id);
    }
}
