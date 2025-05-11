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
        Schema::table('shelf_book', function (Blueprint $table) {
            $table->unique('book_id', 'shelf_book_book_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shelf_book', function (Blueprint $table) {
            $table->dropUnique('shelf_book_book_id_unique');
        });
    }
};
