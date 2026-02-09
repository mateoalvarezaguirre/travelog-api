# Backend API Contract

Everything the Laravel backend must implement for the Travelog frontend to work.

Base URL: `NEXT_PUBLIC_API_BASE_URL` (default `http://localhost:8000/api`)

All endpoints accept and return `Content-Type: application/json` + `Accept: application/json`.

Authenticated endpoints require `Authorization: Bearer <token>` header.

---

## 1. Authentication

### POST `/auth/login`

Login with email/password. Returns a user object and a Bearer token.

**Body:**
```json
{
  "email": "string",
  "password": "string"
}
```

**Response 200:**
```json
{
  "user": {
    "id": 1,
    "name": "string",
    "email": "string",
    "username": "string",
    "avatar": "string | null"
  },
  "token": "string"
}
```

**Response 401:**
```json
{
  "message": "Credenciales incorrectas"
}
```

---

### POST `/auth/google`

Exchange a Google `id_token` for a Travelog session. Creates the user if they don't exist.

**Body:**
```json
{
  "id_token": "string"
}
```

**Response 200:** Same shape as `/auth/login`.

---

### POST `/auth/register`

Create a new user account.

**Body:**
```json
{
  "name": "string",
  "email": "string",
  "password": "string",
  "password_confirmation": "string"
}
```

**Response 201:** Same shape as `/auth/login`.

**Response 422 (validation):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

---

### POST `/auth/forgot-password`

Send a password reset email.

**Body:**
```json
{
  "email": "string"
}
```

**Response 200:**
```json
{
  "message": "Se ha enviado un enlace de recuperación a tu correo electrónico."
}
```

---

### GET `/auth/me`

Get the currently authenticated user. **Requires auth.**

**Response 200:**
```json
{
  "id": 1,
  "name": "string",
  "email": "string",
  "username": "string",
  "avatar": "string | null"
}
```

---

## 2. Journals

### GET `/journals`

List the authenticated user's journals. **Requires auth.** Paginated.

**Query params:**
| Param    | Type   | Description                                    |
|----------|--------|------------------------------------------------|
| `page`   | number | Page number (default 1)                        |
| `search` | string | Full-text search on title/content/location     |
| `tag`    | string | Filter by tag                                  |
| `status` | string | Filter by status (`draft` or `published`)      |
| `tab`    | string | UI tab filter (`recent`, `favorites`, `shared`) |

**Response 200:**
```json
{
  "data": [Journal],
  "meta": {
    "currentPage": 1,
    "lastPage": 5,
    "perPage": 12,
    "total": 56
  }
}
```

---

### GET `/journals/public`

List public journals (explore feed). **No auth required.** Paginated.

**Query params:**
| Param         | Type   | Description                                         |
|---------------|--------|-----------------------------------------------------|
| `page`        | number | Page number                                         |
| `search`      | string | Full-text search                                    |
| `tag`         | string | Filter by tag                                       |
| `destination` | string | Filter by region (`asia`, `europe`, `africa`, etc.) |
| `tab`         | string | Feed type (`featured`, `trending`, `recent`, `following`) |

**Response 200:** Same paginated shape as `GET /journals`.

---

### GET `/journals/:id`

Get a single journal by ID. **Requires auth.**

**Response 200:**
```json
{
  "id": 1,
  "title": "string",
  "content": "string (HTML)",
  "excerpt": "string",
  "date": "2024-03-15",
  "location": "Tokyo, Japan",
  "coordinates": { "lat": 35.6762, "lng": 139.6503 },
  "images": [
    { "id": 1, "url": "string", "caption": "string | null", "order": 0 }
  ],
  "tags": ["aventura", "asia"],
  "status": "published",
  "isPublic": true,
  "likesCount": 42,
  "commentsCount": 7,
  "isLiked": false,
  "author": {
    "id": 1,
    "name": "string",
    "username": "string",
    "avatar": "string | null"
  },
  "createdAt": "2024-03-15T10:30:00Z",
  "updatedAt": "2024-03-15T10:30:00Z"
}
```

