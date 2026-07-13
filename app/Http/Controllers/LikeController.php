<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(BlogPost $blogPost, Request $request, LikeService $likes): JsonResponse
    {
        abort_if(! $blogPost->publish_status, 404);

        return response()->json($likes->toggle($blogPost, $request));
    }
}
