@extends('layouts.app')

@section('title', 'Meu Perfil — Tactical Heat')

@push('styles')
<style>
/* ── Layout ────────────────────────────────────────────────────────── */
.profile-wrap {
    max-width: 780px;
    margin: 0 auto;
}

/* ── Hero do perfil ────────────────────────────────────────────────── */
.profile-hero {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1.75rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.avatar-wrap {
    position: relative;
    flex-shrink: 0;
}
.avatar-img {
    width: 88px; height: 88px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--accent);
    display: block;
}
.avatar-placeholder {
    width: 88px; height: 88px;
    border-radius: 50%;
    background: var(--accent-dim);
    border: 3px solid var(--accent);
    display: flex; align-items: center; justify-content: center;
    font-family: var(--font-display);
    font-size: 2rem; font-weight: 700;
    color: var(--accent);
}
.avatar-upload-btn {
    position: absolute;
    bottom: 0; right: 0;
    width: 28px; height: 28px;
    background: var(--accent);
    border-radius: 50%;
    border: 2px solid var(--bg);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: opacity 0.18s;
}
.avatar-upload-btn:hover { opacity: 0.85; }
.avatar-upload-btn input { display: none; }

.profile-meta { flex: 1; min-width: 0; }
.profile-name {
    font-family: var(--font-display);
    font-size: 1.5rem; font-weight: 700;
    margin-bottom: 2px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.profile-email { font-size: 14px; color: var(--muted); margin-bottom: 10px; }
.profile-bio { font-size: 14px; color: var(--muted); font-style: italic; }
.profile-since { font-size: 12px; color: var(--subtle); margin-top: 8px; }

/* ── Stats row ─────────────────────────────────────────────────────── */
.profile-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.pstat {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1rem;
    text-align: center;
}
.pstat-num {
    font-family: var(--font-display);
    font-size: 1.8rem; font-weight: 700;
    margin-bottom: 2px;
}
.pstat-label { font-size: 12px; color: var(--muted); }

/* ── Tabs ──────────────────────────────────────────────────────────── */
.tab-bar {
    display: flex;
    gap: 4px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 1.5rem;
}
.tab-btn {
    padding: 9px 18px;
    font-size: 14px; font-family: var(--font-body); font-weight: 500;
    color: var(--muted); background: none; border: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    cursor: pointer; transition: all 0.18s;
}
.tab-btn:hover { color: var(--text); }
.tab-btn.active { color: var(--accent); border-bottom-color: var(--accent); }

/* ── Tab panels ────────────────────────────────────────────────────── */
.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* ── Section cards ─────────────────────────────────────────────────── */
.section-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.75rem;
    margin-bottom: 1.25rem;
}
.section-title {
    font-size: 13px; font-weight: 600;
    letter-spacing: 0.06em; text-transform: uppercase;
    color: var(--muted); margin-bottom: 1.25rem;
    display: flex; align-items: center; gap: 8px;
}
.section-title svg { color: var(--accent); }

/* ── Danger zone ───────────────────────────────────────────────────── */
.danger-zone {
    background: rgba(239,68,68,0.04);
    border: 1px solid rgba(239,68,68,0.2);
    border-radius: var(--radius-lg);
    padding: 1.75rem;
}
.danger-title {
    font-size: 13px; font-weight: 600; letter-spacing: 0.06em;
    text-transform: uppercase; color: #f87171; margin-bottom: 6px;
}
.danger-desc { font-size: 13px; color: var(--muted); margin-bottom: 1.25rem; }

/* ── Alerts de sucesso inline ──────────────────────────────────────── */
.inline-success {
    display: flex; align-items: center; gap: 8px;
    background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.25);
    border-radius: var(--radius); padding: 10px 14px;
    font-size: 13px; color: #4ade80; margin-bottom: 1.25rem;
}

