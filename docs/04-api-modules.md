# 04 — API Modules

## The Api/Modules pattern

Pack APIs live under `vendor/bitshost/upmvc-saas-pack/src/Modules/Api/Modules/`. Your host domains use the **same depth**:

```text
src/Modules/Api/Modules/{Domain}/
├── Controller.php
├── Model.php
└── Routes/Routes.php
```

**Depth matters:** the folder must include the literal `Modules` segment. `src/Modules/Api/Bookings/` is **not** discovered.

---

## Adding a domain module

Example: `Bookings`.

**1. Scaffold (recommended):**

```bash
php vendor/bitshost/upmvc-saas-pack/bin/saas-next.php scaffold-domain Bookings
```

Or create manually:

```text
src/Modules/Api/Modules/Bookings/
├── Controller.php
├── Model.php
└── Routes/Routes.php
```

**2. Controller — extends SaasApiController:**

```php
<?php
namespace App\Modules\Api\Modules\Bookings;

use BitsHost\UpmvcSaas\Http\SaasApiController;

class Controller extends SaasApiController
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

**3. Model — always scope `tenant_id`:**

```php
<?php
namespace App\Modules\Api\Modules\Bookings;

use App\Common\Bmvc\BaseModel;

class Model extends BaseModel
{
    public function listByTenant(int $tenantId): array
    {
        $stmt = $this->conn->prepare(
            'SELECT * FROM bookings WHERE tenant_id = :tid ORDER BY date DESC'
        );
        $stmt->execute([':tid' => $tenantId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
```

**4. Routes:**

```php
<?php
namespace App\Modules\Api\Modules\Bookings\Routes;

use App\Modules\Api\Modules\Bookings\Controller;

class Routes
{
    public function routes($router): void
    {
        $router->addRoute('/api/bookings',        Controller::class, 'index',  ['cors', 'jwt']);
        $router->addRoute('/api/bookings/create', Controller::class, 'create', ['cors', 'jwt']);
    }
}
```

---

## SaasApiController

| Property / Method | Provides |
|---|---|
| `$this->user` | JWT payload — `sub`, `username`, `tenant_id`, `role` |
| `$this->body()` | Parsed JSON body |
| `$this->requireFields(['a','b'])` | Body or 400 |
| `$this->success($data, $msg, $code)` | JSON success |
| `$this->error($msg, $code)` | JSON error + exit |

---

## Tenant isolation

Every tenant read/write must filter by `tenant_id`. The framework gives you the id; you enforce it in SQL.

```php
// Always
"SELECT * FROM bookings WHERE tenant_id = :tid"

// Never
"SELECT * FROM bookings"
```

## Demo Billing vs payments pack

This starter may ship `src/Modules/Api/Modules/Billing` as a **Stripe Checkout demo**. For production payment intents and drivers, require **`bitshost/upmvc-payments-pack`** and register `PaymentsServiceProvider`.
