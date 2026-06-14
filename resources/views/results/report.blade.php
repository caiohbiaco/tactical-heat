{{-- resources/views/results/report.blade.php --}}
@extends('layouts.app')

@section('title', 'Relatório IA — {{ $search->city_name }}')

@push('styles')
<style>
.breadcrumb {
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; color: var(--muted); margin-bottom: 1.5rem;
}
.breadcrumb a { color: var(--muted); text-decoration: none; }
.breadcrumb a:hover { color: var(--text); }

.report-wrap { max-width: 760px; }

.report-header {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.75rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}
.report-header-left { display: flex; align-items: center; gap: 1.25rem; }
.report-icon {
    width: 52px; height: 52px;
    background: var(--accent-dim);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.report-title {
    font-family: var(--font-display);
    font-size: 1.2rem; font-weight: 700; margin-bottom: 4px;
}
.report-content {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 2rem 2.25rem;
    line-height: 1.85;
    font-size: 15px;
}
.report-content p { margin-bottom: 1rem; }
.report-content strong { color: var(--text); font-weight: 600; }
</style>
@endpush

@section('content')

<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <a href="{{ route('results.show', $search) }}">{{ $search->city_name }}</a>
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
    <span>Relatório IA</span>
</div>

<div class="report-wrap">

    <div class="report-header">
        <div class="report-header-left">
            <div class="report-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
            </div>
            <div>
                <p class="report-title">Análise climática por IA</p>
                <p class="text-muted text-sm">
                    {{ $search->city_name }} · {{ $search->getRawOriginal('sport') }} · {{ $search->year }}
                </p>
                <p style="font-size:12px; color:var(--muted); margin-top:4px;">
                    Gerado em {{ $report->generated_at->format('d/m/Y \à\s H:i') }}
                    &nbsp;<span style="color:var(--accent); font-weight:600; font-size:11px;">● GEMINI AI</span>
                </p>
            </div>
        </div>

        {{-- Botão Baixar PDF --}}
        <a href="{{ route('report.pdf', $search) }}" class="btn btn-primary" target="_blank">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Baixar PDF
        </a>
    </div>

    <div class="report-content">
        {!! nl2br(e($report->content)) !!}
    </div>

    <div style="display:flex; gap:10px; margin-top:1.5rem;">
        <a href="{{ route('results.risk', $search) }}" class="btn btn-ghost">← Painel de risco</a>
        <a href="{{ route('results.show', $search) }}" class="btn btn-ghost">Ver gráficos</a>
    </div>

</div>
@endsection