<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->references('id')->on('patients')->cascadeOnUpdate();
            $table->foreignId('bed_id')->references('id')->on('beds')->cascadeOnUpdate();
            $table->foreignId('created_by')->references('id')->on('users')->cascadeOnUpdate();
            $table->foreignId('last_modified_by')->references('id')->on('users')->cascadeOnUpdate();
            $table->timestamp('admission_time');
            $table->timestamp('discharge_time')->nullable();
            $table->string('discharge_reason')->nullable();
            $table->text('admission_notes')->nullable();
            $table->enum('admission_type', ['emergency', 'direct' , 'holding' , 'elective', 'inpatient'])->default('inpatient');
            $table->foreignId('room_id')->nullable()->references('id')->on('rooms')->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