**Response 404:**
```json
{
  "message": "Bitácora no encontrada"
}
```

---

### POST `/journals`

Create a new journal. **Requires auth.**

**Body:**
```json
{
  "title": "string",
  "content": "string",
  "date": "2024-03-15",
  "location": "string",
  "coordinates": { "lat": 0.0, "lng": 0.0 },
  "tags": ["string"],
  "status": "published | draft",
  "isPublic": true,
  "imageIds": [1, 2, 3]
}
```

Fields `coordinates`, `tags`, and `imageIds` are optional.

**Response 201:** Full `Journal` object (same shape as GET `/journals/:id`).

---

### PUT `/journals/:id`

Update an existing journal. **Requires auth.** Partial update — only send changed fields.

**Body:** Same fields as POST, all optional.

**Response 200:** Full `Journal` object.

---

### DELETE `/journals/:id`

Delete a journal. **Requires auth.**

**Response 204:** No content.

---

## 3. Social (Likes, Comments, Follows)

### POST `/journals/:journalId/like`

Like a journal. **Requires auth.**

**Response 200:**
```json
{
  "likesCount": 43
}
```

---

### POST `/journals/:journalId/unlike`

Remove a like from a journal. **Requires auth.**

**Response 200:**
```json
{
  "likesCount": 42
}
```

---

### GET `/journals/:journalId/comments`

Get all comments for a journal. **No auth required.**

**Response 200:**
```json
[
  {
    "id": 1,
    "text": "string",
    "user": {
      "id": 1,
      "name": "string",
      "username": "string",
      "avatar": "string | null"
    },
    "createdAt": "2024-03-15T10:30:00Z",
    "likesCount": 3
  }
]
```

---

### POST `/journals/:journalId/comments`

Add a comment to a journal. **Requires auth.**

**Body:**
```json
{
  "text": "string"
}
```

**Response 201:** Single `Comment` object (same shape as in the array above).

---

### POST `/users/:userId/follow`

Follow a user. **Requires auth.**

**Response 204:** No content.

---

### POST `/users/:userId/unfollow`

Unfollow a user. **Requires auth.**

**Response 204:** No content.

---

## 4. Profile

### GET `/profile`

Get the authenticated user's full profile. **Requires auth.**

**Response 200:**
```json
{
  "id": 1,
  "name": "string",
  "email": "string",
  "username": "string",
  "bio": "string | null",
  "avatar": "string | null",
  "coverPhoto": "string | null",
  "location": "string | null",
  "journalCount": 12,
  "followersCount": 150,
  "followingCount": 45,
  "countriesVisited": 8,
  "isFollowing": false
}
```

---

### GET `/users/:username`

Get another user's profile by username. **Requires auth.**

**Response 200:** Same shape as `GET /profile`.

---

### PUT `/profile`

Update the authenticated user's profile. **Requires auth.**

**Body (all fields optional):**
```json
{
  "name": "string",
  "bio": "string",
  "location": "string",
  "avatar": "https://res.cloudinary.com/...",
  "coverPhoto": "https://res.cloudinary.com/..."
}
```

`avatar` and `coverPhoto` are Cloudinary URLs (the frontend uploads directly to Cloudinary, then sends the URL here).

**Response 200:** Full `UserProfile` object.

---

### GET `/profile/stats`

Get the authenticated user's travel statistics. **Requires auth.**

**Response 200:**
```json
{
  "totalDistance": "24,500 km",
  "countriesVisited": 8,
  "citiesExplored": 23,
  "journalsWritten": 12,
  "regions": [
    { "name": "Asia", "percentage": 40 },
    { "name": "Europa", "percentage": 35 },
    { "name": "América del Sur", "percentage": 25 }
  ]
}
```

---

### GET `/users/:username/stats`

Get another user's travel statistics. **Requires auth.**

**Response 200:** Same shape as `GET /profile/stats`.

