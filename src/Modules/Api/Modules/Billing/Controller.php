<?php

namespace App\Modules\Api\Modules\Billing;

use App\Etc\Config\Environment;
use BitsHost\UpmvcSaas\Http\SaasApiController;
use Stripe\StripeClient;
use Stripe\Webhook;

class Controller extends SaasApiController
{
    private function config(string $key): string
    {
        return (string) (Environment::get($key) ?: getenv($key) ?: ($_ENV[$key] ?? ''));
    }

    /**
     * POST /api/billing/checkout  ['cors','jwt']
     * Tenant comes from the JWT — the caller cannot choose which tenant to pay for.
     */
    public function checkout(): never
    {
        $body     = $this->requireFields(['plan_id']);
        $tenantId = (int) ($this->user['tenant_id'] ?? 0);

        if ($tenantId === 0) {
            $this->error('No tenant in token', 403);
        }

        $plan = (new Model())->findPlan((int) $body['plan_id']);
        if (!$plan) {
            $this->error('Plan not found', 404);
        }

        $secret = $this->config('STRIPE_SECRET_KEY');
        if ($secret === '') {
            $this->error('STRIPE_SECRET_KEY not configured', 503);
        }

        $session = (new StripeClient($secret))->checkout->sessions->create([
            'mode'                 => 'payment',
            'client_reference_id'  => (string) $tenantId,
            'metadata'             => ['tenant_id' => $tenantId, 'plan_id' => $plan['id']],
            'line_items'           => [[
                'quantity'   => 1,
                'price_data' => [
                    'currency'     => 'ron',
                    'unit_amount'  => (int) round((float) $plan['price'] * 100),
                    'product_data' => ['name' => 'Plan ' . $plan['name']],
                ],
            ]],
            'success_url' => $this->config('APP_URL') . '/app?paid=1',
            'cancel_url'  => $this->config('APP_URL') . '/app?paid=0',
        ]);

        $this->success(['checkout_url' => $session->url], 'Checkout session created');
    }

    /**
     * POST /api/billing/webhook  ['cors']  — NO jwt.
     * Stripe sends no bearer token, so the tenant is recovered from client_reference_id
     * and the request is authenticated by HMAC signature instead.
     */
    public function webhook(): never
    {
        $whSecret = $this->config('STRIPE_WEBHOOK_SECRET');
        if ($whSecret === '') {
            $this->error('STRIPE_WEBHOOK_SECRET not configured', 503);
        }

        $payload   = file_get_contents('php://input') ?: '';
        $signature = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        try {
            $event = Webhook::constructEvent($payload, $signature, $whSecret);
        } catch (\Throwable) {
            $this->error('Invalid signature', 400);
        }

        if ($event->type !== 'checkout.session.completed') {
            $this->success(['ignored' => $event->type]);
        }

        $session  = $event->data->object;
        $tenantId = (int) ($session->client_reference_id ?? 0);
        $planId   = (int) ($session->metadata->plan_id ?? 0);

        if ($tenantId === 0 || $planId === 0) {
            $this->error('Event missing tenant_id / plan_id', 422);
        }

        $model = new Model();
        if (!$model->findTenant($tenantId)) {
            $this->error('Unknown tenant', 404);
        }

        $model->activatePlan($tenantId, $planId);

        $this->success([
            'tenant_id' => $tenantId,
            'plan_id'   => $planId,
            'tenant'    => $model->findTenant($tenantId),
        ], 'Plan activated');
    }
}
