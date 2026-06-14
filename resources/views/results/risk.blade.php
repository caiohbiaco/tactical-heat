{{-- resources/views/results/risk.blade.php --}}
@extends('layouts.app')

@section('title', 'Painel de Risco — {{ $search->city_name }}')

@push('styles')
<style>
.breadcrumb {
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; color: var(--muted); margin-bottom: 1.5rem;
}
.breadcrumb a { color: var(--muted); text-decoration: none; }
.breadcrumb a:hover { color: var(--text); }

.page-title {
    font-family: var(--font-display);
    font-size: 1.6rem; font-weight: 700; margin-bottom: 4px;
}

/* Tabela de meses */
.risk-table { width: 100%; border-collapse: collapse; }
.risk-table th {
    text-align: left; font-size: 11px; font-weight: 600;
    letter-spacing: 0.08em; text-transform: uppercase;
    color: var(--muted); padding: 10px 16px;
    border-bottom: 1px solid var(--border);
}
.risk-table td {
    padding: 14px 16px; border-bottom: 1px solid var(--border-soft);
    font-size: 14px;
}
.risk-table tr:last-child td { border-bottom: none; }
.risk-table tr:hover td { background: var(--surface-2); }

/* Semáforo */
.semaphore {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    font-size: 13px;
}
.semaphore-dot {
    width: 10px; height: 10px; border-radius: 50%;
    box-shadow: 0 0 6px currentColor;
}
.risk-low    .semaphore-dot { background: #22c55e; color: #22c55e; }
.risk-medium .semaphore-dot { background: #f59e0b; color: #f59e0b; }
.risk-high   .semaphore-dot { background: #ef4444; color: #ef4444; }
.risk-low    .semaphore-text { color: #4ade80; }
.risk-medium .semaphore-text { color: #fbbf24; }
.risk-high   .semaphore-text { color: #f87171; }

/* Legenda do esporte */
.thresholds-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.threshold-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}
.threshold-item {
    background: var(--surface-2);
    border-radius: var(--radius);
    padding: 12px 16px;
}
.threshold-label { font-size: 12px; color: var(--muted); margin-bottom: 4px; }
.threshold-value { font-family: var(--font-display); font-size: 1.2rem; font-weight: 700; }
</style>
@endpush

@section('content')

<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <a href="{{ route('results.show', $search) }}">{{ $search->city_name }}</a>
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span>Painel de Risco</span>
</div>

<div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-bottom:2rem;">
    <div>
        <h1 class="page-title">Painel de risco climático</h1>
        <p class="text-muted text-sm">{{ $search->city_name }} · {{ $search->sport }} · {{ $search->year }}</p>
    </div>
    <div style="display:flex; gap:8px;">
        <a href="{{ route('results.show', $search) }}" class="btn btn-ghost">← Voltar</a>
        @if(!$search->report)
            <form method="POST" action="{{ route('report.generate', $search) }}" style="margin:0">
                @csrf
                <button type="submit" class="btn btn-primary">Gerar relatório IA</button>
            </form>
        @else
            <a href="{{ route('report.show', $search) }}" class="btn btn-primary">Ver relatório IA</a>
        @endif
    </div>
</div>

{{-- Limiares do esporte --}}
<div class="thresholds-card">
    <p style="font-size: 13px; font-weight: 500; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em;">
        Limiares para {{ $search->getRawOriginal('sport') }}
    </p>
    <div class="threshold-grid">
        <div class="threshold-item">
            <p class="threshold-label">Risco médio a partir de</p>
            <p class="threshold-value" style="color: #fbbf24;">{{ $sport->temp_medium_risk }}°C</p>
        </div>
        <div class="threshold-item">
            <p class="threshold-label">Risco alto a partir de</p>
            <p class="threshold-value" style="color: #f87171;">{{ $sport->temp_high_risk }}°C</p>
        </div>
        <div class="threshold-item">
            <p class="threshold-label">Umidade crítica</p>
            <p class="threshold-value" style="color: #a78bfa;">{{ $sport->humidity_medium_risk }}%</p>
        </div>
    </div>
    @if($sport->federation_protocol)
        <p style="margin-top: 1rem; font-size: 13px; color: var(--muted); padding: 10px 14px; background: var(--surface-2); border-radius: var(--radius); border-left: 3px solid var(--accent);">
            {{ $sport->federation_protocol }}
        </p>
    @endif
</div>

{{-- Tabela de meses --}}
<div class="card" style="padding: 0; overflow: hidden;">
    <table class="risk-table">
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
            @php
                $monthNames = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                               'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
                $riskLabels = ['low' => 'Baixo', 'medium' => 'Médio', 'high' => 'Alto'];
            @endphp
            @foreach($climateData->sortBy('month') as $data)
            <tr>
                <td style="font-weight: 500;">{{ $monthNames[$data->month - 1] }}</td>
                <td style="color: #fb923c;">{{ $data->temp_max_avg }}°C</td>
                <td style="color: #60a5fa;">{{ $data->temp_min_avg }}°C</td>
                <td style="color: #a78bfa;">{{ $data->humidity_avg }}%</td>
                <td>{{ $data->heat_index_avg }}°C</td>
                <td>
                    <div class="semaphore risk-{{ $data->risk_level }}">
                        <span class="semaphore-dot"></span>
                        <span class="semaphore-text">{{ $riskLabels[$data->risk_level] }}</span>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection