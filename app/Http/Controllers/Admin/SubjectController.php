<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubjectRequest;
use App\Http\Requests\Admin\UpdateSubjectRequest;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SubjectController extends Controller
{
    /**
     * List subjects together with the faculties and lecturers the forms need.
     */
    public function index(): Response
    {
        $subjects = Subject::query()
            ->with(['faculty:id,name', 'lecturers:id,name'])
            ->latest()
            ->get()
            ->map(fn (Subject $subject): array => [
                'id' => $subject->id,
                'name' => $subject->name,
                'description' => $subject->description,
                'faculty_id' => $subject->faculty_id,
                'faculty_name' => $subject->faculty->name,
                'lecturer_ids' => $subject->lecturers->pluck('id'),
                'lecturer_names' => $subject->lecturers->pluck('name'),
            ]);

        return Inertia::render('admin/Subjects', [
            'subjects' => $subjects,
            'faculties' => Faculty::query()->orderBy('name')->get(['id', 'name']),
            'lecturers' => User::query()
                ->where('role', UserRole::Lecturer)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Create a subject and attach its lecturers.
     */
    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $subject = Subject::create($data);
        $subject->lecturers()->sync($data['lecturer_ids'] ?? []);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Subject created.')]);

        return to_route('admin.subjects.index');
    }

    /**
     * Update a subject and re-sync its lecturers.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $data = $request->validated();

        $subject->update($data);
        $subject->lecturers()->sync($data['lecturer_ids'] ?? []);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Subject updated.')]);

        return to_route('admin.subjects.index');
    }

    /**
     * Delete a subject. Its courses are removed by the database.
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Subject deleted.')]);

        return to_route('admin.subjects.index');
    }
}
