<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFacultyRequest;
use App\Http\Requests\Admin\UpdateFacultyRequest;
use App\Models\Faculty;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FacultyController extends Controller
{
    /**
     * List all faculties with their subject counts.
     */
    public function index(): Response
    {
        $faculties = Faculty::query()
            ->withCount('subjects')
            ->latest()
            ->get()
            ->map(fn (Faculty $faculty): array => [
                'id' => $faculty->id,
                'name' => $faculty->name,
                'subjects_count' => $faculty->subjects_count,
            ]);

        return Inertia::render('admin/Faculties', [
            'faculties' => $faculties,
        ]);
    }

    /**
     * Create a new faculty.
     */
    public function store(StoreFacultyRequest $request): RedirectResponse
    {
        Faculty::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Faculty created.')]);

        return to_route('admin.faculties.index');
    }

    /**
     * Update an existing faculty.
     */
    public function update(UpdateFacultyRequest $request, Faculty $faculty): RedirectResponse
    {
        $faculty->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Faculty updated.')]);

        return to_route('admin.faculties.index');
    }

    /**
     * Delete a faculty. Its subjects and courses are removed by the database.
     */
    public function destroy(Faculty $faculty): RedirectResponse
    {
        $faculty->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Faculty deleted.')]);

        return to_route('admin.faculties.index');
    }
}
