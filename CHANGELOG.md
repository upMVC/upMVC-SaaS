# Changelog

All notable changes to upMVC-SaaS are documented here.

Format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).
Versioning follows [Semantic Versioning](https://semver.org/).

---

## [0.1.0-beta] — 2026-06-07

Initial public beta release.

### Core architecture

- API-first design — all data flows through `Api/*` modules; web modules are thin JS-driven shells with no direct DB access
- PSR-4 autoloading under `App\` with Composer
- Modular MVC structure — each domain is a self-contained `Module/` with its own Controller, Model, View, and Routes

### Authentication

- Session-based login for web shells (`/auth`, `/login`)
- Stateless JWT (HS256) issued at login, stored in `$_SESSION['jwt_token']`, injected into JS shells
- Refresh token rotation with reuse detection and user-token revocation
- Account activation via email token (PHPMailer)
- Role-based access: `platform_admin`, `tenant_owner`, `tenant_user`
- Login enriches session with `tenant_slug` and `tenant_name` via LEFT JOIN — no extra DB calls in web shells

### Multi-tenancy

- Tenant registration with slug auto-generation
- Tenant isolation — all data scoped to `tenant_id`
- Tenant lifecycle: `active`, `trial`, `suspended`
- Plans with price, feature flags (JSON), and limits (JSON)
- `PlanGateMiddleware` — enforces feature flags per route
- `TenantMiddleware` — resolves tenant context from JWT on every API call

### Platform Admin

- Secure admin dashboard at `/platform-admin` (role: `platform_admin`)
- Lists all tenants with live status/plan counts
- Edit tenant: name, status, plan via `PATCH /api/admin/tenants/{id}`
- Impersonate any tenant: issues a scoped JWT, swaps PHP session, redirects to `/app`
- Resume: `/platform-admin/resume` restores the original admin session cleanly
- Toast notifications, modal with blur backdrop, stat cards

### Tenant Admin

- Tenant dashboard at `/app/{slug}/admin`
- Users page at `/app/{slug}/admin/users`
- Dark sidebar with SVG nav icons, plan badge, status indicator
- Impersonation banner when accessed via platform admin
- All data loaded via `Promise.all([/api/tenants/{id}, /api/tenants/{id}/users])`

### Public pages

- `/app/{slug}` — public tenant frontend (JS fetches `/api/public/tenants/{slug}`)
- `/shop/{slug}` — tenant storefront shell
- Both degrade gracefully for suspended tenants

### API endpoints added

- `POST /api/auth/login` — returns access + refresh tokens
- `POST /api/auth/refresh` — rotates refresh token
- `POST /api/auth/logout` — revokes refresh token
- `GET /api/plans` — public plan listing
- `POST /api/tenants/register` — tenant self-registration
- `GET /api/tenants/{id}` — tenant + plan details (JWT)
- `GET /api/tenants/{id}/users` — user list (JWT)
- `PATCH /api/tenants/{id}/update` — tenant self-update (JWT)
- `GET /api/public/tenants/{slug}` — public tenant info (no JWT)
- `GET /api/admin/tenants` — all tenants (platform admin JWT)
- `PATCH /api/admin/tenants/{id}` — admin update (platform admin JWT)
- `POST /api/admin/impersonate` — issue tenant JWT (platform admin JWT)

### UI

- Login/signup pages: split-panel layout, indigo/violet gradient hero, self-contained HTML (no BaseView wrapper)
- Platform Admin: dark sticky header, stat cards, clean table, toast notifications, animated modal
- Tenant Admin: dark sidebar, stat cards with coloured borders, consistent badge system across all pages
- Public pages: glassmorphism nav, gradient hero, SVG feature cards

### Middleware pipeline

- `cors` — CORS headers
- `jwt` — Bearer token validation, injects payload into request context
- `plan_gate` — plan limit enforcement
- `tenant` — tenant context resolution
- Rate limiting configurable via `.env`

### Developer experience

- `post-create-project-cmd` setup script: copies `.env`, auto-generates `JWT_SECRET` + `APP_KEY`, creates runtime directories, prints next steps
- `.gitignore` covers `vendor/`, `.env`, `storage/`, `zbug/`, `private-vault/`, `composer.phar`
- `database/demo.sql` — schema + seed data with platform admin, sample tenants, plans, users
- `database/migrate.php` — migration helper

### Removed

- `gabordemooij/redbean` dependency (was unused, included as a base upMVC demo)
- `BaseControllerOrm` and `BaseModelOrm` (depended on RedBeanPHP, never used in SaaS layer)
- Legacy `Modules/Admin` (generic CRUD demo from upMVC base, replaced by `Modules/PlatformAdmin`)

---

[0.1.0-beta]: https://github.com/upMVC/upMVC-SaaS/releases/tag/v0.1.0-beta
