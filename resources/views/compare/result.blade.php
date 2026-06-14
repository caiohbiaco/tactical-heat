{{-- resources/views/compare/result.blade.php --}}
@extends('layouts.app')

@section('title', 'Comparação — Tactical Heat')

@push('styles')
<style>
.breadcrumb {
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; color: var(--muted); margin-bottom: 1.5rem;
}
.breadcrumb a { color: var(--muted); text-decoration: none; }
.breadcrumb a:hover { color: var(--text); }

.compare-header {
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;
}
.page-title {
    font-family: var(--font-display);
    font-size: 1.6rem; font-weight: 700; margin-bottom: 4px;
}

/* Colunas de stats --*/
.compare-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.city-col {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
}
.city-col-header {
    padding: 12px 16px;
    display: flex; align-items: center; gap: 8px;
    border-bottom: 1px solid var(--border);
}
.city-tag {
    width: 22px; height: 22px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; flex-shrink: 0;
}
.city-tag-a { background: rgba(249,115,22,0.2); color: #f97316; }
.city-tag-b { background: rgba(96,165,250,0.2); color: #60a5fa; }
.city-col-name { font-weight: 600; font-size: 15px; }
.city-col-year { font-size: 12px; color: var(--muted); margin-left: auto; }

.mini-stat { padding: 10px 16px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; font-size: 13px; }
.mini-stat:last-child { border-bottom: none; }
.mini-label { color: var(--muted); font-size: 12px; }
.mini-value { font-weight: 600; }

/* Mapa */
#compareMap {
    height: 280px;
    border-radius: var(--radius);
    border: 1px solid var(--border);
    z-index: 1;
    margin-bottom: 1.5rem;
}

/* Cards de gráfico */
.chart-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.75rem;
    margin-bottom: 1.5rem;
}
.chart-label {
    font-size: 11px; font-weight: 600; color: var(--muted);
    text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 1.25rem;
}

