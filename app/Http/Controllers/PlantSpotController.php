<?php

namespace App\Http\Controllers;

use App\Models\PlantSpot;
use Inertia\Inertia;
use Inertia\Response;

class PlantSpotController extends Controller
{
    public function show(PlantSpot $plantSpot): Response
    {
        $plantSpot->load(['zone.greenhouse']);

        $observations = $plantSpot->observations()
            ->with('category')
            ->orderBy('observed_at')
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'observed_at' => $o->observed_at->toIso8601String(),
                'health_score' => $o->health_score,
                'confidence' => $o->confidence,
                'detection_count' => $o->detection_count,
                'category' => [
                    'slug' => $o->category->slug,
                    'name_uz' => $o->category->name_uz,
                    'type' => $o->category->type,
                    'color' => $o->category->color,
                ],
                'image_url' => asset('storage/'.$o->image_path),
            ]);

        return Inertia::render('PlantSpot', [
            'plant' => [
                'id' => $plantSpot->id,
                'public_code' => $plantSpot->public_code,
                'planted_at' => $plantSpot->planted_at?->toDateString(),
                'zone_code' => $plantSpot->zone->code,
                'greenhouse_name' => $plantSpot->zone->greenhouse->name,
            ],
            'observations' => $observations,
        ]);
    }
}
