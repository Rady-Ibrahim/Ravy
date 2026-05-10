# Cart Flow Testing Guide

## 🔍 Problem Analysis - ACTUAL TEST RESULTS

### ⚠️ CRITICAL BACKEND BUG FOUND

**Test Date:** May 9, 2026
**Test Credentials:**
- Email: radyibrahim777@gmail.com
- Password: Password123!
- User ID: 4

### Test Results:

#### 1. Login Test ✅
```http
POST http://127.0.0.1:8000/api/v1/auth/login
Body: {"email":"radyibrahim777@gmail.com","password":"Password123!"}
```
**Response:** SUCCESS
```json
{
  "token": "5|tnP6NyxKV0Lvl1ZVFmjHg5RRDN54rKxMx2cjiGRcb06e1fb6",
  "token_type": "Bearer",
  "user": {
    "id": 4,
    "first_name": "rady",
    "last_name": "ibrahim",
    "email": "radyibrahim777@gmail.com"
  }
}
```

#### 2. Get Cart Test ❌ BUG CONFIRMED
```http
GET http://127.0.0.1:8000/api/v1/cart
Headers: Authorization: Bearer 5|tnP6NyxKV0Lvl1ZVFmjHg5RRDN54rKxMx2cjiGRcb06e1fb6
```
**Response:** BUG - Returns guest_id instead of user_id
```json
{
  "data": {
    "id": 36,
    "status": "active",
    "guest_id": "0c8ab961-9c99-41cc-8609-c7d18d142c1b",  // ❌ SHOULD BE NULL
    "items": [],
    "totals": {...}
  }
}
```
**Expected:** `user_id: 4, guest_id: null`
**Actual:** `guest_id: "0c8ab961-9c99-41cc-8609-c7d18d142c1b", user_id: null`

#### 3. Add Item to Cart Test ❌ BUG CONFIRMED
```http
POST http://127.0.0.1:8000/api/v1/cart/items
Headers: Authorization: Bearer 5|tnP6NyxKV0Lvl1ZVFmjHg5RRDN54rKxMx2cjiGRcb06e1fb6
Body: {"product_id":1,"variant_id":1,"qty":2}
```
**Response:** BUG - Still creates guest cart
```json
{
  "message": "Item added to cart successfully.",
  "data": {
    "id": 37,
    "status": "active",
    "guest_id": "c03e1fbb-6f61-4855-89cf-12e8a7701619",  // ❌ SHOULD BE NULL
    "items": [...]
  }
}
```

#### 4. Checkout Test ❌ FAILED
```http
POST http://127.0.0.1:8000/api/v1/checkout/place-order
Headers: Authorization: Bearer 5|tnP6NyxKV0Lvl1ZVFmjHg5RRDN54rKxMx2cjiGRcb06e1fb6
Body: {...}
```
**Response:** 422 Unprocessable Content
**Reason:** Cart is being treated as guest cart, not user cart

---

## 🐛 ROOT CAUSE - FIXED ✅

**The authentication middleware was NOT working correctly.**

Even when the Authorization header was sent correctly with a valid token, the backend was treating the user as a guest and creating guest carts instead of user carts.

### Root Cause:
The cart routes didn't have the Sanctum middleware applied, so `$request->user()` always returned null.

### Solution Applied:
1. Created custom middleware `OptionalAuthSanctum.php` for optional authentication
2. Registered middleware in `OrdersServiceProvider.php`
3. Applied `optional.sanctum` middleware to cart and checkout routes
4. Updated `CartController.php` to use `auth('sanctum')->user()` instead of `$request->user()`
5. Updated `CheckoutController.php` to use `auth('sanctum')->user()` instead of `$request->user()`
6. Added `user_id` to the payload response in `CartController.php`
7. Fixed missing `Governorate` import in `CartService.php`

---

## ✅ TEST RESULTS AFTER FIX

### Test Date: May 9, 2026 (After Fix)

#### Authenticated User Flow ✅