/* ── Password strength ─────────────────────────────────────────────── */
.strength-bar {
    height: 3px; border-radius: 2px;
    background: var(--surface-2);
    margin-top: 6px; overflow: hidden;
}
.strength-fill {
    height: 100%; border-radius: 2px;
    width: 0%; transition: width 0.3s, background 0.3s;
}

.input-error { font-size: 12px; color: #f87171; margin-top: 4px; }
.hint { font-size: 12px; color: var(--muted); margin-top: 5px; }
</style>
@endpush

@section('content')
<div class="profile-wrap">

    {{-- ── Hero ── --}}
    <div class="profile-hero">
        <div class="avatar-wrap">
            @if($user->avatar)
                <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="avatar-img">
            @else
                <div class="avatar-placeholder">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif

            {{-- Botão de upload inline --}}
            <form method="POST" action="{{ route('profile.avatar') }}"
                enctype="multipart/form-data" id="avatarForm">
                @csrf
                <label class="avatar-upload-btn" title="Alterar foto">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5">
                        <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    <input type="file" name="avatar" accept="image/*" onchange="document.getElementById('avatarForm').submit()">
                </label>
            </form>
        </div>

        <div class="profile-meta">
            <p class="profile-name">{{ $user->name }}</p>
            <p class="profile-email">{{ $user->email }}</p>
            @if($user->bio)
                <p class="profile-bio">{{ $user->bio }}</p>
            @endif
            <p class="profile-since">
                Membro desde {{ $user->created_at->format('d/m/Y') }}
            </p>

            @if($user->avatar)
                <form method="POST" action="{{ route('profile.avatar.remove') }}" style="margin-top:10px;">
                    @csrf @method('DELETE')
                    <button type="submit" style="font-size:12px; color:var(--muted); background:none; border:none; cursor:pointer; padding:0; text-decoration:underline;">
                        Remover foto
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- ── Stats ── --}}
    <div class="profile-stats">
        <div class="pstat">
            <p class="pstat-num" style="color: var(--accent);">{{ $stats['searches'] }}</p>
            <p class="pstat-label">Análises feitas</p>
        </div>
        <div class="pstat">
            <p class="pstat-num" style="color: #60a5fa;">{{ $stats['cities'] }}</p>
            <p class="pstat-label">Cidades analisadas</p>
        </div>
        <div class="pstat">
            <p class="pstat-num" style="color: #4ade80;">{{ $stats['reports'] }}</p>
            <p class="pstat-label">Relatórios de IA</p>
        </div>
    </div>

    {{-- ── Tabs ── --}}
    @php $activeTab = session('tab', 'info'); @endphp

    <div class="tab-bar">
        <button class="tab-btn {{ $activeTab !== 'password' && $activeTab !== 'danger' ? 'active' : '' }}"
            onclick="switchTab('info', this)">
            Informações
        </button>
        <button class="tab-btn {{ $activeTab === 'password' ? 'active' : '' }}"
            onclick="switchTab('password', this)">
            Senha
        </button>
        <button class="tab-btn {{ $activeTab === 'danger' ? 'active' : '' }}"
            onclick="switchTab('danger', this)">
            Conta
        </button>
    </div>

    {{-- ── Tab: Informações ── --}}
    <div class="tab-panel {{ $activeTab !== 'password' && $activeTab !== 'danger' ? 'active' : '' }}" id="tab-info">

        @if(session('success_info'))
            <div class="inline-success">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                {{ session('success_info') }}
            </div>
        @endif

        @if(session('success_avatar'))
            <div class="inline-success">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                {{ session('success_avatar') }}
            </div>
        @endif

        <div class="section-card">
            <p class="section-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                Dados pessoais
            </p>

            <form method="POST" action="{{ route('profile.info') }}">
                @csrf

                <div class="form-group">
                    <label>Nome completo</label>
                    <input type="text" name="name"
                        value="{{ old('name', $user->name) }}"
                        placeholder="Seu nome" required>
                    @error('name') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email"
                        value="{{ old('email', $user->email) }}"
                        placeholder="seu@email.com" required>
                    @error('email') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label>Bio <span style="color:var(--subtle); font-weight:400;">(opcional)</span></label>
                    <input type="text" name="bio"
                        value="{{ old('bio', $user->bio) }}"
                        placeholder="Ex: Treinador de atletismo em SP"
                        maxlength="255">
                    <p class="hint">Aparece no seu perfil. Máximo 255 caracteres.</p>
                    @error('bio') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Salvar alterações
                </button>
            </form>
        </div>
    </div>

    {{-- ── Tab: Senha ── --}}
    <div class="tab-panel {{ $activeTab === 'password' ? 'active' : '' }}" id="tab-password">

        @if(session('success_password'))
            <div class="inline-success">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                {{ session('success_password') }}
            </div>
        @endif

        <div class="section-card">
            <p class="section-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                Alterar senha
            </p>

            <form method="POST" action="{{ route('profile.password') }}">
                @csrf

                <div class="form-group">
                    <label>Senha atual</label>
                    <input type="password" name="current_password"
                        placeholder="••••••••" required>
                    @error('current_password') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label>Nova senha</label>
                    <input type="password" name="password" id="newPassword"
                        placeholder="Mínimo 8 caracteres" required
                        oninput="checkStrength(this.value)">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <p class="hint" id="strengthLabel">Digite a nova senha</p>
                    @error('password') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label>Confirmar nova senha</label>
                    <input type="password" name="password_confirmation"
                        placeholder="Repita a nova senha" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                    Alterar senha
                </button>
            </form>
        </div>
    </div>

    {{-- ── Tab: Conta ── --}}
    <div class="tab-panel {{ $activeTab === 'danger' ? 'active' : '' }}" id="tab-danger">
        <div class="danger-zone">
            <p class="danger-title">Zona de perigo</p>
            <p class="danger-desc">
                Excluir sua conta é permanente. Todos os dados — análises, relatórios e histórico — serão removidos imediatamente e não poderão ser recuperados.
            </p>

            <form method="POST" action="{{ route('profile.destroy') }}"
                onsubmit="return confirm('Tem certeza? Esta ação não pode ser desfeita.')">
                @csrf @method('DELETE')

                <div class="form-group">
                    <label>
                        Para confirmar, digite <strong style="color:#f87171;">EXCLUIR</strong> abaixo:
                    </label>
                    <input type="text" name="confirm_delete"
                        placeholder="EXCLUIR" required
                        style="border-color: rgba(239,68,68,0.3);">
                    @error('confirm_delete') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn btn-danger">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6l-1 14H6L5 6"/>
                        <path d="M10 11v6"/><path d="M14 11v6"/>
                    </svg>
                    Excluir minha conta permanentemente
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ── Troca de tabs ─────────────────────────────────────────────────────────
function switchTab(id, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + id).classList.add('active');
    btn.classList.add('active');
}

// ── Força da senha ────────────────────────────────────────────────────────
function checkStrength(val) {
    const fill  = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    let score = 0;
    if (val.length >= 8)  score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        { w: '0%',   c: 'transparent', t: 'Digite a nova senha' },
        { w: '25%',  c: '#ef4444',     t: 'Fraca' },
        { w: '50%',  c: '#f59e0b',     t: 'Razoável' },
        { w: '75%',  c: '#3b82f6',     t: 'Boa' },
        { w: '100%', c: '#22c55e',     t: 'Forte' },
    ];
    const l = levels[val.length === 0 ? 0 : score] || levels[1];
    fill.style.width      = l.w;
    fill.style.background = l.c;
    label.textContent     = l.t;
    label.style.color     = l.c === 'transparent' ? 'var(--muted)' : l.c;
}

// ── Ativa a aba correta se voltou com erro de senha ───────────────────────
@if(session('tab') === 'password' || $errors->has('current_password') || $errors->has('password'))
    document.addEventListener('DOMContentLoaded', () => {
        switchTab('password', document.querySelectorAll('.tab-btn')[1]);
    });
@endif
</script>
@endpush