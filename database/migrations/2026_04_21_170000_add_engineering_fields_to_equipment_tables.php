<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('panels', function (Blueprint $table) {
            if (! Schema::hasColumn('panels', 'model')) {
                $table->string('model')->nullable()->after('project_id');
            }
            if (! Schema::hasColumn('panels', 'voc')) {
                $table->double('voc')->nullable()->after('efficiency');
            }
            if (! Schema::hasColumn('panels', 'vmp')) {
                $table->double('vmp')->nullable()->after('voc');
            }
            if (! Schema::hasColumn('panels', 'isc')) {
                $table->double('isc')->nullable()->after('vmp');
            }
            if (! Schema::hasColumn('panels', 'imp')) {
                $table->double('imp')->nullable()->after('isc');
            }
            if (! Schema::hasColumn('panels', 'length_m')) {
                $table->double('length_m')->nullable()->after('imp');
            }
            if (! Schema::hasColumn('panels', 'width_m')) {
                $table->double('width_m')->nullable()->after('length_m');
            }
            if (! Schema::hasColumn('panels', 'temperature_coefficient')) {
                $table->double('temperature_coefficient')->nullable()->after('width_m');
            }
        });

        Schema::table('inverters', function (Blueprint $table) {
            if (! Schema::hasColumn('inverters', 'model')) {
                $table->string('model')->nullable()->after('project_id');
            }
            if (! Schema::hasColumn('inverters', 'ac_power_kw')) {
                $table->double('ac_power_kw')->nullable()->after('power');
            }
            if (! Schema::hasColumn('inverters', 'max_dc_power_kw')) {
                $table->double('max_dc_power_kw')->nullable()->after('ac_power_kw');
            }
            if (! Schema::hasColumn('inverters', 'mppt_count')) {
                $table->unsignedInteger('mppt_count')->nullable()->after('max_dc_power_kw');
            }
            if (! Schema::hasColumn('inverters', 'strings_per_mppt')) {
                $table->unsignedInteger('strings_per_mppt')->nullable()->after('mppt_count');
            }
            if (! Schema::hasColumn('inverters', 'mppt_min_voltage')) {
                $table->double('mppt_min_voltage')->nullable()->after('strings_per_mppt');
            }
            if (! Schema::hasColumn('inverters', 'mppt_max_voltage')) {
                $table->double('mppt_max_voltage')->nullable()->after('mppt_min_voltage');
            }
            if (! Schema::hasColumn('inverters', 'max_dc_voltage')) {
                $table->double('max_dc_voltage')->nullable()->after('mppt_max_voltage');
            }
            if (! Schema::hasColumn('inverters', 'max_input_current')) {
                $table->double('max_input_current')->nullable()->after('max_dc_voltage');
            }
            if (! Schema::hasColumn('inverters', 'nominal_ac_voltage')) {
                $table->double('nominal_ac_voltage')->nullable()->after('max_input_current');
            }
        });

        Schema::table('cables', function (Blueprint $table) {
            if (! Schema::hasColumn('cables', 'voltage_type')) {
                $table->string('voltage_type')->nullable()->after('material');
            }
            if (! Schema::hasColumn('cables', 'ampacity')) {
                $table->double('ampacity')->nullable()->after('voltage_type');
            }
            if (! Schema::hasColumn('cables', 'cost_per_meter')) {
                $table->double('cost_per_meter')->nullable()->after('ampacity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cables', function (Blueprint $table) {
            $table->dropColumn([
                'voltage_type',
                'ampacity',
                'cost_per_meter',
            ]);
        });

        Schema::table('inverters', function (Blueprint $table) {
            $table->dropColumn([
                'model',
                'ac_power_kw',
                'max_dc_power_kw',
                'mppt_count',
                'strings_per_mppt',
                'mppt_min_voltage',
                'mppt_max_voltage',
                'max_dc_voltage',
                'max_input_current',
                'nominal_ac_voltage',
            ]);
        });

        Schema::table('panels', function (Blueprint $table) {
            $table->dropColumn([
                'model',
                'voc',
                'vmp',
                'isc',
                'imp',
                'length_m',
                'width_m',
                'temperature_coefficient',
            ]);
        });
    }
};
