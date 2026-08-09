<?php

namespace Tests\Feature;

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
}
