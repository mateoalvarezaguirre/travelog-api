# MVP Plan — Travelog API

Pending steps to complete the backend MVP per `backend-api-contract.md`.

Legend: `[x]` done, `[ ]` pending.

---

## Phase 1: Foundation (Auth, DB schema, camelCase)

### 1.1 Install & configure Laravel Sanctum
- [ ] `composer require laravel/sanctum` and publish config
- [ ] Add `HasApiTokens` trait to `User` model
- [ ] Create `routes/api.php` and register it in `bootstrap/app.php`

### 1.2 Migrations — alter existing tables
- [ ] **users**: add columns `username` (string, unique), `avatar` (string, nullable), `cover_photo` (string, nullable), `location` (string, nullable), `google_id` (string, nullable)
- [ ] **trip_comments**: add columns `user_id` (unsignedInteger, FK → users), `text` (text)
- [ ] **trips**: add columns `date` (date, nullable), `location` (string, nullable), `latitude` (decimal 10,7, nullable), `longitude` (decimal 10,7, nullable). The contract's `is_public` maps to the existing `visibility` column (public = `public`, private = `private`), and `status` already exists.

### 1.3 Migrations — new tables
- [ ] **tags**: `id` (bigint PK), `name` (string, unique), timestamps
- [ ] **trip_tag** (pivot): `trip_id` (uuid FK → trips), `tag_id` (bigint FK → tags)
- [ ] **likes**: `trip_id` (uuid FK → trips), `user_id` (unsignedBigInteger FK → users), `created_at`. Composite unique on (trip_id, user_id)
- [ ] **follows**: `follower_id` (FK → users), `following_id` (FK → users), `created_at`. Composite unique on (follower_id, following_id)
- [ ] **places**: `id` (bigint PK), `user_id` (FK → users), `name` (string), `country` (string), `date` (date, nullable), `latitude` (decimal 10,7), `longitude` (decimal 10,7), `marker_type` (enum: visited/planned/wishlist), `image` (string, nullable), timestamps

### 1.4 CamelCase JSON middleware
- [ ] Create middleware to transform all outgoing JSON keys to camelCase and all incoming request keys to snake_case
- [ ] Register it globally for `api` routes

---

## Phase 2: Models (fillable, relations, casts)

### 2.1 User model
- [ ] Add `fillable`: name, email, password, username, avatar, cover_photo, location, google_id
- [ ] Add relations: `trips()`, `comments()`, `likes()`, `places()`, `followers()`, `following()`, `biography()`
- [ ] Add `hidden`: password, remember_token

### 2.2 Trip model
- [ ] Set `$keyType = 'string'`, `$incrementing = false` (UUID PK)
- [ ] Add `fillable`: title, content, private_content, owner_id, status, visibility, published_at, date, location, latitude, longitude, likes_count, comments_count
- [ ] Add `casts`: status → StatusEnum, visibility → VisibilityEnum, published_at → datetime, date → date
- [ ] Add relations: `owner()` (belongsTo User), `locations()`, `comments()`, `media()`, `waypoints()`, `route()`, `tags()`, `likes()`

### 2.3 Remaining models
- [ ] **TripComment**: fillable (trip_id, user_id, text), relations (trip, user)
- [ ] **TripLocation**: UUID config, fillable, relations (trip)
- [ ] **TripMedia**: fillable, relations (trip, uploader)
- [ ] **TripWaypoint**: UUID config, fillable, relations (trip)
- [ ] **TripRoute**: UUID config (`$primaryKey = 'trip_id'`), fillable, relations (trip)
- [ ] **Tag** (new model): fillable (name), relations (trips via pivot)
- [ ] **Like** (new model): fillable (trip_id, user_id), relations (trip, user). No `updated_at`.
- [ ] **Follow** (new model): fillable (follower_id, following_id), relations (follower, following). No auto-increment PK.
- [ ] **Place** (new model): fillable, casts (marker_type → enum), relations (user)
- [ ] **UserBiography**: fillable (user_id, content), relations (user)

---

## Phase 3: API Resources (response shaping)

All resources must output camelCase keys and match the shapes in `backend-api-contract.md`.

- [ ] **UserSummaryResource**: id, name, username, avatar
- [ ] **UserProfileResource**: extends summary + email, bio (from user_biographies), coverPhoto, location, journalCount, followersCount, followingCount, countriesVisited, isFollowing
- [ ] **TripResource** (Journal): id, title, content, excerpt (computed: strip HTML, first ~200 chars), date, location, coordinates ({lat, lng} or null), images, tags, status, isPublic (derived from visibility), likesCount, commentsCount, isLiked, author (UserSummaryResource), createdAt, updatedAt
- [ ] **TripImageResource**: id, url (from media_url), caption, order
- [ ] **CommentResource**: id, text, user (UserSummaryResource), createdAt, likesCount
- [ ] **PlaceResource** (MapPlace): id, name, country, date, coordinates, markerType, journalCount, image
- [ ] **PaginatedCollection**: wrap paginated results in `{ data, meta: { currentPage, lastPage, perPage, total } }`
- [ ] **StatsResource**: totalDistance, countriesVisited, citiesExplored, journalsWritten, regions[]

---

## Phase 4: Form Requests (validation)

