<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    private string $baseUrl;

    public function __construct()
    {
        $token = config('services.telegram.bot_token');
        $this->baseUrl = "https://api.telegram.org/bot{$token}";
    }

    public function sendMessage(int|string $chatId, string $text, array $options = []): array
    {
        return Http::asForm()->post("{$this->baseUrl}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            ...$options,
        ])->json();
    }

    public function sendDocument(int|string $chatId, string $filePath, ?string $caption = null): array
    {
        $request = Http::attach('document', file_get_contents($filePath), basename($filePath));

        return $request->post("{$this->baseUrl}/sendDocument", array_filter([
            'chat_id' => $chatId,
            'caption' => $caption,
        ]))->json();
    }

    public function setWebhook(string $url): array
    {
        return Http::asForm()->post("{$this->baseUrl}/setWebhook", [
            'url' => $url,
        ])->json();
    }

    public function getWebhookInfo(): array
    {
        return Http::get("{$this->baseUrl}/getWebhookInfo")->json();
    }
}
