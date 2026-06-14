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
