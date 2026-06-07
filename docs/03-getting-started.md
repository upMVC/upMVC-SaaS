# 03 — Getting Started

## Requirements

- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Composer
- Apache with mod_rewrite (or Nginx equivalent)

---

## 1. Fork or clone

```bash
git clone https://github.com/upMVC/upMVC-SaaS.git my-saas
cd my-saas
composer install
```

---

## 2. Configure environment

Copy the example and fill in your values:

```bash
cp src/Etc/.env.example src/Etc/.env
```

Minimum required:

```env
APP_ENV=development
APP_DEBUG=true

DOMAIN_NAME=http://localhost
SITE_PATH=/my-saas/public

DB_HOST=localhost
DB_PORT=3306
DB_NAME=my_saas_db
DB_USER=root
DB_PASS=

JWT_SECRET=your-random-secret-minimum-32-chars
JWT_ACCESS_TTL=3600
JWT_REFRESH_TTL=2592000

MAIL_HOST=smtp.mailtrap.io
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=ssl
MAIL_PORT=465
```

> `JWT_SECRET` is required. The app throws a `RuntimeException` on first request if it is missing.

---

## 3. Run migrations

```bash
mysql -u root my_saas_db < database/migrations/001_base_schema.sql
mysql -u root my_saas_db < database/migrations/002_saas_layer.sql
```

This creates: `users`, `tenants`, `plans`, `refresh_tokens` and seeds three default plans (Free, Starter, Pro).

---

## 4. Create a platform admin user

Run this once against your database:

```sql
INSERT INTO users (tenant_id, name, username, email, password, token, state, role)
VALUES (
    NULL,
    'Platform Admin',
    'admin',
    'admin@yourapp.com',
    '$2y$10$...bcrypt-hash-of-your-password...',
    '',
    1,
    'platform_admin'
);
```

Generate the bcrypt hash in PHP:
```php
echo password_hash('YourPassword123!', PASSWORD_BCRYPT);
```

---

## 5. Point your web server to `/public`

Apache virtual host example:

```apache
DocumentRoot /var/www/my-saas/public
<Directory /var/www/my-saas/public>
    AllowOverride All
</Directory>
```

The `public/.htaccess` handles all routing automatically.

---

## 6. Test the API

```bash
# Should return the three default plans
curl http://localhost/my-saas/public/api/plans

# Register your first tenant
curl -X POST http://localhost/my-saas/public/api/tenants/register \
  -H "Content-Type: application/json" \
  -d '{"slug":"acme","name":"Acme Corp","username":"acme.admin","email":"admin@acme.com","password":"Test1234!"}'

# Login
curl -X POST http://localhost/my-saas/public/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"acme.admin","password":"Test1234!"}'
```

---

## 7. Access the platform admin dashboard

Log in at `/auth` with your platform admin credentials, then visit `/platform-admin`.
