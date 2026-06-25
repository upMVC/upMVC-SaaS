# 03 — Getting Started

## Requirements

- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Composer
- Apache with mod_rewrite, Nginx equivalent, or PHP built-in server

## 1. Install Dependencies

For local development on the current branches, keep these sibling folders:

```text
D:\GitHub\upMVC
D:\GitHub\upMVC-SaaS-Pack
D:\GitHub\upMVC-SaaS
```

Then run:

```bash
composer install
```

Composer uses local path repositories for:

- `bitshost/upmvc`
- `bitshost/upmvc-saas-pack`

## 2. Configure Environment

The setup script can create `src/Etc/.env` and generate secrets:

```bash
php scripts/setup.php
```

Or copy manually:

```bash
copy src\Etc\.env.example src\Etc\.env
```

Minimum values:

```env
DOMAIN_NAME=http://localhost
SITE_PATH=

DB_HOST=localhost
DB_PORT=3306
DB_NAME=my_saas_db
DB_USER=root
DB_PASS=

JWT_SECRET=your-random-secret-minimum-32-chars
APP_KEY=your-app-key
```

When serving with `php -S localhost:8000 -t public`, use an empty `SITE_PATH`.

## 3. Import Demo Schema And Data

```bash
mysql -u root -p my_saas_db < database/demo.sql
```

The demo creates:

- `users`
- `tenants`
- `plans`
- `refresh_tokens`
- demo tenants and users

Default credentials are documented at the top of `database/demo.sql`.

## 4. Run

```bash
php -S localhost:8000 -t public
```

Open:

| URL | Result |
|-----|--------|
| `/auth` | Login page |
| `/platform-admin` | Platform admin |
| `/app` | Tenant app redirect |
| `/app/{slug}` | Public tenant page |
| `/shop/{slug}` | Tenant shop |

## 5. Validate Package Loading

The starter enables the SaaS pack through:

```php
// src/Etc/packages.php
return [
    \BitsHost\UpmvcSaas\SaasServiceProvider::class,
];
```

If routes from the SaaS pack are available, the composed architecture is working.
