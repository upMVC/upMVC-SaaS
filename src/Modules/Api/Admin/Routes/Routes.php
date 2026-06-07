<?php

namespace App\Modules\Api\Admin\Routes;

use App\Modules\Api\Admin\Controller;

class Routes
{
    public function routes($router): void
    {
        // All routes require JWT — role check (platform_admin) in Controller constructor
        $router->addRoute('/api/admin/tenants',    Controller::class, 'listTenants', ['cors', 'jwt']);
        $router->addRoute('/api/admin/dashboard',  Controller::class, 'dashboard',   ['cors', 'jwt']);
        $router->addRoute('/api/admin/metrics',    Controller::class, 'metrics',     ['cors', 'jwt']);
        $router->addRoute('/api/admin/impersonate', Controller::class, 'impersonate', ['cors', 'jwt']);

        $router->addParamRoute('/api/admin/tenants/{id:int}/status', Controller::class, 'updateStatus',    ['cors', 'jwt']);
        $router->addParamRoute('/api/admin/tenants/{id:int}/plan',   Controller::class, 'updatePlan',      ['cors', 'jwt']);
        $router->addParamRoute('/api/admin/plans/{id:int}',          Controller::class, 'updatePlanDetails', ['cors', 'jwt']);
    }
}
