<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Greenhouse;
use App\Models\Observation;
use App\Models\RobotStatus;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    public function __construct(private TelegramService $telegram) {}

    public function webhook(Request $request)
    {
        $message = $request->input('message');

        if (! $message) {
            return response()->json(['ok' => true]);
        }

        $chatId = $message['chat']['id'] ?? null;
        $text = trim($message['text'] ?? '');

        if (! $chatId) {
            return response()->json(['ok' => true]);
        }

        try {
            match (true) {
                str_starts_with($text, '/start') => $this->handleStart($chatId),
                str_starts_with($text, '/hisobot') => $this->handleReport($chatId),
                str_starts_with($text, '/holat') => $this->handleStatus($chatId),
                default => $this->handleHelp($chatId),
            };
        } catch (\Throwable $e) {
            Log::error('Telegram webhook error: '.$e->getMessage());
            $this->telegram->sendMessage($chatId, "Xatolik yuz berdi: {$e->getMessage()}");
        }

        return response()->json(['ok' => true]);
    }

    private function handleStart(int|string $chatId): void
    {
        $this->telegram->sendMessage($chatId, implode("\n", [
            '🌱 <b>AgroSkan botiga xush kelibsiz!</b>',
            '',
            'Issiqxonangiz holatini shu yerdan kuzatishingiz mumkin.',
            '',
            '<b>Buyruqlar:</b>',
            '/holat — joriy holat qisqa xulosasi',
            '/hisobot — to\'liq PDF hisobotni yuklab olish',
        ]));
    }

    private function handleHelp(int|string $chatId): void
    {
        $this->telegram->sendMessage($chatId, 'Buyruqlar: /holat, /hisobot');
    }

    private function handleStatus(int|string $chatId): void
    {
        $greenhouse = Greenhouse::first();

        if (! $greenhouse) {
            $this->telegram->sendMessage($chatId, 'Issiqxona topilmadi.');

            return;
        }

        $latestObservationIds = Observation::whereHas(
            'plantSpot.zone',
            fn ($q) => $q->where('greenhouse_id', $greenhouse->id)
        )
            ->select(DB::raw('MAX(id) as id'))
            ->groupBy('plant_spot_id')
            ->pluck('id');

        $stats = Observation::whereIn('id', $latestObservationIds)
            ->selectRaw('avg(health_score) as avg_health, count(*) as total')
            ->first();

        $activeAlerts = Alert::whereHas(
            'observation.plantSpot.zone',
            fn ($q) => $q->where('greenhouse_id', $greenhouse->id)
        )->where('status', 'new')->count();

        $robot = RobotStatus::where('greenhouse_id', $greenhouse->id)->latest('last_scan_at')->first();

        $this->telegram->sendMessage($chatId, implode("\n", [
            "🏠 <b>{$greenhouse->name}</b>",
            "📍 {$greenhouse->location} · {$greenhouse->crop}",
            '',
            '📊 O\'rtacha sog\'liq indeksi: <b>'.round((float) $stats->avg_health, 1).'/100</b>',
            '🌿 Jami o\'simliklar: <b>'.(int) $stats->total.'</b>',
            '⚠️ Faol ogohlantirishlar: <b>'.$activeAlerts.'</b>',
            '🤖 Robot holati: <b>'.($robot?->status ?? 'noma\'lum').'</b>',
            '',
            'To\'liq hisobot uchun: /hisobot',
        ]));
    }

    private function handleReport(int|string $chatId): void
    {
        $greenhouse = Greenhouse::first();

        if (! $greenhouse) {
            $this->telegram->sendMessage($chatId, 'Issiqxona topilmadi.');

            return;
        }

        $this->telegram->sendMessage($chatId, 'Hisobot tayyorlanmoqda...');

        $pdfController = app(ReportPdfController::class);
        $path = $pdfController->path($greenhouse);

        $this->telegram->sendDocument(
            $chatId,
            $path,
            "AgroSkan hisobot — {$greenhouse->name} — ".now()->format('d.m.Y')
        );

        @unlink($path);
    }
}
