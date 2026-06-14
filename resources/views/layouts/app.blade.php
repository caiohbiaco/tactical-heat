<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tactical Heat')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:          #0d0f12;
            --surface:     #13161b;
            --surface-2:   #1c2029;
            --border:      rgba(255,255,255,0.07);
            --border-soft: rgba(255,255,255,0.04);
            --accent:      #f97316;
            --accent-dim:  rgba(249,115,22,0.12);
            --accent-glow: rgba(249,115,22,0.25);
            --low:    #22c55e;
            --medium: #f59e0b;
            --high:   #ef4444;
            --text:   #f0ede8;
            --muted:  #7a8099;
            --subtle: #3d4255;
            --font-display: 'Syne', sans-serif;
            --font-body:    'DM Sans', sans-serif;
            --radius:   10px;
            --radius-lg:18px;
            --sidebar-w: 220px;
            --sidebar-collapsed: 0px;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-body);
            font-size: 15px;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* ── Navbar ─────────────────────────────────────────────────────── */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 200;
            background: rgba(13,15,18,0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 0 1.5rem;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .navbar-left { display: flex; align-items: center; gap: 12px; }
        .navbar-brand {
            font-family: var(--font-display);
            font-size: 1.1rem; font-weight: 700;
            color: var(--text); text-decoration: none;
            display: flex; align-items: center; gap: 8px;
        }
        .brand-dot {
            width: 8px; height: 8px;
            background: var(--accent); border-radius: 50%;
        }

        /* Botão de toggle da sidebar na navbar */
        .sidebar-toggle-btn {
            width: 34px; height: 34px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all 0.18s;
            flex-shrink: 0;
        }
        .sidebar-toggle-btn:hover { border-color: var(--accent); background: var(--accent-dim); }
        .sidebar-toggle-btn svg { color: var(--muted); transition: color 0.18s; }
        .sidebar-toggle-btn:hover svg { color: var(--accent); }

        .navbar-nav { display: flex; align-items: center; gap: 4px; }
        .nav-link {
            color: var(--muted); text-decoration: none;
            font-size: 14px; padding: 6px 12px;
            border-radius: var(--radius); transition: all 0.18s;
            background: none; border: none; cursor: pointer;
            font-family: var(--font-body);
        }
        .nav-link:hover { color: var(--text); background: var(--surface-2); }
        .nav-btn {
            background: var(--accent); color: #fff; border: none; cursor: pointer;
            padding: 7px 16px; border-radius: var(--radius);
            font-family: var(--font-body); font-size: 14px; font-weight: 500;
            text-decoration: none; transition: opacity 0.18s;
        }
        .nav-btn:hover { opacity: 0.88; }

        /* ── Sidebar ─────────────────────────────────────────────────────── */
        .sidebar {
            position: fixed;
            top: 56px; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            z-index: 150;
            display: flex;
            flex-direction: column;
            padding: 1.25rem 0;
            transform: translateX(calc(-1 * var(--sidebar-w)));
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar::-webkit-scrollbar { width: 3px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: var(--subtle); border-radius: 2px; }

        /* Hover: mostra a sidebar */
        .sidebar:hover,
        .sidebar.pinned,
        .sidebar-trigger:hover ~ .sidebar,
        body:has(.sidebar-trigger:hover) .sidebar {
            transform: translateX(0);
        }

        /* Trigger invisível na borda esquerda */
        .sidebar-trigger {
            position: fixed;
            top: 56px; left: 0; bottom: 0;
            width: 18px;
            z-index: 160;
            cursor: pointer;
        }

        /* Indicador visual na borda */
        .sidebar-trigger::after {
            content: '';
            position: absolute;
            top: 50%; left: 4px;
            transform: translateY(-50%);
            width: 3px; height: 40px;
            background: var(--accent);
            border-radius: 2px;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .sidebar-trigger:hover::after { opacity: 0.6; }

        /* Pin button dentro da sidebar */
        .sidebar-pin {
            position: absolute;
            top: 10px; right: 10px;
            width: 28px; height: 28px;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: all 0.18s;
            color: var(--muted);
        }
        .sidebar-pin:hover { border-color: var(--accent); color: var(--accent); }
        .sidebar.pinned .sidebar-pin {
            background: var(--accent-dim);
            border-color: var(--accent);
            color: var(--accent);
        }

        /* Nav items da sidebar */
        .sidebar-section {
            padding: 0 10px;
            margin-bottom: 6px;
        }
        .sidebar-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--subtle);
            padding: 0 8px;
            margin-bottom: 4px;
            margin-top: 12px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: var(--radius);
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.18s;
            white-space: nowrap;
            cursor: pointer;
            background: none;
            border: none;
            width: 100%;
            font-family: var(--font-body);
        }
        .sidebar-link:hover {
            background: var(--surface-2);
            color: var(--text);
        }
        .sidebar-link.active {
            background: var(--accent-dim);
            color: var(--accent);
        }
        .sidebar-link svg { flex-shrink: 0; }
        .sidebar-link .link-badge {
            margin-left: auto;
            background: var(--accent);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 99px;
        }

        .sidebar-divider {
            height: 1px;
            background: var(--border);
            margin: 8px 16px;
        }

        /* Ajuste do main quando sidebar está pinada */
        .app-shell {
            padding-top: 56px;
            transition: padding-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        body.sidebar-pinned .app-shell {
            padding-left: var(--sidebar-w);
        }

        .main-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 4rem;
        }

        /* ── Cards ──────────────────────────────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
        }
        .card-sm { padding: 1.25rem; }

        /* ── Buttons ────────────────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 20px; border-radius: var(--radius);
            font-family: var(--font-body); font-size: 14px; font-weight: 500;
            cursor: pointer; text-decoration: none; border: 1px solid transparent;
            transition: all 0.18s;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { opacity: 0.88; }
        .btn-ghost { background: transparent; color: var(--muted); border-color: var(--border); }
        .btn-ghost:hover { color: var(--text); background: var(--surface-2); }
        .btn-danger { background: transparent; color: #ef4444; border-color: rgba(239,68,68,0.25); }
        .btn-danger:hover { background: rgba(239,68,68,0.1); }

        /* ── Badges ─────────────────────────────────────────────────────── */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 99px; font-size: 12px; font-weight: 500; }
        .badge-low    { background: rgba(34,197,94,0.12);  color: #4ade80; }
        .badge-medium { background: rgba(245,158,11,0.12); color: #fbbf24; }
        .badge-high   { background: rgba(239,68,68,0.12);  color: #f87171; }

        /* ── Forms ──────────────────────────────────────────────────────── */
        .form-group { margin-bottom: 1.25rem; }
        label {
            display: block; font-size: 13px; color: var(--muted);
            margin-bottom: 6px; font-weight: 500; letter-spacing: 0.02em;
        }
        input[type="text"], input[type="number"], input[type="email"],
        input[type="password"], select {
            width: 100%; background: var(--surface-2); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 10px 14px; color: var(--text);
            font-family: var(--font-body); font-size: 14px; outline: none;
            transition: border-color 0.18s; appearance: none;
        }
        input:focus, select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-dim); }
        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%237a8099' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 14px center; padding-right: 36px;
        }

        /* ── Alerts ─────────────────────────────────────────────────────── */
        .alert { padding: 12px 16px; border-radius: var(--radius); margin-bottom: 1.5rem; font-size: 14px; }
        .alert-success { background: rgba(34,197,94,0.1);  border: 1px solid rgba(34,197,94,0.25);  color: #4ade80; }
        .alert-danger  { background: rgba(239,68,68,0.1);  border: 1px solid rgba(239,68,68,0.25);  color: #f87171; }

        /* ── Utilities ──────────────────────────────────────────────────── */
        .text-muted  { color: var(--muted); }
        .text-accent { color: var(--accent); }
        .text-sm     { font-size: 13px; }
        .mt-1 { margin-top: 0.5rem; } .mt-2 { margin-top: 1rem; } .mt-3 { margin-top: 1.5rem; }
        .mb-1 { margin-bottom: 0.5rem; } .mb-2 { margin-bottom: 1rem; }
        .flex { display: flex; } .items-center { align-items: center; }
        .gap-2 { gap: 0.5rem; } .gap-3 { gap: 1rem; }
        .justify-between { justify-content: space-between; }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: var(--surface); }
        ::-webkit-scrollbar-thumb { background: var(--subtle); border-radius: 3px; }
    </style>

    @stack('styles')
</head>
<body>

{{-- Trigger invisível para hover na sidebar --}}
<div class="sidebar-trigger" id="sidebarTrigger"></div>

{{-- ── Navbar ──────────────────────────────────────────────────────────── --}}
<nav class="navbar">
    <div class="navbar-left">
        @auth
        <button class="sidebar-toggle-btn" onclick="toggleSidebarPin()" title="Fixar barra lateral">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6"  x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
        @endauth
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            <span class="brand-dot"></span>
            Tactical Heat
        </a>
    </div>
    <div class="navbar-nav">
        @auth
            <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
            <a href="{{ route('search.form') }}" class="nav-link">Nova Análise</a>
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <a href="{{ route('profile.show') }}" class="nav-link">Perfil</a>
                <button type="submit" class="nav-link">Sair</button>
            </form>
        @else
            <a href="{{ route('login') }}"    class="nav-link">Entrar</a>
            <a href="{{ route('register') }}" class="nav-btn">Cadastrar</a>
        @endauth
    </div>
</nav>

{{-- ── Sidebar ──────────────────────────────────────────────────────────── --}}
@auth
<aside class="sidebar" id="sidebar">

    {{-- Pin button --}}
    <button class="sidebar-pin" id="sidebarPin" onclick="toggleSidebarPin()" title="Fixar sidebar">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="12" y1="17" x2="12" y2="22"/>
            <path d="M5 17h14v-1.76a2 2 0 00-1.11-1.79l-1.78-.9A2 2 0 0115 10.76V6h1a2 2 0 000-4H8a2 2 0 000 4h1v4.76a2 2 0 01-1.11 1.79l-1.78.9A2 2 0 005 15.24V17z"/>
        </svg>
    </button>

    {{-- Usuário --}}
    <div style="padding: 0 16px 16px; border-bottom: 1px solid var(--border); margin-bottom: 8px;">
    <div style="display:flex; align-items:center; gap:10px; margin-top:8px;">
        @if(auth()->user()->avatar)
            <img src="{{ Storage::url(auth()->user()->avatar) }}"
                 alt="Avatar"
                 style="width:34px; height:34px; border-radius:50%; object-fit:cover; border:2px solid var(--accent); flex-shrink:0;">
        @else
            <div style="width:34px; height:34px; border-radius:50%; background:var(--accent-dim); display:flex; align-items:center; justify-content:center; flex-shrink:0; border:2px solid var(--accent);">
                <span style="font-size:14px; font-weight:700; color:var(--accent);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
            </div>
        @endif
            <div style="overflow:hidden;">
                <p style="font-size:13px; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ auth()->user()->name }}
                </p>
                <p style="font-size:11px; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ auth()->user()->email }}
                </p>
            </div>
        </div>
    </div>

    {{-- Nav principal --}}
    <div class="sidebar-section">
        <p class="sidebar-section-label">Principal</p>

        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('search.form') }}"
           class="sidebar-link {{ request()->routeIs('search.form') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            Nova análise
            <span class="link-badge">+</span>
        </a>

        <a href="{{ route('compare.form') }}"
           class="sidebar-link {{ request()->routeIs('compare.*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="20" x2="18" y2="10"/>
                <line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6"  y1="20" x2="6"  y2="14"/>
            </svg>
            Comparar cidades
        </a>
    </div>

    <div class="sidebar-divider"></div>

    {{-- Últimas análises --}}
    @php
        $recentSearches = auth()->user()->searches()->latest()->limit(5)->get();
    @endphp
    @if($recentSearches->count())
    <div class="sidebar-section">
        <p class="sidebar-section-label">Recentes</p>
        @foreach($recentSearches as $s)
        <a href="{{ route('results.show', $s) }}"
           class="sidebar-link {{ request()->is('results/'.$s->id.'*') ? 'active' : '' }}"
           style="font-size:13px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
            </svg>
            <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                {{ $s->city_name }}
            </span>
            <span style="margin-left:auto; font-size:11px; color:var(--subtle); flex-shrink:0;">
                {{ $s->year }}
            </span>
        </a>
        @endforeach
    </div>
    <div class="sidebar-divider"></div>
    @endif

    {{-- Conta --}}
    <div class="sidebar-section" style="margin-top:auto;">
        <p class="sidebar-section-label">Conta</p>
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <a href="{{ route('profile.show') }}"
                class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                    Meu perfil
            </a>
            <button type="submit" class="sidebar-link" style="color:#f87171;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Sair
            </button>
        </form>
    </div>

