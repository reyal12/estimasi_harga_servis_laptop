<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FuzzyApiService
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.flask.url', 'http://127.0.0.1:5000'), '/');
        $this->apiKey  = config('services.flask.key', '');
        $this->timeout = (int) config('services.flask.timeout', 5);
    }

    /**
     * Call Flask fuzzy estimation endpoint.
     *
     * @return array{success: bool, data?: array, error?: string}
     */
    public function estimate(float $kerusakan, float $usia, float $kesulitan): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept'    => 'application/json',
            ])
                ->timeout($this->timeout)
                ->post("{$this->baseUrl}/api/v1/estimate", [
                    'tingkat_kerusakan'   => $kerusakan,
                    'usia_laptop'         => $usia,
                    'kesulitan_perbaikan' => $kesulitan,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (($data['success'] ?? false) === true) {
                    return ['success' => true, 'data' => $data];
                }
                Log::error('Flask returned success=false', ['body' => $data]);

                return ['success' => false, 'error' => $data['error']['message'] ?? 'Kesalahan dari layanan fuzzy.'];
            }

            if ($response->status() === 422) {
                $data = $response->json();
                Log::warning('Flask validation error', ['body' => $data]);

                return ['success' => false, 'error' => 'Input tidak valid: ' . ($data['error']['message'] ?? '')];
            }

            Log::error('Flask HTTP error', ['status' => $response->status(), 'body' => $response->body()]);

            return ['success' => false, 'error' => 'Layanan perhitungan sedang bermasalah.'];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Flask connection failed', ['message' => $e->getMessage()]);

            return ['success' => false, 'error' => 'Tidak dapat terhubung ke layanan perhitungan. Silakan coba lagi.'];
        } catch (\Throwable $e) {
            Log::error('Flask unexpected error', ['message' => $e->getMessage()]);

            return ['success' => false, 'error' => 'Terjadi kesalahan tidak terduga.'];
        }
    }

    /**
     * Check Flask health endpoint.
     */
    public function health(): bool
    {
        try {
            $response = Http::withHeaders(['X-API-Key' => $this->apiKey])
                ->timeout(3)
                ->get("{$this->baseUrl}/api/v1/health");

            return $response->successful();
        } catch (\Throwable) {
            return false;
        }
    }
}
