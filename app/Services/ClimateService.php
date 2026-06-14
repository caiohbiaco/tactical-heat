<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ClimateService
{
    // ── Geocoding — converte cidade em lat/long ───────────────────────────
    public function getCoordinates(string $city): array
    {
        $response = Http::withoutVerifying()->get('https://geocoding-api.open-meteo.com/v1/search', [
            'name'     => $city,
            'count'    => 1,
            'language' => 'pt',
            'format'   => 'json',
        ]);

        $results = $response->json('results');

        if (empty($results)) {
            throw new \Exception("Cidade '{$city}' não encontrada.");
        }

        return [
            'name'      => $results[0]['name'],
            'latitude'  => $results[0]['latitude'],
            'longitude' => $results[0]['longitude'],
        ];
    }

    // ── Decide estratégia conforme o ano ──────────────────────────────────
    public function getYearlyClimate(float $lat, float $lon, int $year): array
    {
        $currentYear  = (int) date('Y');
        $currentMonth = (int) date('n'); // sem zero à esquerda

        if ($year < $currentYear) {
            // Ano totalmente passado — archive puro
            return $this->getHistoricalData($lat, $lon, $year);
        }

        if ($year === $currentYear) {
            // Ano atual — meses passados reais + meses futuros projetados
            return $this->getCurrentYearData($lat, $lon, $year, $currentMonth);
        }

        // Ano futuro — projeção pela média dos últimos 10 anos
        return $this->getProjectedData($lat, $lon);
    }

    // ── Histórico completo (anos anteriores) ──────────────────────────────
    private function getHistoricalData(float $lat, float $lon, int $year): array
    {
        $response = Http::withoutVerifying()->get('https://archive-api.open-meteo.com/v1/archive', [
            'latitude'   => $lat,
            'longitude'  => $lon,
            'start_date' => "{$year}-01-01",
            'end_date'   => "{$year}-12-31",
            'daily'      => 'temperature_2m_max,temperature_2m_min,relative_humidity_2m_max',
            'timezone'   => 'America/Sao_Paulo',
        ]);

        return $this->aggregateByMonth($response->json(), false);
    }

    // ── Ano atual: passado = real, futuro = projeção ───────────────────────
    private function getCurrentYearData(float $lat, float $lon, int $year, int $currentMonth): array
    {
        $result = [];

        // Meses já encerrados — dados reais do archive
        // Usa até o último dia do mês ANTERIOR ao atual
        if ($currentMonth > 1) {
            $prevMonth    = $currentMonth - 1;
            $lastDayOfPrev = (int) date('t', mktime(0, 0, 0, $prevMonth, 1, $year));
            $endDate      = sprintf('%04d-%02d-%02d', $year, $prevMonth, $lastDayOfPrev);

            $response = Http::withoutVerifying()->get('https://archive-api.open-meteo.com/v1/archive', [
                'latitude'   => $lat,
                'longitude'  => $lon,
                'start_date' => "{$year}-01-01",
                'end_date'   => $endDate,
                'daily'      => 'temperature_2m_max,temperature_2m_min,relative_humidity_2m_max',
                'timezone'   => 'America/Sao_Paulo',
            ]);

            $result = $this->aggregateByMonth($response->json(), false);
        }

        // Mês atual e futuros — projeção pela média dos últimos 10 anos
        // Uma única chamada busca 10 anos de uma vez usando start/end
        $projected = $this->getProjectedData($lat, $lon);

        foreach ($projected as $month => $data) {
            if ($month >= $currentMonth) {
                $result[$month] = $data; // is_projection = true já vem dentro
            }
        }

        ksort($result);
        return $result;
    }

    // ── Projeção: UMA única chamada buscando 10 anos de uma vez ───────────
    // Antes eram 10 chamadas em loop — isso causava timeout.
    // Agora buscamos o período completo de 10 anos em uma requisição só
    // e calculamos a média por mês do calendário.
    private function getProjectedData(float $lat, float $lon): array
    {
        $currentYear = (int) date('Y');
        $startYear   = $currentYear - 10;
        $endYear     = $currentYear - 1;

        $response = Http::withoutVerifying()
            ->timeout(30) // aumenta timeout pois é um período longo
            ->get('https://archive-api.open-meteo.com/v1/archive', [
                'latitude'   => $lat,
                'longitude'  => $lon,
                'start_date' => "{$startYear}-01-01",
                'end_date'   => "{$endYear}-12-31",
                'daily'      => 'temperature_2m_max,temperature_2m_min,relative_humidity_2m_max',
                'timezone'   => 'America/Sao_Paulo',
            ]);

        $daily = $response->json('daily') ?? [];

        if (empty($daily)) {
            return $this->fallbackMonths();
        }

        // Acumula valores por mês do calendário (1–12) ignorando o ano
        $accumulator = [];

        foreach ($daily['time'] as $i => $date) {
            $month = (int) substr($date, 5, 2); // extrai mês da string YYYY-MM-DD

            $tMax = $daily['temperature_2m_max'][$i];
            $tMin = $daily['temperature_2m_min'][$i];
            $hum  = $daily['relative_humidity_2m_max'][$i];

            if ($tMax !== null) $accumulator[$month]['temp_max'][] = $tMax;
            if ($tMin !== null) $accumulator[$month]['temp_min'][] = $tMin;
            if ($hum  !== null) $accumulator[$month]['humidity'][] = $hum;
        }

        // Calcula média de cada mês com base nos ~10 anos acumulados
        $result = [];
        foreach ($accumulator as $month => $values) {
            $result[$month] = [
                'month'         => $month,
                'temp_max_avg'  => round(array_sum($values['temp_max']) / count($values['temp_max']), 2),
                'temp_min_avg'  => round(array_sum($values['temp_min']) / count($values['temp_min']), 2),
                'humidity_avg'  => round(array_sum($values['humidity'])  / count($values['humidity']),  2),
                'is_projection' => true,
            ];
        }

        ksort($result);
        return $result;
    }

    // ── Agrega dados diários em médias mensais ────────────────────────────
    private function aggregateByMonth(array $data, bool $isProjection): array
    {
        $daily = $data['daily'] ?? [];

        if (empty($daily) || empty($daily['time'])) {
            return [];
        }

        $monthly = [];

        foreach ($daily['time'] as $i => $date) {
            $month = (int) substr($date, 5, 2);

            $tMax = $daily['temperature_2m_max'][$i]        ?? null;
            $tMin = $daily['temperature_2m_min'][$i]        ?? null;
            $hum  = $daily['relative_humidity_2m_max'][$i]  ?? null;

            if ($tMax !== null) $monthly[$month]['temp_max'][] = $tMax;
            if ($tMin !== null) $monthly[$month]['temp_min'][] = $tMin;
            if ($hum  !== null) $monthly[$month]['humidity'][] = $hum;
        }

        $result = [];
        foreach ($monthly as $month => $values) {
            $result[$month] = [
                'month'         => $month,
                'temp_max_avg'  => !empty($values['temp_max']) ? round(array_sum($values['temp_max']) / count($values['temp_max']), 2) : null,
                'temp_min_avg'  => !empty($values['temp_min']) ? round(array_sum($values['temp_min']) / count($values['temp_min']), 2) : null,
                'humidity_avg'  => !empty($values['humidity'])  ? round(array_sum($values['humidity'])  / count($values['humidity']),  2) : null,
                'is_projection' => $isProjection,
            ];
        }

        return $result;
    }

    // ── Fallback caso a API falhe na projeção ─────────────────────────────
    // Retorna valores neutros para não quebrar o fluxo
    private function fallbackMonths(): array
    {
        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $result[$m] = [
                'month'         => $m,
                'temp_max_avg'  => 28.0,
                'temp_min_avg'  => 18.0,
                'humidity_avg'  => 65.0,
                'is_projection' => true,
            ];
        }
        return $result;
    }
}