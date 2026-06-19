# upMVC-SaaS Starter Documentation

`upMVC-SaaS` is now the composed starter application:

```text
upMVC-SaaS = upMVC kernel + upMVC-SaaS-Pack + app config
```

This documentation covers the starter app and the SaaS product flow. Framework internals live in [`upMVC`](https://github.com/upMVC/upMVC). Reusable SaaS modules live in [`upMVC-SaaS-Pack`](https://github.com/upMVC/upMVC-SaaS-Pack).

## Documentation Index

| Doc | What It Covers |
|-----|----------------|
| [01 — What is upMVC-SaaS](01-what-is-saas.md) | The starter concept and SaaS goal |
| [02 — Architecture](02-architecture.md) | Composer packages, request flow, module loading |
| [03 — Getting Started](03-getting-started.md) | Install, configure, import demo data, run |
| [04 — API Modules](04-api-modules.md) | SaaS pack API module conventions |
| [05 — Auth & JWT](05-auth-jwt.md) | Login, refresh tokens, JWT context |
| [06 — Tenants & Plans](06-tenants-plans.md) | Tenants, plans, feature gating |
| [07 — Platform Admin](07-platform-admin.md) | Admin shell, impersonation, tenant control |
| [08 — Your Way](08-your-way.md) | How to customize or override pack behavior |

## Quick Orientation

```text
public/index.php          app entry point
src/Etc/.env.example      app environment template
src/Etc/packages.php      enables the SaaS pack
database/demo.sql         demo schema and data
vendor/bitshost/upmvc     framework/runtime
vendor/bitshost/upmvc-saas-pack
  src/Modules/*           SaaS modules
```

Add local overrides in `src/Modules` only when you need to replace or extend a pack module.
