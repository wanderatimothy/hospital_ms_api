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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('store_path');
            $table->string('entity')->nullable();
            $table->integer('entity_id');
            $table->foreignId('document_type_id')
            ->references('id')->on('document_types')
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->boolean('is_encoded')->default(false);
            $table->boolean('is_text')->default(false);
            $table->text('content')->nullable();
            $table->foreignId('created_by')->references('id')->on('users')->cascadeOnUpdate();
            $table->foreignId('last_modified_by')->references('id')->on('users')->cascadeOnUpdate();
            $table->string('mime_type')->nullable();
            $table->string('size')->nullable();
            $table->json('shared_with')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
