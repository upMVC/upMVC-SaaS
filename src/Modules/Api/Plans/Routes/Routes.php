<?php

namespace App\Modules\Api\Plans\Routes;

use App\Modules\Api\Plans\Controller;

class Routes
{
    public function routes($router): void
    {
        $router->addRoute('/api/plans', Controller::class, 'index', ['cors']);
        $router->addParamRoute('/api/plans/{id:int}', Controller::class, 'show', ['cors']);
    }
}
