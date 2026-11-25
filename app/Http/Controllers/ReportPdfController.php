<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Greenhouse;
use App\Models\Observation;
use App\Models\YieldForecast;
use App\Models\Zone;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReportPdfController extends Controller
{
    public function download(Greenhouse $greenhouse): Response
    {
        return $this->buildPdf($greenhouse)->download(
            "agroskan-hisobot-{$greenhouse->code}-".now()->format('Y-m-d').'.pdf'
        );
    }

    public function path(Greenhouse $greenhouse): string
    {
        $filename = "agroskan-hisobot-{$greenhouse->code}-".now()->format('Y-m-d_H-i').'.pdf';
        $path = storage_path("app/reports/{$filename}");

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0775, true);
        }

        $this->buildPdf($greenhouse)->save($path);

        return $path;
    }

    private function buildPdf(Greenhouse $greenhouse)
    {
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

        $byZone = Zone::where('greenhouse_id', $greenhouse->id)
            ->with(['plantSpots' => function ($q) use ($latestObservationIds) {
                $q->with(['observations' => fn ($q2) => $q2->whereIn('observations.id', $latestObservationIds)->with('category')]);
            }])
            ->get()
            ->map(function (Zone $zone) {
                $scores = $zone->plantSpots->map(fn ($s) => $s->observations->first()?->health_score)->filter();
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
            ->selectRaw('categories.name_uz, categories.type, count(*) as total')
            ->groupBy('categories.id', 'categories.name_uz', 'categories.type')
            ->orderByDesc('total')
            ->get();

        $alerts = Alert::whereHas(
            'observation.plantSpot.zone',
            fn ($q) => $q->where('greenhouse_id', $greenhouse->id)
        )
            ->with(['observation.plantSpot', 'observation.category'])
            ->where('status', 'new')
            ->latest()
            ->take(15)
            ->get();

        $forecasts = YieldForecast::where('greenhouse_id', $greenhouse->id)->orderBy('horizon_days')->get();

        return Pdf::loadView('reports.pdf', [
            'greenhouse' => $greenhouse,
            'generatedAt' => now()->format('d.m.Y H:i'),
            'avgHealth' => round((float) $healthStats->avg_health, 1),
            'totalPlants' => (int) $healthStats->total,
            'activeAlerts' => $alerts->count(),
            'byZone' => $byZone,
            'byCategory' => $byCategory,
            'alerts' => $alerts,
            'forecasts' => $forecasts,
        ])->setPaper('a4');
    }
}
