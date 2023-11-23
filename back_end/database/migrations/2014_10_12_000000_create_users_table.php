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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name',150);
            $table->string('last_name',150);
            $table->string('middle_name',150)->nullable();
            $table->string('phone_number',30)->nullable();
            $table->string('image')->nullable();
            $table->string('gender',10)->nullable();
            $table->string('home_address')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('user_type',false,true)->nullable();
            $table->string('secondary_phone_number',30)->nullable();
            $table->string('emergency_phone_number',30)->nullable();
            $table->softDeletes();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
