# 04 — API Modules

## The Api/* pattern

All business logic lives under `src/Modules/Api/`. Each subdirectory is one domain — a standard upMVC module with Controller, Model, and Routes.

```
src/Modules/Api/
├── Auth/
│   ├── Controller.php
│   ├── Model.php
│   └── Routes/Routes.php
├── Plans/
├── Tenants/
└── Admin/
```

Modules are auto-discovered by `InitModsImproved` — drop a folder with `Routes/Routes.php` and it works. No registration required.

---

## Adding your own domain module

Example: adding a `Bookings` module.

**1. Create the folder structure:**
```
src/Modules/Api/Bookings/
├── Controller.php
├── Model.php
└── Routes/Routes.php
```

**2. Controller — extends BaseApiController:**

```php
<?php
namespace App\Modules\Api\Bookings;

use App\Common\Bmvc\BaseApiController;

class Controller extends BaseApiController
{
    public function index(): never
    {
        $tenantId = (int) ($this->user['tenant_id'] ?? 0);
        $bookings = (new Model())->listByTenant($tenantId);
        $this->success($bookings);
    }

    public function create(): never
    {
        $body = $this->requireFields(['date', 'client_id', 'service_id']);
        $tenantId = (int) ($this->user['tenant_id'] ?? 0);
        $id = (new Model())->create($tenantId, $body);
        $this->success(['id' => $id], 'Booking created', 201);
    }
}
```

**3. Model — extends BaseModel:**

```php
<?php
namespace App\Modules\Api\Bookings;

use App\Common\Bmvc\BaseModel;

class Model extends BaseModel
{
    public function listByTenant(int $tenantId): array
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM bookings WHERE tenant_id = :tid ORDER BY date DESC"
        );
        $stmt->execute([':tid' => $tenantId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
```

**4. Routes:**

```php
<?php
namespace App\Modules\Api\Bookings\Routes;

use App\Modules\Api\Bookings\Controller;

class Routes
{
    public function routes($router): void
    {
        $router->addRoute('/api/bookings',        Controller::class, 'index',  ['cors', 'jwt']);
        $router->addRoute('/api/bookings/create', Controller::class, 'create', ['cors', 'jwt']);
    }
}
```

That's it. The module is live.

---

## BaseApiController — what you get

| Property / Method | What it provides |
|---|---|
| `$this->user` | Decoded JWT payload — `sub`, `username`, `tenant_id`, `role` |
| `$this->body()` | Parsed JSON request body |
| `$this->requireFields(['a','b'])` | Returns body array or sends 400 if fields missing |
| `$this->success($data, $msg, $code)` | JSON `{"success":true,"data":...}` |
| `$this->error($msg, $code)` | JSON `{"error":"..."}` and exits |

---

## Tenant isolation pattern

Every model method that reads or writes tenant data must scope by `tenant_id`. This is your responsibility — the framework gives you the tenant ID via `$this->user['tenant_id']`, you use it in your queries.

```php
// Always scope to tenant
"SELECT * FROM bookings WHERE tenant_id = :tid"

// Never expose cross-tenant data
"SELECT * FROM bookings"  // ← wrong
```
