# MVP Plan — Travelog API (Revised)

Steps to complete the backend MVP per `backend-api-contract.md`.

Legend: `[x]` done, `[ ]` pending.

---

## Current status (as of plan review)

**Phase:** Phases 1–7 and 8 (Search) are implemented. Phase 9 (cross-cutting) partially done: CORS, throttle, policies in place. Remaining: Phase 2 (some model/factory details), Phase 9.2 (error response format), Phase 9.5 (PHPStan rule), Phase 10 (caching, optional auth middleware), Phase 11 (broader test coverage).

**Recently completed:** Places API routes (`routes/api/places.php`), Search context (SearchRepository, use cases, controllers, `routes/api/search.php`, SearchServiceProvider), OptionalSanctumMiddleware for search, throttle on auth (5/min) and social (30/min), TripPolicy and PlacePolicy, `config/cors.php`.

---

## Architecture & Design Decisions

### Bounded-Context DDD Structure

Every feature is organized by bounded context following the established pattern:

```
src/{Context}/
├── Domain/
│   ├── Entities/           # Rich domain objects (not Eloquent)
│   ├── ValueObjects/       # Immutable value types
│   ├── Enums/              # Domain enums
│   ├── Repositories/       # Repository interfaces
│   ├── Contracts/          # Interfaces for external services
│   └── Exceptions/         # Domain-specific exceptions
├── Application/
│   ├── UseCases/           # One class per use case (single responsibility)
│   └── DTOs/
│       ├── In/             # Input DTOs (from request to use case)
│       └── Out/            # Output DTOs (from use case to resource)
└── Infrastructure/
    ├── Http/
    │   ├── Controllers/    # Invokable or thin controllers
    │   ├── Requests/       # Form requests extending BaseFormRequest
    │   └── Resources/      # JSON-serializable response shapers
    ├── Database/
    │   ├── Repositories/   # Eloquent implementations of domain interfaces
    │   └── Mappers/        # Eloquent model ↔ domain entity mapping
    └── Observers/          # Eloquent model observers (counter caches, side effects)
```

Each context gets a service provider in `app/Providers/Contexts/` to bind interfaces to implementations.

Eloquent models remain in `app/Models/` as shared infrastructure — each context accesses them through its own repository.

### Bounded Contexts

| Context     | Responsibility                                              |
|-------------|-------------------------------------------------------------|
| **Auth**    | Login, register, Google OAuth, logout, password reset       |
| **Profile** | View/update profile, user stats, user lookup by username    |
| **Trip**    | Trip CRUD, media registration, tags                         |
| **Social**  | Likes, comments, follows                                    |
| **Place**   | Map places CRUD                                             |
| **Search**  | Cross-context search (journals, users, places)              |
| **Shared**  | Value objects, base exceptions, base request, utilities     |

### Schema Decisions (contract vs existing codebase)

| Conflict | Decision | Rationale |
|----------|----------|-----------|
| Trip PK: contract says `bigint`, codebase uses `UUID` | **Keep UUID** | Better for distributed systems, already in migrations, more DDD-aligned |
| User FK: contract says `user_id`, codebase uses `owner_id` | **Keep `owner_id`** | More domain-expressive; API resource maps `owner` → `author` for the contract |
| Public flag: contract says `is_public` boolean, codebase uses `visibility` enum | **Keep `visibility` enum** | More extensible (supports `unlisted`); API resource derives `isPublic` from it |
| Bio: contract puts `bio` on users table, codebase has `user_biographies` table | **Keep `user_biographies`** | Already exists, respects SRP, allows content to grow independently |
| Images table: contract says `journal_images`, codebase has `trip_media` | **Keep `trip_media`** | Already exists with richer schema (`media_type`, `is_featured`, etc.) |
| Comments table: contract says `comments`, codebase has `trip_comments` (UUID PK) | **Keep `trip_comments`** | Already exists; UUID PK is consistent with other trip sub-entities |
| Internal-only fields: `private_content`, `published_at`, `archived` status, `unlisted` visibility | **Keep but don't expose** | Exist in schema/enums for future use; API resources only output contract-defined fields |

---

## Phase 1: Foundation

### 1.1 Sanctum & API routing
- [x] `composer require laravel/sanctum`, publish config, migration exists
- [x] `HasApiTokens` trait on `User` model
- [x] `routes/api.php` created and registered in `bootstrap/app.php`

