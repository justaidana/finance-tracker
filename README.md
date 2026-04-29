# FinTrack — Personal Finance Tracker

A full-stack Laravel 11 personal finance platform with bilingual support (RU/EN), budgeting, savings goals, and analytics.

## Tech Stack

- **Backend**: Laravel 11, PHP 8.3+, MySQL / SQLite
- **Frontend**: Blade + Alpine.js + Tailwind CSS
- **Auth**: Laravel Breeze (session-based, remember_token)
- **Charts**: Chart.js
- **i18n**: Laravel localization (ru / en), stored per user in DB

---

## Quick Start

### 1. Install dependencies
```bash
cd fintrack
composer install
npm install
```

### 2. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure database in `.env`
For **MySQL**:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fintrack
DB_USERNAME=root
DB_PASSWORD=
```
For quick **SQLite** dev:
```
DB_CONNECTION=sqlite
# database/database.sqlite is already created
```

### 4. Run migrations and seed demo data
```bash
php artisan migrate:fresh --seed
```

### 5. Build frontend assets
```bash
npm run build      # production
npm run dev        # watch mode (hot reload)
```

### 6. Start the development server
```bash
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
