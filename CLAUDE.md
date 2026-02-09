# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Travelog API — a travel journal platform backend built with Laravel 12, PHP 8.2+. Serves a Next.js frontend via a JSON REST API. Users create trips (travel journals), add locations/media/routes, follow other users, like and comment on trips.

## Commands

### Development
```bash
docker compose up -d          # Start all services (app, nginx, postgres, redis, horizon, scheduler)
composer dev                  # Run app server, queue worker, log tail, and vite concurrently (local)
```

### Testing
```bash
composer test                 # Clear config cache then run full test suite
php artisan test              # Run tests directly
php artisan test --filter=ClassName  # Run a single test class
php artisan test --filter=test_method_name  # Run a single test method
```
Tests use SQLite in-memory (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:` set in phpunit.xml). Test suites: `Unit` (tests/Unit) and `Feature` (tests/Feature).

### Static Analysis & Formatting
```bash
composer analyse              # PHPStan on changed files only
composer analyse:all          # PHPStan on entire project (level 8, via larastan)
composer format               # php-cs-fixer on changed files only
composer format:all           # php-cs-fixer on entire project
composer clean-code           # format + analyse (changed files)
composer clean-code:all       # format + analyse (all files)
composer pipeline             # format + analyse + test
```

## Architecture

### Two-Layer Structure: `app/` (Laravel) + `src/` (Domain)

- **`app/`** — Standard Laravel: Models (Eloquent), Controllers, Providers, Middleware. Models are thin (e.g., `App\Models\Trip`, `App\Models\User`).
- **`src/`** — Domain layer with DDD-style organization, autoloaded under the `Src\` namespace. Currently has `src/Trip/Domain/` with Entities, Enums, and ValueObjects.

The `src/` namespace is registered in `composer.json` as `"Src\\": "src/"`.

### Domain Structure (src/)

Organized by bounded context. Current context: `Trip`.

```
src/Trip/Domain/
├── Entities/      # TripEntity, TripLocation (rich domain objects, not Eloquent)
├── Enums/         # StatusEnum (draft/published/archived), VisibilityEnum (public/private/unlisted)
└── ValueObjects/  # Engagement (likes/comments/shares/views), Owner (user summary)
```

Domain entities use UUIDs (trips table PK is UUID). Domain objects are separate from Eloquent models — the Trip Eloquent model (`app/Models/Trip`) maps to the DB while `Src\Trip\Domain\Entities\TripEntity` represents the domain concept.

### Database

PostgreSQL in production (via Docker). Key tables: `users`, `trips` (UUID PK), `trip_locations`, `trip_comments`, `trip_media`, `trip_routes`, `trip_waypoints`, `user_biographies`. Factories exist for all models.

### Infrastructure

- **Laravel Octane** (OpenSwoole) — serves the app in Docker
- **Nginx** — reverse proxy on port 8080
- **Redis** — queue backend and cache
- **Laravel Horizon** — queue dashboard/management
- **Laravel Telescope** — debug dashboard (disabled in tests)

### API Contract

`backend-api-contract.md` defines the full API contract the frontend expects. All API responses must use **camelCase** keys (not Laravel's default snake_case). Pagination follows `{ data: [...], meta: { currentPage, lastPage, perPage, total } }`.

## Code Style

- **PHPStan level 8** with larastan. Config in `phpstan.neon`. Analyses `app/`, `src/`, `config/`, `database/migrations/`, `routes/`, `tests/`.
- **php-cs-fixer** with PSR-12 base + `@PhpCsFixer` + `@PHP81Migration`. Config in `.php-cs-fixer.php`. Key rules: single quotes, `snake_case` test method names (`php_unit_method_casing`), aligned binary operators, no yoda conditions, trailing commas in multiline.
- Test method names must be `snake_case` (enforced by php-cs-fixer).