### 1.2 Migrations — all exist
- [x] **users**: profile columns added (`username`, `avatar`, `cover_photo`, `location`, `google_id`)
- [x] **trips**: base table with UUID PK, `owner_id`, `status`, `visibility`, `likes_count`, `comments_count`
- [x] **trips**: journal columns added (`date`, `location`, `latitude`, `longitude`)
- [x] **trip_comments**: `user_id` and `text` columns added (UUID PK)
- [x] **trip_media**: `media_type`, `media_url`, `caption`, `order`, `is_featured`, `is_visible`, `uploaded_by`
- [x] **trip_locations**, **trip_routes**, **trip_waypoints**: all created
- [x] **tags** + **trip_tag** pivot
- [x] **likes**: composite unique on (`trip_id`, `user_id`)
- [x] **follows**: composite unique on (`follower_id`, `following_id`)
- [x] **places**: `user_id`, `name`, `country`, `date`, `latitude`, `longitude`, `marker_type`, `image`
- [x] **user_biographies**: `user_id`, `content`
- [x] **countries**: ISO country data (`country_name`, `alpha2_code`, `alpha3_code`)
- [x] **personal_access_tokens** (Sanctum)

### 1.3 New migration — performance columns
- [x] **trips**: add `excerpt` column (string, nullable) — stored on save for read performance, avoids stripping HTML on every list request

### 1.4 New migration — database indexes
- [x] `trips`: index on `(owner_id, status)`, index on `(visibility, status, created_at)` for public feed
- [x] `likes`: index on `user_id` (composite unique already covers `trip_id` + `user_id`)
- [x] `follows`: index on `following_id` (composite unique already covers `follower_id` + `following_id`)
- [x] `places`: index on `(user_id, marker_type)`
- [x] `trip_media`: index on `(trip_id, order)`
- [x] `tags`: index on `name` (unique already covers this — verify)
- [x] `trip_tag`: index on `tag_id`

### 1.5 Middleware registration
- [x] `CamelCaseMiddleware` created in `app/Http/Middlewares/`
- [x] `ForceJsonResponseMiddleware` created in `app/Http/Middlewares/`
- [x] Register both middleware globally for `api` route group in `bootstrap/app.php`

### 1.6 Duplicate route cleanup
- [x] Remove duplicate `GET /auth/me` from `routes/api/profile.php` — Auth context owns this route; Profile context provides `GET /profile` separately

---

## Phase 2: Eloquent Models

All model files exist as empty stubs. Configure them for use across contexts.

### 2.1 User model
- [x] `fillable`: name, email, password, username, avatar, cover_photo, location, google_id
- [x] `hidden`: password, remember_token
- [x] `casts`: email_verified_at → datetime, password → hashed
- [ ] Relations: `trips()` hasMany, `comments()` hasMany TripComment, `likes()` hasMany Like, `places()` hasMany Place, `followers()` belongsToMany (via follows, FK following_id → follower_id), `following()` belongsToMany (via follows, FK follower_id → following_id), `biography()` hasOne UserBiography

### 2.2 Trip model
- [ ] Set `$keyType = 'string'`, `$incrementing = false` (UUID PK)
- [ ] `fillable`: title, content, private_content, owner_id, status, visibility, published_at, date, location, latitude, longitude, excerpt
- [ ] `casts`: status → StatusEnum, visibility → VisibilityEnum, published_at → datetime, date → date, latitude → float, longitude → float
- [ ] Relations: `owner()` belongsTo User, `comments()` hasMany TripComment, `media()` hasMany TripMedia, `tags()` belongsToMany Tag (via trip_tag), `likes()` hasMany Like, `locations()` hasMany TripLocation, `waypoints()` hasMany TripWaypoint, `route()` hasOne TripRoute
- [ ] Scope: `scopePublished()` where status = published, `scopePublic()` where visibility = public

### 2.3 TripComment model
- [ ] Set `$keyType = 'string'`, `$incrementing = false` (UUID PK)
- [ ] `fillable`: trip_id, user_id, text
- [ ] Relations: `trip()` belongsTo Trip, `user()` belongsTo User

