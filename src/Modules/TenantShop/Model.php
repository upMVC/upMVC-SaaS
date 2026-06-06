<?php

namespace App\Modules\TenantShop;

use App\Common\Bmvc\BaseModel;
use PDO;

/**
 * TenantShop\Model
 *
 * Skeleton — add queries as your storefront grows.
 * All queries MUST be scoped to $this->tenantId via tq*() helpers.
 */
class Model extends BaseModel
{
    /**
     * Resolve a tenant record by slug.
     * Returns null when slug not found.
     */
    public function getTenantBySlug(string $slug): ?array
    {
        $stmt = $this->conn->prepare(
            'SELECT id, name, slug, plan_id FROM tenants WHERE slug = :slug AND status = "active" LIMIT 1'
        );
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // TODO: add product listings, category queries, etc.
}