---

## 5. Places (Map)

### GET `/places`

Get all of the authenticated user's places/pins for the map. **Requires auth.**

**Response 200:**
```json
[
  {
    "id": 1,
    "name": "Tokyo",
    "country": "Japan",
    "date": "2024-03-15",
    "coordinates": { "lat": 35.6762, "lng": 139.6503 },
    "markerType": "visited",
    "journalCount": 3,
    "image": "string | null"
  }
]
```

`markerType` is one of: `"visited"`, `"planned"`, `"wishlist"`.

---

### POST `/places`

Add a new place/pin to the map. **Requires auth.**

**Body:**
```json
{
  "name": "string",
  "country": "string",
  "date": "2024-03-15",
  "coordinates": { "lat": 0.0, "lng": 0.0 },
  "markerType": "visited | planned | wishlist",
  "image": "string | null"
}
```

`date` and `image` are optional.

**Response 201:** Single `MapPlace` object.

---

### DELETE `/places/:id`

Remove a place from the map. **Requires auth.**

**Response 204:** No content.

---

## 6. Search

### GET `/search/journals?q=:query`

Search journals by title, content, or location. **Optional auth** (pass token if available).

**Query params:**
| Param | Type   | Description     |
|-------|--------|-----------------|
| `q`   | string | Search query    |

**Response 200:**
```json
[Journal, Journal, ...]
```

Returns an array of `Journal` objects (not paginated).

---

### GET `/search/users?q=:query`

Search users by name or username. **Optional auth.**

**Response 200:**
```json
[
  {
    "id": 1,
    "name": "string",
    "username": "string",
    "avatar": "string | null"
  }
]
```

---

### GET `/search/places?q=:query`

Search places by name or country. **Optional auth.**

**Response 200:**
```json
[MapPlace, MapPlace, ...]
```

Returns an array of `MapPlace` objects (not paginated).

---

## 7. Error Response Format

All error responses use this shape:

```json
{
  "message": "Human-readable error message",
  "errors": {
    "field_name": ["Validation error 1", "Validation error 2"]
  }
}
```

- `message` is always present
- `errors` is only present for 422 validation errors
- The frontend reads `response.status` as the status code

Common status codes:
| Code | Meaning                              |
|------|--------------------------------------|
| 200  | Success                              |
| 201  | Created                              |
| 204  | No content (delete, follow/unfollow) |
| 401  | Unauthenticated (invalid/expired token) |
| 403  | Forbidden (not the resource owner)   |
| 404  | Not found                            |
| 422  | Validation error                     |
| 500  | Server error                         |

---

## 8. Pagination Response Format

All paginated endpoints wrap results in:

```json
{
  "data": [...],
  "meta": {
    "currentPage": 1,
    "lastPage": 5,
    "perPage": 12,
    "total": 56
  }
}
```

The frontend sends `?page=N` to navigate pages. The backend decides `perPage`.

**Laravel tip:** This maps to Laravel's `->paginate()` but the keys need to be camelCase. Either use an API Resource that transforms `current_page` → `currentPage`, or set a global response transformer.

---

## 9. JSON Key Casing

The frontend expects **camelCase** keys everywhere:

| Frontend expects | Laravel default (snake_case) |
|------------------|------------------------------|
| `likesCount`     | `likes_count`                |
| `commentsCount`  | `comments_count`             |
| `isLiked`        | `is_liked`                   |
| `isPublic`       | `is_public`                  |
| `coverPhoto`     | `cover_photo`                |
| `createdAt`      | `created_at`                 |
| `updatedAt`      | `updated_at`                 |
| `currentPage`    | `current_page`               |
| `lastPage`       | `last_page`                  |
| `perPage`        | `per_page`                   |
| `markerType`     | `marker_type`                |
| `journalCount`   | `journal_count`              |
| `followersCount` | `followers_count`            |
| `followingCount` | `following_count`            |
| `countriesVisited` | `countries_visited`        |
| `isFollowing`    | `is_following`               |
| `imageIds`       | `image_ids`                  |
| `totalDistance`   | `total_distance`             |
| `citiesExplored` | `cities_explored`            |
| `journalsWritten`| `journals_written`           |

