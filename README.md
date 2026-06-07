# upMVC-SaaS

**Multi-tenant SaaS boilerplate built on the upMVC PHP framework.**

Spin up a production-ready SaaS platform: multi-tenant architecture, JWT authentication, role-based access, plan gating, a platform admin dashboard, and an API-first data layer — all wired up out of the box.

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Packagist](https://img.shields.io/packagist/v/bitshost/upmvc-saas)](https://packagist.org/packages/bitshost/upmvc-saas)

---

## What's included

- **Multi-tenant isolation** — every tenant has their own slug, users, plan, and data scope
- **Dual auth** — session-based login for web shells + stateless JWT for all API calls
- **Role-based access** — `platform_admin`, `tenant_owner`, `tenant_user` enforced at middleware level
- **Plan & feature gating** — `PlanGateMiddleware` reads plan limits and feature flags per request
- **Platform Admin** — manage all tenants, change plans/status, impersonate any tenant
- **Impersonation + resume** — platform admin can log in as any tenant and return cleanly
- **API-first architecture** — all data flows through `Api/*` modules; web modules are thin JS-driven shells
- **JWT rotate** — access token + refresh token rotation with theft detection
- **Rate limiting** — configurable per-route via middleware
- **PHPMailer** — account activation emails out of the box
- **Modern UI** — login page, platform admin, and tenant admin all ship with a clean, responsive design

---

## Architecture

```
src/Modules/
├── Api/                    the data backbone — all business logic lives here
│   ├── Modules/Auth/       POST /api/auth/login, /refresh, /logout
│   ├── Modules/Plans/      GET  /api/plans
│   ├── Modules/Tenants/    tenant registration, show, users, public slug
│   └── Modules/Admin/      platform admin API — tenant CRUD, impersonate
│
├── Auth/                   session-based web login (renders the login/signup pages)
├── PlatformAdmin/          web shell → calls Api/Admin via JS fetch
├── TenantApp/              tenant dashboard shell → calls Api/Tenants via JS fetch
├── TenantShop/             public storefront shell → calls Api/public/tenants via JS fetch
├── Home/                   public landing page
└── Mail/                   PHPMailer wrapper
```

**The rule:** web modules never query the database directly. They render an HTML shell, inject a JWT from the PHP session into JS, and let the browser fetch everything from the API. One data layer, no duplication.

---

## Installation

```bash
composer create-project bitshost/upmvc-saas my-saas
cd my-saas
```

The setup script runs automatically and:
- copies `.env.example` to `.env` with auto-generated `JWT_SECRET` and `APP_KEY`
- creates `storage/` and `src/logs/` directories

---

## Quick start

**1. Configure the environment**

Edit `src/Etc/.env`:

```ini
DOMAIN_NAME=http://localhost
SITE_PATH=/my-saas/public      # or empty if domain root

DB_HOST=localhost
DB_NAME=my_saas_db
DB_USER=root
DB_PASS=secret
```

**2. Import the schema and demo data**

```bash
mysql -u root -p my_saas_db < database/demo.sql
```

**3. Run**

```bash
php -S localhost:8000 -t public
```

**4. Open your browser**

| URL | What you get |
|-----|-------------|
| `/auth` | Login page |
| `/platform-admin` | Platform admin dashboard (role: `platform_admin`) |
| `/app` | Tenant admin — redirects to your tenant slug |
| `/app/{slug}` | Public tenant frontend |
| `/shop/{slug}` | Tenant storefront |

Default platform admin credentials are in `database/demo.sql`.

---

## API endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/auth/login` | — | Login, returns access + refresh tokens |
| POST | `/api/auth/refresh` | Bearer | Rotate refresh token |
| POST | `/api/auth/logout` | Bearer | Revoke refresh token |
| GET | `/api/plans` | — | List all plans |
| POST | `/api/tenants/register` | — | Register a new tenant |
| GET | `/api/tenants/{id}` | JWT | Tenant + plan details |
| GET | `/api/tenants/{id}/users` | JWT | Tenant user list |
| PATCH | `/api/tenants/{id}/update` | JWT | Update tenant |
| GET | `/api/public/tenants/{slug}` | — | Public tenant info |
| GET | `/api/admin/tenants` | JWT (admin) | List all tenants |
| PATCH | `/api/admin/tenants/{id}` | JWT (admin) | Update tenant name/status/plan |
| POST | `/api/admin/impersonate` | JWT (admin) | Get impersonation token |

---

## Tech stack

| Layer | Technology |
|-------|-----------|
| Language | PHP 8.1+ |
| Framework | upMVC (Modular MVC, PSR-4) |
| Auth | Sessions + HS256 JWT |
| Database | MySQL / MariaDB via PDO |
| Email | PHPMailer |
| Frontend | Vanilla JS fetch (no build step) |
| Autoloading | Composer PSR-4 |

---

## Documentation

Full documentation is in [`/docs`](docs/README.md):

| Doc | Contents |
|-----|---------|
| [01 — What is upMVC-SaaS](docs/01-what-is-saas.md) | Concept, goals, what problem it solves |
| [02 — Architecture](docs/02-architecture.md) | Api/* structure, middleware pipeline, dual auth |
| [03 — Getting Started](docs/03-getting-started.md) | Configure, migrate, create first tenant |
| [04 — API Modules](docs/04-api-modules.md) | How Api/* modules work, adding your own |
| [05 — Auth & JWT](docs/05-auth-jwt.md) | JWT login, refresh rotation, logout, theft detection |
| [06 — Tenants & Plans](docs/06-tenants-plans.md) | Multi-tenancy, plan gating, feature flags |
| [07 — Platform Admin](docs/07-platform-admin.md) | Admin shell, Api/Admin endpoints, impersonation |
| [08 — Your Way](docs/08-your-way.md) | This is a starting point, not a rulebook |

For the base framework (routing, module system, middleware, configuration):
**upMVC docs** → https://github.com/upMVC/upMVC

---

## License

MIT — see [LICENSE](LICENSE).

Built by [BitsHost](https://upmvc.com).
