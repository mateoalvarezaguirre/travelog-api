<p align="center">
  <strong>Travelog API</strong>
</p>
<p align="center">
  <em>A travel journal platform backend — Laravel 12 · Hexagonal · DDD</em>
</p>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel" alt="Laravel 12"></a>
  <a href="https://www.php.net"><img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php" alt="PHP 8.2+"></a>
  <a href="https://www.postgresql.org"><img src="https://img.shields.io/badge/PostgreSQL-15-336791?style=flat-square&logo=postgresql" alt="PostgreSQL"></a>
  <a href="https://redis.io"><img src="https://img.shields.io/badge/Redis-7-DC382D?style=flat-square&logo=redis" alt="Redis"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/License-MIT-yellow?style=flat-square" alt="MIT"></a>
</p>

---

## 📖 About

**Travelog API** is the backend for a **travel journal platform**. Users create trips (journals), add locations, media, and routes, follow other users, and like or comment on trips. It serves a **Next.js frontend** via a **JSON REST API** and is built with **Laravel 12**, following **Hexagonal Architecture** and **DDD** (Domain-Driven Design) with bounded contexts.

| Feature | Description |
|--------|-------------|
| **Journals (Trips)** | Create, update, delete, list; draft/published/archived; public/private/unlisted |
| **Locations & media** | Attach locations, images, routes, waypoints to trips |
| **Social** | Follow/unfollow users, like/unlike trips, add/list comments |
| **Profile** | View and update profile, stats, usernames |
| **Places** | User-defined places with coordinates and marker types |
| **Search** | Full-text search for journals, places, and users |
| **Auth** | Email/password + Google OAuth (Sanctum) |

---

## 🏗️ Architecture

The codebase is split into **Laravel app** and **domain layer**:

```
├── app/                    # Laravel (Infrastructure)
│   ├── Models/             # Eloquent models (thin, DB mapping)
│   ├── Http/               # Middleware, base controller
│   ├── Policies/           # Authorization
│   └── Providers/
│
├── src/                    # Domain (DDD, Src\ namespace)
│   ├── Auth/               # Authentication, registration, Google OAuth
│   ├── Trip/               # Journals (entities, status, visibility, engagement)
│   ├── Profile/            # User profiles, stats
│   ├── Social/             # Follows, likes, comments
│   ├── Place/              # User places
│   ├── Search/             # Journals, places, users search
│   └── Shared/             # Core value objects, pagination, exceptions
│
└── routes/api/             # Auth, journals, profile, social, places, search
```

Each bounded context under `src/` follows **Hexagonal** layering:

- **Domain** — Entities, Value Objects, Enums, Repository interfaces, Domain exceptions  
- **Application** — Use cases, DTOs (in/out)  
- **Infrastructure** — Eloquent repositories, HTTP controllers, requests, resources  

Domain and application layers are **framework-agnostic**; Laravel lives in Infrastructure. Repositories return **domain types only** (no Eloquent models or primitives). See **AGENTS.md** for full coding and architecture rules.

---

## 🛠️ Tech stack

| Layer | Technology |
|-------|------------|
| **Runtime** | PHP 8.2+, Laravel 12 |
| **API** | REST JSON, Laravel Sanctum (Bearer tokens) |
| **Database** | PostgreSQL (prod/Docker), SQLite in-memory (tests) |
| **Queue / cache** | Redis (prod), database (local fallback) |
| **Server** | Laravel Octane (OpenSwoole) in Docker, Nginx reverse proxy |
| **Queue dashboard** | Laravel Horizon |
| **Debug** | Laravel Telescope (disabled in tests) |
| **Quality** | PHPStan 8 (Larastan), php-cs-fixer (PSR-12 + PhpCsFixer) |

---

## 📋 Prerequisites

