<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Greenhouse;
use App\Models\Observation;
use App\Models\YieldForecast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    private const CROP_SLUGS = [
        'Qulupnay' => 'strawberry',
        'Pomidor' => 'tomato',
    ];

    public function index(Request $request): Response
    {
        $greenhouses = Greenhouse::orderBy('code')->get(['id', 'code', 'name', 'crop']);

        $greenhouse = Greenhouse::with(['zones.plantSpots', 'zones.robotStatus'])
            ->when(
                $request->integer('greenhouse'),
                fn ($q, $id) => $q->where('id', $id),
                // Tomato has the stronger model (~95% vs strawberry's ~70%),
                // so it's what opens by default.
                fn ($q) => $q->orderByRaw("crop = 'Pomidor' desc")->orderBy('code')
            )
            ->firstOrFail();

        $zones = $greenhouse->zones->map(function ($zone) {
            return [
                'id' => $zone->id,
                'code' => $zone->code,
                'plants' => $zone->plantSpots->map(function ($spot) {
                    $latest = $spot->observations()->with('category')->first();

                    return [
                        'id' => $spot->id,
                        'public_code' => $spot->public_code,
                        'row_no' => $spot->row_no,
                        'position_no' => $spot->position_no,
                        'health_score' => $latest?->health_score,
                        'category_slug' => $latest?->category?->slug,
                        'category_name' => $latest?->category?->name_uz,
                        'category_type' => $latest?->category?->type,
                        'color' => $latest?->category?->color,
                        'image_url' => $latest ? asset('storage/'.$latest->image_path) : null,
                    ];
                })->values(),
            ];
        });

        $latestObservationIds = Observation::whereHas(
            'plantSpot.zone',
            fn ($q) => $q->where('greenhouse_id', $greenhouse->id)
        )
            ->select(DB::raw('MAX(id) as id'))
            ->groupBy('plant_spot_id')
            ->pluck('id');

        $healthStats = Observation::whereIn('id', $latestObservationIds)
            ->selectRaw('avg(health_score) as avg_health, count(*) as total')
            ->first();

        $healthyCount = Observation::whereIn('observations.id', $latestObservationIds)
            ->join('categories', 'categories.id', '=', 'observations.category_id')
            ->where('categories.type', 'healthy')
            ->count();

        $categoryBreakdown = Observation::whereIn('observations.id', $latestObservationIds)
            ->join('categories', 'categories.id', '=', 'observations.category_id')
            ->selectRaw('categories.name_uz, categories.color, categories.type, count(*) as total')
            ->groupBy('categories.id', 'categories.name_uz', 'categories.color', 'categories.type')
            ->orderByDesc('total')
            ->get();

        $activeAlertsCount = Alert::whereHas(
            'observation.plantSpot.zone',
            fn ($q) => $q->where('greenhouse_id', $greenhouse->id)
        )->where('status', 'new')->count();

        $alerts = Alert::with(['observation.plantSpot', 'observation.category'])
            ->whereHas('observation.plantSpot.zone', fn ($q) => $q->where('greenhouse_id', $greenhouse->id))
            ->where('status', 'new')
            ->latest()
            ->take(8)
            ->get()
            ->map(fn (Alert $alert) => $this->formatAlert($alert));

        $forecasts = YieldForecast::where('greenhouse_id', $greenhouse->id)
            ->orderBy('horizon_days')
            ->get(['horizon_days', 'expected_kg', 'grade_a_pct', 'grade_b_pct', 'grade_c_pct']);

        $robots = $greenhouse->zones->map(fn ($zone) => [
            'zone_id' => $zone->id,
            'zone_code' => $zone->code,
            'status' => $zone->robotStatus?->status ?? 'idle',
            'battery_pct' => $zone->robotStatus?->battery_pct,
            'last_scan_at' => $zone->robotStatus?->last_scan_at?->toIso8601String(),
            'scans_completed_today' => $zone->robotStatus?->scans_completed_today ?? 0,
        ]);

        return Inertia::render('Dashboard', [
            'greenhouses' => $greenhouses,
            'greenhouse' => [
                'id' => $greenhouse->id,
                'code' => $greenhouse->code,
                'name' => $greenhouse->name,
                'location' => $greenhouse->location,
                'area_ha' => $greenhouse->area_ha,
                'crop' => $greenhouse->crop,
                'crop_slug' => self::CROP_SLUGS[$greenhouse->crop] ?? 'tomato',
            ],
            'zones' => $zones,
            'stats' => [
                'avg_health' => round((float) $healthStats->avg_health, 1),
                'total_plants' => (int) $healthStats->total,
                'healthy_plants' => $healthyCount,
                'active_alerts' => $activeAlertsCount,
            ],
            'categoryBreakdown' => $categoryBreakdown,
            'alerts' => $alerts,
            'forecasts' => $forecasts,
            'robots' => $robots,
        ]);
    }

    private function formatAlert(Alert $alert): array
    {
        return [
            'id' => $alert->id,
            'severity' => $alert->severity,
            'message' => $alert->message,
            'status' => $alert->status,
            'created_at' => $alert->created_at->toIso8601String(),
            'plant_spot_id' => $alert->observation->plant_spot_id,
            'plant_code' => $alert->observation->plantSpot->public_code,
            'category' => $alert->observation->category->name_uz,
            'image_url' => asset('storage/'.$alert->observation->image_path),
        ];
    }
}
