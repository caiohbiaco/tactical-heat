<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sport;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        Sport::truncate();

        $sports = [
            // ── Esportes originais ──────────────────────────────────────────
            [
                'name'                 => 'Futebol',
                'temp_medium_risk'     => 30.0,
                'temp_high_risk'       => 34.0,
                'humidity_medium_risk' => 70.0,
                'federation_protocol'  => 'FIFA recomenda pausas de hidratação a cada 30min acima de 32°C e pode suspender partidas acima de 38°C de Heat Index.',
            ],
            [
                'name'                 => 'Tênis',
                'temp_medium_risk'     => 28.0,
                'temp_high_risk'       => 32.0,
                'humidity_medium_risk' => 65.0,
                'federation_protocol'  => 'ITF permite pausas de calor (Extreme Heat Policy) quando Heat Stress Index > 28. Australian Open suspende jogos ao ar livre acima de 32°C no nível da quadra.',
            ],
            [
                'name'                 => 'Atletismo',
                'temp_medium_risk'     => 28.0,
                'temp_high_risk'       => 33.0,
                'humidity_medium_risk' => 75.0,
                'federation_protocol'  => 'World Athletics recomenda cancelar provas de fundo acima de 35°C WBGT e desloca largadas para horários noturnos em eventos com Heat Index > 32°C.',
            ],
            [
                'name'                 => 'Vôlei de Praia',
                'temp_medium_risk'     => 32.0,
                'temp_high_risk'       => 38.0,
                'humidity_medium_risk' => 80.0,
                'federation_protocol'  => 'FIVB recomenda cooling breaks entre sets acima de 33°C e pode suspender partidas com WBGT > 32°C.',
            ],

            // ── Esportes aquáticos ───────────────────────────────────────────
            [
                'name'                 => 'Natação em Águas Abertas',
                'temp_medium_risk'     => 31.0,
                'temp_high_risk'       => 35.0,
                'humidity_medium_risk' => 75.0,
                'federation_protocol'  => 'World Aquatics cancela provas se temperatura da água > 31°C ou ar > 35°C. Tóquio 2020 deslocou o triatlo para 6h da manhã por risco térmico.',
            ],
            [
                'name'                 => 'Remo',
                'temp_medium_risk'     => 30.0,
                'temp_high_risk'       => 35.0,
                'humidity_medium_risk' => 70.0,
                'federation_protocol'  => 'World Rowing monitora WBGT e recomenda adiamento de provas acima de 32°C combinado com umidade > 70%.',
            ],

            // ── Esportes de endurance ────────────────────────────────────────
            [
                'name'                 => 'Ciclismo (Estrada)',
                'temp_medium_risk'     => 32.0,
                'temp_high_risk'       => 38.0,
                'humidity_medium_risk' => 70.0,
                'federation_protocol'  => 'UCI não tem protocolo fixo, mas equipes ativam protocolos de calor acima de 35°C. GP do Catar 2023 registrou abandonos por desidratação severa.',
            ],
            [
                'name'                 => 'Maratona',
                'temp_medium_risk'     => 18.0,
                'temp_high_risk'       => 24.0,
                'humidity_medium_risk' => 60.0,
                'federation_protocol'  => 'World Athletics recomenda cancelar ou adiar maratonas acima de 28°C WBGT. Performance ótima entre 8–12°C. Acima de 25°C há aumento estatístico de 1,5% no tempo de conclusão por grau.',
            ],
            [
                'name'                 => 'Triatlo',
                'temp_medium_risk'     => 30.0,
                'temp_high_risk'       => 34.0,
                'humidity_medium_risk' => 70.0,
                'federation_protocol'  => 'World Triathlon pode modificar distâncias ou segmentos acima de 32°C WBGT. Em Tóquio 2020, largada da prova olímpica foi às 6h30 para evitar o calor.',
            ],

            // ── Esportes coletivos ───────────────────────────────────────────
            [
                'name'                 => 'Rugby (Sevens)',
                'temp_medium_risk'     => 30.0,
                'temp_high_risk'       => 35.0,
                'humidity_medium_risk' => 72.0,
                'federation_protocol'  => 'World Rugby exige cooling breaks de 2 minutos no intervalo de cada tempo quando WBGT > 28°C e pode reduzir duração dos tempos acima de 32°C.',
            ],
            [
                'name'                 => 'Beisebol',
                'temp_medium_risk'     => 32.0,
                'temp_high_risk'       => 37.0,
                'humidity_medium_risk' => 75.0,
                'federation_protocol'  => 'MLB monitora Heat Index e pode atrasar jogos por calor extremo. Estádios cobertos tornaram-se padrão em cidades do Sun Belt americano justamente pelo calor.',
            ],
            [
                'name'                 => 'Críquete',
                'temp_medium_risk'     => 33.0,
                'temp_high_risk'       => 38.0,
                'humidity_medium_risk' => 70.0,
                'federation_protocol'  => 'ICC pode interromper partidas por condições de calor extremo. Partidas na Índia e Austrália frequentemente afetadas — árbitros monitoram WBGT em campo.',
            ],

            // ── Esportes de motor ────────────────────────────────────────────
            [
                'name'                 => 'Fórmula 1',
                'temp_medium_risk'     => 35.0,
                'temp_high_risk'       => 40.0,
                'humidity_medium_risk' => 65.0,
                'federation_protocol'  => 'FIA monitora temperatura do cockpit (pode atingir 50°C) e umidade. GP do Catar 2023 teve 5 abandonos por exaustão térmica. Não há protocolo de suspensão — pilotos são responsáveis por comunicar à equipe.',
            ],
            [
                'name'                 => 'MotoGP',
                'temp_medium_risk'     => 35.0,
                'temp_high_risk'       => 42.0,
                'humidity_medium_risk' => 65.0,
                'federation_protocol'  => 'FIM não tem temperatura máxima definida, mas pistas acima de 65°C (asfalto) podem gerar flags de segurança. Pilotos usam ventilação forçada no capacete em eventos quentes.',
            ],

            // ── Esportes de inverno ──────────────────────────────────────────
            [
                'name'                 => 'Esqui Alpino',
                'temp_medium_risk'     => 2.0,
                'temp_high_risk'       => 8.0,
                'humidity_medium_risk' => 60.0,
                'federation_protocol'  => 'FIS cancela provas acima de 10°C por risco de degelo da pista. Jogos de Inverno de Sochi 2014 e Pequim 2022 usaram 100% de neve artificial. Estações europeias fecharam 20% das pistas nos últimos 30 anos por insuficiência de neve natural.',
            ],
            [
                'name'                 => 'Patinação no Gelo',
                'temp_medium_risk'     => 18.0,
                'temp_high_risk'       => 25.0,
                'humidity_medium_risk' => 55.0,
                'federation_protocol'  => 'ISU regula temperatura da arena entre -3°C e 5°C no nível do gelo. Eventos outdoor são raros — o aumento de temperatura global reduziu drasticamente lagos e rios congelados usados para competições tradicionais.',
            ],
        ];

        foreach ($sports as $sport) {
            Sport::create($sport);
        }
    }
}