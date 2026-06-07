<?php

namespace App\Modules\Api\Tenants\Routes;

use App\Modules\Api\Tenants\Controller;

class Routes
{
    public function routes($router): void
    {
        $router->addRoute('/api/tenants/register', Controller::class, 'register', ['cors']);
        $router->addParamRoute('/api/tenants/{id:int}',        Controller::class, 'show',   ['cors', 'jwt']);
        $router->addParamRoute('/api/tenants/{id:int}/update', Controller::class, 'update', ['cors', 'jwt']);
    }
}
