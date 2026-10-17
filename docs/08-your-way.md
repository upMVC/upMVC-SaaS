# 08 — Your Way

## Starting point, not a cage

upMVC-SaaS is a **compose starter**. Patterns that work well (JWT + refresh, shared DB + `tenant_id`, plan JSON flags, pack web shells) are suggestions — except the **pack boundary** rules that keep you from forking the world again.

---

## What you might change

**Domain modules** — add as many `Api/Modules/{Domain}` packages as you need. Keep the discovery path.

**Multi-tenancy strategy** — default is shared schema + `tenant_id`. Stronger isolation (DB-per-tenant) is your choice; still resolve tenant via pack middleware.

**Auth** — JWT for APIs is the pack default. Session-only products can lean on web shells; do not gut JWT if you still expose an API.

**Plans / billing** — plan flags are application-level. For payment *intents* and gateways, use **upmvc-payments-pack**, not another copy of Stripe wiring in every host.

**Web shells** — replace PlatformAdmin / TenantApp with a SPA if you want; keep APIs tenant-scoped.

---

## What is not optional

1. **PSR-4** — namespaces match paths (case-sensitive on Linux).
2. **`tenant_id` on tenant data** — always.
3. **Do not copy** Harvest / payments libraries into the host — require and thin-extend.
4. **Domain notify types** — register in a **host** provider; pack registry stays empty of product keys.

---

## Structure reference

[myCMMS-PHP](https://github.com/BitsHost/myCMMS-PHP) shows a composed host layout (providers, module depth, Packagist pins). Use it for **shape**, not to paste domain code into docs or this starter.

Compose guide: [BUILD_SAAS_APP](https://github.com/upMVC/upMVC/blob/main/docs/BUILD_SAAS_APP.md).  
Agent rules: root [`AGENTS.md`](../AGENTS.md).

---

## Summary

Ship your product. Keep tenancy and ops in packs. Override only what your vertical needs.
