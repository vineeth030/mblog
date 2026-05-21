<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStorySubmissionRequest;
use App\Models\Category;
use App\Models\StorySubmission;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StorySubmissionController extends Controller
{
    public function create(): Response
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);

        session(['captcha_sum' => $a + $b]);

        return Inertia::render('Stories/Submit', [
            'categories' => Category::query()
                ->orderBy('name')
                ->get(['id', 'name']),
            'captcha'    => ['a' => $a, 'b' => $b],
        ]);
    }

    public function store(StoreStorySubmissionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['captcha_answer']);

        StorySubmission::create($data);

        $request->session()->forget('captcha_sum');

        return redirect()
            ->route('stories.submit')
            ->with('success', 'Your story has been submitted successfully. We will review it shortly.');
    }
}
