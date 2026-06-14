{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@section('title', 'Criar Conta — Tactical Heat')

@push('styles')
<style>
.auth-wrap {
    min-height: calc(100vh - 60px);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: -2.5rem;
}
.auth-card {
    width: 100%;
    max-width: 420px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 2.5rem;
}
.auth-title {
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 4px;
}
.input-error { font-size: 12px; color: #f87171; margin-top: 4px; }
.btn-full { width: 100%; justify-content: center; }
.row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="mb-2" style="text-align: center;">
            <p class="auth-title">Criar conta</p>
            <p style="font-size:14px; color:var(--muted);">Analise riscos climáticos para seus eventos esportivos</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label>Nome completo</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="João Silva" required autofocus>
                @error('name') <p class="input-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    placeholder="seu@email.com" required>
                @error('email') <p class="input-error">{{ $message }}</p> @enderror
            </div>

            <div class="row-2">
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="password" placeholder="Mín. 8 caracteres" required>
                    @error('password') <p class="input-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label>Confirmar senha</label>
                    <input type="password" name="password_confirmation" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Criar conta</button>
        </form>

        <p style="text-align:center; font-size:14px; color:var(--muted); margin-top:1.25rem;">
            Já tem conta?
            <a href="{{ route('login') }}" style="color:var(--accent); text-decoration:none;">Entrar</a>
        </p>
    </div>
</div>
@endsection