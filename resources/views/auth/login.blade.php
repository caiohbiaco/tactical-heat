{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('title', 'Entrar — Tactical Heat')

@push('styles')
<style>
.auth-wrap {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.auth-card {
    width: 100%;
    max-width: 400px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 2.5rem;
}
.auth-logo {
    text-align: center;
    margin-bottom: 2rem;
}
.auth-title {
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 4px;
}
.auth-sub {
    font-size: 14px;
    color: var(--muted);
}
.auth-divider {
    text-align: center;
    margin: 1.25rem 0;
    font-size: 13px;
    color: var(--subtle);
    position: relative;
}
.auth-divider::before, .auth-divider::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 42%;
    height: 1px;
    background: var(--border);
}
.auth-divider::before { left: 0; }
.auth-divider::after  { right: 0; }
.input-error {
    font-size: 12px;
    color: #f87171;
    margin-top: 4px;
}
.btn-full { width: 100%; justify-content: center; }

/* Centralização total na página de auth */
.auth-page .app-shell {
    padding-left: 0 !important;
}
.auth-page .main-wrap {
    padding: 0;
    max-width: 100%;
    height: calc(100vh - 56px);
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo">
            <span style="font-family: var(--font-display); font-size: 1.1rem; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <span class="brand-dot"></span>
                Tactical Heat
            </span>
        </div>

        <div class="mb-2" style="text-align: center;">
            <p class="auth-title">Bem-vindo de volta</p>
            <p style="font-size:14px; color:var(--muted);">Faça login para acessar seus relatórios</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    placeholder="seu@email.com" required autofocus>
                @error('email') <p class="input-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="password" placeholder="••••••••" required>
                @error('password') <p class="input-error">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between mb-2" style="font-size:13px;">
                <label style="display:flex; align-items:center; gap:6px; color:var(--muted); margin:0; font-size:13px; cursor:pointer;">
                    <input type="checkbox" name="remember" style="width:auto; accent-color: var(--accent);">
                    Lembrar de mim
                </label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="color:var(--accent); text-decoration:none; font-size:13px;">Esqueci a senha</a>
                @endif
            </div>

            <button type="submit" class="btn btn-primary btn-full">Entrar</button>
        </form>

        <div class="auth-divider">ou</div>

        <p style="text-align:center; font-size:14px; color:var(--muted);">
            Não tem conta?
            <a href="{{ route('register') }}" style="color:var(--accent); text-decoration:none;">Cadastre-se</a>
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.body.classList.add('auth-page');
    document.body.classList.remove('sidebar-pinned');
    localStorage.removeItem('th_sidebar_pinned');
    document.getElementById('sidebar')?.remove();
    document.getElementById('sidebarTrigger')?.remove();
    document.querySelector('.sidebar-toggle-btn')?.remove();
</script>
@endpush