<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('protections') || ! Schema::hasColumn('protections', 'project_id')) {
            return;
        }

        Schema::table('protections', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('protections') || ! Schema::hasColumn('protections', 'project_id')) {
            return;
        }

        Schema::table('protections', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable(false)->change();
        });
    }
};
