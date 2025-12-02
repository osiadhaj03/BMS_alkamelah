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
        Schema::table('authors', function (Blueprint $table) {
            // Add new name fields after the first_name column (previously full_name)
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('middle_name');
            $table->string('laqab')->nullable()->after('last_name')->comment('اللقب - مثل: الإمام، الشيخ، العلامة');
            $table->string('kunyah')->nullable()->after('laqab')->comment('الكنية - مثل: أبو حنيفة، أبو يوسف');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn(['middle_name', 'last_name', 'laqab', 'kunyah']);
        });
    }
};
