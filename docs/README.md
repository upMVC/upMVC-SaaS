# upMVC-SaaS Starter Documentation

```text
upMVC-SaaS = bitshost/upmvc (^2.5) + bitshost/upmvc-saas-pack (^1.1) + thin host
```

Optional later: `bitshost/upmvc-payments-pack`.

This documentation covers the **starter host**. Kernel internals: [upMVC](https://github.com/upMVC/upMVC). Reusable SaaS floor: [upMVC-SaaS-Pack](https://github.com/upMVC/upMVC-SaaS-Pack). Compose how-to: [BUILD_SAAS_APP](https://github.com/upMVC/upMVC/blob/main/docs/BUILD_SAAS_APP.md).

## Documentation Index

| Doc | What it covers |
|-----|----------------|
| [01 — What is upMVC-SaaS](01-what-is-saas.md) | Composed starter vs packs |
| [02 — Architecture](02-architecture.md) | Providers, request flow, Harvest, migrations |
| [03 — Getting Started](03-getting-started.md) | Packagist install, migrate, saas-next |
| [04 — API Modules](04-api-modules.md) | Domain modules under `Api/Modules/{Domain}` |
| [05 — Auth & JWT](05-auth-jwt.md) | Login, refresh, JWT context |
| [06 — Tenants & Plans](06-tenants-plans.md) | Tenants, plans, feature gating |
| [07 — Platform Admin](07-platform-admin.md) | Admin shell, impersonation |
| [08 — Your Way](08-your-way.md) | Customize without forking packs |

## External guides (use these)

| Guide | Why |
|-------|-----|
| [BUILD_SAAS_APP](https://github.com/upMVC/upMVC/blob/main/docs/BUILD_SAAS_APP.md) | N-step compose floor |
| [saas-next](https://github.com/upMVC/upMVC-SaaS-Pack/blob/master/docs/SAAS_NEXT.md) | `doctor` / `init` / `agent` / `scaffold-domain` |
| [Harvest ops](https://github.com/upMVC/upMVC-SaaS-Pack/blob/master/docs/09-harvest-ops.md) | Mail, notify, audit, TOTP, dual-unseal |
| [Payments pack](https://github.com/upMVC/upMVC-Payments-Pack) | Gateways + `payment_intents` |

## Quick orientation

```text
public/index.php              UPMVC_APP_ROOT + boot
src/Etc/.env.example          env template
src/Etc/packages.php          SaasServiceProvider (+ optional payments)
src/Modules/…                 your domains / overrides
AGENTS.md                     Cursor compose rules
vendor/bitshost/upmvc
vendor/bitshost/upmvc-saas-pack
  migrations 001 / 002        SaaS + Harvest tables
  bin/saas-next.php           host helper
```

Prefer **migrate** over copying schema by hand. Seed from the pack when you want demo logins.
