<?php

use App\Models\RobotStatus;
use App\Models\Zone;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('robot_statuses', function (Blueprint $table) {
            $table->foreignId('zone_id')->nullable()->after('greenhouse_id')->constrained()->cascadeOnDelete();
            $table->string('current_crop')->nullable()->after('status');
            $table->timestamp('scan_started_at')->nullable()->after('current_crop');
        });

        // Allow inserting the new per-zone rows before the old column is dropped.
        DB::statement('ALTER TABLE robot_statuses ALTER COLUMN greenhouse_id DROP NOT NULL');

        // One robot per zone instead of one per greenhouse: assign the
        // existing greenhouse-level row to that greenhouse's first zone,
        // then create the remaining zones' robots from scratch.
        RobotStatus::all()->each(function (RobotStatus $robot) {
            $zone = Zone::where('greenhouse_id', $robot->greenhouse_id)->orderBy('code')->first();

            if ($zone) {
                $robot->update(['zone_id' => $zone->id]);
            } else {
                $robot->delete();
            }
        });

        Zone::whereDoesntHave('robotStatus')->get()->each(function (Zone $zone) {
            RobotStatus::create([
                'zone_id' => $zone->id,
                'status' => 'idle',
                'battery_pct' => 100,
                'scans_completed_today' => 0,
                'error_count' => 0,
            ]);
        });

        Schema::table('robot_statuses', function (Blueprint $table) {
            $table->dropForeign(['greenhouse_id']);
            $table->dropColumn('greenhouse_id');
        });

        DB::statement('ALTER TABLE robot_statuses ALTER COLUMN zone_id SET NOT NULL');

        Schema::table('robot_statuses', function (Blueprint $table) {
            $table->unique('zone_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('robot_statuses', function (Blueprint $table) {
            $table->foreignId('greenhouse_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        RobotStatus::with('zone')->get()->each(function (RobotStatus $robot) {
            $robot->update(['greenhouse_id' => $robot->zone->greenhouse_id]);
        });

        Schema::table('robot_statuses', function (Blueprint $table) {
            $table->dropUnique(['zone_id']);
            $table->dropForeign(['zone_id']);
            $table->dropColumn(['zone_id', 'current_crop', 'scan_started_at']);
        });
    }
};