/* Tabela de risco lado a lado */
.risk-compare-table {
    width: 100%; border-collapse: collapse; font-size: 13px;
}
.risk-compare-table th {
    padding: 9px 14px; font-size: 11px; font-weight: 600;
    letter-spacing: 0.07em; text-transform: uppercase; color: var(--muted);
    border-bottom: 1px solid var(--border); text-align: left;
}
.risk-compare-table td {
    padding: 11px 14px; border-bottom: 1px solid var(--border-soft);
}
.risk-compare-table tr:last-child td { border-bottom: none; }
.semaphore { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 500; }
.sem-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.risk-low    .sem-dot { background: #22c55e; box-shadow: 0 0 5px #22c55e; }
.risk-medium .sem-dot { background: #f59e0b; box-shadow: 0 0 5px #f59e0b; }
.risk-high   .sem-dot { background: #ef4444; box-shadow: 0 0 5px #ef4444; }
.risk-low    .sem-text { color: #4ade80; }
.risk-medium .sem-text { color: #fbbf24; }
.risk-high   .sem-text { color: #f87171; }

.winner-badge {
    font-size: 10px; font-weight: 700; padding: 2px 7px;
    border-radius: 99px; background: rgba(34,197,94,0.15); color: #4ade80;
    letter-spacing: 0.04em;
}

@media (max-width: 600px) {
    .compare-stats { grid-template-columns: 1fr; }
}
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endpush

@section('content')

<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <a href="{{ route('compare.form') }}">Comparar cidades</a>
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span>Resultado</span>
</div>

<div class="compare-header">
    <div>
        <h1 class="page-title">{{ $cityA['name'] }} vs {{ $cityB['name'] }}</h1>
        <p class="text-muted text-sm">{{ $sport->name }} · Comparação climática histórica</p>
    </div>
    <a href="{{ route('compare.form') }}" class="btn btn-ghost">← Nova comparação</a>
</div>

{{-- Resumo em cards lado a lado --}}
@php
    $riskLabels = ['low' => 'Baixo', 'medium' => 'Médio', 'high' => 'Alto'];
    $highA = collect($cityA['data'])->where('risk_level','high')->count();
    $highB = collect($cityB['data'])->where('risk_level','high')->count();
    $maxA  = collect($cityA['data'])->max('temp_max_avg');
    $maxB  = collect($cityB['data'])->max('temp_max_avg');
    $humA  = round(collect($cityA['data'])->avg('humidity_avg'), 1);
    $humB  = round(collect($cityB['data'])->avg('humidity_avg'), 1);
    $safierCity = $highA <= $highB ? 'A' : 'B';
@endphp

<div class="compare-stats">
    {{-- Cidade A --}}
    <div class="city-col">
        <div class="city-col-header">
            <span class="city-tag city-tag-a">A</span>
            <span class="city-col-name">{{ $cityA['name'] }}</span>
            <span class="city-col-year">{{ $cityA['year'] }}</span>
        </div>
        <div class="mini-stat">
            <span class="mini-label">Temp. máxima do ano</span>
            <span class="mini-value" style="color:#fb923c;">{{ $maxA }}°C</span>
        </div>
        <div class="mini-stat">
            <span class="mini-label">Umidade média</span>
            <span class="mini-value" style="color:#a78bfa;">{{ $humA }}%</span>
        </div>
        <div class="mini-stat">
            <span class="mini-label">Meses de alto risco</span>
            <span class="mini-value" style="color:#f87171;">{{ $highA }}</span>
        </div>
        <div class="mini-stat">
            <span class="mini-label">Veredicto</span>
            <span class="mini-value">
                @if($safierCity === 'A')
                    <span class="winner-badge">✓ Mais segura</span>
                @else
                    <span style="font-size:12px; color:var(--muted);">Mais arriscada</span>
                @endif
            </span>
        </div>
    </div>

    {{-- Cidade B --}}
    <div class="city-col">
        <div class="city-col-header">
            <span class="city-tag city-tag-b">B</span>
            <span class="city-col-name">{{ $cityB['name'] }}</span>
            <span class="city-col-year">{{ $cityB['year'] }}</span>
        </div>
        <div class="mini-stat">
            <span class="mini-label">Temp. máxima do ano</span>
            <span class="mini-value" style="color:#fb923c;">{{ $maxB }}°C</span>
        </div>
        <div class="mini-stat">
            <span class="mini-label">Umidade média</span>
            <span class="mini-value" style="color:#a78bfa;">{{ $humB }}%</span>
        </div>
        <div class="mini-stat">
            <span class="mini-label">Meses de alto risco</span>
            <span class="mini-value" style="color:#f87171;">{{ $highB }}</span>
        </div>
        <div class="mini-stat">
            <span class="mini-label">Veredicto</span>
            <span class="mini-value">
                @if($safierCity === 'B')
                    <span class="winner-badge">✓ Mais segura</span>
                @else
                    <span style="font-size:12px; color:var(--muted);">Mais arriscada</span>
                @endif
            </span>
        </div>
    </div>
</div>

{{-- Mapa com as duas cidades --}}
<div class="chart-card" style="padding: 1.5rem;">
    <p class="chart-label">Localização no mapa</p>
    <div id="compareMap"></div>
</div>

{{-- Gráfico de temperatura comparativo --}}
<div class="chart-card">
    <p class="chart-label">Temperatura máxima mensal comparativa (°C)</p>
    <canvas id="tempCompareChart" height="90"></canvas>
</div>

{{-- Gráfico de umidade comparativo --}}
<div class="chart-card">
    <p class="chart-label">Umidade média mensal comparativa (%)</p>
    <canvas id="humidCompareChart" height="90"></canvas>
</div>

{{-- Tabela de risco mês a mês --}}
<div class="card" style="padding: 0; overflow: hidden; margin-bottom: 1.5rem;">
    <table class="risk-compare-table">
        <thead>
            <tr>
                <th>Mês</th>
                <th>{{ $cityA['name'] }} (A)</th>
                <th>{{ $cityB['name'] }} (B)</th>
            </tr>
        </thead>
        <tbody>
            @php $monthNames = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']; @endphp
            @foreach(range(1, 12) as $m)
            @php
                $dA = collect($cityA['data'])->firstWhere('month', $m);
                $dB = collect($cityB['data'])->firstWhere('month', $m);
            @endphp
            <tr>
                <td style="font-weight:500;">{{ $monthNames[$m-1] }}</td>
                <td>
                    @if($dA)
                    <div class="semaphore risk-{{ $dA['risk_level'] }}">
                        <span class="sem-dot"></span>
                        <span class="sem-text">{{ $riskLabels[$dA['risk_level']] }}</span>
                        <span class="text-muted" style="font-size:11px;">{{ $dA['temp_max_avg'] }}°C · {{ $dA['humidity_avg'] }}%</span>
                    </div>
                    @endif
                </td>
                <td>
                    @if($dB)
                    <div class="semaphore risk-{{ $dB['risk_level'] }}">
                        <span class="sem-dot"></span>
                        <span class="sem-text">{{ $riskLabels[$dB['risk_level']] }}</span>
                        <span class="text-muted" style="font-size:11px;">{{ $dB['temp_max_avg'] }}°C · {{ $dB['humidity_avg'] }}%</span>
                    </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Mapa com as duas cidades ───────────────────────────────────────────────
const latA = {{ $cityA['lat'] }}, lngA = {{ $cityA['lng'] }};
const latB = {{ $cityB['lat'] }}, lngB = {{ $cityB['lng'] }};

const map = L.map('compareMap');
L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    attribution: '© OpenStreetMap © CARTO', subdomains: 'abcd', maxZoom: 19
}).addTo(map);

const iconA = L.divIcon({
    html: `<div style="width:14px;height:14px;background:#f97316;border-radius:50%;border:3px solid rgba(249,115,22,0.35);box-shadow:0 0 10px rgba(249,115,22,0.6);"></div>`,
    className: '', iconSize: [14,14], iconAnchor: [7,7]
});
const iconB = L.divIcon({
    html: `<div style="width:14px;height:14px;background:#60a5fa;border-radius:50%;border:3px solid rgba(96,165,250,0.35);box-shadow:0 0 10px rgba(96,165,250,0.6);"></div>`,
    className: '', iconSize: [14,14], iconAnchor: [7,7]
});

L.marker([latA, lngA], { icon: iconA })
    .addTo(map)
    .bindPopup(`<strong style="color:#f97316;">A — {{ $cityA['name'] }}</strong><br>{{ $cityA['year'] }}`);

L.marker([latB, lngB], { icon: iconB })
    .addTo(map)
    .bindPopup(`<strong style="color:#60a5fa;">B — {{ $cityB['name'] }}</strong><br>{{ $cityB['year'] }}`);

// Linha conectando as duas cidades
L.polyline([[latA, lngA],[latB, lngB]], { color: 'rgba(255,255,255,0.12)', weight: 2, dashArray: '6,6' }).addTo(map);

// Ajusta zoom para mostrar os dois pontos
const bounds = L.latLngBounds([[latA,lngA],[latB,lngB]]);
map.fitBounds(bounds, { padding: [40, 40] });

// ── Gráficos Chart.js ──────────────────────────────────────────────────────
Chart.defaults.color = '#7a8099';
Chart.defaults.font.family = "'DM Sans', sans-serif";
Chart.defaults.font.size = 12;

const labels   = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
const tempMaxA = {!! json_encode(collect($cityA['data'])->sortBy('month')->pluck('temp_max_avg')->values()) !!};
const tempMaxB = {!! json_encode(collect($cityB['data'])->sortBy('month')->pluck('temp_max_avg')->values()) !!};
const humA     = {!! json_encode(collect($cityA['data'])->sortBy('month')->pluck('humidity_avg')->values()) !!};
const humB     = {!! json_encode(collect($cityB['data'])->sortBy('month')->pluck('humidity_avg')->values()) !!};

new Chart(document.getElementById('tempCompareChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            { label: '{{ $cityA["name"] }} (A)', data: tempMaxA, borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,0.06)', tension: 0.4, fill: true, pointBackgroundColor: '#f97316', pointRadius: 4, borderWidth: 2 },
            { label: '{{ $cityB["name"] }} (B)', data: tempMaxB, borderColor: '#60a5fa', backgroundColor: 'rgba(96,165,250,0.06)', tension: 0.4, fill: true, pointBackgroundColor: '#60a5fa', pointRadius: 4, borderWidth: 2 },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top', labels: { boxWidth: 12, padding: 16 } }, tooltip: { mode: 'index', intersect: false } },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.04)' } },
            y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { callback: v => v + '°C' } }
        }
    }
});

new Chart(document.getElementById('humidCompareChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: '{{ $cityA["name"] }} (A)', data: humA, backgroundColor: 'rgba(249,115,22,0.4)', borderColor: '#f97316', borderWidth: 1, borderRadius: 4 },
            { label: '{{ $cityB["name"] }} (B)', data: humB, backgroundColor: 'rgba(96,165,250,0.4)', borderColor: '#60a5fa', borderWidth: 1, borderRadius: 4 },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top', labels: { boxWidth: 12, padding: 16 } }, tooltip: { mode: 'index', intersect: false } },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.04)' } },
            y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { callback: v => v + '%' } }
        }
    }
});
</script>
@endpush