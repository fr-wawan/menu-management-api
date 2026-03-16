# Restaurant Menu Management API

![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Sanctum](https://img.shields.io/badge/Auth-Sanctum-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

A RESTful API for managing restaurants and their menu items, built with Laravel 12.

## Tech Stack

| Layer | Technology |
|-------|------------|
| Framework | Laravel 12 |
| Language | PHP 8.4 |
| Database | MySQL 8.4 |
| Authentication | Laravel Sanctum (Token-based) |
| Testing | Pest PHP |
| Containerization | Docker (Laravel Sail) |

## Requirements

**Using Docker (recommended):**
- Docker >= 24.x
- Docker Compose >= 2.x

**Without Docker:**
- PHP >= 8.4
- Composer >= 2.x
- MySQL >= 8.x

## Setup Instructions

### Using Docker (Laravel Sail) — Recommended

```bash
# Clone the repository
git clone https://github.com/fr-wawan/menu-management-api
cd menu-management-api

# Copy environment file
cp .env.example .env
```

> [!NOTE]
> Make sure `DB_HOST=mysql`, `DB_DATABASE=laravel`, `DB_USERNAME=sail`, and `DB_PASSWORD=password`
> are set in your `.env` — these match the default Sail configuration.

```bash
# Install dependencies (requires PHP & Composer locally)
composer install

# OR without local PHP, use Docker:
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs

# Start containers
./vendor/bin/sail up -d
```

> [!NOTE]
> MySQL may take a few seconds to fully initialize after the containers start.
> If you get a `Connection refused` error on migration, wait a moment and retry.

```bash
# Generate application key
./vendor/bin/sail artisan key:generate

# Run migrations
./vendor/bin/sail artisan migrate

# Seed the database (2 restaurants with 5 menu items each)
./vendor/bin/sail artisan db:seed

# Run tests
./vendor/bin/sail test
```

The API will be available at: **http://localhost:8081**

---

### Local Development (Without Docker)

```bash
# Clone the repository
git clone https://github.com/fr-wawan/menu-management-api
cd menu-management-api

# Copy and configure environment
cp .env.example .env
# Edit .env: set DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD to match your local MySQL

# Install dependencies
composer install

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed the database
php artisan db:seed

# Start the server
php artisan serve

# Run tests
php artisan test
```

---

## API Documentation

### Postman Collection

A complete Postman collection is included for easy API testing:

1. Import `Menu Management API.postman_collection.json` into Postman
2. Import `Menu Management API.postman_environment.json` as environment
3. Select the **"Menu Management API"** environment
4. Run **Register** or **Login** first — the token will be automatically saved to the environment

### Base URL

```
http://localhost:8081/api
```

---

### Authentication

The API uses token-based authentication via Laravel Sanctum.

#### Register

```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Login

```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Login successful.",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "token": "1|abc123..."
    }
}
```

#### Logout (Requires Auth)

```http
POST /api/auth/logout
Authorization: Bearer <token>
```

---

### Restaurants

| Method | Endpoint | Auth Required | Description |
|--------|----------|:---:|-------------|
| GET | `/restaurants` | ✗ | List all restaurants (paginated) |
| GET | `/restaurants/{id}` | ✗ | Get restaurant with menu items |
| POST | `/restaurants` | ✓ | Create a restaurant |
| PUT | `/restaurants/{id}` | ✓ | Update a restaurant |
| DELETE | `/restaurants/{id}` | ✓ | Delete a restaurant |

#### Query Parameters — `GET /restaurants`

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `search` | string | — | Filter by restaurant name |
| `per_page` | integer | 15 | Items per page (max: 100) |

#### Create Restaurant

```http
POST /api/restaurants
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "Pizza Palace",
    "address": "123 Main Street",
    "phone": "+62-812-3456-7890",
    "opening_hours": "09:00 - 22:00"
}
```

---

### Menu Items

| Method | Endpoint | Auth Required | Description |
|--------|----------|:---:|-------------|
| GET | `/restaurants/{id}/menu_items` | ✗ | List menu items (paginated) |
| POST | `/restaurants/{id}/menu_items` | ✓ | Add a menu item |
| PUT | `/menu_items/{id}` | ✓ | Update a menu item |
| DELETE | `/menu_items/{id}` | ✓ | Delete a menu item |

#### Query Parameters — `GET /restaurants/{id}/menu_items`

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `category` | string | — | Filter by category: `appetizer`, `main_course`, `dessert`, `drink` |
| `search` | string | — | Filter by menu item name |
| `per_page` | integer | 15 | Items per page (max: 100) |

#### Create Menu Item

```http
POST /api/restaurants/1/menu_items
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "Spring Rolls",
    "description": "Crispy vegetable spring rolls",
    "price": 8.99,
    "category": "appetizer",
    "is_available": true
}
```

---

### Response Format

All responses follow a consistent envelope format:

**Success:**

```json
{
    "success": true,
    "message": "Operation successful.",
    "data": { ... }
}
```

**Error:**

```json
{
    "success": false,
    "message": "Error message.",
    "errors": { ... }
}
```

### HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK — request succeeded |
| 201 | Created — resource created |
| 401 | Unauthorized — missing or invalid token |
| 404 | Not Found — resource does not exist |
| 422 | Unprocessable Entity — validation failed |

---

## Design Decisions

### 1. Service Layer Pattern
Business logic is encapsulated in service classes (`RestaurantService`, `MenuItemService`, `AuthService`). Controllers handle only HTTP concerns (request/response), while services own the business rules — making each layer independently testable.

### 2. Form Request Validation
All input validation lives in dedicated Form Request classes, keeping controllers clean and allowing validation rules to be reused or extended without touching controller logic.

### 3. API Resources
Response transformation is delegated to API Resource classes (`RestaurantResource`, `MenuItemResource`), decoupling the internal Eloquent model structure from the public API contract.

### 4. Shallow Nested Routes
Update/delete operations use shallow nesting (`/menu_items/{id}`) instead of fully-nested paths (`/restaurants/{id}/menu_items/{id}`). Creation still uses the nested route for context — this is a standard Laravel convention that keeps URLs concise while preserving semantic clarity.

### 5. Enum for Categories
Menu item categories are backed by a PHP 8.1+ `enum`, enforcing valid values at the type level rather than relying purely on validation rules.

### 6. Authentication Strategy
Read (GET) endpoints are public to support discoverability and unauthenticated clients (e.g., a storefront). Write operations require a Sanctum token, keeping the guard only where it matters.

### 7. Cascade Delete
Deleting a restaurant automatically removes all associated menu items via database-level `ON DELETE CASCADE`, maintaining referential integrity without requiring application-layer cleanup.

---

## Running Tests

```bash
# Using Sail
./vendor/bin/sail test

# Local
php artisan test

# With coverage report
./vendor/bin/sail test --coverage
```

---

## Project Structure

```
app/
├── Enum/MenuItem/
│   └── CategoryEnum.php              # Backed enum for menu categories
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── MenuItemController.php
│   │   └── RestaurantController.php
│   ├── Requests/Api/                 # Form Request validation classes
│   └── Resources/                   # API Resource transformers
├── Models/
│   ├── MenuItem.php
│   ├── Restaurant.php
│   └── User.php
├── Service/                          # Business logic layer
│   ├── AuthService.php
│   ├── MenuItemService.php
│   └── RestaurantService.php
└── Traits/
    └── ApiResponse.php               # Shared response envelope helper
```

---

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