### 2.4 TripMedia model
- [ ] `fillable`: trip_id, media_type, media_url, caption, order, is_featured, is_visible, uploaded_by
- [ ] `casts`: is_featured → boolean, is_visible → boolean, order → integer
- [ ] Relations: `trip()` belongsTo Trip, `uploader()` belongsTo User (FK uploaded_by)

### 2.5 Tag model (new file)
- [ ] `fillable`: name
- [ ] Relations: `trips()` belongsToMany Trip (via trip_tag)

### 2.6 Like model (new file)
- [ ] `fillable`: trip_id, user_id
- [ ] Set `$timestamps = false`, add `$dates = ['created_at']`
- [ ] Relations: `trip()` belongsTo Trip, `user()` belongsTo User

### 2.7 Follow model (new file)
- [ ] `fillable`: follower_id, following_id
- [ ] Set `$timestamps = false`, add `$dates = ['created_at']`
- [ ] Relations: `follower()` belongsTo User, `following()` belongsTo User

### 2.8 Place model (new file)
- [ ] `fillable`: user_id, name, country, date, latitude, longitude, marker_type, image
- [ ] `casts`: date → date, latitude → float, longitude → float, marker_type → MarkerType (new enum in Place context)
- [ ] Relations: `user()` belongsTo User

### 2.9 Other existing models
- [ ] **UserBiography**: `fillable` (user_id, content), relation `user()` belongsTo User
- [ ] **TripLocation**: UUID config, `fillable`, relation `trip()` belongsTo Trip
- [ ] **TripWaypoint**: UUID config, `fillable`, relation `trip()` belongsTo Trip
- [ ] **TripRoute**: `$primaryKey = 'trip_id'`, `$incrementing = false`, `fillable`, relation `trip()` belongsTo Trip

### 2.10 Factories
- [x] **UserFactory**: fully implemented
- [ ] **TripFactory**: fill with realistic data (title, content, date, location, coords, status, visibility)
- [ ] **TripCommentFactory**: fill (text), relate to trip + user
- [ ] **TripMediaFactory**: fill (media_type=image, media_url, caption, order)
- [ ] **TagFactory**: fill (name)
- [ ] **LikeFactory** (new): relate to trip + user
- [ ] **FollowFactory** (new): relate to follower + following
- [ ] **PlaceFactory** (new): fill (name, country, coords, marker_type)
- [ ] **UserBiographyFactory**: fill (content)

---

## Phase 3: Auth Context (extend existing)

Existing: login, register, Google OAuth, forgot-password, `/auth/me`. Need: logout, password reset completion, fix response shape.

### 3.1 Fix AuthUserResource response
- [x] Add `id` to user object in response — contract requires it, currently missing

### 3.2 Logout endpoint
- [x] Create `LogoutUseCase` in `src/Auth/Application/UseCases/`
- [x] Create `LogoutController` (invokable) in `src/Auth/Infrastructure/Http/Controllers/`
- [x] Route: `POST /api/auth/logout` (auth:sanctum) — revoke current token
- [x] Response: `204 No Content`

### 3.3 Password reset completion
- [x] Create `ResetPasswordUseCase` in `src/Auth/Application/UseCases/`
- [x] Create `ResetPasswordRequest` (email, token, password, password_confirmation)
- [x] Create `ResetPasswordController` (invokable) in `src/Auth/Infrastructure/Http/Controllers/`
- [x] Route: `POST /api/auth/reset-password`
- [x] Response: `200 { message }` on success

### 3.4 Routes file
- [x] `POST /auth/login` → LoginController
- [x] `POST /auth/register` → RegisterController
- [x] `POST /auth/google` → GoogleAuthController
- [x] `POST /auth/forgot-password` → AuthController@forgotPassword
- [x] `GET  /auth/me` → AuthController@me (auth:sanctum)
- [x] `POST /auth/logout` → LogoutController (auth:sanctum)
- [x] `POST /auth/reset-password` → ResetPasswordController

---

## Phase 4: Trip Context

New context infrastructure. The domain layer partially exists (`TripEntity`, `StatusEnum`, `VisibilityEnum`, `Engagement`, `Owner`). Needs application + infrastructure layers.

