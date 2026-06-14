{{-- resources/views/results/report-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 12px;
        line-height: 1.7;
        color: #1a1a1a;
        background: #fff;
        padding: 0;
    }

    /* Cabeçalho */
    .header {
        background: #0d0f12;
        color: #fff;
        padding: 28px 36px 24px;
        margin-bottom: 0;
    }
    .brand {
        font-size: 11px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #f97316;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .header h1 {
        font-size: 22px;
        font-weight: bold;
        color: #f0ede8;
        margin-bottom: 6px;
        line-height: 1.3;
    }
    .header-meta {
        font-size: 11px;
        color: #7a8099;
    }
    .header-meta span { margin-right: 16px; }

    /* Linha laranja */
    .accent-bar {
        height: 4px;
        background: linear-gradient(90deg, #f97316 0%, #fb923c 100%);
        margin-bottom: 28px;
    }

    /* Corpo */
    .body-wrap { padding: 0 36px 36px; }

    /* Cards de stats */
    .stats-row {
        display: table;
        width: 100%;
        border-collapse: separate;
        border-spacing: 10px;
        margin: 0 -10px 24px;
    }
    .stat-card {
        display: table-cell;
        width: 25%;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 14px 16px;
        vertical-align: top;
    }
    .stat-label {
        font-size: 10px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .stat-value { font-size: 20px; font-weight: bold; }
    .orange { color: #f97316; }
    .blue   { color: #3b82f6; }
    .purple { color: #8b5cf6; }
    .red    { color: #ef4444; }

    /* Tabela de dados mensais */
    .section-title {
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #6c757d;
        margin-bottom: 10px;
        padding-bottom: 6px;
        border-bottom: 2px solid #f97316;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
        font-size: 11px;
    }
    th {
        background: #f1f3f5;
        color: #495057;
        text-align: left;
        padding: 8px 10px;
        font-weight: bold;
        border-bottom: 1px solid #dee2e6;
    }
    td {
        padding: 7px 10px;
        border-bottom: 1px solid #f1f3f5;
        color: #343a40;
    }
    tr:nth-child(even) td { background: #fafafa; }

    .badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 99px;
        font-size: 10px;
        font-weight: bold;
    }
    .badge-low    { background: #d1fae5; color: #065f46; }
    .badge-medium { background: #fef3c7; color: #92400e; }
    .badge-high   { background: #fee2e2; color: #991b1b; }

    /* Relatório IA */
    .report-box {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-left: 4px solid #f97316;
        border-radius: 6px;
        padding: 20px 24px;
        margin-bottom: 24px;
    }
    .report-box p { margin-bottom: 10px; color: #343a40; }

    /* Rodapé */
    .footer {
        margin-top: 32px;
        padding-top: 14px;
        border-top: 1px solid #dee2e6;
        font-size: 10px;
        color: #adb5bd;
        text-align: center;
    }
    .footer strong { color: #f97316; }
</style>
</head>
<body>

{{-- Cabeçalho --}}
<div class="header">
    <p class="brand">● Tactical Heat</p>
    <h1>Relatório de Análise Climática<br>{{ $search->city_name }} — {{ $search->getRawOriginal('sport') }}</h1>
    <div class="header-meta">
        <span>Ano de referência: {{ $search->year }}</span>
        <span>Gerado em: {{ $report->generated_at->format('d/m/Y H:i') }}</span>
        <span>Lat {{ number_format($search->latitude,4) }}, Lng {{ number_format($search->longitude,4) }}</span>
    </div>
</div>
<div class="accent-bar"></div>

<div class="body-wrap">

    {{-- Stats --}}
    @php
        $maxTemp    = $climateData->max('temp_max_avg');
        $minTemp    = $climateData->min('temp_min_avg');
        $avgHumid   = round($climateData->avg('humidity_avg'), 1);
        $highMonths = $climateData->where('risk_level', 'high')->count();
    @endphp

    <table class="stats-row">
        <tr>
            <td class="stat-card">
                <p class="stat-label">Temp. máxima</p>
                <p class="stat-value orange">{{ $maxTemp }}°C</p>
            </td>
            <td class="stat-card">
                <p class="stat-label">Temp. mínima</p>
                <p class="stat-value blue">{{ $minTemp }}°C</p>
            </td>
            <td class="stat-card">
                <p class="stat-label">Umidade média</p>
                <p class="stat-value purple">{{ $avgHumid }}%</p>
            </td>
            <td class="stat-card">
                <p class="stat-label">Meses alto risco</p>
                <p class="stat-value red">{{ $highMonths }}</p>
            </td>
        </tr>
    </table>

    {{-- Tabela de dados mensais --}}
    <p class="section-title">Dados Climáticos Mensais</p>
    @php
        $monthNames = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                       'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
        $riskLabels = ['low' => 'Baixo', 'medium' => 'Médio', 'high' => 'Alto'];
    @endphp
    <table>
        <thead>
            <tr>
                <th>Mês</th>
                <th>Temp. Máx</th>
                <th>Temp. Mín</th>
                <th>Umidade</th>
                <th>Heat Index</th>
                <th>Risco</th>
            </tr>
        </thead>
        <tbody>
            @foreach($climateData->sortBy('month') as $d)
            <tr>
                <td>{{ $monthNames[$d->month - 1] }}</td>
                <td>{{ $d->temp_max_avg }}°C</td>
                <td>{{ $d->temp_min_avg }}°C</td>
                <td>{{ $d->humidity_avg }}%</td>
                <td>{{ $d->heat_index_avg }}°C</td>
                <td><span class="badge badge-{{ $d->risk_level }}">{{ $riskLabels[$d->risk_level] }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Limiares do esporte --}}
    <p class="section-title">Limiares de Risco — {{ $search->getRawOriginal('sport') }}</p>
    <table>
        <thead>
            <tr><th>Parâmetro</th><th>Risco Médio</th><th>Risco Alto</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>Temperatura</td>
                <td>≥ {{ $sport->temp_medium_risk }}°C</td>
                <td>≥ {{ $sport->temp_high_risk }}°C</td>
            </tr>
            <tr>
                <td>Umidade</td>
                <td>≥ {{ $sport->humidity_medium_risk }}%</td>
                <td>≥ 85%</td>
            </tr>
        </tbody>
    </table>

    @if($sport->federation_protocol)
    <p style="font-size:11px; color:#6c757d; padding: 10px 14px; background:#fff3cd; border-radius:6px; margin-bottom:24px; border-left:3px solid #f59e0b;">
        <strong>Protocolo da federação:</strong> {{ $sport->federation_protocol }}
    </p>
    @endif

    {{-- Relatório da IA --}}
    <p class="section-title">Análise gerada por Inteligência Artificial</p>
    <div class="report-box">
        @foreach(explode("\n", $report->content) as $line)
            @if(trim($line))
                <p>{{ $line }}</p>
            @endif
        @endforeach
    </div>

    {{-- Rodapé --}}
    <div class="footer">
        Documento gerado pelo <strong>Tactical Heat</strong> · Sistema de Análise de Riscos Climáticos para Eventos Esportivos ·
        {{ now()->format('d/m/Y H:i') }}
    </div>
</div>

</body>
</html>