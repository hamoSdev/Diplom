<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreEditorCourseRequest;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class EditorController extends Controller
{
    /**
     * Show the rich-text editor together with the subjects the current user
     * may save an article into.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('editor/Index', [
            'subjects' => $this->savableSubjects($request),
        ]);
    }

    /**
     * Save the written article as a new course under the chosen subject,
     * owned by the current user.
     */
    public function store(StoreEditorCourseRequest $request): RedirectResponse
    {
        $request->user()->courses()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Saved as course.')]);

        return to_route('editor.index');
    }

    /**
     * Subjects the user can save into: their own for a lecturer, all for admin.
     *
     * @return Collection<int, Subject>
     */
    private function savableSubjects(Request $request): Collection
    {
        if ($request->user()->role === UserRole::Admin) {
            return Subject::query()->orderBy('name')->get(['id', 'name']);
        }

        return $request->user()->taughtSubjects()->orderBy('name')->get(['subjects.id', 'subjects.name']);
    }
}
