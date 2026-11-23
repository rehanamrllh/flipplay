<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('media', 'description')) {
            Schema::table('media', function (Blueprint $table): void {
                $table->dropColumn('description');
            });
        }
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table): void {
            $table->text('description')->nullable();
        });
    }
};
