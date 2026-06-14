@extends('layouts.app')

@section('title', 'Recuperar Senha — Tactical Heat')

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
    max-width: 400px;
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
    text-align: center;
}
.input-error { font-size: 12px; color: #f87171; margin-top: 4px; }
.btn-full { width: 100%; justify-content: center; }
</style>
@endpush

@section('content')
<div class="auth-wrap">
    <div class="auth-card">

        <div class="mb-2" style="text-align: center;">
            <p class="auth-title">Recuperar senha</p>
            <p style="font-size:14px; color:var(--muted); margin-top: 4px;">
                Informe seu e-mail e enviaremos um link para redefinir sua senha.
            </p>
        </div>

        {{-- Mensagem de sucesso após envio --}}
        @if(session('status'))
            <div class="alert alert-success" style="margin-bottom: 1.25rem;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    placeholder="seu@email.com" required autofocus>
                @error('email') <p class="input-error">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-full">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,12 2,6"/>
                </svg>
                Enviar link de recuperação
            </button>
        </form>

        <p style="text-align:center; font-size:14px; color:var(--muted); margin-top:1.25rem;">
            Lembrou a senha?
            <a href="{{ route('login') }}" style="color:var(--accent); text-decoration:none;">Voltar ao login</a>
        </p>

    </div>
</div>
@endsection