**Recommended approach:** Use a global middleware or API Resource layer to transform all response keys to camelCase and all incoming request keys to snake_case. Packages like `middlewares/api-camel-case` or a custom JsonResource can handle this.

---

## 10. Database Models (minimum schema)

### `users`
| Column          | Type         | Notes                    |
|-----------------|-------------|--------------------------|
| id              | bigint PK    | auto-increment           |
| name            | string       |                          |
| email           | string       | unique                   |
| username        | string       | unique                   |
| password        | string       | hashed                   |
| bio             | text         | nullable                 |
| avatar          | string       | nullable, Cloudinary URL |
| cover_photo     | string       | nullable, Cloudinary URL |
| location        | string       | nullable                 |
| google_id       | string       | nullable, for OAuth      |
| email_verified_at | timestamp  | nullable                 |
| created_at      | timestamp    |                          |
| updated_at      | timestamp    |                          |

### `journals`
| Column       | Type        | Notes                              |
|-------------|-------------|-------------------------------------|
| id          | bigint PK    |                                    |
| user_id     | bigint FK    | → users.id                         |
| title       | string       |                                    |
| content     | text         | HTML content                       |
| excerpt     | string       | auto-generated or manual           |
| date        | date         | travel date                        |
| location    | string       | human-readable location name       |
| latitude    | decimal(10,7)| nullable                           |
| longitude   | decimal(10,7)| nullable                           |
| status      | enum         | `draft`, `published`               |
| is_public   | boolean      | default true                       |
| created_at  | timestamp    |                                    |
| updated_at  | timestamp    |                                    |

**Notes:** The frontend expects `coordinates` as `{ lat, lng }` — the API Resource must combine `latitude` + `longitude` into this object (and accept it in reverse for create/update).

### `journal_images`
| Column     | Type      | Notes                    |
|-----------|-----------|--------------------------|
| id        | bigint PK  |                         |
| journal_id| bigint FK  | → journals.id           |
| url       | string     | Cloudinary URL          |
| caption   | string     | nullable                |
| order     | integer    | display order           |
| created_at| timestamp  |                         |

### `tags` + `journal_tag` (many-to-many)
| `tags` column | Type      |
|-------------|-----------|
| id          | bigint PK  |
| name        | string     | unique |

| `journal_tag` column | Type     |
|---------------------|----------|
| journal_id          | bigint FK |
| tag_id              | bigint FK |

### `comments`
| Column     | Type      | Notes            |
|-----------|-----------|-------------------|
| id        | bigint PK  |                  |
| journal_id| bigint FK  | → journals.id    |
| user_id   | bigint FK  | → users.id       |
| text      | text       |                  |
| created_at| timestamp  |                  |
| updated_at| timestamp  |                  |

### `likes`
| Column     | Type      | Notes                          |
|-----------|-----------|--------------------------------|
| journal_id| bigint FK  | → journals.id                 |
| user_id   | bigint FK  | → users.id                    |
| created_at| timestamp  |                               |

Composite unique on (`journal_id`, `user_id`).

### `follows`
| Column       | Type      | Notes                          |
|-------------|-----------|--------------------------------|
| follower_id | bigint FK  | → users.id (who follows)      |
| following_id| bigint FK  | → users.id (who is followed)  |
| created_at  | timestamp  |                               |

Composite unique on (`follower_id`, `following_id`).

### `places`
| Column      | Type         | Notes                      |
|------------|-------------|----------------------------|
| id         | bigint PK    |                            |
| user_id    | bigint FK    | → users.id                 |
| name       | string       |                            |
| country    | string       |                            |
| date       | date         | nullable                   |
| latitude   | decimal(10,7)|                            |
| longitude  | decimal(10,7)|                            |
| marker_type| enum         | `visited`, `planned`, `wishlist` |
| image      | string       | nullable, URL              |
| created_at | timestamp    |                            |
| updated_at | timestamp    |                            |

