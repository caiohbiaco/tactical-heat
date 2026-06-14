{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard — Tactical Heat')

@push('styles')
<style>
/* ── Stats Cards ─────────────────────────────────────────────────── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 14px;
    margin-bottom: 2.5rem;
}
.stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: border-color 0.2s;
}
.stat-card:hover { border-color: var(--accent); }
.stat-icon {
    width: 44px; height: 44px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.stat-icon-orange { background: rgba(249,115,22,0.12); }
.stat-icon-blue   { background: rgba(96,165,250,0.12); }
.stat-icon-purple { background: rgba(167,139,250,0.12); }
.stat-icon-green  { background: rgba(34,197,94,0.12);  }
.stat-number {
    font-family: var(--font-display);
    font-size: 1.7rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 3px;
}
.stat-desc {
    font-size: 12px;
    color: var(--muted);
    font-weight: 400;
}

/* ── Section header ──────────────────────────────────────────────── */
.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
    gap: 10px;
}
.section-title {
    font-family: var(--font-display);
    font-size: 1rem;
    font-weight: 600;
}

/* ── Search list ─────────────────────────────────────────────────── */
.search-list { display: flex; flex-direction: column; gap: 10px; }
.search-item {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    transition: border-color 0.18s;
}
.search-item:hover { border-color: rgba(249,115,22,0.3); }
.search-avatar {
    width: 42px; height: 42px;
    border-radius: 10px;
    background: var(--accent-dim);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.search-info { flex: 1; min-width: 0; }
.search-name {
    font-weight: 500;
    font-size: 15px;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.search-meta {
    font-size: 13px; color: var(--muted);
    display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
}
.dot { color: var(--subtle); }
.risk-pills { display: flex; gap: 5px; flex-wrap: wrap; }
.search-actions { display: flex; gap: 6px; align-items: center; }

/* ── Empty state ─────────────────────────────────────────────────── */
.empty-state {
    text-align: center;
    padding: 3.5rem 2rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
}
.empty-icon {
    width: 56px; height: 56px;
    background: var(--accent-dim);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.25rem;
}
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="flex items-center justify-between mb-2" style="margin-bottom: 2rem;">
    <div>
        <h1 style="font-family: var(--font-display); font-size: 1.55rem; font-weight: 700; line-height: 1.2;">
            Olá, {{ explode(' ', auth()->user()->name)[0] }}
        </h1>
        <p class="text-muted text-sm mt-1">Aqui está um resumo das suas análises climáticas</p>
    </div>
    <div style="display:flex; gap:8px;">
        <a href="{{ route('compare.form') }}" class="btn btn-ghost">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="20" x2="18" y2="10"/>
                <line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6"  y1="20" x2="6"  y2="14"/>
            </svg>
            Comparar cidades
        </a>
        <a href="{{ route('search.form') }}" class="btn btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5"  y1="12" x2="19" y2="12"/>
            </svg>
            Nova análise
        </a>
    </div>
</div>

{{-- ── CARDS ESTATÍSTICOS ── --}}
@php
    $totalAnalises = $searches->total();
    $totalCidades  = auth()->user()->searches()->distinct('city_name')->count('city_name');
    $totalEsportes = \App\Models\Sport::count();
    $totalRelat    = auth()->user()->searches()->whereHas('report')->count();
@endphp

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-orange">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </div>
        <div>
            <p class="stat-number" style="color:#f97316;">{{ $totalAnalises }}</p>
            <p class="stat-desc">Análises realizadas</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                <circle cx="12" cy="9" r="2.5"/>
            </svg>
        </div>
        <div>
            <p class="stat-number" style="color:#60a5fa;">{{ $totalCidades }}</p>
            <p class="stat-desc">Cidades analisadas</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-purple">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 8v4l3 3"/>
            </svg>
        </div>
        <div>
            <p class="stat-number" style="color:#a78bfa;">{{ $totalEsportes }}</p>
            <p class="stat-desc">Esportes cadastrados</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon stat-icon-green">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
        </div>
        <div>
            <p class="stat-number" style="color:#4ade80;">{{ $totalRelat }}</p>
            <p class="stat-desc">Relatórios gerados</p>
        </div>
    </div>
</div>

{{-- ── LISTA DE BUSCAS ── --}}
<div class="section-header">
    <p class="section-title">Histórico de análises</p>
    @if($searches->total() > 0)
        <p class="text-muted text-sm">
            {{ $searches->total() }} {{ $searches->total() === 1 ? 'registro' : 'registros' }}
        </p>
    @endif
</div>

@if($searches->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </div>
        <p style="font-family:var(--font-display); font-size:1.1rem; font-weight:600; margin-bottom:8px;">
            Nenhuma análise ainda
        </p>
        <p class="text-muted text-sm" style="max-width:300px; margin:0 auto 1.5rem;">
            Busque dados climáticos históricos de qualquer cidade para avaliar riscos em eventos esportivos.
        </p>
        <a href="{{ route('search.form') }}" class="btn btn-primary">Fazer minha primeira análise</a>
    </div>

@else

    {{-- Lista --}}
    <div class="search-list">
        @foreach($searches as $search)
        <div class="search-item">
            <div class="search-avatar">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                    <circle cx="12" cy="9" r="2.5"/>
                </svg>
            </div>

            <div class="search-info">
                <p class="search-name">
                    {{ $search->city_name }}
                    <span class="text-muted" style="font-weight:400; font-size:14px;">
                        — {{ $search->getRawOriginal('sport') }}
                    </span>
                </p>
                <div class="search-meta">
                    <span>{{ $search->year }}</span>
                    <span class="dot">·</span>
                    <span>{{ $search->created_at->diffForHumans() }}</span>
                    @if($search->report)
                        <span class="dot">·</span>
                        <span style="color:#4ade80; font-size:11px; font-weight:600;">● IA</span>
                    @endif
                </div>
            </div>

            @if($search->climateData->count())
            <div class="risk-pills">
                @php $counts = $search->climateData->groupBy('risk_level'); @endphp
                @foreach(['high' => 'Alto', 'medium' => 'Médio', 'low' => 'Baixo'] as $lvl => $label)
                    @if(isset($counts[$lvl]))
                        <span class="badge badge-{{ $lvl }}">
                            {{ $counts[$lvl]->count() }}× {{ $label }}
                        </span>
                    @endif
                @endforeach
            </div>
            @endif

            <div class="search-actions">
                <a href="{{ route('results.show', $search) }}"
                   class="btn btn-ghost" style="padding:7px 12px; font-size:13px;">
                    Ver
                </a>
                <form method="POST" action="{{ route('searches.destroy', $search) }}"
                    onsubmit="return confirm('Remover esta análise?')" style="margin:0">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="padding:7px 10px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14H6L5 6"/>
                            <path d="M10 11v6"/><path d="M14 11v6"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    {{-- ── Paginação — UMA VEZ, depois de fechar o foreach e a div.search-list ── --}}
    {{ $searches->links('components.pagination') }}

@endif

@endsection