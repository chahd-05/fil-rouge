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
        Schema::create('structures', function (Blueprint $table) {
            $table->id();
        $table->foreignId('project_id')->constrained()->onDelete('cascade');

        $table->string('type');
        $table->string('layout')->nullable();

        $table->float('panel_width');
        $table->float('panel_height');
        $table->float('panel_weight');
        $table->integer('panel_count');

        $table->float('total_surface')->nullable();

        $table->integer('rail_count')->nullable();
        $table->float('rail_length_total')->nullable();

        $table->integer('fixation_count')->nullable();
        $table->float('spacing_between_fixations')->default(1);

        $table->string('structure_material')->nullable();

        $table->float('tilt_angle')->nullable();
        $table->float('wind_load')->nullable();

        $table->float('total_weight')->nullable();
        $table->float('safety_factor')->default(1.5);

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('structures');
    }
};
