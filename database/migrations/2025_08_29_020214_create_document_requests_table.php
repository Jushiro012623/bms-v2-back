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
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doc_type_id')->constrained('document_types')->cascadeOnDelete();
            $table->string('purpose');
            $table->text('notes')->nullable();
            $table->integer('status')->default(1)->comments('1: pending, 2: approved, 3: rejected, 4: released');
            $table->timestamp('request_date')->useCurrent();
            $table->timestamp('release_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};