- [ ] **LoginRequest**: email (required, email), password (required, string)
- [ ] **RegisterRequest**: name (required), email (required, email, unique:users), password (required, min:8, confirmed)
- [ ] **GoogleAuthRequest**: id_token (required, string)
- [ ] **ForgotPasswordRequest**: email (required, email)
- [ ] **StoreTripRequest**: title (required), content (required), date, location, coordinates (lat, lng), tags (array of strings), status (in:draft,published), isPublic (boolean), imageIds (array of ints)
- [ ] **UpdateTripRequest**: same fields as Store, all optional
- [ ] **StoreCommentRequest**: text (required, string)
- [ ] **UpdateProfileRequest**: name, bio, location, avatar (url), coverPhoto (url)
- [ ] **StorePlaceRequest**: name (required), country (required), coordinates (required), markerType (required, in:visited,planned,wishlist), date, image

---

## Phase 5: Controllers & Routes

### 5.1 Auth — `AuthController`
- [ ] `POST /api/auth/login` — validate credentials, return user + Sanctum token
- [ ] `POST /api/auth/register` — create user, return user + token
- [ ] `POST /api/auth/google` — verify Google id_token, find-or-create user, return user + token
- [ ] `POST /api/auth/forgot-password` — send password reset email
- [ ] `GET  /api/auth/me` — return authenticated user (requires auth:sanctum)

### 5.2 Trips (Journals) — `TripController`
- [ ] `GET    /api/journals` — list auth user's trips, paginated. Filters: page, search, tag, status, tab (recent/favorites/shared)
- [ ] `GET    /api/journals/public` — list public trips, paginated. Filters: page, search, tag, destination, tab (featured/trending/recent/following)
- [ ] `GET    /api/journals/{id}` — show single trip
- [ ] `POST   /api/journals` — create trip, attach tags, link imageIds
- [ ] `PUT    /api/journals/{id}` — update trip (partial update, only owner)
- [ ] `DELETE /api/journals/{id}` — delete trip (only owner)

### 5.3 Social — `LikeController`, `CommentController`, `FollowController`
- [ ] `POST /api/journals/{id}/like` — toggle like on, return likesCount
- [ ] `POST /api/journals/{id}/unlike` — toggle like off, return likesCount
- [ ] `GET  /api/journals/{id}/comments` — list comments (no auth required)
- [ ] `POST /api/journals/{id}/comments` — add comment (auth required)
- [ ] `POST /api/users/{id}/follow` — follow user
- [ ] `POST /api/users/{id}/unfollow` — unfollow user

### 5.4 Profile — `ProfileController`
- [ ] `GET /api/profile` — auth user's full profile
- [ ] `PUT /api/profile` — update auth user's profile
- [ ] `GET /api/profile/stats` — auth user's travel stats
- [ ] `GET /api/users/{username}` — other user's profile by username
- [ ] `GET /api/users/{username}/stats` — other user's stats by username

### 5.5 Places — `PlaceController`
- [ ] `GET    /api/places` — list auth user's map places
- [ ] `POST   /api/places` — add new place
- [ ] `DELETE /api/places/{id}` — remove place (only owner)

### 5.6 Search — `SearchController`
- [ ] `GET /api/search/journals?q=` — search trips by title/content/location
- [ ] `GET /api/search/users?q=` — search users by name/username
- [ ] `GET /api/search/places?q=` — search places by name/country

---

## Phase 6: Business logic & computed fields

- [ ] **Excerpt generation**: strip HTML from trip content, truncate to ~200 chars
- [ ] **likes_count / comments_count sync**: increment/decrement counters on trips table when likes/comments are created/deleted (use model observers or DB triggers)
- [ ] **isLiked**: computed per-request based on authenticated user
- [ ] **isFollowing**: computed per-request based on authenticated user
- [ ] **Profile stats**: countriesVisited (distinct country from places where marker_type = visited), journalCount (count of user's trips)
- [ ] **Travel stats** (`/profile/stats`): totalDistance, countriesVisited, citiesExplored, journalsWritten, regions with percentage breakdown
- [ ] **Authorization policies**: TripPolicy (only owner can update/delete), PlacePolicy (only owner can delete)

---

## Phase 7: Cross-cutting concerns

- [ ] **CORS config**: update `config/cors.php` to allow frontend origin (`FRONTEND_URL` env var, default `http://localhost:3000`)
- [ ] **Error response format**: ensure all errors return `{ message, errors? }` shape — customize exception handler
- [ ] **Rate limiting**: add throttle middleware to auth routes
- [ ] **PHPStan rule**: implement `UnitTestPreventDBCallsRule` referenced in `phpstan.neon` (currently missing)

---

## Phase 8: Testing

- [ ] **Unit tests**: domain entities, value objects, enums, excerpt generation
- [ ] **Feature tests**: auth endpoints (login, register, me), trip CRUD, social actions (like/unlike, comment, follow/unfollow), profile endpoints, places CRUD, search endpoints
- [ ] **Factories**: update existing factories to match new schema columns; create factories for Tag, Like, Follow, Place

---

## Mapping: contract name → codebase name

The frontend contract uses "journal" terminology; the backend uses "trip":

| Contract          | Codebase          |
|-------------------|-------------------|
| Journal           | Trip              |
| journal_images    | trip_media        |
| journal_tag       | trip_tag          |
| isPublic          | visibility (enum) |
| coordinates       | latitude+longitude|
| bio (on user)     | user_biographies.content |

API routes will use `/journals` paths (matching the contract) but internally map to Trip models.
