<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the dashboard for the current user's role. Students get their own
     * overview; other roles keep the generic dashboard for now.
     */
    public function index(Request $request): Response
    {
        if ($request->user()->role === UserRole::Student) {
            return $this->studentDashboard($request);
        }

        return Inertia::render('Dashboard');
    }

    /**
     * A first-glance overview for a student: their faculty and how much
     * learning it opens up.
     */
    private function studentDashboard(Request $request): Response
    {
        $faculty = $request->user()->faculty;

        $subjectsCount = 0;
        $coursesCount = 0;

        if ($faculty !== null) {
            $subjectsCount = $faculty->subjects()->count();
            $coursesCount = Course::query()
                ->whereHas('subject', fn ($query) => $query->where('faculty_id', $faculty->id))
                ->count();
        }

        return Inertia::render('student/Dashboard', [
            'faculty' => $faculty?->only('id', 'name'),
            'subjectsCount' => $subjectsCount,
            'coursesCount' => $coursesCount,
        ]);
    }
}
