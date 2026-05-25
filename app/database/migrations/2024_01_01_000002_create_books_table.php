<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('author');
            $table->enum('media', ['physical', 'digital']);
            $table->integer('stock')->default(0)->nullable();
            $table->string('digital_link')->nullable();
            $table->boolean('reserved')->default(false);
            $table->timestamp('reserve_expiration')->nullable();
            $table->uuid('reserved_to')->nullable();
            $table->foreign('reserved_to')->references('id')->on('users')->nullOnDelete();
            $table->boolean('fine')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