</aside>
@endauth

{{-- ── App shell ────────────────────────────────────────────────────────── --}}
<div class="app-shell">
    <main class="main-wrap">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>

<script>
// ── Sidebar pin/unpin ──────────────────────────────────────────────────────
const sidebar  = document.getElementById('sidebar');
const pinBtn   = document.getElementById('sidebarPin');
const body     = document.body;
const STORAGE_KEY = 'th_sidebar_pinned';

// Restaura estado salvo
if (localStorage.getItem(STORAGE_KEY) === '1') {
    sidebar?.classList.add('pinned');
    body.classList.add('sidebar-pinned');
}

function toggleSidebarPin() {
    if (!sidebar) return;
    const isPinned = sidebar.classList.toggle('pinned');
    body.classList.toggle('sidebar-pinned', isPinned);
    localStorage.setItem(STORAGE_KEY, isPinned ? '1' : '0');
}

// Mantém sidebar visível enquanto o mouse está nela ou no trigger
const trigger = document.getElementById('sidebarTrigger');
if (sidebar && trigger) {
    // O hover é gerenciado pelo CSS (:hover), mas garantimos que
    // ao sair da sidebar ela some (exceto se pinada)
    sidebar.addEventListener('mouseleave', () => {
        // CSS cuida disso via :hover, mas forçamos reflow se necessário
    });
}
</script>

@stack('scripts')
</body>
</html>