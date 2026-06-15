<<<<<<< HEAD
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
=======
# Tactical Heat

**Sistema de Análise de Riscos Climáticos para Eventos Esportivos**

Aplicação web desenvolvida em Laravel que cruza dados climáticos históricos com limiares de risco por modalidade esportiva, gerando relatórios textuais via Inteligência Artificial (Google Gemini).

---

## Pré-requisitos

- PHP 8.2 ou superior
- Composer
- MySQL (via XAMPP ou instalação local)
- Node.js (opcional, apenas se querer recompilar assets)
- Chave de API do Google Gemini (gratuita em https://aistudio.google.com)

---

## Instalação

### 1. Clonar o repositório

```bash
git clone https://github.com/caiohbiaco/tactical-heat
cd tactical-heat
```

### 2. Instalar dependências PHP

```bash
composer install
```

### 3. Configurar o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Editar o `.env`

Abra o arquivo `.env` e configure:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tactical_heat
DB_USERNAME=root
DB_PASSWORD=

GEMINI_API_KEY=SUA_CHAVE_AQUI
```

### 5. Criar o banco de dados

No phpMyAdmin (XAMPP) ou MySQL, crie um banco chamado `tactical_heat`.

### 6. Rodar as migrations e seeders

```bash
php artisan migrate --seed
```

Isso criará todas as tabelas e populará os esportes automaticamente.

### 7. Instalar o DomPDF (geração de PDF)

```bash
composer require barryvdh/laravel-dompdf
```

### 8. Iniciar o servidor

```bash
php artisan serve
```

Acesse: http://127.0.0.1:8000

---

## Estrutura do banco de dados

| Tabela | Descrição |
|--------|-----------|
| `users` | Usuários cadastrados |
| `sports` | Esportes e seus limiares de risco |
| `searches` | Buscas realizadas por cada usuário |
| `climate_data` | Dados climáticos mensais por busca |
| `reports` | Relatórios gerados pela IA |
| `sessions` | Sessões de autenticação |
| `cache` | Cache interno do Laravel |
| `jobs` | Fila de jobs assíncronos |

---

## APIs utilizadas

| API | Finalidade | Autenticação |
|-----|-----------|--------------|
| Open-Meteo Geocoding | Converter nome de cidade em coordenadas | Gratuita, sem chave |
| Open-Meteo Archive | Dados históricos (1940–ano anterior) | Gratuita, sem chave |
| Open-Meteo Forecast | Previsão para os próximos 16 dias (ano atual) | Gratuita, sem chave |
| Google Gemini | Geração de relatório textual por IA | Chave gratuita obrigatória |

---

## Funcionalidades

- Cadastro e autenticação de usuários
- Busca de dados climáticos históricos por cidade, esporte e ano
- Suporte ao ano atual (dados reais + previsão + média histórica)
- Gráficos interativos de temperatura, umidade e heat index
- Mapa interativo com localização da cidade (Leaflet.js)
- Painel de risco mensal com semáforo (baixo/médio/alto)
- Comparação climática entre duas cidades
- Geração de relatório de análise por IA (Google Gemini)
- Exportação do relatório em PDF
- Dashboard com estatísticas do usuário
- Barra lateral retrátil com histórico de análises recentes

---

## Tecnologias

- **Backend:** Laravel 11, PHP 8.5
- **Banco:** MySQL
- **Frontend:** Blade, CSS puro, Chart.js, Leaflet.js
- **IA:** Google Gemini API
- **PDF:** barryvdh/laravel-dompdf
- **Autenticação:** Laravel Breeze

---
>>>>>>> 1d4ede8fada069c2e7fd365ef2cd947daf1ab397
