<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->timestamps();
        });

        // Seed the platform defaults an administrator can later tune.
        DB::table('settings')->insert([
            ['key' => 'default_exam_length', 'value' => '15', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'default_questions_per_topic', 'value' => '5', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'revise_threshold', 'value' => '60', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ai_model', 'value' => 'claude-sonnet-5', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