- **PHP 8.2+** (extensions: `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `pcntl`, `redis` for Horizon)
- **Composer**
- **Node.js** (for Vite, optional for API-only)
- **Docker & Docker Compose** (for full stack: app, Nginx, DB, Redis, Horizon, scheduler)

---

## 🚀 Getting started

### 1. Clone and install

```bash
git clone <repository-url>
cd travelog-api
cp .env.example .env
php artisan key:generate
composer install
```

### 2. Environment

Edit `.env`:

- **Local (no Docker):** `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:` or `database/database.sqlite`  
- **Docker:** Use `DB_CONNECTION=pgsql` (or `mysql`) and set `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` to match `docker-compose` (e.g. `postgres` / `db` service).  
- Optional: `QUEUE_CONNECTION=redis`, `REDIS_HOST=127.0.0.1` (or `redis` in Docker).  
- Optional: `TELESCOPE_ENABLED=true` for debug UI.

### 3. Database

```bash
php artisan migrate
# Optional:
php artisan db:seed
```

### 4. Run the app

**Option A — Local (single machine)**  
Starts HTTP server, queue worker, log tail, and Vite:

```bash
composer dev
```

API: `http://localhost:8000` (or port shown by `php artisan serve`).

**Option B — Docker (full stack)**  
Starts app (Octane), Nginx, Redis, Horizon, scheduler, and DB (MariaDB + PostgreSQL; use one):

```bash
docker compose up -d
```

API: **http://localhost:8080** (Nginx).  
First run: run migrations (and optionally seed) inside the `app` container.

---

## 📡 API overview

- **Base URL:** e.g. `http://localhost:8000/api` or `http://localhost:8080/api`
- **Content type:** `application/json` (request and response)
- **Auth:** `Authorization: Bearer <token>` for protected routes
- **Response keys:** **camelCase** (not snake_case)
- **Pagination:** `{ "data": [...], "meta": { "currentPage", "lastPage", "perPage", "total" } }`

| Area | Endpoints (examples) |
|------|----------------------|
| **Health** | `GET /api/health` |
| **Auth** | `POST /auth/login`, `POST /auth/register`, `POST /auth/google`, `POST /auth/forgot-password`, `GET /auth/me`, `POST /auth/logout` |
| **Journals** | `GET/POST /journals`, `GET/PUT/DELETE /journals/:id`, `GET /journals/public` |
| **Social** | `POST/DELETE /journals/:id/like`, `POST /journals/:id/comments`, `GET /journals/:id/comments`, `POST/DELETE /profile/:id/follow` |
| **Profile** | `GET /profile/me`, `GET/PUT /profile/:username`, `GET /profile/:id/stats` |
| **Places** | `GET/POST /places`, `DELETE /places/:id` |
| **Search** | `GET /search/journals`, `GET /search/places`, `GET /search/users` |

Full request/response shapes and status codes: **backend-api-contract.md**.

---

## 🧪 Testing

- **Suites:** `Unit` (`tests/Unit`), `Feature` (`tests/Feature`)  
- **DB:** SQLite in-memory (`phpunit.xml`: `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`)
- **Grouping:** By bounded context, e.g. `tests/Feature/{Auth,Trip,Profile,Social,Place,Shared}`, `tests/Unit/...`

```bash
composer test                    # Clear config cache + run full suite
composer test:coverage           # With coverage (needs PCOV or Xdebug)
php artisan test                 # Run tests directly
php artisan test --filter=ClassName
php artisan test --filter=test_method_name
```

Coverage target: **90%+**.

---

## 🔍 Code quality

| Command | Description |
|--------|-------------|
| `composer format` | php-cs-fixer on **changed** files |
| `composer format:all` | php-cs-fixer on **entire** project |
| `composer analyse` | PHPStan (Larastan) on **changed** files |
| `composer analyse:all` | PHPStan **full** project (level 8) |
| `composer clean-code` | format + analyse (changed) |
| `composer clean-code:all` | format + analyse (all) |
| `composer pipeline` | format + analyse + test |

- **PHPStan:** level 8, config in `phpstan.neon`  
- **php-cs-fixer:** PSR-12, `@PhpCsFixer`, `@PHP81Migration`; test methods in `snake_case`

---

## 📁 Key files

| File | Purpose |
|------|--------|
| **AGENTS.md** | Mandatory coding & code-review rules (Hexagonal, DDD, repositories, no primitives in core, etc.) |
| **CLAUDE.md** | High-level project overview and commands for AI assistants |
| **backend-api-contract.md** | Full API contract for the frontend (request/response shapes, status codes) |
| **phpstan.neon** | PHPStan configuration |
| **.php-cs-fixer.php** | Code style configuration |
| **docker-compose.yaml** | App, Nginx, Redis, Horizon, scheduler, MariaDB, PostgreSQL |

---

## 📜 License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
