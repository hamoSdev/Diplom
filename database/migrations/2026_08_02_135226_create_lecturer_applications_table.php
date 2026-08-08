<?php

use App\Enums\LecturerApplicationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Public "become a lecturer" applications an admin reviews. The CV file lives
     * on the private disk and is only reachable through the admin download route.
     */
    public function up(): void
    {
        Schema::create('lecturer_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->text('message');
            $table->string('cv_path');
            $table->string('cv_original_name');
            $table->string('status', 20)->default(LecturerApplicationStatus::Pending->value)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturer_applications');
    }
};
