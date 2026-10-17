# Agent context — composed upMVC SaaS host

This app uses **Composer libraries**, not a forked kernel:

- `bitshost/upmvc` — kernel
- `bitshost/upmvc-saas-pack` — tenancy + Harvest
- optional: `bitshost/upmvc-payments-pack` (and other packs with `saas-kit.json`)

## Before coding

```bash
php vendor/bitshost/upmvc-saas-pack/bin/saas-next.php doctor
php vendor/bitshost/upmvc-saas-pack/bin/saas-next.php agent --goal "…"
```

Paste `docs/agent/generated/saas-last-prompt.md` into the chat when generated.

Human setup guide (kernel repo): [BUILD_SAAS_APP.md](https://github.com/upMVC/upMVC/blob/main/docs/BUILD_SAAS_APP.md).

## Must

1. Scope tenant data with `tenant_id`
2. Domain modules under `src/Modules/Api/Modules/{Domain}/` (literal `Modules` segment)
3. Prefer pack libraries (`MailGateway`, `NotifyRegistry`, `SecretBox`, `Totp`, `AuditLog`, …) — do not copy them into the app
4. Register product notify types in a host provider — pack registry stays empty of domain keys
5. Prefer `bitshost/upmvc-payments-pack` for payment intents; keep host `Billing` only as a thin demo override

## Never

- Commit `.env`, vault secrets, or live payment keys
- Put vertical-only logic into saas-pack / payments-pack
- Invent prices or payment flows that belong in a pack library
