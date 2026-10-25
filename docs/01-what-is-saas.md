# 01 — What is upMVC-SaaS?

## The short version

**upMVC-SaaS** is a **thin composed host** — not a framework fork. It wires Packagist libraries so you can start a multi-tenant product without rewriting auth, tenancy, or ops:

- Who is logged in? (JWT — kernel + pack Auth)
- Which tenant? (`tenant` middleware, `tenant_id` scoping)
- What can this plan use? (feature gates)
- How do operators run the platform? (platform admin + Harvest ops)

Your competitive advantage is **domain modules**, not another copy of mail/notify/audit.

---

## Where it sits

```text
Your product modules (bookings, assets, invoices, …)
        ↓
this starter (public/, .env, packages.php, thin overrides)
        ↓
upmvc-saas-pack   ← tenancy + Harvest (mail, notify, audit, MFA, i18n, …)
upmvc-payments-pack (optional)
        ↓
upmvc             ← routing, modules, JWT middleware, migrate tool
        ↓
PHP 8.3+
```

You `composer require` the floor. You register providers. You migrate. You build `src/Modules/Api/Modules/{Domain}/`.

---

## What it is NOT

- Not a second framework — **upMVC** is the kernel
- Not a place to paste vertical product code into the pack
- Not a hosted SaaS — you own and run the code
- Not “fork upMVC and add tenants” — that era is over; use packs

---

## Lineage (compose)

```text
upMVC + saas-pack (+ payments) → this starter → your product
```

Structure reference (layout only): [myCMMS-PHP](https://github.com/BitsHost/myCMMS-PHP).  
Teach-to-fish guide: [BUILD_SAAS_APP](https://github.com/upMVC/upMVC/blob/main/docs/BUILD_SAAS_APP.md).

See [08 — Your Way](08-your-way.md) for adapting without breaking boundaries.
