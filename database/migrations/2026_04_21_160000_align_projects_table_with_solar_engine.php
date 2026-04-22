<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('projects')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'city')) {
                $table->string('city')->nullable()->after('input_hash');
            }

            if (! Schema::hasColumn('projects', 'input_data')) {
                $table->json('input_data')->nullable()->after('city');
            }

            if (! Schema::hasColumn('projects', 'required_kw')) {
                $table->double('required_kw')->nullable()->after('input_data');
            }

            if (! Schema::hasColumn('projects', 'real_kw')) {
                $table->double('real_kw')->nullable()->after('required_kw');
            }

            if (! Schema::hasColumn('projects', 'panels')) {
                $table->integer('panels')->nullable()->after('real_kw');
            }

            if (! Schema::hasColumn('projects', 'production')) {
                $table->json('production')->nullable()->after('panels');
            }

            if (! Schema::hasColumn('projects', 'costs')) {
                $table->json('costs')->nullable()->after('production');
            }

            if (! Schema::hasColumn('projects', 'roi_years')) {
                $table->double('roi_years')->nullable()->after('costs');
            }
        });

        if (Schema::hasColumn('projects', 'location') && Schema::hasColumn('projects', 'city')) {
            DB::table('projects')
                ->whereNull('city')
                ->update([
                    'city' => DB::raw('COALESCE(location, name, "Legacy Project")'),
                ]);
        }

        if (Schema::hasColumn('projects', 'consumption') && Schema::hasColumn('projects', 'input_data')) {
            $legacyRows = DB::table('projects')
                ->select('id', 'city', 'location', 'consumption', 'surface', 'budget')
                ->whereNull('input_data')
                ->get();

            foreach ($legacyRows as $row) {
                DB::table('projects')
                    ->where('id', $row->id)
                    ->update([
                        'input_data' => json_encode([
                            'legacy' => true,
                            'form' => [
                                'city' => $row->city ?? $row->location ?? 'Legacy Project',
                                'consumption' => $row->consumption,
                                'surface' => $row->surface,
                                'budget' => $row->budget,
                            ],
                        ]),
                    ]);
            }
        }
    }

    public function down(): void
    {
        // Intentionally left non-destructive because this migration aligns legacy data.
    }
};
