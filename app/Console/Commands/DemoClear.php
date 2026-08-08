<?php

namespace App\Console\Commands;

use App\Models\Faculty;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Remove everything DemoSeeder created, scoped strictly to demo markers so real
 * data is never touched. Deleting the demo lecturer/student cascades their
 * courses and attempts; demo subjects and faculty are removed explicitly.
 */
class DemoClear extends Command
{
    protected $signature = 'demo:clear';

    protected $description = 'Remove all demo data created by DemoSeeder.';

    public function handle(): int
    {
        foreach (Storage::files('course-materials') as $file) {
            if (str_contains($file, 'demo-course-')) {
                Storage::delete($file);
            }
        }

        User::query()->where('email', 'like', '%@demo.local')->get()->each->delete();

        $faculty = Faculty::query()->where('name', 'Demo Faculty')->first();

        if ($faculty !== null) {
            Subject::query()->where('faculty_id', $faculty->id)->get()->each->delete();
            $faculty->delete();
        }

        $this->info('Demo data removed.');

        return self::SUCCESS;
    }
}
