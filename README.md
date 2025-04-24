# ACME Donation Platform
## 🚀 Getting Started
1. Clone the repository
```bash
git clone https://github.com/luismgsantos/acme-donation-platform.git
```
2. Install PHP dependencies
```bash
composer install # This is necessary to later have ./vendor/bin/sail to run `./vendor/bin/sail up -d`
```
> **_NOTE:_** You can kind of skip this and just run `docker compose up -d` and then you have to install php dependencies, build the assets and run the migrations on your own with `php artisan migrate`.

3. Install and build frontend assets
```bash
npm install
npm run build
```

## 🧱 Tech Stack
  * PHP 8.4 / Laravel 12
  * SQLite
  * Sanctum for API auth
  * Pest for testing
  * PHPStan (level 8)
  * Dockerized with Laravel Sail

## 📁 Project Overview
| Path                           | Description                                                      |
| ------------------------------ | ---------------------------------------------------------------- |
| `app/Models/`                  | Eloquent models        |
| `app/Http/Controllers/Api/V1/` | Versioned API controllers                                        |
| `app/Http/Controllers/Api/`    | API controllers                                                  |
| `app/Http/Resources/V1/`       | Versioned API resources (`CampaignResource`, `DonationResource`) |
| `app/Notifications/`           | Notifications like `DonationMade`                                |
| `routes/api.php`               | Main API routes entry point (calls versioned route groups)       |
| `routes/auth.php`              | Central auth routes (used across versions)                       |
| `routes/api/v1/campaigns.php`  | V1 Routes for Campaigns                                          |
| `routes/api/v1/donations.php`  | V1 Routes for Donations                                          |
| `tests/Feature/`               | Pest feature tests for API endpoints                             |
| `README.md`                    | Project documentation                                            |

## ✅ Features
  * Authenticated users can create donation campaigns
  * Authenticated users can donate
  * Notifications for both donors and campaign creators (stored in database)
  * Pest tests for core features
  * Dockerized dev environment with SQLite

## ⚒ Dev Notes
  * API is versioned under `/api/v1/`
  * Authentication is handled via Sanctum (see `routes/auth.php`)
  * Use php artisan tinker for quick testing or poking the database
  * Factories and seeders help build fake data fast

## 🧪 Running Tests
```bash
./vendor/bin/sail test
```
Or directly via Pest:
```bash
./vendor/bin/sail pest
```

## 🐳 Docker
### Start the application
```bash
./vendor/bin/sail up -d
```

### Run migrations
```bash
./vendor/bin/sail artisan migrate
```

### Access the app
Open `http://localhost:8000` in your browser.
You can also use `sail shell` or `sail artisan` for interacting with the container.
