# FinTrack — Personal Finance Tracker

🌐 **Live:** https://fintrack.myworktime.kz

A full-stack Laravel 11 personal finance platform with bilingual support (RU/EN), budgeting, savings goals, and analytics.

## Tech Stack

- **Backend**: Laravel 11, PHP 8.3+, MySQL / SQLite
- **Frontend**: Blade + Alpine.js + Tailwind CSS
- **Auth**: Laravel Breeze (session-based, remember_token)
- **Charts**: Chart.js
- **i18n**: Laravel localization (ru / en), stored per user in DB

---

## Production Deploy (https://fintrack.myworktime.kz)

```bash
# 1. Clone
git clone https://github.com/justaidana/finance-tracker.git fintrack
cd fintrack

# 2. PHP dependencies (no dev)
composer install --no-dev --optimize-autoloader

# 3. Frontend assets
npm ci && npm run build

# 4. Environment
cp .env.example .env
php artisan key:generate
# Fill in DB_PASSWORD and other secrets:
nano .env

# 5. Database
php artisan migrate --force
php artisan db:seed --force   # optional demo data

# 6. Permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 7. Optimize caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Nginx config

```nginx
server {
    listen 80;
    server_name fintrack.myworktime.kz;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name fintrack.myworktime.kz;

    root /var/www/fintrack/public;
    index index.php;

    ssl_certificate     /etc/letsencrypt/live/fintrack.myworktime.kz/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/fintrack.myworktime.kz/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
```

---

## Local Development

```bash
cp .env.example .env
# Change DB_CONNECTION=sqlite in .env for local dev
php artisan key:generate
php artisan migrate:fresh --seed
npm install && npm run dev
php artisan serve
```
Open [http://localhost:8000](http://localhost:8000)

---

## Demo Credentials

| Field    | Value              |
|----------|--------------------|
| Email    | demo@fintrack.app  |
| Password | demo1234           |
| Locale   | Russian (ru)       |
| Currency | KZT (₸)            |

---

## Features

| Page          | URL             | Description                                      |
|---------------|-----------------|--------------------------------------------------|
| Dashboard     | `/dashboard`    | Summary cards, quick-add, budget bars, tip       |
| Transactions  | `/transactions` | Full CRUD, filters, sort, CSV export             |
| Budget Planner| `/budgets`      | Per-category monthly limits with progress bars   |
| Savings Goals | `/savings`      | Goals with progress, add funds, motivation badge |
| Analytics     | `/analytics`    | Bar, donut, line charts via Chart.js             |
| Financial Tips| `/advice`       | 6-section financial literacy content             |
| Profile       | `/profile`      | Name, email, password, locale, currency, delete  |

---

## Project Structure

```
app/
  Helpers/currency.php        # currencySymbol(), formatMoney(), allCurrencies()
  Http/
    Controllers/              # DashboardController, TransactionController, …
    Middleware/SetLocale.php   # Reads user.locale → App::setLocale()
    Requests/                 # TransactionRequest, BudgetRequest, SavingsGoalRequest
  Models/                     # User, Category, Transaction, Budget, SavingsGoal
  Policies/                   # TransactionPolicy, BudgetPolicy, SavingsGoalPolicy
  Providers/AppServiceProvider.php

database/
  migrations/                 # All schema migrations
  seeders/                    # Demo user, 10 categories, 30 tx, budgets, goals

lang/
  en/app.php                  # English strings
  en/tips.php                 # 23 financial tips (EN)
  ru/app.php                  # Russian strings
  ru/tips.php                 # 23 financial tips (RU)

resources/
  css/app.css                 # Tailwind + custom components
  js/app.js                   # Alpine.js + Chart.js setup
  views/
    layouts/app.blade.php     # Sidebar layout with trust badge + toast system
    layouts/guest.blade.php   # Centered auth layout
    dashboard.blade.php
    transactions/
    budgets/
    savings/
    analytics/
    advice.blade.php
    profile/
    auth/                     # login, register (Breeze-customized)
```

---

## Security

- CSRF protection on all forms
- Policies guard every owner-only action (Transaction, Budget, SavingsGoal)
- Passwords hashed with bcrypt
- Remember-me ON by default; session invalidated on logout
- No raw SQL — all queries via Eloquent ORM

---

## Adding New Languages

1. Create `lang/{locale}/app.php` and `lang/{locale}/tips.php`
2. Add the locale to the `in:` validation rule in `SetLocale.php` and `ProfileController`
3. Add the switcher link in `layouts/app.blade.php`
