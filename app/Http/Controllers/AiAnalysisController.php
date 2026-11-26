<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AiAnalysisController extends Controller
{
    private const ML_DIR = '/opt/agroskan-ml';

    private const COARSE_LABELS = [
        'healthy' => "Sog'lom",
        'disease' => 'Kasallik',
        'pest' => 'Zararkunanda',
        'deficiency' => 'Oziq yetishmovchiligi',
    ];

    private const COARSE_COLORS = [
        'healthy' => '#2E9E44',
        'disease' => '#D62828',
        'pest' => '#8E44AD',
        'deficiency' => '#E6C229',
    ];

    private const RIPENESS_LABELS = [
        'ripe' => 'Pishgan',
        'unripe' => 'Pishmagan',
        'ready' => "Yig'ishtirishga tayyor",
        'overripe' => 'Ortiq pishib ketgan',
    ];

    private const RIPENESS_COLORS = [
        'ripe' => '#D62828',
        'unripe' => '#2E9E44',
        'ready' => '#2E9E44',
        'overripe' => '#C68E17',
    ];

    private function metadata(): array
    {
        return json_decode(file_get_contents(self::ML_DIR.'/model_metadata.json'), true);
    }

    public function index(): Response
    {
        $metadata = $this->metadata();

        return Inertia::render('AiAnalysis', [
            'modelInfo' => [
                'algorithm' => $metadata['algorithm'] ?? 'random_forest',
                'train_size' => $metadata['train_size'],
                'val_size' => $metadata['val_size'],
                'test_size' => $metadata['test_size'],
                'coarse_test_accuracy' => $metadata['coarse_test_accuracy'],
                'trained_at' => $metadata['trained_at'],
                'crops' => $metadata['crops'],
            ],
        ]);
    }

    public function analyze(Request $request): RedirectResponse
    {
        $validCrops = array_keys($this->metadata()['crops'] ?? []);

        $validated = $request->validate([
            'image' => 'required|image|max:10240',
            'crop' => ['required', 'in:'.implode(',', $validCrops)],
        ]);

        $path = $request->file('image')->store('ai-uploads', 'public');
        $fullPath = Storage::disk('public')->path($path);

        $result = Process::timeout(30)->run([
            'python3', self::ML_DIR.'/predict.py', $fullPath, $validated['crop'],
        ]);

        if (! $result->successful()) {
            return back()->withErrors(['image' => "Tahlil amalga oshmadi: {$result->errorOutput()}"]);
        }

        $prediction = json_decode($result->output(), true);

        if (isset($prediction['error'])) {
            return back()->withErrors(['image' => $prediction['error']]);
        }

        $fineCategories = Category::whereIn('slug', array_keys($prediction['fine']['probabilities']))
            ->get()
            ->keyBy('slug');

        $fineProbabilities = collect($prediction['fine']['probabilities'])
            ->map(fn (float $prob, string $slug) => [
                'slug' => $slug,
                'name_uz' => $fineCategories->get($slug)?->name_uz ?? $slug,
                'color' => $fineCategories->get($slug)?->color ?? '#888888',
                'probability' => $prob,
            ])
            ->values();

        $coarseProbabilities = collect($prediction['coarse']['probabilities'])
            ->map(fn (float $prob, string $slug) => [
                'slug' => $slug,
                'name_uz' => self::COARSE_LABELS[$slug] ?? $slug,
                'color' => self::COARSE_COLORS[$slug] ?? '#888888',
                'probability' => $prob,
            ])
            ->values();

        $coarseLabel = $prediction['coarse']['label'];
        $ripenessLabel = $prediction['ripeness']['label'];

        // The fine model is trained independently from the coarse one, so its
        // raw top pick can disagree with the coarse verdict (e.g. "healthy"
        // overall but a low-confidence disease name shown as the "exact
        // diagnosis" — confusing for farmers). Keep the headline fine
        // diagnosis consistent by picking the best-scoring fine class that
        // actually belongs to the coarse category we're reporting.
        $fineSlugInCoarseGroup = collect($prediction['fine']['probabilities'])
            ->keys()
            ->first(fn (string $slug) => $fineCategories->get($slug)?->type === $coarseLabel);

        $fineLabel = $fineSlugInCoarseGroup ?? $prediction['fine']['label'];
        $fineConfidence = $fineSlugInCoarseGroup
            ? $prediction['fine']['probabilities'][$fineSlugInCoarseGroup]
            : $prediction['fine']['confidence'];
        $topFine = $fineCategories->get($fineLabel);

        return back()->with('analysis', [
            'crop' => $validated['crop'],
            'image_url' => asset('storage/'.$path),
            'coarse' => [
                'label' => $coarseLabel,
                'label_name' => self::COARSE_LABELS[$coarseLabel] ?? $coarseLabel,
                'color' => self::COARSE_COLORS[$coarseLabel] ?? '#888888',
                'confidence' => $prediction['coarse']['confidence'],
                'probabilities' => $coarseProbabilities,
            ],
            'fine' => [
                'label' => $fineLabel,
                'label_name' => $topFine?->name_uz ?? $fineLabel,
                'color' => $topFine?->color ?? '#888888',
                'confidence' => $fineConfidence,
                'probabilities' => $fineProbabilities,
            ],
            'ripeness' => [
                'label' => $ripenessLabel,
                'label_name' => self::RIPENESS_LABELS[$ripenessLabel] ?? $ripenessLabel,
                'color' => self::RIPENESS_COLORS[$ripenessLabel] ?? '#888888',
                'confidence' => $prediction['ripeness']['confidence'],
            ],
        ]);
    }
}