**1. Login Test:**
```http
POST http://127.0.0.1:8000/api/v1/auth/login
```
**Response:** SUCCESS
```json
{
  "token": "6|gDk3rVqVjppO5fIuCQ5wV6qY74pDp6zlyrEWHQOO986efadf",
  "user": {"id": 4, "email": "radyibrahim777@gmail.com"}
}
```

**2. Get Cart Test:**
```http
GET http://127.0.0.1:8000/api/v1/cart
Headers: Authorization: Bearer 6|gDk3rVqVjppO5fIuCQ5wV6qY74pDp6zlyrEWHQOO986efadf
```
**Response:** ✅ FIXED
```json
{
  "data": {
    "user_id": 4,  // ✅ CORRECT
    "guest_id": null,  // ✅ CORRECT
    "items": []
  }
}
```

**3. Add Item to Cart Test:**
```http
POST http://127.0.0.1:8000/api/v1/cart/items
Headers: Authorization: Bearer 6|gDk3rVqVjppO5fIuCQ5wV6qY74pDp6zlyrEWHQOO986efadf
Body: {"product_id":1,"variant_id":1,"qty":2}
```
**Response:** ✅ FIXED
```json
{
  "data": {
    "user_id": 4,  // ✅ CORRECT
    "guest_id": null,  // ✅ CORRECT
    "items": [
      {
        "product_id": 1,
        "qty": 2,
        "line_total": 4400
      }
    ]
  }
}
```

**4. Checkout Test:**
```http
POST http://127.0.0.1:8000/api/v1/checkout/place-order
Headers: Authorization: Bearer 6|gDk3rVqVjppO5fIuCQ5wV6qY74pDp6zlyrEWHQOO986efadf
```
**Response:** ✅ SUCCESS
```json
{
  "message": "Order created and waiting for payment.",
  "data": {
    "order_number": "RAVY-20260509-94313",
    "grand_total": 4425
  }
}
```

---

#### Guest User Flow ✅

**1. Get Cart Test (No Auth):**
```http
GET http://127.0.0.1:8000/api/v1/cart
```
**Response:** ✅ WORKING
```json
{
  "data": {
    "user_id": null,  // ✅ CORRECT
    "guest_id": "f88a4ee6-68fb-48b8-95f0-d041643315a1",  // ✅ CORRECT
    "items": []
  }
}
```

**2. Add Item to Cart Test (No Auth):**
```http
POST http://127.0.0.1:8000/api/v1/cart/items
Body: {"product_id":1,"variant_id":1,"qty":1}
```
**Response:** ✅ WORKING
```json
{
  "data": {
    "user_id": null,  // ✅ CORRECT
    "guest_id": "5d81d6f9-a4a4-4597-a76a-7793a8cef03e",  // ✅ CORRECT
    "items": [
      {
        "product_id": 1,
        "qty": 1,
        "line_total": 2200
      }
    ]
  }
}
```

**3. Checkout Test (Guest):**
```http
POST http://127.0.0.1:8000/api/v1/checkout/place-order
Body: {"guest_id":"5d81d6f9-a4a4-4597-a76a-7793a8cef03e",...}
```
**Response:** ✅ SUCCESS
```json
{
  "message": "Order created and waiting for payment.",
  "data": {
    "order_number": "RAVY-20260509-27594",
    "grand_total": 2225
  }
}
```

---

## 🎯 FINAL SUMMARY

### ✅ Authentication Bug FIXED

Both authenticated user and guest flows are now working correctly:

| Flow | Status | Details |
|------|--------|---------|
| **Authenticated User** | ✅ WORKING | `user_id: 4, guest_id: null` |
| **Guest User** | ✅ WORKING | `user_id: null, guest_id: UUID` |

