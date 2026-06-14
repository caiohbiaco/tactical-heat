{{-- resources/views/search/form.blade.php --}}
@extends('layouts.app')

@section('title', 'Nova Análise — Tactical Heat')

@push('styles')
<style>
.form-wrap {
    max-width: 580px;
    margin: 0 auto;
}
.page-eyebrow {
    font-size: 12px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--accent);
    font-weight: 600;
    margin-bottom: 8px;
}
.page-title {
    font-family: var(--font-display);
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 6px;
}
.sport-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: 10px;
    margin-top: 6px;
}
.sport-option { display: none; }
.sport-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 14px 10px;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.18s;
    font-size: 12px;
    color: var(--muted);
    text-align: center;
    background: var(--surface-2);
}
.sport-label:hover { border-color: var(--accent); color: var(--text); }
.sport-option:checked + .sport-label {
    border-color: var(--accent);
    background: var(--accent-dim);
    color: var(--accent);
    box-shadow: 0 0 0 1px var(--accent);
}
.sport-icon { font-size: 1.5rem; line-height: 1; }
.hint {
    font-size: 12px;
    color: var(--muted);
    margin-top: 6px;
    transition: color 0.2s;
}
.hint-warning { color: #f59e0b !important; }
.input-error { font-size: 12px; color: #f87171; margin-top: 4px; }

.year-warning-banner {
    display: none;
    align-items: center;
    gap: 10px;
    background: rgba(245,158,11,0.08);
    border: 1px solid rgba(245,158,11,0.3);
    border-radius: var(--radius);
    padding: 10px 14px;
    margin-top: 10px;
    font-size: 13px;
    color: #fbbf24;
    line-height: 1.5;
}
.year-warning-banner.visible { display: flex; }
.year-warning-banner svg { flex-shrink: 0; }

/* ── Autocomplete ─────────────────────────────────────────────────── */
.city-wrapper { position: relative; }
.city-suggestions {
    position: absolute;
    top: calc(100% + 4px);
    left: 0; right: 0;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    z-index: 999;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    display: none;
}
.city-suggestions.open { display: block; }
.suggestion-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    cursor: pointer;
    transition: background 0.15s;
    border-bottom: 1px solid var(--border-soft);
    font-size: 14px;
}
.suggestion-item:last-child { border-bottom: none; }
.suggestion-item:hover,
.suggestion-item.highlighted { background: var(--surface-2); }
.suggestion-item svg { color: var(--muted); flex-shrink: 0; }
.suggestion-main { font-weight: 500; color: var(--text); }
.suggestion-sub  { font-size: 12px; color: var(--muted); margin-top: 1px; }
.suggestion-loading,
.suggestion-empty {
    padding: 12px 14px;
    font-size: 13px;
    color: var(--muted);
    text-align: center;
}
.suggestion-flag { font-size: 1.1rem; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div class="form-wrap">

    <div style="margin-bottom: 2.5rem;">
        <p class="page-eyebrow">Nova análise</p>
        <h1 class="page-title">Onde e quando será o evento?</h1>
        <p class="text-muted text-sm">
            Usamos dados históricos reais da Open-Meteo para calcular riscos climáticos mensais.
        </p>
    </div>

    <form method="POST" action="{{ route('search.store') }}" id="searchForm">
        @csrf

        {{-- Cidade com autocomplete --}}
        <div class="form-group">
            <label>Cidade</label>
            <div class="city-wrapper">
                {{-- Campo visível — só para o usuário digitar/ver --}}
                <input
                    type="text"
                    id="cityDisplay"
                    placeholder="Ex: São Paulo, New York, Tokyo…"
                    autocomplete="off">

                {{-- Campos hidden — enviados ao backend --}}
                <input type="hidden" name="city"      id="cityHidden"   value="{{ old('city') }}">
                <input type="hidden" name="latitude"  id="latHidden"    value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="lngHidden"    value="{{ old('longitude') }}">

                <div class="city-suggestions" id="citySuggestions"></div>
            </div>
            @error('city') <p class="input-error">{{ $message }}</p> @enderror
            <p class="hint" id="cityHint">Digite o nome da cidade e selecione na lista.</p>
        </div>

        {{-- Ano --}}
        <div class="form-group">
            <label>Ano de referência</label>
            <input
                type="number"
                name="year"
                id="yearInput"
                value="{{ old('year', date('Y')) }}"
                min="1940"
                max="{{ date('Y') }}"
                required>
            @error('year') <p class="input-error">{{ $message }}</p> @enderror

            <p class="hint" id="yearHint">
                Dados históricos disponíveis de 1940 até {{ date('Y') - 1 }}.
                O ano atual ({{ date('Y') }}) usa dados parciais + previsão.
            </p>

            <div class="year-warning-banner" id="yearWarningBanner">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <span>
                    <strong>Ano atual selecionado.</strong>
                    Os meses já passados usam dados reais; os próximos 16 dias usam previsão;
                    os meses futuros usam média histórica dos últimos 5 anos como estimativa.
                </span>
            </div>
        </div>

        {{-- Esporte --}}
        <div class="form-group">
            <label>Modalidade esportiva</label>
            @error('sport') <p class="input-error">{{ $message }}</p> @enderror

            <div class="sport-grid">
                @foreach($sports as $sport)
                @php
                    $icons = [
                        'Futebol'                  => '⚽',
                        'Tênis'                    => '🎾',
                        'Atletismo'                => '🏃',
                        'Vôlei de Praia'           => '🏐',
                        'Natação em Águas Abertas' => '🏊',
                        'Remo'                     => '🚣',
                        'Ciclismo (Estrada)'       => '🚴',
                        'Maratona'                 => '🏅',
                        'Triatlo'                  => '🥇',
                        'Rugby (Sevens)'           => '🏉',
                        'Beisebol'                 => '⚾',
                        'Críquete'                 => '🏏',
                        'Fórmula 1'                => '🏎️',
                        'MotoGP'                   => '🏍️',
                        'Esqui Alpino'             => '⛷️',
                        'Patinação no Gelo'        => '⛸️',
                    ];
                    $icon = $icons[$sport->name] ?? '🏅';
                @endphp
                <div>
                    <input class="sport-option" type="radio"
                        id="sport_{{ $sport->id }}"
                        name="sport"
                        value="{{ $sport->name }}"
                        {{ old('sport') === $sport->name ? 'checked' : '' }}>
                    <label class="sport-label" for="sport_{{ $sport->id }}">
                        <span class="sport-icon">{{ $icon }}</span>
                        {{ $sport->name }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        <div style="display:flex; gap:12px; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                Analisar clima
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// ── Ano ───────────────────────────────────────────────────────────────────
const yearInput     = document.getElementById('yearInput');
const yearHint      = document.getElementById('yearHint');
const warningBanner = document.getElementById('yearWarningBanner');
const currentYear   = {{ date('Y') }};

function updateYearFeedback() {
    const val = parseInt(yearInput.value);
    if (val === currentYear) {
        warningBanner.classList.add('visible');
        yearHint.classList.add('hint-warning');
        yearHint.textContent = '⚠ Dados reais (jan–hoje) + previsão (16 dias) + estimativa histórica (meses futuros).';
    } else {
        warningBanner.classList.remove('visible');
        yearHint.classList.remove('hint-warning');
        yearHint.textContent = 'Dados históricos confirmados. Disponíveis de 1940 até ' + (currentYear - 1) + '.';
    }
}
yearInput.addEventListener('input', updateYearFeedback);
updateYearFeedback();

// ── Autocomplete de cidades ───────────────────────────────────────────────
const cityDisplay    = document.getElementById('cityDisplay');
const cityHidden     = document.getElementById('cityHidden');
const latHidden      = document.getElementById('latHidden');
const lngHidden      = document.getElementById('lngHidden');
const suggestionsBox = document.getElementById('citySuggestions');
const cityHint       = document.getElementById('cityHint');

let debounceTimer  = null;
let highlighted    = -1;
let currentResults = [];

// Emoji de bandeira a partir do código ISO do país (ex: "BR" → 🇧🇷)
function countryFlag(code) {
    if (!code) return '🌍';
    return code.toUpperCase()
        .replace(/./g, c => String.fromCodePoint(127397 + c.charCodeAt(0)));
}

// Linha secundária: "Estado, País"
function formatSub(item) {
    return [item.admin1, item.country].filter(Boolean).join(', ');
}

function renderSuggestions(items) {
    highlighted    = -1;
    currentResults = items;

    if (!items.length) {
        suggestionsBox.innerHTML = '<div class="suggestion-empty">Nenhuma cidade encontrada.</div>';
        suggestionsBox.classList.add('open');
        return;
    }

    suggestionsBox.innerHTML = items.map((item, i) => `
        <div class="suggestion-item" data-index="${i}">
            <span class="suggestion-flag">${countryFlag(item.country_code)}</span>
            <div style="min-width:0; flex:1;">
                <div class="suggestion-main">${item.name}</div>
                <div class="suggestion-sub">${formatSub(item)}</div>
            </div>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </div>
    `).join('');

    suggestionsBox.querySelectorAll('.suggestion-item').forEach(el => {
        el.addEventListener('mousedown', e => {
            e.preventDefault(); // evita blur antes do clique
            selectCity(parseInt(el.dataset.index));
        });
    });

    suggestionsBox.classList.add('open');
}

function selectCity(index) {
    const item = currentResults[index];
    if (!item) return;

    // ── A CORREÇÃO PRINCIPAL ESTÁ AQUI ───────────────────────────────────
    // cityHidden recebe SOMENTE item.name (ex: "Londres")
    // Nunca o texto completo "Londres, Inglaterra, Reino Unido"
    cityHidden.value = item.name;

    // Guarda lat/long para o controller não precisar chamar geocoding de novo
    latHidden.value  = item.latitude;
    lngHidden.value  = item.longitude;

    // O campo visível pode mostrar o texto completo — é só para o usuário ver
    cityDisplay.value = [item.name, item.admin1, item.country].filter(Boolean).join(', ');

    // Feedback visual de confirmação
    cityHint.textContent = `✓ ${item.name} — ${formatSub(item)} (${item.latitude}, ${item.longitude})`;
    cityHint.style.color = '#4ade80';
    cityDisplay.style.borderColor = '';

    closeSuggestions();
}

function closeSuggestions() {
    suggestionsBox.classList.remove('open');
    suggestionsBox.innerHTML = '';
    currentResults = [];
    highlighted    = -1;
}

async function fetchCities(query) {
    suggestionsBox.innerHTML = '<div class="suggestion-loading">Buscando cidades…</div>';
    suggestionsBox.classList.add('open');

    try {
        const res  = await fetch(
            `https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(query)}&count=8&language=pt&format=json`
        );
        const data = await res.json();
        renderSuggestions(data.results || []);
    } catch {
        suggestionsBox.innerHTML = '<div class="suggestion-empty">Erro ao buscar cidades. Tente novamente.</div>';
    }
}

// Digitar no campo — limpa os hiddens para forçar nova seleção
cityDisplay.addEventListener('input', () => {
    const val = cityDisplay.value.trim();

    cityHidden.value = '';
    latHidden.value  = '';
    lngHidden.value  = '';
    cityHint.textContent = 'Digite o nome da cidade e selecione na lista.';
    cityHint.style.color = '';

    if (val.length < 2) { closeSuggestions(); return; }

    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => fetchCities(val), 350);
});

// Navegação por teclado ↑ ↓ Enter Escape
cityDisplay.addEventListener('keydown', e => {
    const items = suggestionsBox.querySelectorAll('.suggestion-item');
    if (!items.length) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        highlighted = Math.min(highlighted + 1, items.length - 1);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        highlighted = Math.max(highlighted - 1, 0);
    } else if (e.key === 'Enter' && highlighted >= 0) {
        e.preventDefault();
        selectCity(highlighted);
        return;
    } else if (e.key === 'Escape') {
        closeSuggestions();
        return;
    }

    items.forEach((el, i) => el.classList.toggle('highlighted', i === highlighted));
    if (items[highlighted]) items[highlighted].scrollIntoView({ block: 'nearest' });
});

// Fecha ao clicar fora
document.addEventListener('click', e => {
    if (!cityDisplay.contains(e.target) && !suggestionsBox.contains(e.target)) {
        closeSuggestions();
    }
});

// Validação antes de enviar — garante que o usuário selecionou da lista
document.getElementById('searchForm').addEventListener('submit', e => {
    if (!cityHidden.value.trim()) {
        e.preventDefault();
        cityDisplay.style.borderColor = '#ef4444';
        cityHint.textContent = '⚠ Selecione uma cidade da lista antes de continuar.';
        cityHint.style.color = '#f87171';
        cityDisplay.focus();
    }
});
</script>
@endpush