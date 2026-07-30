<?php

namespace App\Modules\Api\Modules\Billing\Routes;

use App\Modules\Api\Modules\Billing\Controller;

class Routes
{
    public function routes($router): void
    {
        // Tenant-initiated: JWT required, tenant taken from the token.
        $router->addRoute('/api/billing/checkout', Controller::class, 'checkout', ['cors', 'jwt'], ['POST']);

        // Stripe-initiated: no JWT possible — authenticated by HMAC signature instead.
        $router->addRoute('/api/billing/webhook', Controller::class, 'webhook', ['cors'], ['POST']);
    }
}
