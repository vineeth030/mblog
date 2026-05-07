<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStorySubmissionStatusRequest;
use App\Models\StorySubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Inertia\Inertia;
use Inertia\Response;

class StorySubmissionController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');

        $submissions = StorySubmission::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, StorySubmission::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (StorySubmission $s) => [
                'id'         => $s->id,
                'title'      => $s->title,
                'email'      => $s->email,
                'status'     => $s->status,
                'created_at' => $s->created_at->toDateTimeString(),
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
        return Inertia::render('Admin/StorySubmissions/Show', [
            'submission' => [
                'id'         => $storySubmission->id,
                'title'      => $storySubmission->title,
                'email'      => $storySubmission->email,
                'status'     => $storySubmission->status,
                'pdf_url'    => $storySubmission->pdf_url,
                'created_at' => $storySubmission->created_at->toDateTimeString(),
                'updated_at' => $storySubmission->updated_at->toDateTimeString(),
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

    public function download(StorySubmission $storySubmission): StreamedResponse
    {
        abort_unless($storySubmission->pdf_file && Storage::disk('public')->exists($storySubmission->pdf_file), 404);

        $filename = sprintf(
            '%s.pdf',
            \Illuminate\Support\Str::slug($storySubmission->title) ?: 'story-submission-'.$storySubmission->id
        );

        return Storage::disk('public')->download($storySubmission->pdf_file, $filename);
    }

    public function destroy(StorySubmission $storySubmission): RedirectResponse
    {
        $storySubmission->deletePdfFile();
        $storySubmission->delete();

        return redirect()
            ->route('admin.story-submissions.index')
            ->with('success', 'Submission deleted.');
    }
}
