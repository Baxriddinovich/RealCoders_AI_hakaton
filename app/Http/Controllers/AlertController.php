<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Greenhouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AlertController extends Controller
{
    public function index(Request $request): Response
    {
        $alerts = Alert::with(['observation.plantSpot.zone.greenhouse', 'observation.category'])
            ->when($request->string('status')->toString(), fn ($q, $status) => $q->where('status', $status))
            ->when(
                $request->integer('greenhouse'),
                fn ($q, $id) => $q->whereHas('observation.plantSpot.zone', fn ($q2) => $q2->where('greenhouse_id', $id))
            )
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Alert $alert) => [
                'id' => $alert->id,
                'severity' => $alert->severity,
                'message' => $alert->message,
                'status' => $alert->status,
                'created_at' => $alert->created_at->toIso8601String(),
                'plant_code' => $alert->observation->plantSpot->public_code,
                'plant_spot_id' => $alert->observation->plant_spot_id,
                'zone_code' => $alert->observation->plantSpot->zone->code,
                'greenhouse_name' => $alert->observation->plantSpot->zone->greenhouse->name,
                'category' => $alert->observation->category->name_uz,
                'image_url' => asset('storage/'.$alert->observation->image_path),
            ]);

        return Inertia::render('Alerts', [
            'alerts' => $alerts,
            'filters' => [
                'status' => $request->string('status')->toString(),
                'greenhouse' => $request->integer('greenhouse') ?: null,
            ],
            'greenhouses' => Greenhouse::orderBy('code')->get(['id', 'code', 'name', 'crop']),
        ]);
    }

    public function update(Request $request, Alert $alert): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:new,acknowledged,resolved',
        ]);

        $alert->update($validated);

        return back();
    }
}
