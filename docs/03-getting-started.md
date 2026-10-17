# 03 — Getting Started

## Requirements

- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Composer
- Apache + mod_rewrite, Nginx, or `php -S`

## 1. Install

```bash
composer create-project bitshost/upmvc-saas my-saas
cd my-saas
```

Or clone this repo and run `composer install`.

Pins (Packagist):

```json
"bitshost/upmvc": "^2.5",
"bitshost/upmvc-saas-pack": "^1.1"
```

## 2. Configure environment

```bash
php scripts/setup.php
```

Or copy `src/Etc/.env.example` → `src/Etc/.env`.

Minimum:

```env
DOMAIN_NAME=http://localhost:8000
SITE_PATH=

DB_HOST=localhost
DB_PORT=3306
DB_NAME=upmvc_saas
DB_USER=root
DB_PASS=

JWT_SECRET=your-random-secret-minimum-32-chars
APP_KEY=your-app-key
APP_ENV=development
```

Built-in server: empty `SITE_PATH`. XAMPP subfolder: set `SITE_PATH` to the public path (e.g. `/upMVC-SaaS/public`).

## 3. Migrate (preferred)

```bash
php vendor/bitshost/upmvc/src/Tools/migrate.php
```

You get kernel `users` + pack SaaS tables (`001`) + Harvest ops (`002`).

Optional demo users/tenants from the pack:

```bash
mysql -u root -p upmvc_saas < vendor/bitshost/upmvc-saas-pack/database/seed.sql
```

**Alternative shortcut:** `mysql … < database/demo.sql` (legacy one-shot). Prefer migrate + pack seed for new installs.

## 4. Doctor

```bash
php vendor/bitshost/upmvc-saas-pack/bin/saas-next.php doctor
php vendor/bitshost/upmvc-saas-pack/bin/saas-next.php packs
```

## 5. Run

```bash
php -S localhost:8000 -t public
```

| URL | Result |
|-----|--------|
| `/auth` | Login |
| `/platform-admin` | Platform admin |
| `/app` / `/app/{slug}` | Tenant app / public |
| `/shop/{slug}` | Shop shell |

## 6. Providers

```php
// src/Etc/packages.php
return [
    \BitsHost\UpmvcSaas\SaasServiceProvider::class,
];
```

If pack routes answer, the compose stack is working.

## Next

- Scaffold a domain: `php vendor/bitshost/upmvc-saas-pack/bin/saas-next.php scaffold-domain Bookings`
- Full guide: [BUILD_SAAS_APP](https://github.com/upMVC/upMVC/blob/main/docs/BUILD_SAAS_APP.md)
- Harvest: [docs/09](https://github.com/upMVC/upMVC-SaaS-Pack/blob/master/docs/09-harvest-ops.md)
