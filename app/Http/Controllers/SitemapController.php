<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $posts = BlogPost::query()
            ->where('publish_status', true)
            ->select('slug', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        $xml = view('sitemap.index', [
            'posts'      => $posts,
            'lastmod'    => optional($posts->first())->updated_at ?? now(),
        ])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