---

## 11. Computed / Virtual Fields

These fields are not stored directly in the database — the API Resource must compute them:

| Field            | How to compute                                                   |
|-----------------|------------------------------------------------------------------|
| `journal.likesCount`    | `COUNT(likes WHERE journal_id = ?)`                       |
| `journal.commentsCount` | `COUNT(comments WHERE journal_id = ?)`                   |
| `journal.isLiked`       | `EXISTS(likes WHERE journal_id = ? AND user_id = auth)` — only when authenticated |
| `journal.excerpt`       | First ~200 chars of `content` stripped of HTML, or a dedicated column |
| `journal.coordinates`   | `{ lat: latitude, lng: longitude }` or `null` if both are null |
| `journal.author`        | `UserSummary` from the related `user`                     |
| `profile.journalCount`  | `COUNT(journals WHERE user_id = ?)`                       |
| `profile.followersCount`| `COUNT(follows WHERE following_id = ?)`                   |
| `profile.followingCount`| `COUNT(follows WHERE follower_id = ?)`                    |
| `profile.countriesVisited` | `COUNT(DISTINCT country FROM places WHERE user_id = ? AND marker_type = 'visited')` |
| `profile.isFollowing`   | `EXISTS(follows WHERE follower_id = auth AND following_id = ?)` — only when viewing another user |
| `place.journalCount`    | Number of journals at this place (by location match or coordinates proximity) |

---

## 12. CORS

The Laravel backend must allow CORS from the frontend origin:

```php
// config/cors.php
'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'supports_credentials' => true,
```

---

## 13. Endpoint Summary Table

| Method | Endpoint                          | Auth     | Description                    |
|--------|-----------------------------------|----------|--------------------------------|
| POST   | `/auth/login`                     | No       | Login with email/password      |
| POST   | `/auth/google`                    | No       | Login/register with Google     |
| POST   | `/auth/register`                  | No       | Create account                 |
| POST   | `/auth/forgot-password`           | No       | Send reset email               |
| GET    | `/auth/me`                        | Yes      | Current user info              |
| GET    | `/journals`                       | Yes      | List my journals (paginated)   |
| GET    | `/journals/public`                | No       | Public feed (paginated)        |
| GET    | `/journals/:id`                   | Yes      | Journal detail                 |
| POST   | `/journals`                       | Yes      | Create journal                 |
| PUT    | `/journals/:id`                   | Yes      | Update journal                 |
| DELETE | `/journals/:id`                   | Yes      | Delete journal                 |
| POST   | `/journals/:id/like`              | Yes      | Like a journal                 |
| POST   | `/journals/:id/unlike`            | Yes      | Unlike a journal               |
| GET    | `/journals/:id/comments`          | No       | List comments                  |
| POST   | `/journals/:id/comments`          | Yes      | Add comment                    |
| POST   | `/users/:id/follow`               | Yes      | Follow user                    |
| POST   | `/users/:id/unfollow`             | Yes      | Unfollow user                  |
| GET    | `/profile`                        | Yes      | My profile                     |
| PUT    | `/profile`                        | Yes      | Update my profile              |
| GET    | `/profile/stats`                  | Yes      | My travel stats                |
| GET    | `/users/:username`                | Yes      | User profile by username       |
| GET    | `/users/:username/stats`          | Yes      | User stats by username         |
| GET    | `/places`                         | Yes      | My map places                  |
| POST   | `/places`                         | Yes      | Add map place                  |
| DELETE | `/places/:id`                     | Yes      | Remove map place               |
| GET    | `/search/journals?q=`             | Optional | Search journals                |
| GET    | `/search/users?q=`                | Optional | Search users                   |
| GET    | `/search/places?q=`               | Optional | Search places                  |
