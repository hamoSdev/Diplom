<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * List every user with a full picture of what they are linked to:
     * a student's faculty (and its subjects), a lecturer's subjects and courses.
     */
    public function index(): Response
    {
        $facultySubjectCounts = Faculty::query()->withCount('subjects')->pluck('subjects_count', 'id');

        $users = User::query()
            ->with(['faculty:id,name', 'taughtSubjects:id,name'])
            ->withCount('courses')
            ->latest()
            ->get()
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'role_label' => $user->role->label(),
                'faculty_id' => $user->faculty_id,
                'faculty_name' => $user->faculty?->name,
                'faculty_subjects_count' => $user->faculty_id ? (int) ($facultySubjectCounts[$user->faculty_id] ?? 0) : null,
                'taught_subjects' => $user->taughtSubjects->pluck('name'),
                'courses_count' => $user->courses_count,
            ]);

        return Inertia::render('admin/Users', [
            'users' => $users,
            'faculties' => Faculty::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Create a new user. A faculty is stored only for students.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'faculty_id' => $this->facultyIdFor($data),
            'email_verified_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User created.')]);

        return to_route('admin.users.index');
    }

    /**
     * Update an existing user. The password is changed only when provided.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'faculty_id' => $this->facultyIdFor($data),
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User updated.')]);

        return to_route('admin.users.index');
    }

    /**
     * Delete a user. Admins cannot delete their own account.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            Inertia::flash('toast', ['type' => 'error', 'message' => __('You cannot delete your own account.')]);

            return to_route('admin.users.index');
        }

        $user->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User deleted.')]);

        return to_route('admin.users.index');
    }

    /**
     * A faculty is only meaningful for students; other roles are never linked.
     *
     * @param  array<string, mixed>  $data
     */
    private function facultyIdFor(array $data): ?int
    {
        if ($data['role'] !== UserRole::Student->value) {
            return null;
        }

        return isset($data['faculty_id']) ? (int) $data['faculty_id'] : null;
    }
}
