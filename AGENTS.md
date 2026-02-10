# AGENTS.md — Code Review & Coding Rules (Laravel + Hexagonal + DDD)

These are the **mandatory** coding and code‑review rules for this repository.

The project is a **Laravel** application that follows **Hexagonal Architecture**, **DDD (bounded contexts)**, and **Clean Code** practices.  
All agents (Cursor, Claude, etc.) must follow these rules when proposing or applying changes.

---

## 0) Non‑negotiable Architecture Rules (Hexagonal + DDD)

### 0.1 Domain purity and boundaries
- The **Domain** layer must be as **framework‑agnostic** as possible.
- Do **not** import Laravel/Illuminate/Eloquent classes in **Domain** or **Application** layers.
- Laravel‑specific concerns must live in **Infrastructure** (controllers, requests, resources, Eloquent models, providers, jobs, events, queues, migrations, etc.).
- **Carbon** is allowed for date/time handling, but prefer wrapping dates in **Value Objects** where it improves expressiveness and safety.

### 0.2 No Eloquent Models outside Infrastructure
- Eloquent Models are **Infrastructure‑only**.
- Mapping between Infrastructure Models and Domain Entities/Value Objects must be done via **mappers/assemblers**.

### 0.3 Controllers contain no business logic
- Controllers are pure orchestration:
  - authorize/validate
  - map request data into Domain types / Command objects
  - call a Use Case
  - return a Resource/Response
- No business decisions, rules, calculations, or state transitions belong in controllers.

### 0.4 Use cases return DTOs; HTTP layer formats responses
- Use cases return **Output DTOs**.
- Output DTOs may expose **public properties** and may include **Domain objects** as properties.
- **Resources** (or equivalent presenters) build the HTTP response format from DTOs.
- Use cases must never return Laravel Responses/JsonResources/arrays.

✅ Good
```php
final class CreateTripOutput
{
    public function __construct(
        public Trip $trip,
        public User $owner,
    ) {}
}
```

Controller:
```php
$output = ($this->createTripUseCase)($command);
return new TripResource($output->trip);
```

❌ Bad
```php
return response()->json([...]);
return ['trip' => $tripModel];
```

### 0.5 Framework isolation rule
- The project must be as **abstracted from Laravel as possible**:
  - framework code lives in **Infrastructure**
  - Domain/Application code should not depend on Laravel internals
- Exceptions must be explicit and justified (e.g., Carbon).

---

## 1) Repository Rules (Hard Requirements)

### 1.1 Repositories never return Models or primitives
- A repository must **never** return:
  - Eloquent Models
  - raw arrays
  - primitive values (`string`, `int`, `float`, `bool`)
- Repositories must return **Domain types**:
  - Entities / Aggregates
  - Value Objects
  - Domain Collections (typed)
  - Domain DTOs (only when clearly part of the domain contract)

✅ Good
```php
public function getById(UserId $id): ?User;
public function search(UserSearchCriteria $criteria): Users;
public function save(User $user): void;
```

❌ Bad
```php
public function find(int $id): ?UserModel;
public function getEmail(int $id): string;
public function list(): array;
```

### 1.2 Repository interfaces vs implementations
- Repository **interfaces** live in Domain/Application (depending on your layering).
- Implementations (Eloquent/SQL/etc.) live in **Infrastructure**.

### 1.3 Persistence mapping is explicit
- Infrastructure repositories must map:
  - Model/DB row → Entity/VO (rehydration)
  - Entity/VO → Model/DB row (persistence)
- Avoid leaking persistence structure into domain objects.

---

## 2) No Primitive Parameters (Repository / Adapter / Service)

### 2.1 No primitives in core method parameters
No method in these layers may accept primitive parameters:
- Repositories
- Adapters/Gateways (external services)
- Application services / Domain services

Always accept **Domain types**:
- Value Objects (e.g., UserId, Email, Money, CurrencyId)
- Entities / Aggregates
- Command/Query objects containing Domain types

✅ Good
```php
public function charge(Charge $charge): ChargeResult;
public function getByAccount(AccountNumber $account): ?Wallet;
```

❌ Bad
```php
public function charge(string $token, int $amount, int $merchantId): array;
public function getByAccount(string $account): ?array;
```

### 2.2 Where primitives are allowed
Primitives are allowed at **system boundaries** only:
- HTTP layer input (requests/controllers/resources)
- CLI entrypoints
- Infrastructure parsing/serialization (DB rows, HTTP payloads)

Convert primitives to Domain types **as early as possible**.

---

## 3) Clean Code Principles

