<?php

namespace App\Services;

use App\Models\Search;
use App\Models\Report;
use Illuminate\Support\Facades\Http;

class ReportGenerator
{
    public function generate(Search $search): Report
    {
        $climateData = $search->climateData()->orderBy('month')->get();

        $monthNames = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                       'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

        $dataText = $climateData->map(fn($d) =>
            "{$monthNames[$d->month - 1]}: Tmax={$d->temp_max_avg}°C, " .
            "Tmín={$d->temp_min_avg}°C, Umidade={$d->humidity_avg}%, " .
            "HeatIndex={$d->heat_index_avg}°C, Risco={$d->risk_level}"
        )->implode("\n");

        $sportName = $search->getRawOriginal('sport');

        $prompt = <<<EOT
Você é um especialista em medicina esportiva e climatologia aplicada ao esporte.

Analise os dados climáticos mensais abaixo para a cidade de {$search->city_name}
no ano {$search->year}, considerando o esporte: {$sportName}.

Dados mensais:
{$dataText}

Gere um relatório com:
1. Resumo geral das condições climáticas do ano
2. Meses de maior risco e por que fisiologicamente são perigosos
3. Meses ideais para competição ou treino intenso
4. Sugestões práticas de adaptação (horários, equipamentos, hidratação)
5. Recomendações de prevenção de lesões por calor

Seja objetivo e técnico. Use os títulos numerados acima.
EOT;

        $apiKey = config('services.gemini.key');

        if (empty($apiKey)) {
            throw new \Exception('GEMINI_API_KEY não configurada no .env');
        }

        $response = Http::withoutVerifying()
            ->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [['text' => $prompt]]
                        ]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 1500,
                        'temperature'     => 0.7,
                    ]
                ]
            );

        if ($response->failed()) {
            throw new \Exception('Erro na Gemini API: ' . $response->body());
        }

        $content = $response->json('candidates.0.content.parts.0.text');

        if (!$content) {
            throw new \Exception('Resposta vazia da Gemini API. Retorno: ' . $response->body());
        }

        return Report::updateOrCreate(
            ['search_id' => $search->id],
            ['content' => $content, 'generated_at' => now()]
        );
    }
}