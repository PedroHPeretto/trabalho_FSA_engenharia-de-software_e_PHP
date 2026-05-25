<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE books ADD COLUMN cover_image MEDIUMBLOB NULL AFTER digital_link');
        DB::statement('ALTER TABLE books ADD COLUMN pdf MEDIUMBLOB NULL AFTER cover_image');
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['cover_image', 'pdf']);
        });
    }
};
