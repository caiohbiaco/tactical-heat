{{-- resources/views/results/show.blade.php --}}
@extends('layouts.app')

@section('title', '{{ $search->city_name }} {{ $search->year }} — Tactical Heat')

@push('styles')
<style>
.breadcrumb {
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; color: var(--muted); margin-bottom: 1.5rem;
}
.breadcrumb a { color: var(--muted); text-decoration: none; }
.breadcrumb a:hover { color: var(--text); }

.page-header {
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;
}
.city-name {
    font-family: var(--font-display);
    font-size: 2rem; font-weight: 700; line-height: 1.15; margin-bottom: 4px;
}
.actions-bar { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }

.stat-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 12px;
    margin-bottom: 2rem;
}
.strip-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1rem 1.25rem;
}
.strip-label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px; }
.strip-value { font-family: var(--font-display); font-size: 1.5rem; font-weight: 700; }

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

/* Mapa */
#map {
    height: 300px;
    border-radius: var(--radius);
    border: 1px solid var(--border);
    z-index: 1;
}
.leaflet-container { background: #1c2029 !important; }

/* Badge de risco no popup */
.map-popup { font-family: 'DM Sans', sans-serif; }
.map-popup strong { color: #f97316; }
</style>

{{-- Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endpush

@section('content')

<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span>{{ $search->city_name }}</span>
</div>

<div class="page-header">
    <div>
        <h1 class="city-name">{{ $search->city_name }}</h1>
        <p class="text-muted" style="font-size:14px;">
            {{ $search->getRawOriginal('sport') }} · {{ $search->year }}
            · {{ number_format($search->latitude, 4) }}, {{ number_format($search->longitude, 4) }}
        </p>
    </div>
    <div class="actions-bar">
        <a href="{{ route('results.risk', $search) }}" class="btn btn-ghost">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            Painel de risco
        </a>

        @if($search->report)
            {{-- Botão baixar PDF --}}
            <a href="{{ route('report.pdf', $search) }}" class="btn btn-ghost" target="_blank">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Baixar PDF
            </a>
            <a href="{{ route('report.show', $search) }}" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                Ver relatório IA
            </a>
        @else
            <form method="POST" action="{{ route('report.generate', $search) }}" style="margin:0">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4"/><path d="M12 8h.01"/>
                    </svg>
                    Gerar relatório IA
                </button>
            </form>
        @endif
    </div>
</div>

{{-- Stats rápidas --}}
@php
    $maxTemp    = $climateData->max('temp_max_avg');
    $minTemp    = $climateData->min('temp_min_avg');
    $avgHumid   = round($climateData->avg('humidity_avg'), 1);
    $highMonths = $climateData->where('risk_level', 'high')->count();
@endphp

<div class="stat-strip">
    <div class="strip-card">
        <p class="strip-label">Temp. máxima</p>
        <p class="strip-value" style="color:#fb923c;">{{ $maxTemp }}°C</p>
    </div>
    <div class="strip-card">
        <p class="strip-label">Temp. mínima</p>
        <p class="strip-value" style="color:#60a5fa;">{{ $minTemp }}°C</p>
    </div>
    <div class="strip-card">
        <p class="strip-label">Umidade média</p>
        <p class="strip-value" style="color:#a78bfa;">{{ $avgHumid }}%</p>
    </div>
    <div class="strip-card">
        <p class="strip-label">Meses alto risco</p>
        <p class="strip-value" style="color:#f87171;">{{ $highMonths }}</p>
    </div>
</div>

{{-- Mapa Leaflet --}}
<div class="chart-card">
    <p class="chart-label">Localização no mapa</p>
    <div id="map"></div>
</div>

{{-- Gráfico temperatura --}}
<div class="chart-card">
    <p class="chart-label">Temperatura mensal (°C)</p>
    <canvas id="tempChart" height="90"></canvas>
</div>

{{-- Gráfico umidade + heat index --}}
<div class="chart-card">
    <p class="chart-label">Umidade & Heat Index</p>
    <canvas id="humidityChart" height="90"></canvas>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Mapa Leaflet ───────────────────────────────────────────────────────────
const lat = {{ $search->latitude }};
const lng = {{ $search->longitude }};
const city = "{{ $search->city_name }}";
const sport = "{{ $search->getRawOriginal('sport') }}";
const year = {{ $search->year }};

const map = L.map('map').setView([lat, lng], 10);

L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    attribution: '© OpenStreetMap © CARTO',
    subdomains: 'abcd',
    maxZoom: 19
}).addTo(map);

// Ícone customizado laranja
const icon = L.divIcon({
    html: `<div style="
        width:14px; height:14px;
        background:#f97316;
        border-radius:50%;
        border:3px solid rgba(249,115,22,0.35);
        box-shadow:0 0 10px rgba(249,115,22,0.6);
    "></div>`,
    className: '',
    iconSize: [14, 14],
    iconAnchor: [7, 7],
});

const riskCounts = {
    high:   {{ $climateData->where('risk_level','high')->count() }},
    medium: {{ $climateData->where('risk_level','medium')->count() }},
    low:    {{ $climateData->where('risk_level','low')->count() }},
};

const popupHtml = `
<div class="map-popup" style="min-width:160px; font-size:13px; line-height:1.6;">
    <strong style="font-size:15px; display:block; margin-bottom:4px;">${city}</strong>
    ${sport} · ${year}<br>
    <span style="color:#f87171;">● Alto: ${riskCounts.high} meses</span><br>
    <span style="color:#fbbf24;">● Médio: ${riskCounts.medium} meses</span><br>
    <span style="color:#4ade80;">● Baixo: ${riskCounts.low} meses</span>
</div>`;

L.marker([lat, lng], { icon })
    .addTo(map)
    .bindPopup(popupHtml, { className: 'dark-popup' })
    .openPopup();

// ── Gráficos Chart.js ──────────────────────────────────────────────────────
const labels   = {!! json_encode($climateData->pluck('month')->map(fn($m) => ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'][$m-1])) !!};
const tempMax  = {!! json_encode($climateData->pluck('temp_max_avg')) !!};
const tempMin  = {!! json_encode($climateData->pluck('temp_min_avg')) !!};
const humidity = {!! json_encode($climateData->pluck('humidity_avg')) !!};
const heatIdx  = {!! json_encode($climateData->pluck('heat_index_avg')) !!};

Chart.defaults.color = '#7a8099';
Chart.defaults.font.family = "'DM Sans', sans-serif";
Chart.defaults.font.size = 12;

new Chart(document.getElementById('tempChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            { label: 'Máx (°C)', data: tempMax, borderColor: '#f97316', tension: 0.4, fill: false, pointBackgroundColor: '#f97316', pointRadius: 4, borderWidth: 2 },
            { label: 'Mín (°C)', data: tempMin, borderColor: '#60a5fa', tension: 0.4, fill: false, pointBackgroundColor: '#60a5fa', pointRadius: 4, borderWidth: 2 },
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

new Chart(document.getElementById('humidityChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Umidade (%)', data: humidity, backgroundColor: 'rgba(167,139,250,0.4)', borderColor: '#a78bfa', borderWidth: 1, borderRadius: 4, yAxisID: 'y' },
            { label: 'Heat Index (°C)', data: heatIdx, type: 'line', borderColor: '#fb923c', tension: 0.4, pointBackgroundColor: '#fb923c', pointRadius: 4, borderWidth: 2, yAxisID: 'y1', backgroundColor: 'transparent' },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top', labels: { boxWidth: 12, padding: 16 } }, tooltip: { mode: 'index', intersect: false } },
        scales: {
            x:  { grid: { color: 'rgba(255,255,255,0.04)' } },
            y:  { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { callback: v => v + '%' }, position: 'left' },
            y1: { grid: { display: false }, ticks: { callback: v => v + '°C' }, position: 'right' }
        }
    }
});
</script>
@endpush