# 02 — Architecture

## Three Pieces

```text
bitshost/upmvc
  standalone project + reusable runtime/kernel

bitshost/upmvc-saas-pack
  reusable SaaS modules, middleware, provider, demo schema

upMVC-SaaS
  ready starter app using both packages
```

This repo is the starter app. It intentionally keeps only app-owned files and consumes the framework and SaaS modules through Composer.

## Request Flow

```text
HTTP Request
  ↓
public/index.php
  ↓
vendor/bitshost/upmvc/src/Etc/Start.php
  ↓
src/Etc/packages.php
  ↓
BitsHost\UpmvcSaas\SaasServiceProvider
  ↓
Router + middleware pipeline
  ↓
SaaS pack module controller
```

`public/index.php` defines `UPMVC_APP_ROOT`, so the kernel knows that this starter app owns `.env`, package registration, and local overrides.

## Module Loading

The kernel can scan multiple module paths.

```text
vendor/bitshost/upmvc-saas-pack/src/Modules
src/Modules
```

Pack modules are registered first. Local app modules are registered last, so app routes can override pack routes.

This repo does not include `src/Modules` by default. Create it only when you need local custom modules or overrides.

## SaaS Pack Modules

The SaaS pack provides:

```text
Api/
  Modules/Auth      POST /api/auth/login, /refresh, /logout
  Modules/Plans     GET  /api/plans
  Modules/Tenants   tenant registration and tenant APIs
  Modules/Admin     platform admin APIs

Auth                session login/signup pages
PlatformAdmin       platform admin web shell
TenantApp           tenant admin/public app shell
TenantShop          public tenant shop shell
Home                landing page
Mail                PHPMailer wrapper
```

## Middleware Pipeline

Core middleware comes from `upMVC`. SaaS-specific middleware is registered by the SaaS pack provider.

| Key | Source | What It Does |
|-----|--------|--------------|
| `cors` | upMVC | CORS headers and preflight |
| `jwt` | upMVC | Validates Bearer token and sets `$GLOBALS['current_user']` |
| `tenant` | SaaS pack | Resolves tenant and sets `$GLOBALS['current_tenant']` |
| `feature:x` | SaaS pack | Checks feature flag availability |

## Database

The starter keeps `database/demo.sql` for a one-command demo install. Future migration files can live in the SaaS pack and be registered through the provider.

Core SaaS tables:

```text
users
tenants
plans
refresh_tokens
```
