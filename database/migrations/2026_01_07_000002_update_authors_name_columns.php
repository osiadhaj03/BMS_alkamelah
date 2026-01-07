<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure existing rows have a full_name before making the column required.
        DB::statement(
            "UPDATE authors SET full_name = TRIM(CONCAT_WS(' ', laqab, kunyah, first_name, middle_name, last_name)) " .
            "WHERE full_name IS NULL OR full_name = ''"
        );

        Schema::table('authors', function (Blueprint $table): void {
            $table->string('first_name')->nullable()->change();
            $table->string('full_name')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table): void {
            $table->string('first_name')->nullable(false)->change();
            $table->string('full_name')->nullable()->change();
        });
    }
};
