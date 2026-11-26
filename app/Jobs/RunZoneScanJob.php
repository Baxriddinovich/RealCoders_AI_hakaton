<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\Category;
use App\Models\Observation;
use App\Models\Zone;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class RunZoneScanJob implements ShouldQueue
{
    use Queueable;

    private const ML_METADATA_PATH = '/opt/agroskan-ml/model_metadata.json';

    public function __construct(
        private readonly int $zoneId,
        private readonly string $crop,
    ) {}

    public function handle(): void
    {
        $zone = Zone::with(['plantSpots', 'robotStatus'])->find($this->zoneId);
        $robot = $zone?->robotStatus;

        if (! $zone || ! $robot) {
            return;
        }

        $fineSlugs = $this->fineSlugsForCrop($this->crop);
        $categories = Category::whereIn('slug', $fineSlugs)->get();
        $healthyCategories = $categories->where('type', 'healthy')->values();
        $problemCategories = $categories->where('type', '!=', 'healthy')->values();

        if ($categories->isEmpty()) {
            $robot->update(['status' => 'error', 'error_count' => $robot->error_count + 1]);

            return;
        }

        foreach ($zone->plantSpots as $spot) {
            $category = $this->pickCategory($healthyCategories, $problemCategories);
            $previous = $spot->observations()->first();

            $baseHealth = max(5, 100 - $category->severity_weight);
            $healthScore = max(5, min(100, $baseHealth + random_int(-6, 6)));
            $confidence = $category->type === 'healthy' ? random_int(92, 99) : random_int(78, 97);

            $observation = Observation::create([
                'plant_spot_id' => $spot->id,
                'category_id' => $category->id,
                'image_path' => $previous?->image_path ?? '',
                'confidence' => $confidence,
                'health_score' => $healthScore,
                'detection_count' => $previous?->detection_count ?? 1,
                'observed_at' => now(),
            ]);

            if ($healthScore < 72 && $category->type !== 'healthy') {
                Alert::create([
                    'observation_id' => $observation->id,
                    'severity' => $healthScore < 55 ? 'high' : ($healthScore < 65 ? 'medium' : 'low'),
                    'message' => sprintf('%s belgisi — %s, %s%% ishonch', $category->name_uz, $spot->public_code, $confidence),
                    'status' => 'new',
                ]);
            }

            // Camera dwell time per plant spot — keeps the robot visibly
            // "scanning" for a few real seconds instead of finishing instantly.
            usleep(80_000);
        }

        $robot->update([
            'status' => 'idle',
            'current_crop' => null,
            'scan_started_at' => null,
            'last_scan_at' => now(),
            'scans_completed_today' => $robot->scans_completed_today + 1,
            'battery_pct' => max(10, $robot->battery_pct - random_int(3, 8)),
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function fineSlugsForCrop(string $crop): array
    {
        $metadata = json_decode(file_get_contents(self::ML_METADATA_PATH), true);

        return $metadata['crops'][$crop]['fine_classes'] ?? [];
    }

    /**
     * @param  Collection<int, Category>  $healthy
     * @param  Collection<int, Category>  $problems
     */
    private function pickCategory(Collection $healthy, Collection $problems): Category
    {
        $useHealthy = $problems->isEmpty() || ($healthy->isNotEmpty() && random_int(1, 100) <= 85);

        return $useHealthy ? $healthy->random() : $problems->random();
    }
}
