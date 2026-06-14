<?php

namespace App\Services;

use App\Models\Sport;

class RiskAnalyzer
{
    // Fórmula de Rothfusz para Heat Index
    public function calculateHeatIndex(float $tempC, float $humidity): float
    {
        $T = ($tempC * 9/5) + 32; // converte para Fahrenheit
        $R = $humidity;

        $HI = -42.379 + 2.04901523*$T + 10.14333127*$R
            - 0.22475541*$T*$R - 0.00683783*$T*$T
            - 0.05481717*$R*$R + 0.00122874*$T*$T*$R
            + 0.00085282*$T*$R*$R - 0.00000199*$T*$T*$R*$R;

        return round(($HI - 32) * 5/9, 2); // volta para Celsius
    }

    public function assessRisk(float $tempMax, float $humidity, Sport $sport): string
    {
        if ($tempMax >= $sport->temp_high_risk || $humidity >= 85) {
            return 'high';
        }
        if ($tempMax >= $sport->temp_medium_risk || $humidity >= $sport->humidity_medium_risk) {
            return 'medium';
        }
        return 'low';
    }

    public function analyzeSearch(array $monthlyData, Sport $sport): array
    {
        return array_map(function ($month) use ($sport) {
            $heatIndex = $this->calculateHeatIndex($month['temp_max_avg'], $month['humidity_avg']);
            $risk      = $this->assessRisk($month['temp_max_avg'], $month['humidity_avg'], $sport);

            return array_merge($month, [
                'heat_index_avg' => $heatIndex,
                'risk_level'     => $risk,
            ]);
        }, $monthlyData);
    }
}