<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStorySubmissionStatusRequest;
use App\Models\StorySubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StorySubmissionController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');

        $submissions = StorySubmission::query()
            ->with('category:id,name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('author_name', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, StorySubmission::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (StorySubmission $s) => [
                'id'          => $s->id,
                'title'       => $s->title,
                'email'       => $s->email,
                'author_name' => $s->author_name,
                'category'    => $s->category?->name,
                'tags'        => $s->tags,
                'status'      => $s->status,
                'created_at'  => $s->created_at->toDateTimeString(),
            ]);

        return Inertia::render('Admin/StorySubmissions/Index', [
            'submissions' => $submissions,
            'filters'     => [
                'search' => $search,
                'status' => $status,
            ],
            'statuses'    => StorySubmission::STATUSES,
        ]);
    }

    public function show(StorySubmission $storySubmission): Response
    {
        $storySubmission->load('category:id,name');

        return Inertia::render('Admin/StorySubmissions/Show', [
            'submission' => [
                'id'            => $storySubmission->id,
                'title'         => $storySubmission->title,
                'email'         => $storySubmission->email,
                'author_name'   => $storySubmission->author_name,
                'category'      => $storySubmission->category?->name,
                'tags'          => $storySubmission->tags,
                'story_content' => $storySubmission->story_content,
                'status'        => $storySubmission->status,
                'created_at'    => $storySubmission->created_at->toDateTimeString(),
                'updated_at'    => $storySubmission->updated_at->toDateTimeString(),
            ],
            'statuses' => StorySubmission::STATUSES,
        ]);
    }

    public function updateStatus(
        UpdateStorySubmissionStatusRequest $request,
        StorySubmission $storySubmission
    ): RedirectResponse {
        $storySubmission->update($request->validated());

        return back()->with('success', 'Submission status updated.');
    }

    public function destroy(StorySubmission $storySubmission): RedirectResponse
    {
        $storySubmission->delete();

        return redirect()
            ->route('admin.story-submissions.index')
            ->with('success', 'Submission deleted.');
    }
}