### 4.1 Domain layer (extend)
- [x] `TripRepository` interface in `src/Trip/Domain/Repositories/` — `findById(string $id)`, `findByOwner(int $ownerId, TripFilters $filters)`, `findPublic(PublicTripFilters $filters)`, `save(TripEntity $trip)`, `delete(string $id)`
- [x] `TripFilters` value object — page, search, tag, status, tab
- [x] `PublicTripFilters` value object — page, search, tag, destination, tab
- [x] Extend `TripEntity` with: `date`, `location`, `coordinates` (Coordinates VO from Shared), `excerpt`, `tags` (array of strings), `media` (array)
- [x] `TripNotFoundException` extending BaseException (404)
- [x] `UnauthorizedTripActionException` extending BaseException (403)

### 4.2 Application layer
- [x] **DTOs (In)**: `CreateTripDTO`, `UpdateTripDTO`, `GetTripDTO`, `ListTripsDTO`, `ListPublicTripsDTO`
- [x] **DTOs (Out)**: `TripDTO`, `TripSummaryDTO` (for lists — without full content)
- [x] **Use cases**: `CreateTripUseCase`, `UpdateTripUseCase`, `DeleteTripUseCase`, `GetTripUseCase`, `ListTripsUseCase`, `ListPublicTripsUseCase`
- [x] `CreateTripUseCase` must: generate excerpt on save, attach tags (find-or-create), link media by IDs, set `published_at` when status = published

### 4.3 Infrastructure layer
- [x] **Repository**: `TripEloquentRepository` implements `TripRepository`
- [x] **Mapper**: `TripMapper` — Eloquent Trip model ↔ TripEntity
- [x] **Controllers** (invokable, one per action):
  - `ListTripsController` — `GET /api/journals` (auth)
  - `ListPublicTripsController` — `GET /api/journals/public` (no auth, optional auth for `isLiked`)
  - `GetTripController` — `GET /api/journals/{id}` (auth)
  - `CreateTripController` — `POST /api/journals` (auth)
  - `UpdateTripController` — `PUT /api/journals/{id}` (auth, owner only)
  - `DeleteTripController` — `DELETE /api/journals/{id}` (auth, owner only)
- [x] **Requests**: `StoreTripRequest`, `UpdateTripRequest` (extends BaseFormRequest)
- [x] **Resources**: `TripResource`, `TripCollectionResource`, `TripImageResource`

### 4.4 Media registration sub-flow
- [x] `POST /api/media` (auth) — register a Cloudinary URL, returns `{ id, url }`
- [x] The frontend uploads directly to Cloudinary, then calls this endpoint to register the URL in `trip_media`
- [x] `RegisterMediaController`, `RegisterMediaRequest`, `RegisterMediaUseCase`
- [x] The returned `id` is what the frontend sends as `imageIds` when creating/updating a trip

### 4.5 Excerpt generation
- [x] Domain service or method on `TripEntity`: strip HTML tags, collapse whitespace, truncate to 200 chars with word boundary
- [x] Store in `excerpt` column on save (via use case) — avoids computing on every read
- [x] Recalculate when `content` changes on update

### 4.6 Trip tab/filter query logic

Define how each tab translates to a database query:

**`GET /journals` (auth user's trips):**
| Tab | Query |
|-----|-------|
| `recent` (default) | `WHERE owner_id = auth` ORDER BY `created_at DESC` |
| `favorites` | `WHERE id IN (SELECT trip_id FROM likes WHERE user_id = auth)` ORDER BY `likes.created_at DESC` |
| `shared` | `WHERE id IN (SELECT trip_id FROM trip_comments WHERE user_id = auth AND owner_id != auth)` — trips the user engaged with |

**`GET /journals/public`:**
| Tab | Query |
|-----|-------|
| `recent` (default) | `WHERE visibility = public AND status = published` ORDER BY `created_at DESC` |
| `featured` | Same base + `WHERE likes_count >= 10` ORDER BY `likes_count DESC` — threshold configurable |
| `trending` | Same base + order by likes received in last 7 days (subquery on `likes.created_at`) |
| `following` | Same base + `WHERE owner_id IN (SELECT following_id FROM follows WHERE follower_id = auth)` — requires auth |

**`destination` filter**: map region string to country codes using the `countries` table + a static continent→alpha2 mapping. Filter trips via `JOIN trip_locations` or by `trips.location ILIKE '%country%'` for MVP.

### 4.7 Service provider
- [x] Create `app/Providers/Contexts/TripServiceProvider.php`
- [x] Bind `TripRepository` interface → `TripEloquentRepository`
- [x] Register in `bootstrap/providers.php`

### 4.8 Routes
- [x] Create `routes/api/journals.php`, require from `routes/api.php`
- [x] All routes under prefix `journals`

### 4.9 Performance: eager loading
- [x] List queries: eager load `owner`, `tags`, `media` (limit to visible, ordered)
- [x] Detail query: eager load `owner`, `tags`, `media`, `comments.user`
- [x] Use `withCount('likes', 'comments')` only as fallback — prefer counter cache columns

---

## Phase 5: Social Context

New context for likes, comments, and follows.

### 5.1 Domain layer
- [x] `LikeRepository` interface: `toggle(string $tripId, int $userId): bool`, `exists(string $tripId, int $userId): bool`
- [x] `CommentRepository` interface: `findByTrip(string $tripId): array`, `save(CommentEntity): void`
- [x] `FollowRepository` interface: `follow(int $followerId, int $followingId): void`, `unfollow(...)`, `isFollowing(...): bool`
- [x] `CommentEntity` in `src/Social/Domain/Entities/`
- [x] `AlreadyLikedException`, `NotLikedException`, `CannotFollowSelfException` exceptions

### 5.2 Application layer
- [x] **Use cases**: `LikeTripUseCase`, `UnlikeTripUseCase`, `AddCommentUseCase`, `ListCommentsUseCase`, `FollowUserUseCase`, `UnfollowUserUseCase`
- [x] **DTOs**: `LikeTripDTO`, `AddCommentDTO`, `CommentDTO` (out), `FollowDTO`

### 5.3 Infrastructure layer
- [x] **Repositories**: `LikeEloquentRepository`, `CommentEloquentRepository`, `FollowEloquentRepository`
- [x] **Controllers** (invokable):
  - `LikeTripController` — `POST /api/journals/{id}/like` (auth)
  - `UnlikeTripController` — `POST /api/journals/{id}/unlike` (auth)
  - `ListCommentsController` — `GET /api/journals/{id}/comments` (no auth)
  - `AddCommentController` — `POST /api/journals/{id}/comments` (auth)
  - `FollowUserController` — `POST /api/users/{id}/follow` (auth)
  - `UnfollowUserController` — `POST /api/users/{id}/unfollow` (auth)
- [x] **Requests**: `StoreCommentRequest`
- [x] **Resources**: `CommentResource` — `id`, `text`, `user` (UserSummaryResource), `createdAt`, `likesCount` (return `0` for MVP — no comment-like endpoints in contract)

### 5.4 Model observers (counter caches)
- [x] `LikeObserver`: on created → `Trip::increment('likes_count')`, on deleted → `Trip::decrement('likes_count')`
- [x] `TripCommentObserver`: on created → `Trip::increment('comments_count')`, on deleted → `Trip::decrement('comments_count')`
- [x] Register observers in `AppServiceProvider` or a dedicated `ObserverServiceProvider`

### 5.5 Response shapes
- [x] `POST /journals/{id}/like` → `200 { likesCount: N }`
- [x] `POST /journals/{id}/unlike` → `200 { likesCount: N }`
- [x] `GET /journals/{id}/comments` → `200 [Comment, ...]` (flat array, NOT paginated)
- [x] `POST /journals/{id}/comments` → `201 Comment`
- [x] `POST /users/{id}/follow` → `204 No Content`
- [x] `POST /users/{id}/unfollow` → `204 No Content`

### 5.6 Service provider
- [x] Create `app/Providers/Contexts/SocialServiceProvider.php`
- [x] Bind all repository interfaces
- [x] Register in `bootstrap/providers.php`

### 5.7 Routes
- [x] Create `routes/api/social.php`, require from `routes/api.php`

---

## Phase 6: Profile Context (extend existing)

Existing: `GetProfileController` fetching basic user data. Need: full profile with stats, update, user lookup by username, travel stats.

### 6.1 Domain layer (extend)
- [x] Extend `User` value object with: `bio`, `coverPhoto`, `location`, `journalCount`, `followersCount`, `followingCount`, `countriesVisited`, `isFollowing`
- [x] Add `updateProfile()` method or `UpdateProfileDTO` fields
- [x] Add to `UserRepository`: `findByUsername(string $username): ?User`, `update(int $userId, array $data): User`
- [x] `StatsValueObject`: `totalDistance`, `countriesVisited`, `citiesExplored`, `journalsWritten`, `regions[]`
- [x] Add to repository: `getStats(int $userId): StatsValueObject`

### 6.2 Application layer (extend)
- [x] `UpdateProfileUseCase` with `UpdateProfileDTO` (in)
- [x] `GetUserByUsernameUseCase`
- [x] `GetStatsUseCase`
- [x] Extend `UserDTO` (out) to include all profile fields

### 6.3 Infrastructure layer (extend)
- [x] Update `ProfileResource` to output full profile shape per contract
- [x] Create `UserSummaryResource` in `src/Shared/` (reused by Trip, Social, Profile contexts): `id`, `name`, `username`, `avatar`
- [x] Create `StatsResource`: `totalDistance`, `countriesVisited`, `citiesExplored`, `journalsWritten`, `regions[]`
- [x] **Controllers**:
  - Update `GetProfileController` → full profile with counts
  - `UpdateProfileController` — `PUT /api/profile` (auth)
  - `GetStatsController` — `GET /api/profile/stats` (auth)
  - `GetUserByUsernameController` — `GET /api/users/{username}` (auth)
  - `GetUserStatsController` — `GET /api/users/{username}/stats` (auth)
- [x] **Requests**: `UpdateProfileRequest` — name, bio, location, avatar (url), coverPhoto (url), all optional
- [x] Update `UserEloquentRepository` to compute counts via efficient queries

### 6.4 Profile stats computation
- [x] `journalCount`: `COUNT(trips WHERE owner_id = ? AND status = published)`
- [x] `followersCount`: `COUNT(follows WHERE following_id = ?)`
- [x] `followingCount`: `COUNT(follows WHERE follower_id = ?)`
- [x] `countriesVisited`: `COUNT(DISTINCT country FROM places WHERE user_id = ? AND marker_type = 'visited')`
- [x] `isFollowing`: `EXISTS(follows WHERE follower_id = auth AND following_id = ?)` — only for other users; `false` for own profile

### 6.5 Travel stats computation (`/profile/stats`)
- [x] `journalsWritten`: count of user's published trips
- [x] `countriesVisited`: distinct countries from places (marker_type = visited)
- [x] `citiesExplored`: distinct place names from places (marker_type = visited)
- [x] `totalDistance`: sum of `trip_routes.distance_meters` for user's trips, formatted as `"X km"`
- [x] `regions`: group visited countries by continent using `countries` table + static continent mapping, calculate percentage per region

### 6.6 Service provider
- [x] `app/Providers/Contexts/ProfileServiceProvider.php` exists
- [x] Update bindings for new repository methods

### 6.7 Routes
- [x] Refactor `routes/api/profile.php` — remove duplicate `/auth/me`, add profile and user endpoints

---

## Phase 7: Place Context

New context for map places/pins.

### 7.1 Domain layer
- [x] `PlaceEntity` in `src/Place/Domain/Entities/`
- [x] `MarkerType` in `src/Place/Domain/Enums/`: visited, planned, wishlist
- [x] `PlaceRepository` interface: `findByUser(int $userId): array`, `save(PlaceEntity): PlaceEntity`, `delete(int $id): void`, `findById(int $id): ?PlaceEntity`
- [x] `PlaceNotFoundException`, `UnauthorizedPlaceActionException`

### 7.2 Application layer
- [x] **Use cases**: `ListPlacesUseCase`, `CreatePlaceUseCase`, `DeletePlaceUseCase`
- [x] **DTOs**: `CreatePlaceDTO` (in), `PlaceDTO` (out)

### 7.3 Infrastructure layer
- [x] `PlaceEloquentRepository`
- [x] **Controllers** (invokable):
  - `ListPlacesController` — `GET /api/places` (auth) → flat array, NOT paginated
  - `CreatePlaceController` — `POST /api/places` (auth)
  - `DeletePlaceController` — `DELETE /api/places/{id}` (auth, owner only)
- [x] **Requests**: `StorePlaceRequest`
- [x] **Resources**: `PlaceResource` — `id`, `name`, `country`, `date`, `coordinates` ({lat, lng}), `markerType`, `journalCount`, `image`

### 7.4 Place `journalCount` computation
- [x] For MVP: count trips by the same user where `trips.location ILIKE '%{place.name}%'` OR coordinate proximity (Haversine within ~50km radius)
- [x] Use a simple query scope, not a full geospatial engine

### 7.5 Service provider
- [x] Create `app/Providers/Contexts/PlaceServiceProvider.php`
- [x] Register in `bootstrap/providers.php`

### 7.6 Routes
- [x] Create `routes/api/places.php`, require from `routes/api.php`

---

## Phase 8: Search Context

Cross-context search. Lightweight context — delegates to existing repositories or uses direct Eloquent queries.

### 8.1 Domain layer
- [x] `SearchRepository` interface: `searchTrips(string $query, ?int $authUserId): array`, `searchUsers(string $query): array`, `searchPlaces(string $query): array`

### 8.2 Application layer
- [x] `SearchTripsUseCase`, `SearchUsersUseCase`, `SearchPlacesUseCase`

### 8.3 Infrastructure layer
- [x] `SearchEloquentRepository` — uses `ILIKE` (Postgres) / `LIKE` (SQLite for tests) on relevant columns
- [x] **Controllers**:
  - `SearchTripsController` — `GET /api/search/journals?q=` (optional auth — compute `isLiked` when authenticated)
  - `SearchUsersController` — `GET /api/search/users?q=` (optional auth)
  - `SearchPlacesController` — `GET /api/search/places?q=` (optional auth)
- [x] All search endpoints return **flat arrays**, NOT paginated
- [x] Limit results to a sensible max (e.g., 50) to prevent unbounded queries

### 8.4 Search queries
- [x] **Journals**: search `trips.title`, `trips.content`, `trips.location` WHERE `visibility = public AND status = published`
- [x] **Users**: search `users.name`, `users.username`
- [x] **Places**: search `places.name`, `places.country`

### 8.5 Service provider
- [x] Create `app/Providers/Contexts/SearchServiceProvider.php`
- [x] Register in `bootstrap/providers.php`

### 8.6 Routes
- [x] Create `routes/api/search.php`, require from `routes/api.php`

---

## Phase 9: Cross-cutting Concerns

### 9.1 CORS
- [x] Update `config/cors.php`: `allowed_origins` → `[env('FRONTEND_URL', 'http://localhost:3000')]`, `supports_credentials` → `true`

### 9.2 Error response format
- [ ] Customize exception handler in `bootstrap/app.php` → `withExceptions()` to ensure all errors return `{ message, errors? }` shape
- [ ] Map common exceptions: `AuthenticationException` → 401, `AuthorizationException` → 403, `ModelNotFoundException` → 404, `ValidationException` → 422
- [ ] Ensure `BaseException` subclasses (already have `render()` method) are handled correctly

### 9.3 Rate limiting
- [x] Add throttle middleware to auth routes (login, register, forgot-password): `throttle:5,1` (5 attempts per minute)
- [x] Add throttle middleware to social write routes (like, comment, follow): `throttle:30,1`

### 9.4 Authorization policies
- [x] `TripPolicy`: `update(User $user, Trip $trip)` → `$user->id === $trip->owner_id`, `delete()` → same
- [x] `PlacePolicy`: `delete(User $user, Place $place)` → `$user->id === $place->user_id`
- [x] Register policies in `AuthServiceProvider` or auto-discover

### 9.5 PHPStan rule
- [ ] Implement `UnitTestPreventDBCallsRule` referenced in `phpstan.neon` (currently missing file)

### 9.6 Database seeder
- [ ] `DatabaseSeeder` that creates: 10 users with biographies, 30 trips with tags/media/comments, like/follow relationships, places
- [ ] Use factories for realistic dev data

---

## Phase 10: Performance & Optimization

### 10.1 Counter caches (avoid COUNT on every request)
- [x] `likes_count` and `comments_count` columns exist on `trips`
- [ ] Sync via model observers (Phase 5.4)
- [ ] Consider `followers_count` / `following_count` on `users` table if profile queries become slow (defer — COUNT with index is fine for MVP)

### 10.2 Eager loading strategy
- [ ] **Trip lists**: always `with(['owner:id,name,username,avatar', 'tags:id,name', 'media' => fn($q) => $q->where('is_visible', true)->orderBy('order')])`
- [ ] **Trip detail**: add `comments.user:id,name,username,avatar`
- [ ] **Profile**: use `withCount` for `trips`, `followers`, `following` or precompute in repository
- [ ] **Prevent N+1**: enable `Model::preventLazyLoading()` in `AppServiceProvider` for non-production environments

### 10.3 Query optimization
- [ ] Use `select()` to limit columns on list queries (never load `content` or `private_content` on list endpoints)
- [ ] Use `chunk()` or cursor for any background/seeder operations
- [ ] Trending trips: consider a scheduled job that pre-computes trending scores into a cache key (avoid complex subqueries on every request)

### 10.4 Caching (Redis)
- [ ] Cache public feed by tab+page+filters — TTL 2 min, invalidate on new public trip or like
- [ ] Cache user stats — TTL 15 min, invalidate on trip/place create/delete
- [ ] Cache tag list — TTL 1 hour
- [ ] Use cache tags for granular invalidation where possible

### 10.5 Optional auth middleware
- [ ] Create `OptionalAuth` middleware (or use `auth:sanctum` with `optional` guard) for endpoints that work both authenticated and unauthenticated (`/journals/public`, search endpoints)
- [ ] When authenticated: compute `isLiked` per trip via `EXISTS` subquery or bulk-load liked trip IDs for the page

---

## Phase 11: Testing

### 11.1 Unit tests (tests/Unit)
- [ ] Domain entities: `TripEntity` construction, excerpt generation logic
- [ ] Value objects: `Coordinates`, `Engagement` (increment methods), `Owner`, `Email`
- [ ] Enums: `StatusEnum`, `VisibilityEnum`, `MarkerType`
- [ ] Password hashing and validation

### 11.2 Feature tests (tests/Feature)
- [ ] **Auth**: login (valid, invalid), register (valid, duplicate email, weak password), Google OAuth, forgot-password, logout, reset-password, `/auth/me`
- [ ] **Trips**: list (own trips with filters/tabs), list public (with tabs), get single, create (with tags, imageIds), update (partial, owner only, non-owner 403), delete (owner only, non-owner 403)
- [ ] **Social**: like (toggle, idempotent), unlike, list comments (no auth), add comment (auth), follow (prevent self-follow), unfollow
- [ ] **Profile**: get own profile (all fields), update profile, get user by username, get stats, get user stats
- [ ] **Places**: list own places, create, delete (owner only, non-owner 403)
- [ ] **Search**: search journals, users, places (with and without auth)
- [ ] **Edge cases**: 404 on missing resources, 401 on unauthenticated access, 403 on unauthorized actions

### 11.3 Factories
- [x] **UserFactory**: complete
- [ ] All other factories (see Phase 2.10)

---

## Contract ↔ Codebase Mapping

The frontend contract uses "journal" terminology; the backend uses "trip" internally.

| Contract field | Codebase mapping |
|----------------|------------------|
| Journal | Trip model / TripEntity |
| `journal.author` | `trip.owner` (belongsTo User) via UserSummaryResource |
| `journal.isPublic` | `trip.visibility === 'public'` (derived in TripResource) |
| `journal.coordinates` | `{ lat: trip.latitude, lng: trip.longitude }` or `null` |
| `journal.images[]` | `trip.media` (TripMedia model, `media_url` → `url`) |
| `journal.tags[]` | `trip.tags.pluck('name')` — array of strings, not objects |
| `journal.excerpt` | `trip.excerpt` column (stored, computed on save) |
| `journal.likesCount` | `trip.likes_count` column (counter cache) |
| `journal.commentsCount` | `trip.comments_count` column (counter cache) |
| `journal.isLiked` | `EXISTS(likes WHERE trip_id AND user_id = auth)` — per request |
| `comment.likesCount` | Return `0` for MVP (no comment-like endpoints in contract) |
| `profile.bio` | `user_biographies.content` (via `user.biography` relation) |
| `profile.isFollowing` | `EXISTS(follows WHERE follower_id = auth AND following_id = ?)` |
| `place.journalCount` | Count of trips matching place by location name or coordinate proximity |

API routes use `/journals` paths (matching the contract) but map to Trip models internally.
