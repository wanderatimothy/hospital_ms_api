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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('unique_no',255)->unique();
            $table->string('first_name',150);
            $table->string('last_name',150);
            $table->string('image',255)->nullable();
            $table->string('other_names',150)->nullable();
            $table->string('title',150)->nullable();
            $table->string('gender',10);
            $table->string('email');
            $table->string('phone_number');
            $table->string('blood_group');
            $table->date('last_visit_date')->nullable();
            $table->string('address')->nullable();
            $table->string('emergency_contact_first_name')->nullable();
            $table->string('emergency_contact_last_name')->nullable();
            $table->string('emergency_contact_phone_number')->nullable();
            $table->enum('emergency_contact_relationship',[
                'sibling','parent','child','husband','wife', 'cousin', 'guardian'
            ])->nullable();
            $table->boolean('is_insured')->default(false);
            $table->enum('health_insuarance_coverage',[
                'In-Patient',
                'Out-Patient',
            ])->nullable();
            $table->boolean('has_extra_fields')->default(false);
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
        Schema::dropIfExists('patients');
    }
};
