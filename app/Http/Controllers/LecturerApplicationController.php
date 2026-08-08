<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLecturerApplicationRequest;
use App\Http\Requests\UpdateLecturerApplicationRequest;
use App\Models\LecturerApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * "Become a lecturer" applications. Anyone may submit one from the public page;
 * only an admin lists them, downloads the CV, changes the status or deletes.
 * CVs live on the private disk, reachable only through the admin download route.
 */
class LecturerApplicationController extends Controller
{
    /**
     * Store a public application with its attached CV.
     */
    public function store(StoreLecturerApplicationRequest $request): RedirectResponse
    {
        $file = $request->file('cv');

        LecturerApplication::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'message' => $request->validated('message'),
            'cv_path' => $file->store('lecturer-cvs'),
            'cv_original_name' => $file->getClientOriginalName(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Application sent. We will be in touch.')]);

        return back();
    }

    /**
     * List all applications for an administrator.
     */
    public function index(): Response
    {
        $applications = LecturerApplication::query()
            ->latest('id')
            ->get()
            ->map(fn (LecturerApplication $application): array => [
                'id' => $application->id,
                'name' => $application->name,
                'email' => $application->email,
                'message' => $application->message,
                'status' => $application->status->value,
                'status_label' => $application->status->label(),
                'date' => $application->created_at?->format('M j, Y'),
                'cv_url' => route('admin.applications.cv', $application),
            ]);

        return Inertia::render('admin/Applications', [
            'applications' => $applications,
        ]);
    }

    /**
     * Change an application's review status.
     */
    public function update(UpdateLecturerApplicationRequest $request, LecturerApplication $application): RedirectResponse
    {
        $application->update(['status' => $request->validated('status')]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Application updated.')]);

        return back();
    }

    /**
     * Remove an application and its CV file.
     */
    public function destroy(LecturerApplication $application): RedirectResponse
    {
        Storage::delete($application->cv_path);
        $application->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Application deleted.')]);

        return back();
    }

    /**
     * Download an application's CV (admin only).
     */
    public function download(LecturerApplication $application): StreamedResponse
    {
        return Storage::download($application->cv_path, $application->cv_original_name);
    }
}
