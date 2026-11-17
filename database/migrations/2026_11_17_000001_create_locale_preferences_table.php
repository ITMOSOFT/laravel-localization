<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('localization.tables.preferences', 'locale_preferences'), function (Blueprint $table): void {
            $table->id();
            $table->string('subject_type');
            $table->string('subject_id');
            $table->string('locale', 20);
            $table->timestamps();
            $table->unique(['subject_type', 'subject_id'], 'locale_preference_subject_unique');
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('localization.tables.preferences', 'locale_preferences'));
    }
};
