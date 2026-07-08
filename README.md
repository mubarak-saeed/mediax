# MediaX - Digital Content Streaming Platform

<p align="center">
  <strong>A complete Arabic-first platform for streaming movies and series, reading books, and managing subscriptions with online payments.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Filament-5-FBBF24?logo=data:image/svg+xml;base64,..." alt="Filament 5">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4">
  <img src="https://img.shields.io/badge/Stripe-Payments-635BFF?logo=stripe&logoColor=white" alt="Stripe">
</p>

---

## Overview

**MediaX** is a digital content platform that supports movies, series, and e-books, with a multi-tier subscription system and a full admin control panel.

### Key Features

- Multi-type content management: movies, series, books, and cinematic series
- Stripe-powered subscriptions: Basic / Pro / Premium plans
- Flexible authentication: email/password plus Google login
- Advanced search: full-text search via Laravel Scout
- Favorites and ratings: users can favorite and rate content and episodes
- Access control: free, basic, pro, and premium access levels
- Bilingual support: Arabic and English
- Filament v5 admin panel: full management of content, users, and payments

---

## Tech Stack

| Technology | Version | Description |
|------------|---------|-------------|
| Laravel | 12 | Core framework |
| Filament | 5 | Admin panel |
| Livewire | 4 | Interactive components (search, favorites, ratings) |
| Tailwind CSS | 4 | Frontend styling |
| Laravel Cashier | 16 | Stripe payment integration |
| Laravel Scout | 10 | Search engine |
| Laravel Socialite | 5 | Social login (Google) |
| MySQL | 8.0+ | Database |
| Vite | 7 | Frontend asset bundling |

---

## Project Structure

```text
mediax/
|-- app/
|   |-- Actions/Subscription/     # Subscription business logic
|   |-- Enums/                    # Enums (roles, types, currencies...)
|   |-- Filament/                 # Admin panel (Resources, Pages, Widgets)
|   |-- Http/
|   |   |-- Controllers/          # Web controllers
|   |   |-- Middleware/           # Middleware
|   |   \-- Requests/             # Validation requests
|   |-- Listeners/                # Event listeners (Stripe webhooks)
|   |-- Models/                   # Eloquent models
|   |-- Observers/                # Model observers
|   |-- Policies/                 # Authorization policies
|   \-- Services/                 # Domain services
|-- database/
|   |-- factories/                # Test data factories
|   |-- migrations/               # Database migrations
|   \-- seeders/                  # Initial data seeders
|-- resources/views/              # Blade templates
|-- routes/
|   |-- web.php                   # Web routes
|   |-- ai.php                    # AI routes
|   \-- console.php               # Scheduled/console commands
\-- tests/                        # PHPUnit tests
```

---

## Data Models

| Model | Description |
|-------|-------------|
| `User` | Users with different roles (user, admin, super admin) |
| `Content` | Main content (movies, series, books) |
| `Season` | Seasons for series |
| `Episode` | Season episodes |
| `Section` | Main sections (series, movies, books) |
| `SubSection` | Subsections (Arabic, Turkish, anime...) |
| `Genre` | Categories (action, drama, comedy...) |
| `Plan` | Subscription plans integrated with Stripe |
| `PlanFeature` | Features included in each plan |
| `Payment` | Payment records |
| `Favorite` | User favorites (content, season, or episode) |
| `Rating` | User ratings and reviews |

---

## Quick Start

### Requirements

- PHP 8.2+ with extensions: `mbstring`, `openssl`, `pdo_mysql`, `xml`, `bcmath`, `intl`, `curl`, `fileinfo`
- MySQL 8.0+
- Composer 2.x
- Node.js 20+ and npm

### Installation

```bash
# 1. Clone the repository
git clone <repo-url> mediax
cd mediax

# 2. Full setup (dependencies + database + build)
composer run setup
```

> WARNING: `composer run setup` includes `migrate:fresh --seed` and will delete current database data.

### Manual Setup

```bash
# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed

# Create public storage symlink
php artisan storage:link

# Build assets
npm run build
```

### Run in Development

```bash
# Run Laravel + Vite together
composer run dev

# Or run each server separately
php artisan serve        # Laravel server
npm run dev              # Vite dev server
```

---

## Environment Variables

Copy `.env.example` to `.env` and configure the following values:

| Variable | Description |
|----------|-------------|
| `APP_URL` | Full application URL |
| `DB_*` | MySQL database settings |
| `STRIPE_KEY` | Stripe publishable key |
| `STRIPE_SECRET` | Stripe secret key |
| `STRIPE_WEBHOOK_SECRET` | Stripe webhook secret |
| `GOOGLE_CLIENT_ID` | Google OAuth client ID |
| `GOOGLE_CLIENT_SECRET` | Google OAuth client secret |
| `ADMIN_PASSWORD` | Initial admin password |
| `SCOUT_DRIVER` | Search driver (`database`) |

---

## Default Admin Accounts

| Email | Role |
|-------|------|
| `superadmin@mediax.com` | Super Admin |
| `admin@mediax.com` | Platform Admin |

> The password is set through the `ADMIN_PASSWORD` variable in `.env`. Change it immediately after first login.

---

## Admin Panel

The admin panel is available at `/admin` and includes:

- Section and subsection management
- Content management (movies, series, books) with seasons and episodes
- Genre management
- Subscription plan and feature management
- Payments and invoices overview
- User and role management

---

## Testing

```bash
# Run all tests
php artisan test --compact

# Run a specific test file
php artisan test --compact tests/Feature/SomeTest.php
```

---

## Code Formatting

```bash
# Format only modified files
vendor/bin/pint --dirty --format agent
```

---

## Production Deployment

```bash
# Install dependencies without dev packages
composer install --no-dev --optimize-autoloader

# Build assets
npm install && npm run build

# Performance optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan icons:cache

# Schedule tasks
# Add to crontab:
# * * * * * cd /path/to/mediax && php artisan schedule:run >> /dev/null 2>&1
```


---

## License

This project is licensed under the MIT License.