- A method should do only one thing; extract complex logic into smaller methods.
- Methods longer than 20 lines should be refactored.
- Use clear and descriptive names for methods and variables. Avoid abbreviations.
- Avoid duplicated code; use reusable methods or services.
- Avoid commented-out code.
- Avoid comments in code; use descriptive private methods instead.
- Use constants instead of magic values.
- Each PHP file should contain only one public class.
- Use early returns to reduce nesting; avoid else blocks.
- Do not access superglobals directly (`$_POST`, `$_GET`).
- Use DTOs or value objects to group related parameters ONLY if the project already uses them.
- Use readonly properties when values shouldn't change after construction.
- Avoid nested switch/if/match structures; use polymorphism instead.
- Do not place business logic in controllers, requests, events, or listeners.
- Use strict typing: `declare(strict_types=1);`

---

## 4) SOLID Principles

- Classes should follow the Single Responsibility Principle (SRP).
- Manage dependencies using interfaces (Dependency Injection and DIP).
- Apply the Dependency Inversion Principle: depend on abstractions, not concrete implementations.
- Follow the Open/Closed Principle: code should be open for extension but closed for modification.

---

## 5) Design Principles

- Prefer iteration and modularization over duplication.

- Apply the **Tell, Don't Ask** principle: instead of querying an object's state and deciding, delegate the action directly to the object.

  ✅ Example:
  Instead of:
  ```php
  if ($user->isActive()) {
      $user->deactivate();
  }
  ```

  Use:
  ```php
  $user->deactivateIfActive();
  ```

