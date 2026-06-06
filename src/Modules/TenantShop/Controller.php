<?php

namespace App\Modules\TenantShop;

/**
 * TenantShop\Controller
 *
 * Public tenant storefront. No auth required.
 * Resolves tenant by slug, renders the storefront view.
 *
 * Route: /shop/{slug}
 *        /shop/{slug}/{page}
 */
class Controller
{
    public function display(string $reqRoute, string $reqMet): void
    {
        // Extract slug from URI: /shop/{slug}[/{page}]
        $parts = explode('/', trim($reqRoute, '/'));
        $slug  = $parts[1] ?? '';

        if ($slug === '') {
            header('Location: ' . BASE_URL);
            exit;
        }

        $model  = new Model(); // tenantId resolved after slug lookup
        $tenant = $model->getTenantBySlug($slug);

        $view = new View();

        if (!$tenant) {
            $view->notFound($slug);
            return;
        }

        $view->storefront($tenant);
    }
}
