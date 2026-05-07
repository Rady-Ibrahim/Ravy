# Ravy E-Commerce Platform

A modular Laravel-based e-commerce platform with a focus on clean architecture, scalability, and maintainability.

## 📋 Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Project](#running-the-project)
- [API Documentation](#api-documentation)
- [Design Patterns](#design-patterns)
- [Architecture](#architecture)
- [Module Structure](#module-structure)
- [Testing](#testing)

## ✨ Features

- **Modular Architecture**: Feature-based modules for scalability
- **Authentication**: User registration, login, social auth (Google, Facebook)
- **Product Management**: Categories, products, variants, specifications
- **Shopping Cart**: Full cart management with real-time calculations
- **Order Management**: Order processing with multiple payment methods
- **Payment Integration**: 
  - Cash on Delivery (COD)
  - Paymob payment gateway with webhook support
- **Wishlist**: Product wishlist functionality
- **Multi-language**: Arabic and English support
- **API-first Design**: RESTful API with proper documentation

## 🔧 Requirements

- PHP >= 8.2
- Composer >= 2.0
- MySQL >= 8.0 or SQLite
- Node.js >= 18 (for frontend development)
- Laravel >= 10.x

## 📦 Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd Ravy
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

Edit `.env` file:

```env
DB_CONNECTION=sqlite
# Or for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=ravy
# DB_USERNAME=root
# DB_PASSWORD=your_password
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Seed Database (Optional)

```bash
php artisan db:seed
```

## ⚙️ Configuration

### Environment Variables

Configure the following environment variables in `.env`:

```env
# Application
APP_NAME="Ravy"
APP_ENV=local
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite

# Payment Gateway (Paymob)
PAYMOB_API_KEY=your_paymob_api_key
PAYMOB_INTEGRATION_ID=your_integration_id
PAYMOB_HMAC_SECRET=your_hmac_secret
PAYMOB_BASE_URL=https://accept.paymob.com/api

# Social Authentication
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/api/v1/auth/google/callback

FACEBOOK_CLIENT_ID=your_facebook_client_id
FACEBOOK_CLIENT_SECRET=your_facebook_client_secret
FACEBOOK_REDIRECT_URI=http://localhost:8000/api/v1/auth/facebook/callback
```

## 🚀 Running the Project

### Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Frontend Development

```bash
npm run dev
```

### Queue Worker (if using queues)

```bash
php artisan queue:work
```

## 📚 API Documentation

### Base URL

```
http://localhost:8000/api/v1
```

### Authentication

All authenticated endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer {token}
```

### Available Endpoints

#### Authentication
- `POST /auth/register` - Register new user
- `POST /auth/login` - Login user
- `POST /auth/logout` - Logout user
- `POST /auth/google` - Google OAuth
- `POST /auth/facebook` - Facebook OAuth

#### Categories
- `GET /categories` - List all categories
- `GET /categories/{slug}` - Get category by slug
- `GET /categories/{slug}/breadcrumb` - Get category breadcrumb

#### Products
- `GET /products` - List all products (with filters)
- `GET /products/{slug}` - Get product details
- `POST /products/{slug}/view` - Increment product views
- `POST /products/{slug}/wishlist` - Toggle wishlist
- `GET /wishlist` - Get user wishlist

#### Cart (Auth Required)
- `GET /cart` - Get user cart
- `POST /cart/items` - Add item to cart
- `PUT /cart/items/{id}` - Update item quantity
- `DELETE /cart/items/{id}` - Remove item from cart
- `DELETE /cart` - Clear cart

#### Checkout (Auth Required)
- `GET /checkout/summary` - Get checkout summary
- `POST /checkout/place-order` - Place order

#### Payments (Auth Required)
- `GET /payments/methods` - Get available payment methods
- `POST /payments/initiate` - Initiate payment
- `GET /payments/status/{order}` - Get payment status

#### Webhooks (No Auth)
- `POST /webhooks/paymob` - Paymob payment webhook

### Postman Collection

A complete Postman collection is available in `postman/complete-api.postman_collection.json` with all endpoints organized in folders:
- Authentication
- Categories
- Products
- Orders (Cart, Checkout)
- Payments
- Webhooks

## 🎨 Design Patterns

### 1. Modular Architecture

The application follows a modular architecture where each feature is organized into separate modules:

```
Modules/
├── Auth/
├── Category/
├── Product/
├── Orders/
└── Payments/
```

**Benefits:**
- Separation of concerns
- Easy to maintain and scale
- Reusable components
- Independent development

### 2. Repository Pattern

Each module uses repositories to abstract data access logic:

```php
interface ProductRepositoryInterface
{
    public function findAll(): Collection;
    public function findBySlug(string $slug): ?Product;
}
```

**Benefits:**
- Decouples business logic from data access
- Easy to test with mock repositories
- Centralized query logic

### 3. Service Layer Pattern

Business logic is encapsulated in service classes:

```php
class CartService
{
    public function addItem(User $user, array $payload): Cart
    {
        // Business logic here
    }
}
```

**Benefits:**
- Reusable business logic
- Thin controllers
- Easy to test

### 4. Strategy Pattern (Payments)

Payment gateways implement a common interface:

```php
interface PaymentGatewayContract
{
    public function initiate(Order $order, array $context): PaymentResponseDTO;
    public function verify(array $payload): PaymentResponseDTO;
}

class CodPaymentGateway implements PaymentGatewayContract {}
class PaymobPaymentGateway implements PaymentGatewayContract {}
```

**Benefits:**
- Easy to add new payment gateways
- Runtime strategy selection
- Open/Closed Principle

### 5. Factory Pattern (Payment Gateway)

Factory creates the appropriate gateway based on payment method:

```php
class PaymentGatewayFactory
{
    public static function make(string $method): PaymentGatewayContract
    {
        return match($method) {
            'cod' => new CodPaymentGateway(),
            'paymob' => new PaymobPaymentGateway(),
        };
    }
}
```

**Benefits:**
- Centralized object creation
- Encapsulates instantiation logic
- Easy to extend

### 6. Data Transfer Objects (DTOs)

DTOs ensure type-safe data transfer between layers:

```php
class PaymentResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $transactionId,
        public readonly ?string $redirectUrl,
        // ...
    ) {}
}
```

**Benefits:**
- Type safety
- Clear data contracts
- Immutable data structures

### 7. Orchestrator Pattern

PaymentService orchestrates the payment flow:

```php
class PaymentService
{
    public function initiatePayment(Order $order, string $paymentMethod): PaymentResponseDTO
    {
        $gateway = PaymentGatewayFactory::make($paymentMethod);
        $transaction = $this->createTransaction($order, $paymentMethod);
        $response = $gateway->initiate($order, $context);
        // ... orchestration logic
    }
}
```

**Benefits:**
- Complex workflow management
- Transaction coordination
- Error handling in one place

### 8. Request/Response Validation

FormRequest classes handle validation:

```php
class CheckoutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'in:cod,paymob'],
            'shipping_address.*' => ['required'],
        ];
    }
}
```

**Benefits:**
- Reusable validation rules
- Automatic error responses
- Authorization logic

### 9. Resource Transformation

API Resources transform models for API responses:

```php
class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            // ...
        ];
    }
}
```

**Benefits:**
- Consistent API responses
- Hides sensitive data
- Flexible output formatting

### 10. Idempotency Pattern

Payment transactions use idempotency keys to prevent duplicate processing:

```php
$idempotencyKey = md5("{$order->id}-{$paymentMethod}-{$timestamp}");
$existingTransaction = PaymentTransaction::where('idempotency_key', $idempotencyKey)->first();
```

**Benefits:**
- Prevents duplicate payments
- Safe retry logic
- Data consistency

## 🏗️ Architecture

### Layered Architecture

```
┌─────────────────────────────────────┐
│         Controllers Layer          │  (HTTP Request Handling)
├─────────────────────────────────────┤
│          Services Layer            │  (Business Logic)
├─────────────────────────────────────┤
│         Repositories Layer         │  (Data Access)
├─────────────────────────────────────┤
│            Database                │  (Persistence)
└─────────────────────────────────────┘
```

### Module Structure

Each module follows a consistent structure:

```
Modules/{ModuleName}/
├── Contracts/           # Interfaces
├── DTOs/               # Data Transfer Objects
├── Factories/          # Database Factories
├── Gateways/           # External Service Integrations
├── Http/
│   ├── Controllers/    # API Controllers
│   ├── Middleware/     # Custom Middleware
│   └── Requests/       # Form Requests
├── Models/             # Eloquent Models
├── Resources/          # API Resources
├── Services/           # Business Logic
├── database/
│   ├── factories/      # Factories
│   ├── migrations/     # Database Migrations
│   └── seeders/        # Database Seeders
├── Providers/          # Service Providers
├── Routes/             # Route Definitions
└── config/             # Module Configuration
```

## 🧪 Testing

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific module tests
php artisan test --filter=ProductTest

# Run with coverage
php artisan test --coverage
```

### Test Structure

```php
// Feature Test
class ProductTest extends TestCase
{
    public function test_user_can_get_products()
    {
        $response = $this->getJson('/api/v1/products');
        $response->assertStatus(200);
    }
}

// Unit Test
class CartServiceTest extends TestCase
{
    public function test_can_add_item_to_cart()
    {
        $service = new CartService();
        $result = $service->addItem($user, $payload);
        $this->assertInstanceOf(Cart::class, $result);
    }
}
```

## 🔐 Security

- **Authentication**: Laravel Sanctum for API authentication
- **Authorization**: Role-based access control
- **Validation**: Request validation on all inputs
- **Rate Limiting**: API rate limiting
- **Webhook Security**: HMAC signature verification for payment webhooks
- **CSRF Protection**: Enabled for web routes
- **SQL Injection**: Protected by Eloquent ORM
- **XSS Protection**: Input sanitization

## 📝 License

This project is proprietary software.

## 👥 Team

- **Development Team**: Ravy Development Team

## 📞 Support

For support, please contact the development team.