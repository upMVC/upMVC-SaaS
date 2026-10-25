# upMVC-SaaS

Thin **composed** multi-tenant SaaS starter. You do **not** fork the kernel — you require packs from Packagist and add only your domain.

```text
bitshost/upmvc                 → kernel (boot, router, modules, JWT)
bitshost/upmvc-saas-pack       → tenancy + Harvest ops (mail, notify, audit, MFA, …)
bitshost/upmvc-payments-pack   → optional payments
this repo                      → public/, .env, packages.php, your Modules/
```

Human setup guide (authoritative N-step):  
**[Build a SaaS app on upMVC](https://github.com/upMVC/upMVC/blob/main/docs/BUILD_SAAS_APP.md)**

Compose helper (after `composer install`):

```bash
php vendor/bitshost/upmvc-saas-pack/bin/saas-next.php doctor
php vendor/bitshost/upmvc-saas-pack/bin/saas-next.php init
php vendor/bitshost/upmvc-saas-pack/bin/saas-next.php scaffold-domain YourDomain
```

See pack **[saas-next](https://github.com/upMVC/upMVC-SaaS-Pack/blob/master/docs/SAAS_NEXT.md)** (`saas-kit.json` discovers optional packs such as payments).

## Requirements

- PHP 8.3+
- Composer
- MySQL 5.7+ / MariaDB 10.3+

## Quick start

```bash
composer create-project bitshost/upmvc-saas my-saas
cd my-saas
# or: clone this repo, then composer install

php scripts/setup.php          # .env + secrets + storage dirs
# edit src/Etc/.env — DOMAIN_NAME, SITE_PATH, DB_*

php vendor/bitshost/upmvc/src/Tools/migrate.php
# optional seed (pack):
# mysql -u root -p your_db < vendor/bitshost/upmvc-saas-pack/database/seed.sql

php vendor/bitshost/upmvc-saas-pack/bin/saas-next.php doctor
php -S localhost:8000 -t public
```

| URL | What you get |
|-----|----------------|
| `/auth` | Login (pack) |
| `/platform-admin` | Platform admin shell |
| `/app` / `/app/{slug}` | Tenant app / public |
| `/shop/{slug}` | Tenant shop shell |

Prefer **migrate** (kernel `users` → pack `001`/`002` Harvest). `database/demo.sql` remains a one-shot shortcut if you want it; pack `database/README.md` explains seed vs schema.

## Package registration

```php
// src/Etc/packages.php
return [
    \BitsHost\UpmvcSaas\SaasServiceProvider::class,
    // \BitsHost\UpmvcPayments\PaymentsServiceProvider::class,  // after: composer require bitshost/upmvc-payments-pack
];
```

## What this app owns

```text
public/                 front controller (UPMVC_APP_ROOT)
src/Etc/.env.example    environment template
src/Etc/packages.php    provider registration
src/Modules/            optional local domains / overrides (e.g. demo Billing)
scripts/setup.php       create-project helper
docs/                   starter docs
AGENTS.md               Cursor / agent compose rules
```

Kernel and SaaS modules live under `vendor/` — do not copy them into the app.

## Local path repos (optional, maintainers only)

Released pins are Packagist (`^2.5` / `^1.1`). For sibling-folder work, add path repositories temporarily; do not commit them as the default for end users.

## Documentation

| Doc | Why |
|-----|-----|
| [docs/](docs/README.md) | Starter index |
| [BUILD_SAAS_APP](https://github.com/upMVC/upMVC/blob/main/docs/BUILD_SAAS_APP.md) | Compose floor |
| [upMVC-SaaS-Pack](https://github.com/upMVC/upMVC-SaaS-Pack) | Tenancy + Harvest |
| [Harvest ops](https://github.com/upMVC/upMVC-SaaS-Pack/blob/master/docs/09-harvest-ops.md) | Mail / notify / dual-unseal |
| [saas-next](https://github.com/upMVC/upMVC-SaaS-Pack/blob/master/docs/SAAS_NEXT.md) | doctor / init / agent / scaffold |
| [upMVC-Payments-Pack](https://github.com/upMVC/upMVC-Payments-Pack) | Optional payments |
| Structure reference | [myCMMS-PHP](https://github.com/BitsHost/myCMMS-PHP) (layout only — not domain copy) |

## License

MIT — see [LICENSE](LICENSE).
