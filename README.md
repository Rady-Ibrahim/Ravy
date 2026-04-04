Modules/Auth/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   └── AuthController.php       # login, register, verify, logout, profile
│   │   └── Admin/
│   │       └── AuthController.php       # showLoginForm, login, logout
│   ├── Requests/
│   │   ├── Api/
│   │   │   ├── LoginRequest.php
│   │   │   ├── RegisterRequest.php
│   │   │   └── VerifyRequest.php       # للتحقق من OTP/email
│   │   └── Admin/
│   │       └── AdminLoginRequest.php
│   └── Middleware/                      # لو في أي Middleware خاص بالموديول
├── Services/
│   ├── Api/
│   │   ├── LoginService.php
│   │   ├── RegisterService.php
│   │   ├── OtpService.php
│   │   └── LogoutService.php
│   └── Admin/
│       ├── LoginService.php
│       └── LogoutService.php
├── Models/
│   └── User.php                          # HasFactory, scopes, type/status/phone
├── Routes/
│   ├── api.php                            # middleware api + prefix api/v1/auth
│   └── web.php                            # prefix admin/auth + لوحة admin
├── database/
│   ├── migrations/
│   ├── seeders/
│   │   └── AuthDatabaseSeeder.php
│   └── factories/
│       └── UserFactory.php               # يربط الموديل User الخاص بالموديول
├── Providers/
│   └── AuthServiceProvider.php           # تحميل Routes, Migrations, config
├── config/
│   └── config.php                         # إعدادات خاصة بالموديول
└── composer.json                          # psr-4: "Modules\\Auth\\": "./"