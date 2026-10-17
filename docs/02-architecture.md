# 02 — Architecture

## Three pieces (+ optional fourth)

```text
bitshost/upmvc                 kernel / runtime
bitshost/upmvc-saas-pack       SaaS modules + Harvest libraries + migrations 001/002
bitshost/upmvc-payments-pack   optional payments (suggest)
upMVC-SaaS (this repo)         thin host app
```

## Request flow

```text
HTTP
  → public/index.php          (defines UPMVC_APP_ROOT)
  → vendor/.../upmvc/src/Etc/Start.php
  → src/Etc/packages.php
  → BitsHost\UpmvcSaas\SaasServiceProvider
  → Router + middleware
  → pack or host module controller
```

## Module loading

```text
vendor/bitshost/upmvc-saas-pack/src/Modules   (first)
src/Modules                                   (last — can override pack routes)
```

Domain APIs belong under **`src/Modules/Api/Modules/{Domain}/`** (literal `Modules` segment). Shallower trees are not discovered.

## What the SaaS pack provides

**APIs / shells:** Auth, Plans, Tenants, Admin, PlatformAdmin, TenantApp, TenantShop, Home, …  
**Harvest:** `MailGateway`, `SecretBox`, `Notify*`, `AuditLog`, `Totp`, Attention, i18n overlays — see [Harvest ops](https://github.com/upMVC/upMVC-SaaS-Pack/blob/master/docs/09-harvest-ops.md).  
**Migrations:** `001_saas_layer` (plans/tenants/refresh_tokens), `002_saas_ops_layer` (ops tables).

## Middleware

| Key | Source | Role |
|-----|--------|------|
| `cors` | upMVC | CORS / preflight |
| `jwt` | upMVC | Bearer → `$GLOBALS['current_user']` |
| `tenant` | saas-pack | tenant context |
| `feature:x` | saas-pack | plan feature flag |

## Database

Preferred:

```bash
php vendor/bitshost/upmvc/src/Tools/migrate.php
```

Order: kernel → pack → app. Optional demo shortcut: `database/demo.sql`. Pack seed: `vendor/bitshost/upmvc-saas-pack/database/seed.sql`.

## Optional payments

```bash
composer require bitshost/upmvc-payments-pack
# uncomment PaymentsServiceProvider in packages.php
```

Host `src/Modules/Api/Modules/Billing` is a **demo Stripe Checkout** override — prefer the payments pack for new payment-intent work.

## Helper CLI

```bash
php vendor/bitshost/upmvc-saas-pack/bin/saas-next.php doctor
```

Discovers packs via each package’s `saas-kit.json`.
