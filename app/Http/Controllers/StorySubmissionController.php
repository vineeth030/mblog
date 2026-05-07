<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStorySubmissionRequest;
use App\Models\StorySubmission;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StorySubmissionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Stories/Submit');
    }

    public function store(StoreStorySubmissionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['pdf_file'] = $request->file('pdf_file')
            ->store('story-submissions', 'public');

        StorySubmission::create($data);

        return redirect()
            ->route('stories.submit')
            ->with('success', 'Your story has been submitted successfully. We will review it shortly.');
    }
}
