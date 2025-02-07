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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Reporting user
            $table->string('reportable_type'); // Type of reported entity (user, blog, comment)
            $table->unsignedBigInteger('reportable_id'); // ID of the reported entity
            $table->text('reason'); // Report reason
            $table->enum('status', ['pending', 'resolved', 'dismissed'])->default('pending'); // Report status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