### Files Modified:
1. `Modules/Orders/Http/Middleware/OptionalAuthSanctum.php` - Created
2. `Modules/Orders/Providers/OrdersServiceProvider.php` - Registered middleware
3. `Modules/Orders/Routes/api.php` - Applied middleware to cart/checkout routes
4. `Modules/Orders/Http/Controllers/Api/CartController.php` - Updated to use `auth('sanctum')`
5. `Modules/Orders/Http/Controllers/Api/CheckoutController.php` - Updated to use `auth('sanctum')`
6. `Modules/Orders/Services/Api/CartService.php` - Added Governorate import and user_id to payload

---

---

## 📋 Authenticated User Flow

### Step 1: Register
```http
POST {{base_url}}/api/v1/auth/register
Headers: Accept: application/json, Content-Type: application/json
Body:
{
  "first_name": "Test",
  "last_name": "User",
  "email": "testuser@example.com",
  "password": "Password123!",
  "password_confirmation": "Password123!",
  "phone": "+201000000001"
}
```

**Response:**
```json
{
  "token": "4|4sUqhmn5ol9aZoV6PUxAsqqyWJv44OanAdIgGa0B87af3c24",
  "token_type": "Bearer",
  "user": {
    "id": 4,
    "first_name": "Test",
    "last_name": "User",
    "email": "testuser@example.com"
  }
}
```

### Step 2: Login
```http
POST {{base_url}}/api/v1/auth/login
Headers: Accept: application/json, Content-Type: application/json
Body:
{
  "email": "testuser@example.com",
  "password": "Password123!"
}
```

**Response:**
```json
{
  "token": "4|4sUqhmn5ol9aZoV6PUxAsqqyWJv44OanAdIgGa0B87af3c24",
  "token_type": "Bearer",
  "user": {
    "id": 4,
    "email": "testuser@example.com"
  }
}
```

### Step 3: Get Cart (Authenticated)
```http
GET {{base_url}}/api/v1/cart
Headers: 
  Accept: application/json
  Authorization: Bearer 4|4sUqhmn5ol9aZoV6PUxAsqqyWJv44OanAdIgGa0B87af3c24
```

**Expected Response:**
```json
{
  "data": {
    "id": 10,
    "status": "active",
    "user_id": 4,
    "guest_id": null,
    "items": [],
    "totals": {
      "subtotal": 0,
      "shipping_amount": 0,
      "discount_amount": 0,
      "grand_total": 0
    }
  }
}
```

### Step 4: Add Item to Cart (Authenticated)
```http
POST {{base_url}}/api/v1/cart/items
Headers:
  Accept: application/json
  Content-Type: application/json
  Authorization: Bearer 4|4sUqhmn5ol9aZoV6PUxAsqqyWJv44OanAdIgGa0B87af3c24
Body:
{
  "product_id": 1,
  "variant_id": 1,
  "qty": 2
}
```

**Expected Response:**
```json
{
  "message": "Item added to cart successfully.",
  "data": {
    "id": 10,
    "status": "active",
    "user_id": 4,
    "guest_id": null,
    "items": [
      {
        "id": 5,
        "product_id": 1,
        "variant_id": 1,
        "product_name": "Wool Coat Men",
        "variant_sku": "COAT-BLK-M",
        "qty": 2,
        "unit_price": 2200,
        "line_total": 4400
      }
    ],
    "totals": {
      "subtotal": 4400,
      "shipping_amount": 0,
      "discount_amount": 0,
      "grand_total": 4400
    }
  }
}
```

### Step 5: Checkout (Authenticated)
```http
POST {{base_url}}/api/v1/checkout/place-order
Headers:
  Accept: application/json
  Content-Type: application/json
  Authorization: Bearer 4|4sUqhmn5ol9aZoV6PUxAsqqyWJv44OanAdIgGa0B87af3c24
Body:
{
  "governorate_id": 1,
  "shipping_address": {
    "first_name": "Test",
    "last_name": "User",
    "email": "testuser@example.com",
    "phone": "+201000000001",
    "country": "مصر",
    "city": "القاهرة",
    "address_line_1": "شارع الجامعة 15"
  },
  "payment_method": "cod",
  "packaging_option": "eco"
}
```