- Follow the **DRY (Don't Repeat Yourself)** principle: avoid duplicating logic or structure; extract repeated logic into reusable methods or classes.

- Apply the **KISS (Keep It Simple, Stupid)** principle: write clear, simple solutions; avoid overengineering.

- Follow the **YAGNI (You Aren't Gonna Need It)** principle: don't implement functionality until it's actually needed.

- Respect the **Law of Demeter (Principle of least knowledge)**: avoid deep method chaining.

- Avoid unnecessary nested structures. Extract conditions or logic into private methods.

- Don't wrap trivial logic in multiple layers of abstraction. Abstractions should solve real complexity.

- Classes should not depend on too many other classes. Evaluate composition and cohesion.

- If a method has multiple levels of indentation, consider splitting it.

---

## 6) Standard Development Rules

- Controllers should follow the **Single Action Controller** pattern: only one public method named `__invoke`.
  - Class name must start with a verb and be descriptive (e.g., `CreateUser`, `UpdateInvoiceStatus`).
- Use Laravel's `Http` facade instead of Guzzle or cURL (Infrastructure).
- All method parameters, return types, and class attributes must have explicit type declarations. Avoid the `mixed` type.
- Every PHP file should start with `declare(strict_types=1);`
- Use the **Repository Pattern** for database access:
  - Repository interfaces must not depend on Eloquent models.
  - Repositories should only be shared across domains if placed in a `/Shared` folder.
- Use **Enums** instead of magic values or primitive constants.
- Avoid placing business logic in **Form Requests**, especially database-related validation rules like `exists` or `unique`.
- Do not use `static` methods unless the method is purely stateless and cannot affect object state.
- Use **constructor injection** for dependencies whenever possible.
- Constructors must not contain business logic. They should only initialize attributes or perform validation related to object construction.
- In Eloquent, do **not use** `whereDate`, `whereYear`, or `whereTime`. Instead, use `whereBetween` or explicit date ranges.
- Methods must **not receive parameters by reference** (i.e., avoid `&$param`).
- Use **Carbon** instead of native PHP date/time functions.
- Always use `config(...)` instead of `env(...)` inside the application logic.
- Avoid returning `array` or generic `object` when modeling finite or structured data. Instead, create dedicated data classes to encapsulate that data.

---

## 7) Standard Naming Conventions

### Controllers
**Format:** Singular form with "Controller" suffix  
- ✅ Good: `CreateArticleController`  
- ❌ Bad: `CreateArticlesController`

### Routes
**Format:** Plural form for resource routes  
- ✅ Good: `articles/1`  
- ❌ Bad: `article/1`

### Route Names
**Format:** snake_case with dot notation for nested routes  
- ✅ Good: `users.show_active`  
- ❌ Bad: `users.show-active`, `show-active-users`

### Models
**Format:** Singular form, PascalCase  
- ✅ Good: `User`  
- ❌ Bad: `Users`

### Relationships
#### hasOne or belongsTo
**Format:** Singular form, camelCase  
- ✅ Good: `articleComment`  
- ❌ Bad: `articleComments`, `article_comment`

#### All Other Relationships (hasMany, belongsToMany, etc.)
**Format:** Plural form, camelCase  
- ✅ Good: `articleComments`  
- ❌ Bad: `articleComment`, `article_comments`

### Database Tables
**Format:** Plural form, snake_case  
- ✅ Good: `article_comments`  
- ❌ Bad: `article_comment`, `articleComments`

### Pivot Tables
**Format:** Singular model names in alphabetical order, snake_case  
- ✅ Good: `article_user`  
- ❌ Bad: `user_article`, `articles_users`

### Table Columns
**Format:** snake_case without model name prefix  
- ✅ Good: `meta_title`  
- ❌ Bad: `MetaTitle`, `article_meta_title`

### Model Properties
**Format:** snake_case for database attributes, camelCase for accessors/mutators  
- ✅ Good: `$model->created_at`  
- ❌ Bad: `$model->createdAt`

### Foreign Keys
**Format:** Singular model name with `_id` suffix  
- ✅ Good: `article_id`  
- ❌ Bad: `ArticleId`, `id_article`, `articles_id`

### Primary Keys
**Format:** `id` (default Laravel convention)  
- ✅ Good: `id`  
- ❌ Bad: `custom_id`

### Migrations
**Format:** Date prefix with descriptive action in snake_case  
- ✅ Good: `2017_01_01_000000_create_articles_table`  
- ❌ Bad: `2017_01_01_000000_articles`

### Methods
**Format:** camelCase for general methods  
- ✅ Good: `getAll`  
- ❌ Bad: `get_all`

### Resource Controller Methods
**Format:** Use standard Laravel resource method names  
- ✅ Good: `store`  
- ❌ Bad: `saveArticle`

### Variables / Collections / Objects
- Variables: camelCase, descriptive  
- Collections: plural camelCase  
- Objects: singular camelCase

### Config and Language Files
**Format:** snake_case  
- ✅ Good: `articles_enabled`  
- ❌ Bad: `ArticlesEnabled`, `articles-enabled`

### Views
**Format:** kebab-case  
- ✅ Good: `show-filtered.blade.php`

### Config Files
**Format:** snake_case  
- ✅ Good: `google_calendar.php`  
- ❌ Bad: `googleCalendar.php`

### Contracts (Interfaces)
**Format:** Adjective or noun form  
- ✅ Good: `AuthenticationInterface`  
- ❌ Bad: `Authenticatable`, `IAuthentication`

### Traits
**Format:** Adjective form (or `Trait` suffix if following PSR)  
- ✅ Good: `Notifiable` or `NotifiableTrait`

### Enums
**Format:** Singular form  
- ✅ Good: `UserType`  
- ❌ Bad: `UserTypes`, `UserTypeEnum`

### Form Requests
**Format:** Singular form with "Request" suffix  
- ✅ Good: `UpdateUserRequest`

### Seeders
**Format:** Singular form with "Seeder" suffix  
- ✅ Good: `UserSeeder`

### File Naming Guidelines
1. PHP Classes: PascalCase matching the class name  
2. Blade Templates: kebab-case with `.blade.php` extension  
3. Migration Files: timestamp + descriptive action  
4. Config / Language Files: snake_case with `.php` extension

---

## 8) Testing Rules

### 8.1 General
- Do not use `alias` or `overload` in mocks. Instead, enforce dependency injection to make mocking possible.
- Test methods must follow either the **AAA** (Arrange-Act-Assert) or **Given-When-Then** pattern.
- Test class names must match the class or feature being tested.

### 8.2 Feature tests (success paths of exposed services)
- Feature tests must validate the **success paths** of the exposed services (HTTP endpoints / public service interfaces).
- Feature tests should prioritize “happy path” coverage: correct status codes, payload shape, and essential persistence side effects.
- Feature tests should include error cases only when:
  - the error is **very important**, **high-risk**, **regression-prone**, or
  - the error is a strict **public contract** that must be guaranteed (authorization, idempotency, invariants, etc.).
- Avoid excessive DB assertions such as `->assertDatabaseHas(...)` unless they add real value to the contract being tested.

### 8.3 Integration tests (Infrastructure layer)
- Integration tests evaluate the **Infrastructure layer**:
  - repositories (queries + mapping Model/DB → Domain and back)
  - adapters/gateways (HTTP, messaging, third-party integrations)
  - mappers/assemblers (Model ↔ Domain mapping)
- The goal is to confirm Infrastructure implementations:
  - work correctly with real dependencies (DB, etc.),
  - respect Domain/Application contracts,
  - correctly translate between external representations and domain objects.

### 8.4 Unit tests (Use cases + domain logic + remaining errors)
- Unit tests must **not** interact with the database. All dependencies must be mocked/faked through interfaces.
- Unit tests must validate:
  - use case orchestration rules,
  - domain services and domain logic,
  - value objects and invariants,
  - and **the remaining error cases** (especially those not covered in Feature tests).
- Unit tests should cover edge cases, branching logic, and specific failure scenarios:
  - invalid state transitions
  - missing entities
  - invariant violations
  - external service failures (simulated via mocks)

---

## 9) If a request conflicts with these rules
- Do not implement a solution that violates the non‑negotiable rules.
- Propose an alternative design that respects the architecture:
  - add a Value Object
  - add a Mapper/Assembler
  - adjust repository contracts
  - introduce a Command/Query object
