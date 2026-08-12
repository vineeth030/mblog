<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_finds_published_post_by_malayalam_title(): void
    {
        BlogPost::create([
            'title'          => 'ഒരു മലയാളം കഥ',
            'slug'           => 'malayalam-story',
            'description'    => 'ഒരു ചെറിയ വിവരണം',
            'content'        => 'ഇത് കഥയുടെ ഉള്ളടക്കമാണ്.',
            'publish_status' => true,
        ]);

        $response = $this->get('/search?q=' . urlencode('മലയാളം'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Blog/Search', false)
            ->where('posts.data.0.slug', 'malayalam-story')
        );
    }

    public function test_search_excludes_unpublished_posts(): void
    {
        BlogPost::create([
            'title'          => 'ഡ്രാഫ്റ്റ് സ്റ്റോറി',
            'slug'           => 'draft-story',
            'description'    => 'not yet live',
            'content'        => 'draft content',
            'publish_status' => false,
        ]);

        $response = $this->get('/search?q=' . urlencode('ഡ്രാഫ്റ്റ്'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Blog/Search', false)
            ->where('posts.data', [])
        );
    }

    public function test_empty_and_short_queries_return_no_results_without_error(): void
    {
        $this->get('/search')->assertStatus(200);
        $this->get('/search?q=a')->assertStatus(200)->assertInertia(fn ($page) => $page
            ->where('posts.data', [])
        );
    }

    public function test_search_finds_published_post_by_author_name(): void
    {
        $author = Author::create([
            'name' => 'Vineeth Vijayan',
            'slug' => 'vineeth-vijayan',
        ]);

        BlogPost::create([
            'title'          => 'A Story With Unrelated Title',
            'slug'           => 'author-search-story',
            'description'    => 'unrelated description',
            'content'        => 'unrelated content',
            'author_id'      => $author->id,
            'publish_status' => true,
        ]);

        $response = $this->get('/search?q=' . urlencode('Vineeth'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Blog/Search', false)
            ->where('posts.data.0.slug', 'author-search-story')
        );
    }

    public function test_search_no_longer_matches_description_or_content_only(): void
    {
        BlogPost::create([
            'title'          => 'Unrelated Title',
            'slug'           => 'description-only-story',
            'description'    => 'unique-description-term',
            'content'        => 'unique-content-term',
            'publish_status' => true,
        ]);

        $descriptionMatch = $this->get('/search?q=' . urlencode('unique-description-term'));
        $descriptionMatch->assertStatus(200);
        $descriptionMatch->assertInertia(fn ($page) => $page
            ->component('Blog/Search', false)
            ->where('posts.data', [])
        );

        $contentMatch = $this->get('/search?q=' . urlencode('unique-content-term'));
        $contentMatch->assertStatus(200);
        $contentMatch->assertInertia(fn ($page) => $page
            ->component('Blog/Search', false)
            ->where('posts.data', [])
        );
    }
}
