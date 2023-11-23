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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->references('id')->on('patients')->cascadeOnUpdate();
            $table->boolean('patient_is_minor')->default(false);
            $table->date('appointment_date');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->text('description')->nullable();
            $table->integer('room_id')->nullable();
            $table->foreignId('doctor_id')->references('id')->on('users')->cascadeOnUpdate();
            $table->enum('status', ['pending', 'approved', 'rejected' , 'completed' , 'canceled', 'in_progress'])->default('pending');
            $table->enum('reschedule', ['no', 'yes'])->default('no')->nullable();
            $table->text('reschedule_reason')->nullable();
            $table->foreignId('created_by')->references('id')->on('users')->cascadeOnUpdate();
            $table->foreignId('last_modified_by')->references('id')->on('users')->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
