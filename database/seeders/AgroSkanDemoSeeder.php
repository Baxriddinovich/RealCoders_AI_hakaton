<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Greenhouse;
use App\Models\Observation;
use App\Models\PlantSpot;
use App\Models\RobotStatus;
use App\Models\YieldForecast;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AgroSkanDemoSeeder extends Seeder
{
    /**
     * Dataset prefix => category slug, per greenhouse dataset folder.
     * Longest/most specific prefixes are checked first to avoid false matches.
     *
     * @var array<string, array<string, string>>
     */
    private const PREFIX_MAPS = [
        'dataset' => [
            'fertile_K_P' => 'deficiency_potassium_phosphorus',
            'fertile_Ca' => 'deficiency_calcium',
            'fertile_Fe' => 'deficiency_iron',
            'ill_anthracnose' => 'disease_anthracnose',
            'ill_blight' => 'disease_blight',
            'ill_gray_mold' => 'disease_gray_mold',
            'ill_insect' => 'pest_insect',
            'ill_leaf_spot' => 'disease_leaf_spot',
            'ill_powdery_mildew' => 'disease_powdery_mildew',
            'flower' => 'healthy_flower',
            'fruit' => 'healthy_fruit',
        ],
        'dataset-tomato' => [
            'Tomato___Bacterial_spot' => 'disease_tomato_bacterial_spot',
            'Tomato___Early_blight' => 'disease_tomato_early_blight',
            'Tomato___Late_blight' => 'disease_tomato_late_blight',
            'Tomato___Leaf_Mold' => 'disease_tomato_leaf_mold',
            'Tomato___Septoria_leaf_spot' => 'disease_tomato_septoria_leaf_spot',
            'Tomato___Spider_mites' => 'pest_tomato_spider_mites',
            'Tomato___Target_Spot' => 'disease_tomato_target_spot',
            'Tomato___Yellow_Leaf_Curl_Virus' => 'disease_tomato_yellow_leaf_curl_virus',
            'Tomato___mosaic_virus' => 'disease_tomato_mosaic_virus',
            'Tomato___healthy' => 'healthy_tomato',
        ],
    ];

    public function run(): void
    {
        $categories = $this->seedCategories();

        $strawberryHouse = Greenhouse::updateOrCreate(
            ['code' => 'GH01'],
            [
                'name' => 'Jizzax pilot issiqxonasi',
                'location' => 'Jizzax viloyati',
                'area_ha' => 0.5,
                'crop' => 'Qulupnay',
                'cover_type' => 'Plyonka',
            ]
        );
        $this->seedGreenhouseData($strawberryHouse, 'dataset', $categories, [420.50, 860.00]);

        $tomatoHouse = Greenhouse::updateOrCreate(
            ['code' => 'GH02'],
            [
                'name' => 'Farg\'ona pomidor issiqxonasi',
                'location' => 'Farg\'ona viloyati',
                'area_ha' => 0.8,
                'crop' => 'Pomidor',
                'cover_type' => 'Polikarbonat',
            ]
        );
        $this->seedGreenhouseData($tomatoHouse, 'dataset-tomato', $categories, [1120.00, 2260.00]);
    }

    /**
     * @param  array<string, Category>  $categories
     * @param  array{0: float, 1: float}  $forecastKg
     */
    private function seedGreenhouseData(Greenhouse $greenhouse, string $datasetFolder, array $categories, array $forecastKg): void
    {
        $zones = $this->seedZones($greenhouse);
        $prefixMap = self::PREFIX_MAPS[$datasetFolder];

        $files = collect(Storage::disk('public')->files($datasetFolder))
            ->filter(fn (string $path) => str_ends_with($path, '.jpg'))
            ->shuffle(2026)
            ->values();

        $rowCursor = array_fill_keys($zones->pluck('id')->all(), 1);
        $posCursor = array_fill_keys($zones->pluck('id')->all(), 1);

        $zoneIds = $zones->pluck('id')->all();
        $zoneIndex = 0;

        foreach ($files as $imagePath) {
            $basename = basename($imagePath, '.jpg');
            $slug = $this->resolveCategorySlug($basename, $prefixMap);

            if ($slug === null) {
                continue;
            }

            $category = $categories[$slug];
            $zone = $zones[$zoneIds[$zoneIndex % count($zoneIds)]];
            $zoneIndex++;

            $row = $rowCursor[$zone->id];
            $pos = $posCursor[$zone->id];
            $posCursor[$zone->id]++;
            if ($posCursor[$zone->id] > $zone->position_count) {
                $posCursor[$zone->id] = 1;
                $rowCursor[$zone->id]++;
                $row = $rowCursor[$zone->id];
            }

            $publicCode = sprintf('%s-%s-R%02d-P%03d', $greenhouse->code, $zone->code, $row, $pos);

            $plantSpot = PlantSpot::create([
                'zone_id' => $zone->id,
                'public_code' => $publicCode,
                'row_no' => $row,
                'position_no' => $pos,
                'planted_at' => Carbon::now()->subDays(random_int(40, 120)),
            ]);

            $detectionCount = $this->detectionCount($imagePath);
            $baseHealth = max(5, 100 - $category->severity_weight);

            // Real label is the final/current scan; a few synthetic earlier
            // scans are layered in so the plant card has a trend to chart —
            // the underlying image + category + severity is the real data point.
            $historyDays = [6, 4, 2, 0];
            foreach ($historyDays as $daysAgo) {
                $isLatest = $daysAgo === 0;
                $jitter = $isLatest ? 0 : random_int(-8, 8);
                $healthScore = max(5, min(100, $baseHealth + $jitter));
                $confidence = $category->type === 'healthy'
                    ? random_int(92, 99)
                    : random_int(78, 97);

                $observation = Observation::create([
                    'plant_spot_id' => $plantSpot->id,
                    'category_id' => $category->id,
                    'image_path' => $imagePath,
                    'confidence' => $confidence,
                    'health_score' => $healthScore,
                    'detection_count' => $detectionCount,
                    'observed_at' => Carbon::now()->subDays($daysAgo)->setTime(7, 14),
                ]);

                if ($isLatest && $healthScore < 72 && $category->type !== 'healthy') {
                    Alert::create([
                        'observation_id' => $observation->id,
                        'severity' => $healthScore < 55 ? 'high' : ($healthScore < 65 ? 'medium' : 'low'),
                        'message' => sprintf(
                            '%s belgisi — %s, %s',
                            $category->name_uz,
                            $plantSpot->public_code,
                            $confidence.'% ishonch'
                        ),
                        'status' => 'new',
                    ]);
                }
            }
        }

        $this->seedYieldForecast($greenhouse, $forecastKg);
        $this->seedRobotStatuses($zones);
        $this->seedExpenses($greenhouse);
    }

    /**
     * @return array<string, Category>
     */
    private function seedCategories(): array
    {
        $rows = [
            ['healthy_fruit', 'Sog\'lom meva', 'Healthy Fruit', 'healthy', 0, '#2E9E44'],
            ['healthy_flower', 'Sog\'lom gul', 'Healthy Flower', 'healthy', 0, '#4CAF50'],
            ['deficiency_calcium', 'Kaltsiy yetishmovchiligi', 'Calcium Deficiency', 'deficiency', 15, '#E6C229'],
            ['deficiency_iron', 'Temir yetishmovchiligi', 'Iron Deficiency', 'deficiency', 15, '#D4A017'],
            ['deficiency_potassium_phosphorus', 'Kaliy-fosfor yetishmovchiligi', 'Potassium-Phosphorus Deficiency', 'deficiency', 18, '#C68E17'],
            ['disease_anthracnose', 'Antraknoz', 'Anthracnose', 'disease', 35, '#B22222'],
            ['disease_blight', 'So\'lish kasalligi (Blight)', 'Blossom Blight', 'disease', 32, '#8B0000'],
            ['disease_gray_mold', 'Kulrang chirish', 'Gray Mold', 'disease', 38, '#D62828'],
            ['pest_insect', 'Zararkunanda zarari', 'Insect Damage', 'pest', 20, '#8E44AD'],
            ['disease_leaf_spot', 'Barg dog\'lanishi', 'Leaf Spot', 'disease', 25, '#F28C28'],
            ['disease_powdery_mildew', 'Un-shudring', 'Powdery Mildew', 'disease', 30, '#7F8C8D'],

            // Pomidor (PlantVillage dataset)
            ['healthy_tomato', "Sog'lom pomidor", 'Healthy Tomato', 'healthy', 0, '#2E9E44'],
            ['disease_tomato_bacterial_spot', 'Bakterial dog\'lanish', 'Bacterial Spot', 'disease', 28, '#B22222'],
            ['disease_tomato_early_blight', 'Erta so\'lish (Alternarioz)', 'Early Blight', 'disease', 30, '#A0522D'],
            ['disease_tomato_late_blight', 'Kech so\'lish (Fitoftora)', 'Late Blight', 'disease', 40, '#6B0F1A'],
            ['disease_tomato_leaf_mold', 'Barg mog\'ori', 'Leaf Mold', 'disease', 26, '#7F8C8D'],
            ['disease_tomato_septoria_leaf_spot', 'Septoriya dog\'lanishi', 'Septoria Leaf Spot', 'disease', 27, '#F28C28'],
            ['disease_tomato_target_spot', 'Nishon dog\'i', 'Target Spot', 'disease', 29, '#C0392B'],
            ['disease_tomato_yellow_leaf_curl_virus', 'Sarg\'ayish virusi (TYLCV)', 'Yellow Leaf Curl Virus', 'disease', 42, '#D4AC0D'],
            ['disease_tomato_mosaic_virus', 'Mozaika virusi (ToMV)', 'Mosaic Virus', 'disease', 38, '#8E44AD'],
            ['pest_tomato_spider_mites', 'O\'rgimchakkana zarari', 'Spider Mites', 'pest', 22, '#7D3C98'],
        ];

        $categories = [];
        foreach ($rows as [$slug, $nameUz, $nameEn, $type, $severity, $color]) {
            $categories[$slug] = Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name_uz' => $nameUz,
                    'name_en' => $nameEn,
                    'type' => $type,
                    'severity_weight' => $severity,
                    'color' => $color,
                ]
            );
        }

        return $categories;
    }

    /**
     * @return Collection<int, Zone>
     */
    private function seedZones(Greenhouse $greenhouse)
    {
        $codes = ['B1', 'B2', 'B3', 'B4'];

        return collect($codes)->mapWithKeys(function (string $code) use ($greenhouse) {
            $zone = Zone::updateOrCreate(
                ['greenhouse_id' => $greenhouse->id, 'code' => $code],
                ['row_count' => 20, 'position_count' => 20]
            );

            return [$zone->id => $zone];
        });
    }

    /**
     * @param  array<string, string>  $prefixMap
     */
    private function resolveCategorySlug(string $basename, array $prefixMap): ?string
    {
        // Matched by substring rather than by stripping a fixed prefix,
        // since tomato class names ("Tomato___Bacterial_spot") themselves
        // contain "__", which would otherwise collide with the
        // "split__name__idx" filename convention.
        foreach ($prefixMap as $needle => $slug) {
            if (str_contains($basename, $needle)) {
                return $slug;
            }
        }

        return null;
    }

    private function detectionCount(string $imagePath): int
    {
        $labelPath = Str::beforeLast($imagePath, '.jpg').'.txt';

        if (! Storage::disk('public')->exists($labelPath)) {
            return 1;
        }

        $contents = trim(Storage::disk('public')->get($labelPath));

        return $contents === '' ? 0 : count(explode("\n", $contents));
    }

    /**
     * @param  array{0: float, 1: float}  $forecastKg
     */
    private function seedYieldForecast(Greenhouse $greenhouse, array $forecastKg): void
    {
        foreach ([7, 14] as $i => $horizon) {
            YieldForecast::updateOrCreate(
                ['greenhouse_id' => $greenhouse->id, 'horizon_days' => $horizon],
                [
                    'forecast_date' => Carbon::now()->toDateString(),
                    'expected_kg' => $forecastKg[$i],
                    'grade_a_pct' => 62.0,
                    'grade_b_pct' => 28.0,
                    'grade_c_pct' => 10.0,
                ]
            );
        }
    }

    private function seedExpenses(Greenhouse $greenhouse): void
    {
        if ($greenhouse->expenses()->exists()) {
            return;
        }

        $monthlyPlan = [
            'gaz' => [900_000, 1_600_000],
            'svet' => [700_000, 1_300_000],
            'suv' => [200_000, 450_000],
            'ishchi_kuchi' => [1_500_000, 2_500_000],
            'urug_kochat' => [0, 600_000],
            'ogit' => [150_000, 500_000],
            'dori' => [0, 350_000],
            'texnika' => [0, 400_000],
        ];

        for ($monthsAgo = 3; $monthsAgo >= 0; $monthsAgo--) {
            $month = Carbon::now()->subMonths($monthsAgo);

            foreach ($monthlyPlan as $category => [$min, $max]) {
                if ($max === 0) {
                    continue;
                }

                Expense::create([
                    'greenhouse_id' => $greenhouse->id,
                    'category' => $category,
                    'amount' => random_int($min, $max),
                    'spent_on' => $month->copy()->setDay(random_int(3, 25)),
                ]);
            }
        }
    }

    /**
     * @param  Collection<int, Zone>  $zones
     */
    private function seedRobotStatuses(Collection $zones): void
    {
        foreach ($zones as $zone) {
            RobotStatus::updateOrCreate(
                ['zone_id' => $zone->id],
                [
                    'status' => 'idle',
                    'battery_pct' => random_int(80, 100),
                    'last_scan_at' => Carbon::now()->setTime(7, 14),
                    'scans_completed_today' => 1,
                    'error_count' => 0,
                ]
            );
        }
    }
}
