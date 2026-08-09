<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Platform parameters an administrator can tune: exam length, how many
 * questions the AI drafts per topic, the weak-topic threshold, and the AI
 * model. They are read live by the exam engine, generators and course defaults.
 */
class SettingsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Settings', [
            'settings' => [
                'default_exam_length' => Setting::int('default_exam_length', 15),
                'default_questions_per_topic' => Setting::int('default_questions_per_topic', 5),
                'revise_threshold' => Setting::int('revise_threshold', 60),
                'ai_model' => Setting::get('ai_model', config('services.anthropic.model')),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'default_exam_length' => ['required', 'integer', 'min:1', 'max:50'],
            'default_questions_per_topic' => ['required', 'integer', 'min:1', 'max:20'],
            'revise_threshold' => ['required', 'integer', 'min:1', 'max:100'],
            'ai_model' => ['required', 'string', 'max:100'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, (string) $value);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Settings saved.')]);

        return back();
    }
}
