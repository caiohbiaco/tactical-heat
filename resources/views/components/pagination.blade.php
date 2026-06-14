@if ($paginator->hasPages())
<nav style="display:flex; align-items:center; justify-content:space-between; margin-top: 1.5rem; flex-wrap: wrap; gap: 12px;">

    {{-- Info --}}
    <p style="font-size: 13px; color: var(--muted);">
        Mostrando
        <span style="color: var(--text); font-weight: 500;">{{ $paginator->firstItem() }}</span>
        –
        <span style="color: var(--text); font-weight: 500;">{{ $paginator->lastItem() }}</span>
        de
        <span style="color: var(--text); font-weight: 500;">{{ $paginator->total() }}</span>
        análises
    </p>

    {{-- Botões --}}
    <div style="display:flex; align-items:center; gap:6px;">

        {{-- Anterior --}}
        @if($paginator->onFirstPage())
            <span style="
                display:inline-flex; align-items:center; gap:5px;
                padding: 7px 14px; border-radius: var(--radius);
                border: 1px solid var(--border); color: var(--subtle);
                font-size: 13px; cursor: not-allowed; user-select:none;
            ">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Anterior
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="
                display:inline-flex; align-items:center; gap:5px;
                padding: 7px 14px; border-radius: var(--radius);
                border: 1px solid var(--border); color: var(--muted);
                font-size: 13px; text-decoration:none; transition: all 0.18s;
            "
            onmouseover="this.style.color='var(--text)'; this.style.background='var(--surface-2)'"
            onmouseout="this.style.color='var(--muted)'; this.style.background='transparent'">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Anterior
            </a>
        @endif

        {{-- Números das páginas --}}
        @foreach($elements as $element)
            @if(is_string($element))
                <span style="color: var(--subtle); font-size: 13px; padding: 0 4px;">…</span>
            @endif
            @if(is_array($element))
                @foreach($element as $page => $url)
                    @if($page == $paginator->currentPage())
                        <span style="
                            display:inline-flex; align-items:center; justify-content:center;
                            width: 34px; height: 34px; border-radius: var(--radius);
                            background: var(--accent); color: #fff;
                            font-size: 13px; font-weight: 600;
                        ">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" style="
                            display:inline-flex; align-items:center; justify-content:center;
                            width: 34px; height: 34px; border-radius: var(--radius);
                            border: 1px solid var(--border); color: var(--muted);
                            font-size: 13px; text-decoration:none; transition: all 0.18s;
                        "
                        onmouseover="this.style.color='var(--text)'; this.style.background='var(--surface-2)'"
                        onmouseout="this.style.color='var(--muted)'; this.style.background='transparent'">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Próxima --}}
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="
                display:inline-flex; align-items:center; gap:5px;
                padding: 7px 14px; border-radius: var(--radius);
                border: 1px solid var(--border); color: var(--muted);
                font-size: 13px; text-decoration:none; transition: all 0.18s;
            "
            onmouseover="this.style.color='var(--text)'; this.style.background='var(--surface-2)'"
            onmouseout="this.style.color='var(--muted)'; this.style.background='transparent'">
                Próxima
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </a>
        @else
            <span style="
                display:inline-flex; align-items:center; gap:5px;
                padding: 7px 14px; border-radius: var(--radius);
                border: 1px solid var(--border); color: var(--subtle);
                font-size: 13px; cursor: not-allowed; user-select:none;
            ">
                Próxima
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </span>
        @endif

    </div>
</nav>
@endif