# upMVC-SaaS

Ready-to-run SaaS starter application built from:

- `bitshost/upmvc` — standalone upMVC project/runtime and reusable kernel
- `bitshost/upmvc-saas-pack` — installable SaaS module pack

This repo is the **application layer**. It owns the public entry point, environment, package registration, setup script, and project-specific configuration. The framework code and SaaS modules are pulled in through Composer.

## Current Local Development Setup

This branch uses local path repositories while the pack architecture is being tested:

```json
"repositories": [
  { "type": "path", "url": "../upMVC", "options": { "symlink": true } },
  { "type": "path", "url": "../upMVC-SaaS-Pack", "options": { "symlink": true } }
]
```

The app currently requires:

```json
"bitshost/upmvc": "dev-feature/package-module-packs as 2.1.0",
"bitshost/upmvc-saas-pack": "dev-main as 1.0.0"
```

When the branches are released, these become normal version constraints.

## What This App Contains

```text
public/                 front controller and web server entry
src/Etc/.env.example    app environment template
src/Etc/packages.php    package provider registration
database/demo.sql       demo schema and seed data
scripts/setup.php       create-project setup helper
docs/                   starter documentation
```

What it does **not** contain anymore:

```text
src/Common/             provided by bitshost/upmvc
src/Etc kernel files    provided by bitshost/upmvc
src/Modules/            provided by bitshost/upmvc-saas-pack, unless overridden locally
```

## Architecture

```text
HTTP request
  -> public/index.php
  -> vendor/bitshost/upmvc/src/Etc/Start.php
  -> src/Etc/packages.php
  -> BitsHost\UpmvcSaas\SaasServiceProvider
  -> vendor/bitshost/upmvc-saas-pack/src/Modules/*
```

Local app modules can be added under `src/Modules`. The kernel scans package module paths first and the app module path last, so app routes can override pack routes.

## Quick Start

```bash
composer install
php scripts/setup.php
mysql -u root -p my_saas_db < database/demo.sql
php -S localhost:8000 -t public
```

Then open:

| URL | What You Get |
|-----|--------------|
| `/auth` | Login page from the SaaS pack |
| `/platform-admin` | Platform admin shell |
| `/app` | Tenant admin redirect |
| `/app/{slug}` | Public tenant frontend |
| `/shop/{slug}` | Tenant shop shell |

Default demo credentials are documented in `database/demo.sql`.

## Package Registration

The SaaS pack is enabled by `src/Etc/packages.php`:

```php
return [
    \BitsHost\UpmvcSaas\SaasServiceProvider::class,
];
```

The provider registers the SaaS module path, protected routes, `tenant` middleware, and `feature:*` middleware factory.

## Documentation

See [`docs/README.md`](docs/README.md).

For the base framework, see [upMVC](https://github.com/upMVC/upMVC).  
For the reusable SaaS modules, see [upMVC-SaaS-Pack](https://github.com/upMVC/upMVC-SaaS-Pack).

## License

MIT — see [LICENSE](LICENSE).
