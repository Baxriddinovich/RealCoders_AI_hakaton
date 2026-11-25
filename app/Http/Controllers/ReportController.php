<?php

namespace App\Http\Controllers;

use App\Models\Greenhouse;
use App\Models\Observation;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        $greenhouses = Greenhouse::orderBy('code')->get(['id', 'code', 'name', 'crop']);

        $greenhouse = Greenhouse::when(
            $request->integer('greenhouse'),
            fn ($q, $id) => $q->where('id', $id),
            fn ($q) => $q->orderBy('code')
        )->firstOrFail();

        $latestObservationIds = Observation::whereHas(
            'plantSpot.zone',
            fn ($q) => $q->where('greenhouse_id', $greenhouse->id)
        )
            ->select(DB::raw('MAX(id) as id'))
            ->groupBy('plant_spot_id')
            ->pluck('id');

        $byZone = Zone::where('greenhouse_id', $greenhouse->id)
            ->with(['plantSpots' => function ($q) use ($latestObservationIds) {
                $q->with(['observations' => fn ($q2) => $q2->whereIn('observations.id', $latestObservationIds)->with('category')]);
            }])
            ->get()
            ->map(function (Zone $zone) {
                $scores = $zone->plantSpots
                    ->map(fn ($s) => $s->observations->first()?->health_score)
                    ->filter();

                $problemCount = $zone->plantSpots
                    ->map(fn ($s) => $s->observations->first())
                    ->filter(fn ($o) => $o && $o->category->type !== 'healthy')
                    ->count();

                return [
                    'zone_code' => $zone->code,
                    'plant_count' => $zone->plantSpots->count(),
                    'avg_health' => $scores->isNotEmpty() ? round($scores->avg(), 1) : null,
                    'problem_count' => $problemCount,
                ];
            });

        $byCategory = Observation::whereIn('observations.id', $latestObservationIds)
            ->join('categories', 'categories.id', '=', 'observations.category_id')
            ->selectRaw('categories.name_uz, categories.type, categories.color, count(*) as total')
            ->groupBy('categories.id', 'categories.name_uz', 'categories.type', 'categories.color')
            ->orderByDesc('total')
            ->get();

        return Inertia::render('Reports', [
            'greenhouses' => $greenhouses,
            'greenhouseId' => $greenhouse->id,
            'byZone' => $byZone,
            'byCategory' => $byCategory,
        ]);
    }
}
