{{-- resources/views/compare/form.blade.php --}}
@extends('layouts.app')

@section('title', 'Comparar Cidades — Tactical Heat')

@push('styles')
<style>
.page-eyebrow {
    font-size: 12px; letter-spacing: 0.1em; text-transform: uppercase;
    color: var(--accent); font-weight: 600; margin-bottom: 8px;
}
.page-title {
    font-family: var(--font-display);
    font-size: 1.8rem; font-weight: 700; margin-bottom: 6px;
}
.compare-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
    margin-bottom: 1.25rem;
}
.city-block {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
}
.city-block-label {
    font-size: 11px; font-weight: 600; letter-spacing: 0.08em;
    text-transform: uppercase; color: var(--muted); margin-bottom: 1rem;
    display: flex; align-items: center; gap: 8px;
}
.city-tag {
    width: 20px; height: 20px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700;
}
.city-tag-a { background: rgba(249,115,22,0.2); color: #f97316; }
.city-tag-b { background: rgba(96,165,250,0.2); color: #60a5fa; }

.sport-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: 8px; margin-top: 6px;
}
.sport-option { display: none; }
.sport-label {
    display: flex; flex-direction: column; align-items: center; gap: 6px;
    padding: 12px 8px; border: 1px solid var(--border); border-radius: var(--radius);
    cursor: pointer; transition: all 0.18s; font-size: 12px; color: var(--muted);
    text-align: center; background: var(--surface-2);
}
.sport-label:hover { border-color: var(--accent); color: var(--text); }
.sport-option:checked + .sport-label {
    border-color: var(--accent); background: var(--accent-dim);
    color: var(--accent); box-shadow: 0 0 0 1px var(--accent);
}
.sport-icon { font-size: 1.4rem; }
.input-error { font-size: 12px; color: #f87171; margin-top: 4px; }
.hint { font-size: 12px; color: var(--muted); margin-top: 5px; }

@media (max-width: 600px) {
    .compare-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

<div style="max-width: 720px; margin: 0 auto;">
    <p class="page-eyebrow">Comparação</p>
    <h1 class="page-title">Comparar duas cidades</h1>
    <p class="text-muted text-sm" style="margin-bottom: 2rem;">
        Selecione duas cidades e o mesmo esporte para ver a análise lado a lado.
    </p>

    <form method="POST" action="{{ route('compare.result') }}">
        @csrf

        {{-- Cidades --}}
        <div class="compare-grid">
            <div class="city-block">
                <p class="city-block-label">
                    <span class="city-tag city-tag-a">A</span>
                    Cidade 1
                </p>
                <div class="form-group">
                    <label>Nome da cidade</label>
                    <input type="text" name="city_a" value="{{ old('city_a') }}"
                        placeholder="Ex: São Paulo" required>
                    @error('city_a') <p class="input-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Ano</label>
                    <input type="number" name="year_a" value="{{ old('year_a', date('Y') - 1) }}"
                        min="1940" max="{{ date('Y') - 1 }}" required>
                    @error('year_a') <p class="input-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="city-block">
                <p class="city-block-label">
                    <span class="city-tag city-tag-b">B</span>
                    Cidade 2
                </p>
                <div class="form-group">
                    <label>Nome da cidade</label>
                    <input type="text" name="city_b" value="{{ old('city_b') }}"
                        placeholder="Ex: Fortaleza" required>
                    @error('city_b') <p class="input-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Ano</label>
                    <input type="number" name="year_b" value="{{ old('year_b', date('Y') - 1) }}"
                        min="1940" max="{{ date('Y') - 1 }}" required>
                    @error('year_b') <p class="input-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Esporte --}}
        <div class="form-group" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem;">
            <label style="font-size:11px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; color:var(--muted);">
                Modalidade esportiva
            </label>
            @error('sport') <p class="input-error">{{ $message }}</p> @enderror
            <div class="sport-grid" style="margin-top:12px;">
                @foreach($sports as $sport)
                @php
                    $icons = ['Futebol'=>'⚽','Tênis'=>'🎾','Atletismo'=>'🏃','Ciclismo'=>'🚴','Vôlei de Praia'=>'🏐','Natação em Águas Abertas' => '🏊','Remo'=> '🚣','Ciclismo (Estrada)'=> '🚴','Maratona'=> '🏅','Triatlo'=> '🥇','Rugby (Sevens)'=> '🏉','Beisebol'=> '⚾','Críquete'=> '🏏','Fórmula 1'=> '🏎️','MotoGP'=> '🏍️','Esqui Alpino'=> '⛷️','Patinação no Gelo'=> '⛸️',];
                    $icon = $icons[$sport->name] ?? '🏅';
                @endphp
                <div>
                    <input class="sport-option" type="radio"
                        id="sport_{{ $sport->id }}"
                        name="sport" value="{{ $sport->name }}"
                        {{ old('sport') === $sport->name ? 'checked' : '' }}>
                    <label class="sport-label" for="sport_{{ $sport->id }}">
                        <span class="sport-icon">{{ $icon }}</span>
                        {{ $sport->name }}
                    </label>
                </div>
                @endforeach
            </div>
            <p class="hint">O mesmo esporte será usado para calcular os limiares de risco de ambas as cidades.</p>
        </div>

        <div style="display:flex; gap:12px; margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
                Comparar
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection