# Restaurant Menu Management API

A RESTful API for managing restaurants and their menu items, built with Laravel.

## Tech Stack

- **Framework**: Laravel 12
- **PHP**: 8.4
- **Database**: MySQL 8.4
- **Authentication**: Laravel Sanctum (Token-based)
- **Testing**: Pest PHP
- **Containerization**: Docker (Laravel Sail)

## Requirements

- Docker & Docker Compose
- OR PHP 8.2+, Composer, MySQL

## Setup Instructions

### Using Docker (Laravel Sail) - Recommended

```bash
# Clone the repository
git clone https://github.com/fr-wawan/menu-management-api
cd menu-management-api

# Copy environment file
cp .env.example .env

# Install dependencies (if you have PHP & Composer locally)
composer install

# OR if you don't have PHP locally, use Docker:
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs

# Start containers
./vendor/bin/sail up -d

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

### Local Development (Without Docker)

```bash
# Clone the repository
git clone <repository-url>
cd menu-management-api

# Copy environment file and configure database
cp .env.example .env

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

## API Documentation

### Postman Collection

A complete Postman collection is included for easy API testing:

1. Import `Menu Management API.postman_collection.json` into Postman
2. Import `Menu Management API.postman_environment.json` as environment
3. Select the "Menu Management API" environment
4. Run "Register" or "Login" first - the token will be automatically saved

### Base URL
```
http://localhost:8081/api
```

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

### Restaurants

| Method | Endpoint | Auth Required | Description |
|--------|----------|---------------|-------------|
| GET | `/restaurants` | No | List all restaurants (paginated) |
| GET | `/restaurants/{id}` | No | Get restaurant with menu items |
| POST | `/restaurants` | Yes | Create a restaurant |
| PUT | `/restaurants/{id}` | Yes | Update a restaurant |
| DELETE | `/restaurants/{id}` | Yes | Delete a restaurant |

#### Query Parameters (GET /restaurants)
- `search` - Search by restaurant name
- `per_page` - Items per page (default: 15, max: 100)

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

### Menu Items

| Method | Endpoint | Auth Required | Description |
|--------|----------|---------------|-------------|
| GET | `/restaurants/{id}/menu_items` | No | List menu items (paginated) |
| POST | `/restaurants/{id}/menu_items` | Yes | Add a menu item |
| PUT | `/menu_items/{id}` | Yes | Update a menu item |
| DELETE | `/menu_items/{id}` | Yes | Delete a menu item |

#### Query Parameters (GET /restaurants/{id}/menu_items)
- `category` - Filter by category (`appetizer`, `main_course`, `dessert`, `drink`)
- `search` - Search by menu item name
- `per_page` - Items per page (default: 15, max: 100)

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

### Response Format

All responses follow a consistent format:

**Success Response:**
```json
{
    "success": true,
    "message": "Operation successful.",
    "data": { ... }
}
```

**Error Response:**
```json
{
    "success": false,
    "message": "Error message.",
    "errors": { ... }
}
```

### HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created |
| 401 | Unauthorized |
| 404 | Not Found |
| 422 | Validation Error |

## Design Decisions

### 1. Service Layer Pattern
Business logic is encapsulated in service classes (`RestaurantService`, `MenuItemService`, `AuthService`) to keep controllers thin and improve testability. Controllers handle HTTP concerns while services handle business rules.

### 2. Form Request Validation
All input validation is handled by dedicated Form Request classes, keeping validation logic separate from controllers and enabling reuse.

### 3. API Resources
Response transformation is handled by API Resource classes (`RestaurantResource`, `MenuItemResource`) to ensure consistent JSON output and decouple internal models from API responses.

### 4. Shallow Nested Routes
Menu item update/delete routes use shallow nesting (`/menu_items/{id}` instead of `/restaurants/{id}/menu_items/{id}`) for cleaner URLs while maintaining the nested route for creation.

### 5. Enum for Categories
Menu item categories use PHP 8.1+ backed enums for type safety and validation, preventing invalid category values.

### 6. Authentication Strategy
- **Public endpoints**: GET requests (list/view) are public for discoverability
- **Protected endpoints**: Write operations (create/update/delete) require authentication
- This reflects real-world scenarios where menus are publicly viewable but only authorized users can modify them.

### 7. Cascade Delete
When a restaurant is deleted, all associated menu items are automatically deleted via database foreign key constraints, maintaining referential integrity.

## Running Tests

```bash
# Using Sail
./vendor/bin/sail test

# Local
php artisan test

# With coverage
./vendor/bin/sail test --coverage
```

## Project Structure

```
app/
├── Enum/MenuItem/
│   └── CategoryEnum.php          # Menu item categories
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php    # Authentication
│   │   ├── MenuItemController.php
│   │   └── RestaurantController.php
│   ├── Requests/Api/             # Form validation
│   └── Resources/                # API response transformation
├── Models/
│   ├── MenuItem.php
│   ├── Restaurant.php
│   └── User.php
├── Service/                      # Business logic layer
│   ├── AuthService.php
│   ├── MenuItemService.php
│   └── RestaurantService.php
└── Traits/
    └── ApiResponse.php           # Consistent API responses
```

## License

This project is open-sourced software licensed under the MIT license.
