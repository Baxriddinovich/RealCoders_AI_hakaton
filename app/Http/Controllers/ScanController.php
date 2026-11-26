<?php

namespace App\Http\Controllers;

use App\Jobs\RunZoneScanJob;
use App\Models\RobotStatus;
use App\Models\Zone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function start(Request $request): RedirectResponse
    {
        $metadata = json_decode(file_get_contents('/opt/agroskan-ml/model_metadata.json'), true);
        $validCrops = array_keys($metadata['crops'] ?? []);

        $validated = $request->validate([
            'greenhouse_id' => 'required|exists:greenhouses,id',
            'crop' => ['required', 'in:'.implode(',', $validCrops)],
        ]);

        $zones = Zone::where('greenhouse_id', $validated['greenhouse_id'])->with('robotStatus')->get();

        foreach ($zones as $zone) {
            $robot = $zone->robotStatus;

            if (! $robot || $robot->status === 'scanning') {
                continue;
            }

            $robot->update([
                'status' => 'scanning',
                'current_crop' => $validated['crop'],
                'scan_started_at' => now(),
            ]);

            RunZoneScanJob::dispatch($zone->id, $validated['crop']);
        }

        return back();
    }

    public function statuses(Request $request): JsonResponse
    {
        $robots = RobotStatus::with('zone')
            ->whereHas('zone', fn ($q) => $q->where('greenhouse_id', $request->integer('greenhouse_id')))
            ->get()
            ->map(fn (RobotStatus $robot) => [
                'zone_id' => $robot->zone_id,
                'zone_code' => $robot->zone->code,
                'status' => $robot->status,
                'battery_pct' => $robot->battery_pct,
                'last_scan_at' => $robot->last_scan_at?->toIso8601String(),
                'scans_completed_today' => $robot->scans_completed_today,
            ])
            ->values();

        return response()->json(['robots' => $robots]);
    }
}
