<?php

namespace App\Modules\Api\Modules\Billing;

use App\Common\Bmvc\BaseModel;

class Model extends BaseModel
{
    public function findPlan(int $planId): array|false
    {
        $stmt = $this->conn->prepare(
            "SELECT id, name, price FROM plans WHERE id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $planId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /** Called from the webhook — no JWT context, tenant comes from the event payload. */
    public function activatePlan(int $tenantId, int $planId): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE tenants SET plan_id = :plan, status = 'active' WHERE id = :tenant"
        );
        return $stmt->execute([':plan' => $planId, ':tenant' => $tenantId]);
    }

    public function findTenant(int $tenantId): array|false
    {
        $stmt = $this->conn->prepare(
            "SELECT id, slug, plan_id, status FROM tenants WHERE id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $tenantId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