**Expected Response:**
```json
{
  "message": "Order placed successfully.",
  "data": {
    "order_number": "ORD-12345",
    "status": "pending",
    "items": [...]
  }
}
```

---

## 📋 Guest User Flow

### Step 1: Get Cart (Guest - First Request)
```http
GET {{base_url}}/api/v1/cart
Headers: Accept: application/json
```

**Response:**
```json
{
  "data": {
    "id": 35,
    "status": "active",
    "guest_id": "71015c2d-7997-45d8-baa5-cde6ef397271",
    "items": [],
    "totals": {
      "subtotal": 0,
      "shipping_amount": 0,
      "discount_amount": 0,
      "grand_total": 0
    }
  }
}
```

**⚠️ Important:** The `guest_id` is sent as a cookie. You must include this cookie in subsequent requests.

### Step 2: Add Item to Cart (Guest)
```http
POST {{base_url}}/api/v1/cart/items
Headers:
  Accept: application/json
  Content-Type: application/json
  Cookie: guest_cart_id=71015c2d-7997-45d8-baa5-cde6ef397271
Body:
{
  "product_id": 1,
  "variant_id": 1,
  "qty": 2
}
```

**Response:**
```json
{
  "message": "Item added to cart successfully.",
  "data": {
    "id": 35,
    "status": "active",
    "guest_id": "71015c2d-7997-45d8-baa5-cde6ef397271",
    "items": [
      {
        "id": 6,
        "product_id": 1,
        "variant_id": 1,
        "product_name": "Wool Coat Men",
        "variant_sku": "COAT-BLK-M",
        "qty": 2,
        "unit_price": 2200,
        "line_total": 4400
      }
    ],
    "totals": {
      "subtotal": 4400,
      "shipping_amount": 0,
      "discount_amount": 0,
      "grand_total": 4400
    }
  }
}
```

### Step 3: Checkout (Guest)
```http
POST {{base_url}}/api/v1/checkout/place-order
Headers:
  Accept: application/json
  Content-Type: application/json
  Cookie: guest_cart_id=71015c2d-7997-45d8-baa5-cde6ef397271
Body:
{
  "guest_id": "71015c2d-7997-45d8-baa5-cde6ef397271",
  "governorate_id": 1,
  "shipping_address": {
    "first_name": "Guest",
    "last_name": "User",
    "email": "guest@example.com",
    "phone": "+201000000002",
    "country": "مصر",
    "city": "القاهرة",
    "address_line_1": "شارع الجامعة 15"
  },
  "payment_method": "cod",
  "packaging_option": "eco"
}
```

**Response:**
```json
{
  "message": "Order placed successfully.",
  "data": {
    "order_number": "ORD-12346",
    "status": "pending",
    "items": [...]
  }
}
```

---

## 🔧 Troubleshooting

### Problem: Getting `guest_id` instead of `user_id` when authenticated

**Cause:** Authorization header not being sent

**Solution:**
1. Make sure `Authorization: Bearer {{token}}` is in headers
2. Update the `token` variable in Postman with the actual token
3. Do NOT include `user_id` in the request body

### Problem: Cart ID increments on each request

**Cause:** Cookie not being persisted

**Solution:**
1. Enable cookies in Postman: Settings → General → Enable cookie jar
2. Include the cookie in subsequent requests
3. Use `credentials: 'include'` in frontend fetch calls

### Problem: "Cart is empty" error on checkout

**Cause:** Items added to guest cart, but checkout using user cart

**Solution:**
1. Ensure consistent authentication state
2. Use the same guest_id throughout the guest flow
3. Use the same token throughout the authenticated flow

---

## 📊 Key Differences

| Feature | Authenticated User | Guest User |
|---------|-------------------|------------|
| Identification | Token in headers | Cookie (guest_id) |
| Cart storage | user_id in DB | guest_id in DB |
| Request body | NO user_id | guest_id optional |
| Headers | Authorization required | No Authorization |
| Cookie handling | Not needed | Required for persistence |
