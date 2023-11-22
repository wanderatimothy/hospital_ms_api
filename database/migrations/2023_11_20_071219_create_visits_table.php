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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->date('visit_date');
            $table->enum('visit_outcome',['ADMITTED','NO_ADMITTED']);
            $table->boolean('has_appointment')->default(false);
            $table->integer('appointment_id')->nullable();
            $table->integer('patient_id')->nullable();
            $table->timestamp('arrival_time');
            $table->timestamp('departure_time');
            $table->string('phone_number');
            $table->string('first_name',150)->nullable();
            $table->string('last_name',150)->nullable();
            $table->string('gender',10)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->boolean('is_insured')->default(false);
            $table->enum('health_insuarance_coverage',[
                'In-Patient',
                'Out-Patient',
            ])->nullable();
            $table->integer('insuarance_provider_id')->nullable();
            $table->softDeletes();
            $table->foreignId('created_by')->references('id')->on('users')->cascadeOnUpdate();
            $table->foreignId('last_modified_by')->references('id')->on('users')->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
